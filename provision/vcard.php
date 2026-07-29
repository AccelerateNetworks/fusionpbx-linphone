<?php
require_once "../root.php";
require_once "resources/require.php";

// Company directory served as a vCard 4.0 document for Linphone's VCard4 friend
// list (provisioned via misc/contacts-vcard-list). Token-authed and login-less
// like provision/index.php; scoped to the requesting device's domain.
//
// This replaces the inline [friend_N] provisioning in template.php, which was
// loaded twice per boot (config parse + provisioning re-apply) and doubled every
// contact (issue #39). liblinphone fetches this URL with a single GET and, on the
// VCard4 sync, clears the list and re-imports it — so it can never double.

$token = $_GET['token'] ?? '';

// Authenticate exactly as template.php does: the device's provisioning_secret
// resolves to its extension + domain. No session / check_auth here (public,
// token-authed endpoint).
$database = new database;
$sql = "select linphone_devices.domain_uuid ";
$sql .= "from v_extensions, linphone_devices ";
$sql .= "where linphone_devices.provisioning_secret = :token ";
$sql .= "and v_extensions.domain_uuid = linphone_devices.domain_uuid ";
$sql .= "and v_extensions.extension_uuid = linphone_devices.extension_uuid";
$device = $database->select($sql, array('token' => $token), 'row');
unset($sql);

if (!$device) {
  // 403, not 401: a 401 without a matching auth_info aborts liblinphone's fetch
  // path; 403 simply yields a failed/empty sync without hijacking HTTP auth.
  http_response_code(403);
  echo "forbidden";
  die();
}

$domain_uuid = $device['domain_uuid'];

// Domain name = SIP host for internal-extension (IMPP) entries.
$sql = "select domain_name from v_domains where domain_uuid = :domain_uuid";
$domain_name = $database->select($sql, array('domain_uuid' => $domain_uuid), 'column');
unset($sql);

// Directory contacts for this domain only (the per-subdomain filter).
// NOTE: template.php binds a bare, undefined $domain_uuid for this same query (a
// latent bug — there is no session in the provision context to populate it); we
// correctly scope by the authenticated device's domain. ORDER BY groups a
// contact's phones together and keeps UIDs stable across syncs.
$sql = "select c.contact_uuid, c.contact_organization, c.contact_name_given, ";
$sql .= "c.contact_name_family, p.phone_label, p.phone_number ";
$sql .= "from v_contacts as c, v_contact_phones as p ";
$sql .= "where c.contact_uuid = p.contact_uuid ";
$sql .= "and p.phone_type_voice = '1' ";
$sql .= "and c.domain_uuid = :domain_uuid ";
$sql .= "order by c.contact_uuid, p.phone_number";
$rows = $database->select($sql, array('domain_uuid' => $domain_uuid), 'all');
unset($sql);

header('Content-Type: text/vcard; charset=utf-8');

if (!is_array($rows) || count($rows) === 0) {
  // No contacts: emit nothing. The client clears its list and imports zero
  // friends — a valid, non-error outcome (an empty directory).
  die();
}

// One vCard per contact; group the ordered phone rows by contact_uuid.
$contacts = array();
foreach ($rows as $row) {
  $uuid = $row['contact_uuid'];
  if (!isset($contacts[$uuid])) {
    $contacts[$uuid] = array(
      'given'  => $row['contact_name_given'],
      'family' => $row['contact_name_family'],
      'org'    => $row['contact_organization'],
      'phones' => array(),
    );
  }
  $contacts[$uuid]['phones'][] = array(
    'label'  => $row['phone_label'],
    'number' => $row['phone_number'],
  );
}

foreach ($contacts as $uuid => $contact) {
  $given  = trim((string)$contact['given']);
  $family = trim((string)$contact['family']);
  $org    = trim((string)$contact['org']);

  $fn = trim($given . ' ' . $family);
  if ($fn === '') { $fn = $org; }          // organization-only contact
  if ($fn === '') { $fn = 'Unknown'; }

  echo "BEGIN:VCARD\r\n";
  echo "VERSION:4.0\r\n";
  echo "UID:an-" . vcard_escape($uuid) . "\r\n";
  echo "FN:" . vcard_escape($fn) . "\r\n";
  echo "N:" . vcard_escape($family) . ";" . vcard_escape($given) . ";;;\r\n";
  if ($org !== '') {
    echo "ORG:" . vcard_escape($org) . "\r\n";
  }

  foreach ($contact['phones'] as $phone) {
    $number = trim((string)$phone['number']);
    if ($number === '') { continue; }

    if (is_sip_endpoint($number)) {
      // A real SIP endpoint. IMPP:sip: makes it dialable as SIP and
      // presence-capable, and is unambiguous (no dial-plan normalization risk).
      // A bare extension gets this domain appended; a value that already carries
      // its own user@domain (an off-domain SIP address) is emitted as-is.
      $sip = preg_replace('/^sips?:/i', '', $number);   // drop any scheme; we re-add sip:
      if (strpos($sip, '@') === false) {
        $sip .= '@' . $domain_name;                     // bare extension on this domain
      }
      echo "IMPP:sip:" . vcard_escape($sip) . "\r\n";
    } else {
      // External / PSTN number: keep it a phone number. liblinphone resolves TEL
      // to a dialable URI at call time against the *active* account's dial plan,
      // so it is domain-agnostic and doesn't pin the contact to this domain.
      $type = vcard_param_value($phone['label']);
      if ($type !== '') {
        echo "TEL;TYPE=" . $type . ":" . vcard_escape($number) . "\r\n";
      } else {
        echo "TEL:" . vcard_escape($number) . "\r\n";
      }
    }
  }

  echo "END:VCARD\r\n";
}

// Extension heuristic: short numbers are internal extensions on this domain
// (mirrors template.php, which used strlen < 8 to decide presence subscribe).
// A value already in explicit SIP form (contains '@' or a sip: scheme) is also
// treated as a SIP endpoint.
function is_sip_endpoint($number) {
  if (strpos($number, '@') !== false || stripos($number, 'sip:') === 0) {
    return true;
  }
  return strlen($number) < 8;
}

// RFC 6350 §3.4 escaping for property VALUES: backslash, comma, semicolon, newline.
function vcard_escape($value) {
  $value = (string)$value;
  $value = str_replace('\\', '\\\\', $value);
  $value = str_replace(array(',', ';'), array('\\,', '\\;'), $value);
  $value = str_replace(array("\r\n", "\n", "\r"), '\\n', $value);
  return $value;
}

// TYPE parameter value: restrict to a safe unquoted token (drop anything that
// would need quoting/escaping in a param).
function vcard_param_value($value) {
  $value = preg_replace('/[^A-Za-z0-9 _-]+/', '', (string)$value);
  return trim($value);
}
