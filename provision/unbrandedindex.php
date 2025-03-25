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
		<p>1. Install the Accelerate Networks app from <span id="appstore"><a href="https://play.google.com/store/apps/details?id=com.acceleratenetworks.mobile&hl=en_US&gl=US" target="_blank" rel="noopener">Google Play</a>, or the <a href="https://apps.apple.com/app/apple-store/id6736579700" target="_blank" rel="noopener">Apple App Store</a></span></p>
		<p>2. Paste the URL to this page in the provisioning URL box. You may also scan a QR code below.</p>
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
		<h2>Smartphone Setup</h2>
                <p>1. Install the Accelerate Networks app from <span id="appstore"><a href="https://play.google.com/store/apps/details?id=com.acceleratenetworks.mobile&hl=en_US&gl=US" target="_blank" rel="noopener">Google Play</a>, or the <a href="https://apps.apple.com/app/apple-store/id6736579700" target="_blank" rel="noopener">Apple App Store</a></span></p>
                <p>2. Open the app, choose Fetch Remote Configuration. Tap QRCode, scan a QR code below then Fetch & Apply.</p>
                <p>3. Call 4254997999 inside the Accelerate Networks app and music should play. You can also call our echo test line *9196 to test your microphone.</p>

	</span>

	<script>
		var isMobile = navigator.userAgent.match(/(iPhone|iPod|iPad|Android)/);
		var isApple = navigator.userAgent.match(/(iPhone|iPod|iPad)/);

		if (isMobile) {
			document.querySelector('.desktop').style.display = "none";
			document.querySelector('.maybe-mobile').style.display = "none";
			if (isApple) {
				document.getElementById("appstore").innerHTML = 'the <a href="https://apps.apple.com/app/apple-store/id6736579700" target="_blank" rel="noopener">Apple App Store</a>';
				// setTimeout(function() {
				// 	window.location = "https://apps.apple.com/app/apple-store/id6736579700";
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
<?php
			$otpauth = "otpauth";

			require_once dirname(__DIR__, 3) . '/resources/qr_code/QRErrorCorrectLevel.php';
			require_once dirname(__DIR__, 3) . '/resources/qr_code/QRCode.php';
			require_once dirname(__DIR__, 3) . '/resources/qr_code/QRCodeImage.php';

			try {
				$code = new QRCode (- 1, QRErrorCorrectLevel::H);
				$code->addData("https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
				$code->make();
				$img = new QRCodeImage ($code, $width=500, $height=500, $quality=50);
				$img->draw();
				$image = $img->getImage();
				$img->finish();
			}
			catch (Exception $error) {
				echo $error;
			}

echo "	<div id='provisoning'>\n";
echo "		<img src=\"data:image/jpeg;base64,".base64_encode($image)."\" style='margin-top: 0px; padding: 5px; background: white; max-width: 100%;'><br />\n";
echo "	</div>\n";
?>
</body>
</html>
