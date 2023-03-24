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

if($_POST['extension_uuid']) { // add/update
    $sql = "update linphone_devices set extension_uuid = :extension_uuid, name = :name where domain_uuid = :domain_uuid and device_uuid = :device_uuid";
    $parameters['domain_uuid'] = $domain_uuid;
    $parameters['extension_uuid'] = $_POST['extension_uuid'];
    $parameters['name'] = $_POST['name'];

    if(strlen($_POST['device_uuid']) > 0) {
        $parameters['device_uuid'] = $_POST['device_uuid'];
    } else {
        $sql = "insert into linphone_devices(device_uuid, provisioning_secret, name, domain_uuid, extension_uuid) VALUES (:device_uuid, :provisioning_secret, :name, :domain_uuid, :extension_uuid)";
        $parameters['device_uuid'] = uuid();
        $parameters['provisioning_secret'] = generate_password(20, 3); // generate a random 20 character alphanumeric string
    }

    error_log("executing sql: ".$sql);
    error_log("sql params: ".print_r($parameters, true));
    $database = new database;
    $database->execute($sql, $parameters);
    
    header('Location: device_edit.php?device_uuid='.$parameters['device_uuid'], false, 302);
    die();
}

$sql = "select * from linphone_devices where domain_uuid = :domain_uuid and device_uuid = :device_uuid";
$parameters['domain_uuid'] = $domain_uuid;
$parameters['device_uuid'] = $_REQUEST['device_uuid'];
$database = new database;
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
echo button::create(['type'=>'submit','label'=>"save", 'icon'=>$_SESSION['theme']['button_icon_save'],'id'=>'btn_save','style'=>'margin-left: 15px;']);
if($device) {
    echo button::create(['type'=>'button','label'=>$text['button-qr_code'],'icon'=>'qrcode','collapse'=>'hide-sm-dn','onclick'=>"$('#qr_code_container').fadeIn(400);"]);
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
        <input class="formfld" type="text" name="name" value="<?php echo $device['name']; ?>" /><br />
        leave blank to autofill with device hostname
    </td>
</tr>
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
                    $name = "(".$name.")";
                }

                echo "<option value=\"".$extension['extension_uuid']."\"".$selected.">".$extension['extension'].$name."</option>\n";
             } ?>
        </select>
    </td>
</tr>


</table>
<?php
if($device) {
    $qr_mode = '4';
    $qr_size = '0.2';
    echo "<script src='".PROJECT_PATH."/resources/jquery/jquery-qrcode.min.js'></script>";
    echo "<script type='text/javascript'>";
    echo "	$(document).ready(function() {";
    echo "		$('#qr_code').qrcode({ ";
    echo "			render: 'canvas', ";
    echo "			minVersion: 6, ";
    echo "			maxVersion: 40, ";
    echo "			ecLevel: 'H', ";
    echo "			size: 650, ";
    echo "			radius: 0.2, ";
    echo "			quiet: 6, ";
    echo "			background: '#fff', ";
    echo "			mode: ".$qr_mode.", ";
    echo "			mSize: ".$qr_size.", ";
    echo "			mPosX: 0.5, ";
    echo "			mPosY: 0.5, ";
    echo "			text: \"https://".$_SESSION['domain_name']."/app/linphone/provision/?token=".$device['provisioning_secret']."\"";
    echo "			".$qr_option;
    echo "		});";
    echo "	});";
    echo "</script>";
}
require_once "resources/footer.php";
