<?php
if(strpos($_SERVER['HTTP_USER_AGENT'], "Linphone") !== false) {
	include "template.php";
	die();
}

?>
<!DOCTYPE html>
<html>
<body>
	<h2>Hello, this page will help you setup Accelerate Networks Mobile, so you can answer calls on the go!</h2>
	<p>If you don&#39;t have the app already, you can download it from...</p>
	<p id="appstore">Detecting OS...<p>
	<p><br><br></p>
	<p id="info"></p>

	<script>
		var isMobile = navigator.userAgent.match(/(iPhone|iPod|iPad|Android)/);
		var isApple = navigator.userAgent.match(/(iPhone|iPod|iPad)/);

		if (isMobile) {
			if (isApple) {
			<!-- Apple devices -->
				document.getElementById("appstore").innerHTML = 'the <a href="https://apps.apple.com/us/app/accelerate-networks-mobile/id1560522124" target="_blank" rel="noopener">Apple App Store</a>';
				setTimeout(function() {
					document.getElementById('info').innerHTML = "launching app store";
					window.location = "https://apps.apple.com/th/app/accelerate-networks-mobile/id1560522124";
				}, 250);
			} else {
			<!-- Android devises -->
				document.getElementById("appstore").innerHTML = 'the <a href="https://play.google.com/store/apps/details?id=com.acceleratenetworks.mobile&hl=en_US&gl=US" target="_blank" rel="noopener">Google Play Store</a>'
			}
		} else {
			<!-- Anything not Android or iOS -->
			document.getElementById("appstore").innerHTML = 'We have an <a href="https://play.google.com/store/apps/details?id=com.acceleratenetworks.mobile&hl=en_US&gl=US" target="_blank" rel="noopener">Android app</a>, and an <a href="https://apps.apple.com/us/app/accelerate-networks-mobile/id1560522124" target="_blank" rel="noopener">iOS app</a>'
		}


		window.location.href = window.location.href.replace("https", "linphone-config");
/*
		var providedInfo = window.location.href.split("#");

		if (providedInfo.length == 1) {
			<!-- Blank info -->

		} else {
			var version = providedInfo[0];
			if (version == 'v0') {
			<!-- Version 0  beta -->
				link = providedInfo[1].split("/")[1];
				config = "acceleratenetworks-mobile-config://" + window.location.href.split("#")[1];

				document.getElementById("demo").innerHTML = 
				`Once you have our app installed, paste this click <a href="` + config + `" target="_blank" rel="noopener">here</a>!<br>
				<br>
				<br>
				If this doesn&#39;t work and you need to paste a URL, you can copy the next line <br>
				<br>
				<br>
				https://acceleratenetworks.sip.callpipe.com/` + link
			} else if (version == 'v1') {
			<!-- Version 1  Prototype -->
			} else {
			<!-- Unknown/catchall -->
			}
		}
*/
	</script>
</body>
</html>
