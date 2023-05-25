<?php
/*
	GNU Public License
	Version: GPL 3
*/
require_once "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";
require_once "resources/header.php";

if(!(permission_exists('linphone_manage_all') || permission_exists('linphone_manage_domain'))) {
    echo "permission denied";
    require_once "resources/footer.php";
    exit;
}

//show the content
echo "<div class='action_bar' id='action_bar'>\n";
echo "	<div class='heading'><b>Linphone Profiles</b></div>\n";
echo "	<div class='actions'>\n";
echo button::create(['type'=>'button','label'=>"back",'icon'=>$_SESSION['theme']['button_icon_back'],'id'=>'btn_back','style'=>'margin-right: 15px;','link'=>'index.php']);
if(permission_exists('linphone_manage_domain') || permission_exists('linphone_manage_all')) { // edit page doesn't currently support linphone_manage_self
    echo button::create(['type'=>'button','label'=>"New",'icon'=>$_SESSION['theme']['button_icon_add'],'id'=>'btn_add','name'=>'btn_add','link'=>'profile_edit.php']);
}
echo "	</div>\n";
echo "	<div style='clear: both;'></div>\n";
echo "</div>\n";
echo "<br /><br />\n";

?>
<table class="table">
<tr>
    <th>Profile</th>
    <th>Actions</th>
</tr>
<?php
$sql = "SELECT * FROM linphone_profiles WHERE domain_uuid = :domain_uuid";
$parameters['domain_uuid'] = $domain_uuid;
$database = new database;
$profiles = $database->select($sql, $parameters, 'all');
unset($parameters);
foreach($profiles as $profile) {
?>
<tr>
    <td><a href="profile_edit.php?profile_uuid=<?php echo $profile['profile_uuid']; ?>"><?php echo $profile['name']; ?></a></td>
    <td class="middle button"><?php
        if(permission_exists('linphone_manage_domain') || permission_exists('linphone_manage_all')) { // edit page doesn't currently support linphone_manage_self
            echo "<form method='post' action='profile_edit.php'>";
            echo "<input type='hidden' name='profile_uuid' value='".$profile['profile_uuid']."'>";
            echo button::create(['type'=>'button','icon'=>'pen','id'=>'btn_toggle','name'=>'btn_edit', 'link' => 'profile_edit.php?profile_uuid='.$profile['profile_uuid']]);
            echo button::create(['type'=>'submit', 'icon'=>'trash','id'=>'btn_delete','name' => "action", 'value' => "delete"]);
            echo "</form>";
        }
    ?></td>
</tr>
<?php } ?>
</table>
<?php
require('footer.php');
