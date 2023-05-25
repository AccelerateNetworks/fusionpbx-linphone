<?php
if(strpos($_SERVER['HTTP_USER_AGENT'], "Linphone") !== false || strpos($_SERVER['HTTP_USER_AGENT'], "AN Mobile") !== false || isset($_GET['xml'])) {
	include "template.php";
	die();
}
?><!DOCTYPE html>
<html>
<head>
	<meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
	<style type="text/css">
		* {
			font-family: sans-serif;
		}
	</style>
</head>
<body>
	<h1>Let's get you set up</h1>
	<span class="mobile">
		<h3 class="maybe-mobile">If you're setting up a mobile device...</h3>
		<p>1. Install our app, AN Mobile, from <span id="appstore"><a href="https://play.google.com/store/apps/details?id=com.acceleratenetworks.mobile&hl=en_US&gl=US" target="_blank" rel="noopener">Google Play</a>, or the <a href="https://apps.apple.com/us/app/accelerate-networks-mobile/id1560522124" target="_blank" rel="noopener">Apple App Store</a></span></p>
		<p>2. Paste the URL to this page in the provisioning URL box. You may also scan a QR code within the app.</p>
		<?php if(isset($_GET['force_show_prov_link'])) { ?><a class="click-to-provision" href="javascript: void(0);">Click here to configure linphone with your account</a>.<?php } ?>
	</span>
	<span class="desktop">
		<h3 class="maybe-desktop">If you're setting up a desktop (or laptop!)</h3>
		<p>1. Install Linphone Desktop (you can skip this step if it's already installed):</p>
		<ul>
			<li><a href="https://linphone.org/releases/windows/latest_app_win64">Windows</a></li>
			<li><a href="https://linphone.org/releases/macosx/latest_app">Mac</a></li>
			<li><a href="https://linphone.org/releases/linux/latest_app">Linux</a></li>
		</ul>

		<p>2. <a class="click-to-provision" href="javascript: void(0);">Click here to configure linphone with your account</a>.</p>
	</span>

	<script>
		var isMobile = navigator.userAgent.match(/(iPhone|iPod|iPad|Android)/);
		var isApple = navigator.userAgent.match(/(iPhone|iPod|iPad)/);

		if (isMobile) {
			document.querySelector('.desktop').style.display = "none";
			document.querySelector('.maybe-mobile').style.display = "none";
			if (isApple) {
				document.getElementById("appstore").innerHTML = 'the <a href="https://apps.apple.com/us/app/accelerate-networks-mobile/id1560522124" target="_blank" rel="noopener">Apple App Store</a>';
				// setTimeout(function() {
				// 	window.location = "https://apps.apple.com/th/app/accelerate-networks-mobile/id1560522124";
				// }, 250);
			} else {
				document.getElementById("appstore").innerHTML = '<a href="https://play.google.com/store/apps/details?id=com.acceleratenetworks.mobile&hl=en_US&gl=US" target="_blank" rel="noopener">Google Play</a>'
				// setTimeout(function() {
				// 	window.location = "https://play.google.com/store/apps/details?id=com.acceleratenetworks.mobile&hl=en_US&gl=US";
				// }, 250);
			}
		} else {
			document.querySelector('.mobile').style.display = "none";
			document.querySelector('.maybe-desktop').style.display = "none";
		}

		// attempt provisioning regardless of OS, this will silently fail on mobile devices without the app installed
		let provisioningURL = window.location.href.replace("https", "linphone-config");
		document.querySelectorAll('.click-to-provision').forEach((e) => e.setAttribute("href", provisioningURL));
	</script>
</body>
</html>
