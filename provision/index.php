<?php
if(strpos($_SERVER['HTTP_USER_AGENT'], "Linphone") !== false) {
	include "template.php";
	die();
}
?><!DOCTYPE html>
<html>
<body>
	<h2>Hello, this page will help you setup Accelerate Networks Mobile, so you can answer calls on the go!</h2>
	<p>Please first install AN Mobile from <span id="appstore"><a href="https://play.google.com/store/apps/details?id=com.acceleratenetworks.mobile&hl=en_US&gl=US" target="_blank" rel="noopener">Google Play</a>, or the <a href="https://apps.apple.com/us/app/accelerate-networks-mobile/id1560522124" target="_blank" rel="noopener">Apple App Store</a></span></p>
	<br><br>
	<p id="info"></p>

	<script>
		var isMobile = navigator.userAgent.match(/(iPhone|iPod|iPad|Android)/);
		var isApple = navigator.userAgent.match(/(iPhone|iPod|iPad)/);

		if (isMobile) {
			if (isApple) {
				document.getElementById("appstore").innerHTML = 'the <a href="https://apps.apple.com/us/app/accelerate-networks-mobile/id1560522124" target="_blank" rel="noopener">Apple App Store</a>';
				setTimeout(function() {
					window.location = "https://apps.apple.com/th/app/accelerate-networks-mobile/id1560522124";
				}, 250);
			} else {
				document.getElementById("appstore").innerHTML = '<a href="https://play.google.com/store/apps/details?id=com.acceleratenetworks.mobile&hl=en_US&gl=US" target="_blank" rel="noopener">Google Play</a>'
				setTimeout(function() {
					window.location = "https://play.google.com/store/apps/details?id=com.acceleratenetworks.mobile&hl=en_US&gl=US";
				}, 250);
			}
		}

		// attempt provisioning regardless of OS, this will silently fail on mobile devices without the app installed
		window.location.href = window.location.href.replace("https", "linphone-config");
	</script>
</body>
</html>
