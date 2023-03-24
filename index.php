<?php
/*
	GNU Public License
	Version: GPL 3
*/
require_once "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";
require_once "resources/header.php";

// TODO: permission check

$sql = "select linphone_devices.*, v_extensions.extension from linphone_devices, v_extensions where linphone_devices.domain_uuid = :domain_uuid and v_extensions.domain_uuid = linphone_devices.domain_uuid and v_extensions.extension_uuid = linphone_devices.extension_uuid";
$parameters['domain_uuid'] = $domain_uuid;
$database = new database;
$devices = $database->select($sql, $parameters, 'all');
unset($parameters);

//show the content
echo "<div class='action_bar' id='action_bar'>\n";
echo "	<div class='heading'><b>Linphone (".count($devices).")</b></div>\n";
echo "	<div class='actions'>\n";
echo button::create(['type'=>'button','label'=>"New",'icon'=>$_SESSION['theme']['button_icon_add'],'id'=>'btn_add','name'=>'btn_add','link'=>'device_edit.php']);
echo "	</div>\n";
echo "	<div style='clear: both;'></div>\n";
echo "</div>\n";
echo "<br /><br />\n";

?>
<table class="table">
<tr>
    <th>Extension</th>
    <th>Name</th>
    <th>Actions</th>
</tr>
<?php
foreach($devices as $device) {
?>
<tr>
    <td><a href="device_edit.php?device_uuid=<?php echo $device['device_uuid']; ?>"><?php echo $device['extension']; ?></a></td>
    <td><?php echo $device['name']; ?></td>
    <td><?php
        echo button::create(['type'=>'button','label'=>$text['button-qr_code'],'icon'=>'qrcode','collapse'=>'hide-sm-dn','onclick'=>"$('#qr_code_container').fadeIn(400);"]);
    ?></td>
</tr>
<?php } ?>
</table>
<?php

require_once "resources/footer.php";
