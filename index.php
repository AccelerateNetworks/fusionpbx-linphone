<?php
/*
	GNU Public License
	Version: GPL 3
*/
require_once "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";
require_once "resources/header.php";

$parameters = array();
$sql = "";
if(permission_exists('linphone_manage_all') && $_GET['show'] == "all") {
    $sql = "select linphone_devices.*, v_extensions.extension from linphone_devices, v_extensions where v_extensions.domain_uuid = linphone_devices.domain_uuid and v_extensions.extension_uuid = linphone_devices.extension_uuid ORDER BY v_extensions.extension";
} else if(permission_exists('linphone_manage_domain')) {
    $sql = "select linphone_devices.*, v_extensions.extension from linphone_devices, v_extensions where linphone_devices.domain_uuid = :domain_uuid and v_extensions.domain_uuid = linphone_devices.domain_uuid and v_extensions.extension_uuid = linphone_devices.extension_uuid ORDER BY v_extensions.extension";
    $parameters['domain_uuid'] = $domain_uuid;
} else if(permission_exists('linphone_manage_self')) {
    $sql = "select linphone_devices.*, v_extensions.extension from linphone_devices, v_extensions, v_extension_users where linphone_devices.domain_uuid = :domain_uuid and v_extension_users.user_uuid = :user_uuid and v_extension_users.extension_uuid = v_extensions.extension_uuid and v_extensions.domain_uuid = linphone_devices.domain_uuid and v_extensions.extension_uuid = linphone_devices.extension_uuid ORDER BY v_extensions.extension;";
    $parameters['domain_uuid'] = $domain_uuid;
    $parameters['user_uuid'] = $_SESSION['user_uuid'];
} else {
    echo "permission denied";
    require_once "resources/footer.php";
    exit;
}

$database = new database;
$devices = $database->select($sql, $parameters, 'all');
unset($parameters);

//show the content
echo "<div class='action_bar' id='action_bar'>\n";
echo "	<div class='heading'><b>Linphone (".count($devices).")</b></div>\n";
echo "	<div class='actions'>\n";
if(permission_exists('linphone_manage_all')) {
    if($_GET['show'] == "all") {
        echo button::create(['type'=>'button','label'=>"show domain",'icon'=>$_SESSION['theme']['button_icon_all'],'link'=>'?show=domain']);
    } else {
        echo button::create(['type'=>'button','label'=>"show all",'icon'=>$_SESSION['theme']['button_icon_all'],'link'=>'?show=all']);
    }
}
if(permission_exists('linphone_manage_domain') || permission_exists('linphone_manage_all')) { // edit page doesn't currently support linphone_manage_self
    echo button::create(['type'=>'button','label'=>"New",'icon'=>$_SESSION['theme']['button_icon_add'],'id'=>'btn_add','name'=>'btn_add','link'=>'device_edit.php']);
}
echo "	</div>\n";
echo "	<div style='clear: both;'></div>\n";
echo "</div>\n";
echo "<br /><br />\n";

?>
<table class="table">
<tr>
    <th>Extension</th>
    <th>Name</th>
    <th>User Agent</th>
    <th>Actions</th>
</tr>
<?php
foreach($devices as $device) {
?>
<tr>
    <td><a href="device_edit.php?device_uuid=<?php echo $device['device_uuid']; ?>"><?php echo $device['extension']; ?></a></td>
    <td><?php echo $device['name']; ?></td>
    <td><?php echo $device['user_agent']; ?></td>
    <td class="middle button"><?php
        $provisioning_url = "https://".$_SESSION['domain_name']."/app/linphone/provision/?token=".$device['provisioning_secret'];
        echo button::create(['type'=>'button','icon'=>'qrcode','onclick'=>"show_qr(\"".$provisioning_url."\")"]);
        echo button::create(['type'=>'button','icon'=>'clipboard', 'onclick'=>'copy("'.$provisioning_url.'")']);
        if(permission_exists('linphone_manage_domain') || permission_exists('linphone_manage_all')) { // edit page doesn't currently support linphone_manage_self
            echo button::create(['type'=>'button','icon'=>'pen','id'=>'btn_toggle','name'=>'btn_edit', 'link' => 'device_edit.php?device_uuid='.$device['device_uuid']]);
        }
    ?></td>
</tr>
<?php } ?>
</table>
<?php
require('footer.php');
