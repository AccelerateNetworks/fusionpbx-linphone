<?php
require_once "root.php";
require_once "resources/require.php";

// things that we might want configurable eventually
$sip_transport = "tls";
$audio_codecs_enabled = array("opus", "G722");
$audio_codecs_disabled = array("speex", "PCMU", "PCMA", "GSM", "G729", "BV16", "L16"); // do we actually need to list these to disable them?
$video_codecs_enabled = array("VP8", "H264"); // no disabled list in existing template

$sql = "select v_extension.* from v_extensions, linphone_devices where linphone_devices.token = :token and v_extensions.domain_uuid = linphone_devices.domain_uuid and v_extensions.extension_uuid = linphone_devices.extension_uuid";
$parameters['token'] = $_GET['token'];
$database = new database;
$extension = $database->select($sql, $parameters, 'row');
unset($parameters);

if(!$extension) {
  http_response_code(401);
  echo "unauthorized";
  die();
}

$sql = "select domain_name from v_domains where domain_uuid = :domain_uuid";
$parameters['domain_uuid'] = $extension['domain_uuid'];
$database = new database;
$domain_name = $database->select($sql, $parameters, 'column');
unset($parameters);

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
?>
<config xmlns="http://www.linphone.org/xsds/lpconfig.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.linphone.org/xsds/lpconfig.xsd lpconfig.xsd">
  <section name="misc">
    <entry name="transient_provisioning" overwrite="true">0</entry>
    <entry name="uuid" overwrite="true">317971da-65c4-419f-a0ca-69fe26523e2b</entry>
    <!--    <entry name="contacts-vcard-list" overwrite="true">downloading contact vcard list doesn't seem to work great yet</entry> -->
  </section>
  <section name="sip">
    <entry name="verify_server_certs" overwrite="true">0</entry>
    <entry name="verify_server_cn" overwrite="true">0</entry>
    <entry name="media_encryption" overwrite="true">srtp</entry>
  </section>
  <section name="ui">
    <entry name="exit_on_close" overwrite="true">1</entry>
    <entry name="logs_enabled" overwrite="true">1</entry>
  </section>
<?php
$codec_num=0;
foreach($audio_codecs_enabled as $codec) {
?>
  <section name="audio_codec_<?php echo $codec_num++; ?>">
    <entry name="mime" overwrite="true"><?php echo $codec; ?></entry>
    <entry name="enabled" overwrite="true">1</entry>
  </section>
<?php
}

foreach($audio_codecs_disabled as $codec) {
?>
  <section name="audio_codec_<?php echo $codec_num++; ?>">
    <entry name="mime" overwrite="true"><?php echo $codec; ?></entry>
    <entry name="enabled" overwrite="true">0</entry>
  </section>
  <?php
}

$codec_num = 0;
foreach($video_codecs_enabled as $codec) {
?>
  <section name="video_codec_<?php echo $codec_num++; ?>">
    <entry name="mime" overwrite="true"><?php echo $codec; ?></entry>
    <!-- <entry name="rate" overwrite="true">90000</entry> TODO: this was set for VP8 and unset for H264 -->
    <entry name="enabled" overwrite="true">1</entry>
  </section>
<?php } ?>
  <section name="proxy_default_values">
    <entry name="avpf" overwrite="true">0</entry>
  </section>
  <section name="auth_info_1">
    <entry name="username" overwrite="true"><?php echo $extension['extension']; ?></entry>
    <entry name="passwd" ><?php echo $extension['password']; ?></entry>
    <entry name="realm" overwrite="true"><?php echo $domain_name; ?></entry>
    <entry name="domain" overwrite="true"><?php echo $domain_name; ?></entry>
    <entry name="algorithm" overwrite="true">MD5</entry>
  </section>
  <section name="proxy_1">
    <entry name="reg_proxy" overwrite="true"><?php echo "&lt;sip:".$domain_name.";transport=".$sip_transport."&gt;"; ?></entry>
    <entry name="reg_identity" overwrite="true"><?php "\"".$extension['display_name']."\" &lt;sip:".$extension['extension']."@".$domain_name."&gt;"; ?></entry>
    <entry name="reg_route" overwrite="true"><?php echo "&lt;sip:".$domain_name.";transport=".$sip_transport."&gt;"; ?></entry>
    <entry name="realm" overwrite="true"><?php echo $domain_name; ?></entry>
    <entry name="reg_expires" overwrite="true">3600</entry>
    <entry name="reg_sendregister" overwrite="true">true</entry>
    <entry name="publish" overwrite="true">1</entry>
    <entry name="dial_escape_plus" overwrite="true">0</entry>
  </section>
<!--
{foreach $contacts as $contact}{foreach $contact.numbers as $number}
  <section name="friend_{$id++}">
    <entry name="url">"{$contact.contact_name_given} {$row.contact_name_family} ({$number.phone_label})" sip:{$number.phone_number}@{$a.server_address}.sip.callpipe.com</entry>
    <entry name="pol">accept</entry>
    <entry name="subscribe">{if $number.phone_number|count_characters >= 10}0{else}1{/if}</entry>
  </section>
{/foreach}{/foreach}
-->
</config>
