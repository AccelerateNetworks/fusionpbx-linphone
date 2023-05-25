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

if(sizeof($_POST) > 0) {
    $profile_uuid = $_POST['profile_uuid'];
    $parameters['profile_uuid'] = $profile_uuid;
    $parameters['domain_uuid'] = $domain_uuid;

    if($_POST['action'] == "delete") {
        if(strlen($profile_uuid) > 0) {
            $sql = "DELETE FROM linphone_profile_devices WHERE profile = :profile_uuid AND domain_uuid = :domain_uuid";
            $database->execute($sql, $parameters);

            $sql = "DELETE FROM linphone_profile_settings WHERE profile_uuid = :profile_uuid AND domain_uuid = :domain_uuid";
            $database->execute($sql, $parameters);

            $sql = "DELETE FROM linphone_profiles WHERE profile_uuid = :profile_uuid AND domain_uuid = :domain_uuid";
            $database->execute($sql, $parameters);
        }
        header("Location: /app/linphone/profile_list.php");
        exit();
    }

    // Upsert the profile
    $sql = "UPDATE linphone_profiles SET name = :name WHERE profile_uuid = :profile_uuid AND domain_uuid = :domain_uuid";
    if(strlen($_POST['profile_uuid']) == 0) { // POST but profile_uuid isnt set, create new profile
        $profile_uuid = uuid();
        $parameters['profile_uuid'] = $profile_uuid;
        $sql = "INSERT INTO linphone_profiles(profile_uuid, domain_uuid, name) VALUES(:profile_uuid, :domain_uuid, :name)";
    }
    $parameters['name'] = $_POST['profile_name'];
    $database->execute($sql, $parameters);
    unset($parameters);

    foreach($_POST['setting'] as $i => $setting) {
        $parameters['profile_uuid'] = $profile_uuid;
        $parameters['domain_uuid'] = $domain_uuid;
        $parameters['setting'] = $setting;

        if($_POST['delete'][$setting]) {
            $sql = "DELETE FROM linphone_profile_settings WHERE profile_uuid = :profile_uuid AND domain_uuid = :domain_uuid AND setting = :setting";
            $database->execute($sql, $parameters);
            unset($parameters);
            continue;
        }

        if(strlen($setting) < 3) {
            continue;
        }

        $sql = "UPDATE linphone_profile_settings SET value = :value WHERE profile_uuid = :profile_uuid AND domain_uuid = :domain_uuid AND setting = :setting RETURNING 1";
        $parameters['value'] = $_POST['value'][$i];
        if(!$database->execute($sql, $parameters)) {
            $sql = "INSERT INTO linphone_profile_settings (profile_uuid, domain_uuid, setting, value) VALUES (:profile_uuid, :domain_uuid, :setting, :value)";
            $database->execute($sql, $parameters);
        }
        unset($parameters);
    }

    foreach($_POST['new_section'] as $i => $section) {
        if(strlen($section) == 0) {
            continue;
        }
        $setting = $section.".".$_POST['new_key'][$i];
        $parameters['profile_uuid'] = $profile_uuid;
        $parameters['domain_uuid'] = $domain_uuid;
        $parameters['setting'] = $setting;
        $parameters['value'] = $_POST['new_value'][$i];
        error_log("new setting: ".print_r($parameters, true)."\n");

        $sql = "UPDATE linphone_profile_settings SET value = :value WHERE profile_uuid = :profile_uuid AND domain_uuid = :domain_uuid AND setting = :setting RETURNING 1";
        if(!$database->execute($sql, $parameters)) {
            $sql = "INSERT INTO linphone_profile_settings (profile_uuid, domain_uuid, setting, value) VALUES (:profile_uuid, :domain_uuid, :setting, :value)";
            $database->execute($sql, $parameters);
        }
        unset($parameters);
    }

    if($_GET['profile_uuid'] != $profile_uuid) {
        header("Location: /app/linphone/profile_edit.php?profile_uuid=".$profile_uuid);
        exit();
    }
}


echo "<form method='POST'>";
if(strlen($_GET['profile_uuid']) > 0) {
    $sql = "SELECT * FROM linphone_profiles WHERE profile_uuid = :profile_uuid AND domain_uuid = :domain_uuid";
    $parameters['profile_uuid'] = $_GET['profile_uuid'];
    $parameters['domain_uuid'] = $domain_uuid;
    $profile = $database->select($sql, $parameters, 'row');
    unset($parameters);
    echo "<input type='hidden' name='profile_uuid' value='".$profile['profile_uuid']."' />";
}

echo "<div class='action_bar' id='action_bar'>\n";
echo "	<div class='heading'><b>Edit Linphone Device Profile</b></div>\n";
echo "	<div class='actions'>\n";
echo button::create(['type'=>'button','label'=>"back",'icon'=>$_SESSION['theme']['button_icon_back'],'id'=>'btn_back','style'=>'margin-right: 15px;','link'=>'profile_list.php']);
echo button::create(['type'=>'submit','label'=>"save", 'icon'=>$_SESSION['theme']['button_icon_save'],'id'=>'btn_save','style'=>'margin-left: 15px;']);
if(strlen($_GET['profile_uuid']) > 0) {
    echo button::create(['type'=>'submit','label'=>"delete", 'icon'=>'trash','id'=>'btn_delete','name' => "action", 'value' => "delete"]);
}
echo "  </div>";
echo "	<div style='clear: both;'></div>\n";
echo "</div>\n";
echo "<br /><br />\n";
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="30%" class="vncellreq" valign="top" align="left" nowrap="nowrap">Name</td>
        <td width="70%" class="vtable" align="left">
            <input class="formfld" class="vtable" type="text" name="profile_name" value="<?php echo $profile['name']; ?>" /><br />
        </td>
    </tr>
    <tr>
        <td width="30%" class="vncellreq" valign="top" align="left" nowrap="nowrap">Settings</td>
        <td width="70%" class="vtable" align="left">
            <table class="table">
                <tr>
                    <th class="vtable">Section</th>
                    <th class="vtable">Key</th>
                    <th class="vtable">Value</th>
                    <th class="vtable">Delete</th>
                </tr>
<?php
$sql = "SELECT setting, value FROM linphone_profile_settings WHERE profile_uuid = :profile_uuid AND domain_uuid = :domain_uuid";
$parameters['profile_uuid'] = $_REQUEST['profile_uuid'];
$parameters['domain_uuid'] = $domain_uuid;
$database = new database;
$settings = $database->select($sql, $parameters, 'all');
unset($parameters);
foreach($settings as $setting) {
    $key = explode(".", $setting['setting']);
    echo "<tr>";
    echo "<td class='vtable'>".$key[0]."</td>";
    echo "<td class='vtable'>".$key[1]."</td>";
    echo "<input type='hidden' name='setting[]' value='".$setting['setting']."' />";
    echo "<td class='vtable'><input class='formfld' type='text' name='value[]' value='".$setting['value']."' /></td>";
    echo "<td class='vtable'><input class='formfld' type='checkbox' name='delete[".$setting['setting']."]' /></td>";
    echo "</tr>";
}
?>
                <tr>
                    <td><input class="formfld" class="vtable" type="text" name="new_section[]" /></td>
                    <td><input class="formfld" class="vtable" type="text" name="new_key[]" /></td>
                    <td><input class="formfld" class="vtable" type="text" name="new_value[]" /></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</form>
<?php
require('footer.php');
