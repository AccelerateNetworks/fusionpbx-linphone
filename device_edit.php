<?php
/*
	GNU Public License
	Version: GPL 3
*/
require_once "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";
require_once "resources/header.php";

if(!(permission_exists('linphone_manage_domain') || permission_exists('linphone_manage_all'))) {
    echo "permission denied";
    require_once "resources/footer.php";
    exit;
}

$database = new database;
if($_POST['extension_uuid']) { // add/update
    $sql = "update linphone_devices set extension_uuid = :extension_uuid, name = :name where domain_uuid = :domain_uuid and device_uuid = :device_uuid";
    $parameters['domain_uuid'] = $domain_uuid;
    $parameters['extension_uuid'] = $_POST['extension_uuid'];
    $parameters['name'] = $_POST['name'];

    $device_uuid = $_POST['device_uuid'];
    if(strlen($_POST['device_uuid']) > 0) {
        $parameters['device_uuid'] = $device_uuid;
    } else {
        $device_uuid = uuid();
        $sql = "insert into linphone_devices(device_uuid, provisioning_secret, name, domain_uuid, extension_uuid) VALUES (:device_uuid, :provisioning_secret, :name, :domain_uuid, :extension_uuid)";
        $parameters['device_uuid'] = $device_uuid;
        $parameters['provisioning_secret'] = generate_password(20, 3); // generate a random 20 character alphanumeric string
    }

    $database->execute($sql, $parameters);
    unset($parameters);
    
    $sql = "SELECT profile FROM linphone_profile_devices WHERE domain_uuid = :domain_uuid AND device_uuid = :device_uuid";
    $parameters['domain_uuid'] = $domain_uuid;
    $parameters['device_uuid'] = $device_uuid;
    $current_profile_list = $database->select($sql, $parameters, 'all');
    unset($parameters);

    foreach($current_profile_list as $profile) {
        $current_profiles[$profile['profile']] = true;
    }

    foreach($_POST['profiles'] as $profile) {
        if(isset($current_profiles[$profile])) {
            unset($current_profiles[$profile]);
            continue;
        }

        $sql = "INSERT INTO linphone_profile_devices (device_uuid, domain_uuid, profile) VALUES (:device_uuid, :domain_uuid, :profile)";
        $parameters['device_uuid'] = $device_uuid;
        $parameters['domain_uuid'] = $domain_uuid;
        $parameters['profile'] = $profile;
        $database->execute($sql, $parameters);
        unset($parameters);
    }

    foreach(array_keys($current_profiles) as $profile) {
        $sql = "DELETE FROM linphone_profile_devices WHERE device_uuid = :device_uuid AND domain_uuid = :domain_uuid AND profile = :profile";
        $parameters['device_uuid'] = $device_uuid;
        $parameters['domain_uuid'] = $domain_uuid;
        $parameters['profile'] = $profile;
        $database->execute($sql, $parameters);
        unset($parameters);
    }

    header('Location: device_edit.php?device_uuid='.$device_uuid, false, 302);
    die();
}

$sql = "select * from linphone_devices where domain_uuid = :domain_uuid and device_uuid = :device_uuid";
$parameters['domain_uuid'] = $domain_uuid;
$parameters['device_uuid'] = $_REQUEST['device_uuid'];
$device = $database->select($sql, $parameters, 'row');
unset($parameters);

$sql = "select extension, extension_uuid, effective_caller_id_name from v_extensions where domain_uuid = :domain_uuid";
$parameters['domain_uuid'] = $domain_uuid;
$database = new database;
$extensions = $database->select($sql, $parameters, 'all');
unset($parameters);

echo "<form method='post' name='frm' id='frm'>\n";

if($device) {
    echo "<input type='hidden' name='device_uuid' value='".$device['device_uuid']."' />";
}
echo "<div class='action_bar' id='action_bar'>\n";
echo "	<div class='heading'><b>Edit Linphone Device</b></div>\n";
echo "	<div class='actions'>\n";
echo button::create(['type'=>'button','label'=>"back",'icon'=>$_SESSION['theme']['button_icon_back'],'id'=>'btn_back','style'=>'margin-right: 15px;','link'=>'index.php']);
echo button::create(['type'=>'submit','label'=>"save", 'icon'=>$_SESSION['theme']['button_icon_save'],'id'=>'btn_save','style'=>'margin-left: 15px;']);
if($device) {
    $provisioning_url = "https://".$_SESSION['domain_name']."/app/linphone/provision/?token=".$device['provisioning_secret'];
    echo button::create(['type'=>'button','label'=>"Show Provisioning QR",'icon'=>'qrcode','onclick'=>"show_qr(\"".$provisioning_url."\")"]);
    echo button::create(['type'=>'button','label'=>"Copy Provisioning URL",'icon'=>'clipboard', 'onclick'=>'copy("'.$provisioning_url.'")']);
}
echo "  </div>";
echo "	<div style='clear: both;'></div>\n";
echo "</div>\n";
echo "<br /><br />\n";

?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="30%" class="vncellreq" valign="top" align="left" nowrap="nowrap">Extension</td>
        <td width="70%" class="vtable" align="left">
            <select class="formfld" name="extension_uuid">
                <?php foreach($extensions as $extension) {
                    $selected = "";
                    if($extension['extension_uuid'] == $device['extension_uuid']) {
                        $selected=" selected";
                    }

                    $name = $extension['effective_caller_id_name'];
                    if($name != "") {
                        $name = " (".$name.")";
                    }

                    echo "<option value=\"".$extension['extension_uuid']."\"".$selected.">".$extension['extension'].$name."</option>\n";
                } ?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="30%" class="vncellreq" valign="top" align="left" nowrap="nowrap">Name<br /><small>optional</small></td>
        <td width="70%" class="vtable" align="left">
            <input class="formfld" type="text" name="name" value="<?php echo $device['name']; ?>" /><br />
        </td>
    </tr>
    <tr>
        <td width="30%" class="vncellreq" valign="top" align="left" nowrap="nowrap">Profiles</td>
        <td width="70%" class="vtable" align="left">
            <table class="vtable">
            <?php
            $sql = "SELECT * FROM linphone_profiles WHERE domain_uuid = :domain_uuid";
            $parameters['domain_uuid'] = $domain_uuid;
            $profiles = $database->select($sql, $parameters, 'all');

            $sql = "SELECT profile FROM linphone_profile_devices WHERE domain_uuid = :domain_uuid AND device_uuid = :device_uuid";
            $parameters['device_uuid'] = $device['device_uuid'];
            $device_profiles_list = $database->select($sql, $parameters, 'all');
            unset($parameters);

            foreach($device_profiles_list as $profile) {
                $device_profiles[$profile['profile']] = true;
            }

            foreach($profiles as $profile) {
                $profile_uuid = $profile['profile_uuid'];

                $checked = "";
                if(isset($device_profiles[$profile_uuid])) {
                    $checked = "checked ";
                }

                echo "<tr><td>";
                echo "<label for='profile-".$profile_uuid."'>";
                echo "<input type='checkbox' name='profiles[]' value='".$profile_uuid."' id='profile-".$profile_uuid."' ".$checked."/> ";
                echo $profile['name'];
                echo "</label></td></tr>";
            }
            ?>
            </table>
        </td>
    </tr>
</table>
<?php
require('footer.php');
