<?php
require_once "../root.php";
require_once "resources/require.php";

// things that we might want configurable eventually
$sip_transport = "tls";
$audio_codecs_enabled = array("opus", "G722"); // Codec list will be pushed to Linphone Desktop clients
$audio_codecs_disabled = array("speex", "PCMU", "PCMA", "GSM", "G729", "BV16", "L16"); // do we actually need to list these to disable them?
$video_codecs_enabled = array("VP8", "H264"); // no disabled list in existing template
$audio_codecs_enabled_mobile = array("opus"); // Codec list will be pushed to iOS and Android clients
$audio_codecs_disabled_mobile = array("speex", "PCMU", "PCMA", "GSM", "G729", "BV16", "L16", "G722"); // do we actually need to list these to disable them?

$is_mobile = strpos($_SERVER['HTTP_USER_AGENT'], "AN Mobile") !== false || strpos($_SERVER['HTTP_USER_AGENT'], "Accelerate") !== false || strpos($_SERVER['HTTP_USER_AGENT'], "LinphoneiOS") !== false; // Detect AN Mobile or Accelerate user agents for slight config differences

$sql = "select v_extensions.*, linphone_devices.user_agent, linphone_devices.device_uuid, linphone_devices.name as device_name, linphone_devices.upload_secret from v_extensions, linphone_devices where linphone_devices.provisioning_secret = :token and v_extensions.domain_uuid = linphone_devices.domain_uuid and v_extensions.extension_uuid = linphone_devices.extension_uuid";
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

$linphone_config['misc']['uuid'] = "317971da-65c4-419f-a0ca-69fe26523e2b";
$linphone_config['misc']['transient_provisioning'] = "0";
$linphone_config['misc']['version_check_url_root'] = "https://".$domain_name."/app/linphone";
$linphone_config['misc']['config-uri'] = "https://".$domain_name."/app/linphone/provision/index.php?token=".$_GET['token'];
$linphone_config['misc']['file_transfer_server_url'] = "https://".$domain_name."/app/webtexting/upload-hook.php?token=".$extension['upload_secret'];
// Company directory: delivered as a VCard4 friend list (see provision/vcard.php),
// not inline [friend_N] sections. Inline friends were loaded twice per boot
// (config parse + provisioning re-apply, transient_provisioning=0) and doubled
// every contact (issue #39); the VCard4 sync clears + re-imports, so it can't.
$linphone_config['misc']['contacts-vcard-list'] = "https://".$domain_name."/app/linphone/provision/vcard.php?token=".$_GET['token'];


$linphone_config['sip']['verify_server_certs'] = "0";
$linphone_config['sip']['verify_server_cn'] = "0";
$linphone_config['sip']['default_proxy'] = "0";
$linphone_config['sip']['media_encryption'] = "none";
$linphone_config['sip']['lime'] = "0";
if($is_mobile) {
  $linphone_config['sip']['use_ipv6'] = "0";
} else {
  $linphone_config['sip']['use_ipv6'] = "0";
}

$linphone_config['ui']['exit_on_close'] = "0";
$linphone_config['ui']['logs_enabled'] = "1";

$linphone_config['proxy_default_values']['avfp'] = "0";
$linphone_config['proxy_default_values']['quality_reporting_collecto'] = "sip:voipmetrics@acceleratenetworks.sip.callpipe.com;transport=tls";
$linphone_config['proxy_default_values']['quality_reporting_enabled'] = "0";
$linphone_config['proxy_default_values']['quality_reporting_interval'] = "100";
$linphone_config['proxy_default_values']['cpim_in_basic_chat_rooms_enabled'] = "1";

$linphone_config['auth_info_0']['username'] = $extension['extension'];
$linphone_config['auth_info_0']['passwd'] = $extension['password'];
$linphone_config['auth_info_0']['domain'] = $domain_name;
$linphone_config['auth_info_0']['realm'] = $domain_name;
$linphone_config['auth_info_0']['algorithm'] = "MD5";

$proxy = $domain_name;
if($is_mobile) {
  $proxy = "flexisip.prod.callpipe.com";
}

$linphone_config['proxy_0']['reg_proxy'] = "<sip:".$proxy.";transport=tls>";
$linphone_config['proxy_0']['reg_route'] = "<sip:".$proxy.";transport=tls>";
$linphone_config['proxy_0']['reg_identity'] = "\"".$extension['effective_caller_id_name']."\" <sips:".$extension['extension']."@".$domain_name.">";
$linphone_config['proxy_0']['realm'] = $domain_name;
if($is_mobile) {
  $linphone_config['proxy_0']['reg_expires'] = "604800";
} else {
  $linphone_config['proxy_0']['reg_expires'] = "60";
}
$linphone_config['proxy_0']['reg_sendregister'] = "1";
$linphone_config['proxy_0']['publish'] = "1";
$linphone_config['proxy_0']['dial_escape_plus'] = "0";
$linphone_config['proxy_0']['push_notification_allowed'] = "1";
$linphone_config['proxy_0']['cpim_in_basic_chat_rooms_enabled'] = "1";

$linphone_config['nat_policy_0']['protocols'] = "stun";
$linphone_config['nat_policy_0']['stun_server'] = "stun.l.google.com:19302";

$linphone_config['nat_policy_default_values']['protocols'] = "stun,ice";
$linphone_config['nat_policy_default_values']['stun_server'] = "stun.l.google.com:19302";

if(!$is_mobile) { // Linphone Desktop gets a codec list
  $codec_num=0;
  foreach($audio_codecs_enabled as $codec) {
    $section = 'audio_codec_'.$codec_num++;
    $linphone_config[$section]['mime'] = $codec;
    $linphone_config[$section]['enabled'] = 1;
  }

  foreach($audio_codecs_disabled as $codec) {
    $section = 'audio_codec_'.$codec_num++;
    $linphone_config[$section]['mime'] = $codec;
    $linphone_config[$section]['enabled'] = 0;
  }

  $codec_num = 0;
  foreach($video_codecs_enabled as $codec) {
    $section = 'video_codec_'.$codec_num++;
    $linphone_config[$section]['mime'] = $codec;
    // $linphone_config[$section]['rate'] = "90000"; // this was set for VP8
    $linphone_config[$section]['enabled'] = 1;
  }
}

if($is_mobile) { // Linphone Desktop gets a codec list
  $codec_num=0;
  foreach($audio_codecs_enabled_mobile as $codec) {
    $section = 'audio_codec_'.$codec_num++;
    $linphone_config[$section]['mime'] = $codec;
    $linphone_config[$section]['enabled'] = 1;
  }

  foreach($audio_codecs_disabled_mobile as $codec) {
    $section = 'audio_codec_'.$codec_num++;
    $linphone_config[$section]['mime'] = $codec;
    $linphone_config[$section]['enabled'] = 0;
  }

  $codec_num = 0;
  foreach($video_codecs_enabled as $codec) {
    $section = 'video_codec_'.$codec_num++;
    $linphone_config[$section]['mime'] = $codec;
    // $linphone_config[$section]['rate'] = "90000"; // this was set for VP8
    $linphone_config[$section]['enabled'] = 1;
  }
}

$sql = "SELECT linphone_profile_settings.setting, linphone_profile_settings.value FROM linphone_profile_settings, linphone_profile_devices WHERE linphone_profile_settings.profile_uuid = linphone_profile_devices.profile AND linphone_profile_devices.device_uuid = :device_uuid";
$parameters['device_uuid'] = $extension['device_uuid'];
$settings = $database->select($sql, $parameters, 'all');
unset($parameters);
foreach($settings as $setting) {
  $key = explode(".", $setting['setting']);
  $linphone_config[$key[0]][$key[1]] = $setting['value'];
}

// special case values
if($linphone_config['proxy_0']['reg_identity_port']) {
  $linphone_config['proxy_0']['reg_identity'] = "\"".$extension['effective_caller_id_name']."\" <sips:".$extension['extension']."@".$domain_name.":".$linphone_config['proxy_0']['reg_identity_port'].">";
  unset($linphone_config['proxy_0']['reg_identity_port']);
}

if($linphone_config['proxy_0']['proxy_domain']) {
  $proxy = $linphone_config['proxy_0']['proxy_domain'];
  unset($linphone_config['proxy_0']['proxy_domain']);
  $linphone_config['proxy_0']['reg_proxy'] = "<sip:".$proxy.";transport=tls>";
  $linphone_config['proxy_0']['reg_route'] = "<sip:".$proxy.";transport=tls>";
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


foreach($linphone_config as $section=>$values) {
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
