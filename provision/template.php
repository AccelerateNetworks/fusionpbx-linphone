<?php
require_once "../root.php";
require_once "resources/require.php";

// things that we might want configurable eventually
$sip_transport = "tls";
$audio_codecs_enabled = array("opus", "G722"); // Codec list will be pushed to Linphone Desktop clients
$audio_codecs_disabled = array("speex", "PCMU", "PCMA", "GSM", "G729", "BV16", "L16"); // do we actually need to list these to disable them?
$video_codecs_enabled = array("VP8", "H264"); // no disabled list in existing template

$is_mobile = strpos($_SERVER['HTTP_USER_AGENT'], "AN Mobile") !== false; // Detect AN Mobile user agent for slight config differences

$sql = "select v_extensions.*, linphone_devices.user_agent, linphone_devices.device_uuid, linphone_devices.name as device_name from v_extensions, linphone_devices where linphone_devices.provisioning_secret = :token and v_extensions.domain_uuid = linphone_devices.domain_uuid and v_extensions.extension_uuid = linphone_devices.extension_uuid";
$parameters['token'] = $_GET['token'];
$database = new database;
$extension = $database->select($sql, $parameters, 'row');
unset($parameters);

if(!$extension) {
  http_response_code(401);
  echo "unauthorized";
  die();
}

if($extension['user_agent'] != $_SERVER['HTTP_USER_AGENT']) {
  $sql = "update linphone_devices set user_agent = :user_agent where domain_uuid = :domain_uuid and device_uuid = :device_uuid";
  $parameters['domain_uuid'] = $extension['domain_uuid'];
  $parameters['device_uuid'] = $extension['device_uuid'];
  $parameters['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
  $domain_name = $database->execute($sql, $parameters);
  unset($parameters);
}

// if the device name is blank, set it to the hostname from the user agent
$useragent = str_split($_SERVER['HTTP_USER_AGENT']);
if($extension['device_name'] == "" && count($useragent) > 1) {
  if(preg_match('/(?<product>[\w\- ]+)\/(?<version>[\w\.\+]+) \((?<hostname>[\w\\\'\+\.\-_ ]+)\) (?<platform>.*)/', $_SERVER['HTTP_USER_AGENT'], $matches)) {
    $sql = "update linphone_devices set name = :hostname where domain_uuid = :domain_uuid and device_uuid = :device_uuid";
    $parameters['domain_uuid'] = $extension['domain_uuid'];
    $parameters['device_uuid'] = $extension['device_uuid'];
    $parameters['hostname'] = $matches['hostname'];
    $domain_name = $database->execute($sql, $parameters);
    unset($parameters);
  }
}

$sql = "select domain_name from v_domains where domain_uuid = :domain_uuid";
$parameters['domain_uuid'] = $extension['domain_uuid'];
$domain_name = $database->select($sql, $parameters, 'column');
unset($parameters);

$config['misc']['uuid'] = "317971da-65c4-419f-a0ca-69fe26523e2b";
$config['misc']['transient_provisioning'] = "0";
$config['misc']['version_check_url_root'] = "https://".$domain_name."/app/linphone";
$config['misc']['config-uri'] = "https://".$domain_name."/app/linphone/provision/index.php?token=".$_GET['token'];


$config['sip']['verify_server_certs'] = "0";
$config['sip']['verify_server_cn'] = "0";
$config['sip']['default_proxy'] = "0";
$config['sip']['media_encryption'] = "none";
$config['sip']['lime'] = "0";
if($is_mobile) {
  $config['sip']['use_ipv6'] = "1";
} else {
  $config['sip']['use_ipv6'] = "0";
}

$config['ui']['exit_on_close'] = "0";
$config['ui']['logs_enabled'] = "1";

$config['proxy_default_values']['avfp'] = "0";
$config['proxy_default_values']['quality_reporting_collecto'] = "sip:voipmetrics@acceleratenetworks.sip.callpipe.com;transport=tls";
$config['proxy_default_values']['quality_reporting_enabled'] = "0";
$config['proxy_default_values']['quality_reporting_interval'] = "100";

$config['auth_info_0']['username'] = $extension['extension'];
$config['auth_info_0']['passwd'] = $extension['password'];
$config['auth_info_0']['domain'] = $domain_name;
$config['auth_info_0']['realm'] = $domain_name;
$config['auth_info_0']['algorithm'] = "MD5";

$proxy = $domain_name;
if($is_mobile) {
  $proxy = "flexisip.callpipe.com";
}

$config['proxy_0']['reg_proxy'] = "<sip:".$proxy.";transport=tls>";
$config['proxy_0']['reg_route'] = "<sip:".$proxy.";transport=tls>";
$config['proxy_0']['reg_identity'] = "\"".$extension['effective_caller_id_name']."\" <sips:".$extension['extension']."@".$domain_name.":5065>";
$config['proxy_0']['realm'] = $domain_name;
if($is_mobile) {
  $config['proxy_0']['reg_expires'] = "604800";
} else {
  $config['proxy_0']['reg_expires'] = "60";
}
$config['proxy_0']['reg_sendregister'] = "1";
$config['proxy_0']['publish'] = "1";
$config['proxy_0']['dial_escape_plus'] = "0";
$config['proxy_0']['push_notification_allowed'] = "1";

$config['nat_policy_0']['protocols'] = "stun,ice";
$config['nat_policy_0']['stun_server'] = "stun.l.google.com:19302";

$config['nat_policy_default_values']['protocols'] = "stun,ice";
$config['nat_policy_default_values']['stun_server'] = "stun.l.google.com:19302";

if(!$is_mobile) { // Linphone Desktop gets a codec list
  $codec_num=0;
  foreach($audio_codecs_enabled as $codec) {
    $section = 'audio_codec_'.$codec_num++;
    $config[$section]['mime'] = $codec;
    $config[$section]['enabled'] = 1;
  }

  foreach($audio_codecs_disabled as $codec) {
    $section = 'audio_codec_'.$codec_num++;
    $config[$section]['mime'] = $codec;
    $config[$section]['enabled'] = 0;
  }

  $codec_num = 0;
  foreach($video_codecs_enabled as $codec) {
    $section = 'video_codec_'.$codec_num++;
    $config[$section]['mime'] = $codec;
    // $config[$section]['rate'] = "90000"; // this was set for VP8
    $config[$section]['enabled'] = 1;
  }
}

$sql = "SELECT linphone_profile_settings.setting, linphone_profile_settings.value FROM linphone_profile_settings, linphone_profile_devices WHERE linphone_profile_settings.profile_uuid = linphone_profile_devices.profile AND linphone_profile_devices.device_uuid = :device_uuid";
$parameters['device_uuid'] = $extension['device_uuid'];
$settings = $database->select($sql, $parameters, 'all');
unset($parameters);
foreach($settings as $setting) {
  $key = explode(".", $setting['setting']);
  $config[$key[0]][$key[1]] = $setting['value'];
}

// special case values
if($config['proxy_0']['reg_identity_port']) {
  $config['proxy_0']['reg_identity'] = "\"".$extension['effective_caller_id_name']."\" <sips:".$extension['extension']."@".$domain_name.":".$config['proxy_0']['reg_identity_port'].">";
  unset($config['proxy_0']['reg_identity_port']);
}

if($config['proxy_0']['proxy_domain']) {
  $proxy = $config['proxy_0']['proxy_domain'];
  unset($config['proxy_0']['proxy_domain']);
  $config['proxy_0']['reg_proxy'] = "<sip:".$proxy.";transport=tls>";
  $config['proxy_0']['reg_route'] = "<sip:".$proxy.";transport=tls>";
}

$sql = "select c.contact_uuid, c.contact_organization, c.contact_name_given, c.contact_name_family, ";
$sql .= "c.contact_type, c.contact_category, p.phone_label,";
$sql .= "p.phone_number, p.phone_extension, p.phone_primary ";
$sql .= "from v_contacts as c, v_contact_phones as p ";
$sql .= "where c.contact_uuid = p.contact_uuid ";
$sql .= "and p.phone_type_voice = '1' ";
$sql .= "and c.domain_uuid = :domain_uuid ";
$parameters['domain_uuid'] = $domain_uuid;
$database_contacts = $database->select($sql, $parameters, 'all');
$i = 0;
foreach ($database_contacts as $contact) {
  $section = "friend_".$i++;
  $config[$section]['url'] = '"'.$contact['contact_name_given']." ".$contact['contact_name_family']." - ".$contact['contact_organization']." (".$contact['phone_label'].')" <sip:'.$contact['phone_number']."@".$domain_name.">";
  $config[$section]['pol'] = "accept";
  if(strlen($contact['phone_number']) < 8) {
    $config[$section]['subscribe'] = "1";
  } else {
    $config[$section]['subscribe'] = "0";
  }
}

$xw = xmlwriter_open_memory();
xmlwriter_set_indent($xw, 1);
$res = xmlwriter_set_indent_string($xw, ' ');

xmlwriter_start_document($xw, '1.0', 'UTF-8');

xmlwriter_start_element($xw, 'config');

xmlwriter_start_attribute($xw, 'xmlns');
xmlwriter_text($xw, 'http://www.linphone.org/xsds/lpconfig.xsd');
xmlwriter_end_attribute($xw);

xmlwriter_start_attribute($xw, 'xmlns:xsi');
xmlwriter_text($xw, 'http://www.w3.org/2001/XMLSchema-instance');
xmlwriter_end_attribute($xw);

xmlwriter_start_attribute($xw, 'xsi:schemaLocation');
xmlwriter_text($xw, 'http://www.linphone.org/xsds/lpconfig.xsd lpconfig.xsd');
xmlwriter_end_attribute($xw);


foreach($config as $section=>$values) {
  xmlwriter_start_element($xw, 'section');

  xmlwriter_start_attribute($xw, 'name');
  xmlwriter_text($xw, $section);
  xmlwriter_end_attribute($xw);

  foreach($values as $key=>$value) {
    xmlwriter_start_element($xw, 'entry');

    xmlwriter_start_attribute($xw, 'name');
    xmlwriter_text($xw, $key);
    xmlwriter_end_attribute($xw);

    xmlwriter_start_attribute($xw, 'overwrite');
    xmlwriter_text($xw, "true");
    xmlwriter_end_attribute($xw);

    xmlwriter_text($xw, $value);

    xmlwriter_end_element($xw);
  }
  xmlwriter_end_element($xw);
}

xmlwriter_end_element($xw);

echo xmlwriter_output_memory($xw);
