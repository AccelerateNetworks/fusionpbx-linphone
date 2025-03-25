<?php
if(strpos($_SERVER['HTTP_USER_AGENT'], "Linphone") !== false || strpos($_SERVER['HTTP_USER_AGENT'], "AN Mobile") !== false || isset($_GET['xml'])) {
	include "template.php";
	die();
}
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
	<style type="text/css">
		.header {
			padding-bottom: 1rem;
			border-bottom: .05rem solid #e5e5e5;
		}

		/* unvisited link */
		a {
			color: #3279B2;
		}

		/* mouse over link */
		a:hover {
			color: #BB6026;
		}

		/* selected link */
		a:active {
			color: #3279B2;
		}

		/* Custom list element icon */
		.checkmark-list>ul {
			list-style: none;
			padding: 0px;
		}

		.checkmark-list>ul>li>a {
			color: white;
			text-decoration: none;
		}

		/* Custom page footer */
		.footer {
			background-color: #343a40;
		}
	</style>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
	<div class="bg-white pb-2 pt-2 shadow-sm">
		<div class="container">
			<div class="row d-flex justify-content-between text-center align-items-center no-gutters">
				<div class="col-xl-2 col-lg-2 col-md-4 col-sm-4 col-6">
					<a href="/" class="navbar-brand mx-auto font-weight-normal d-none d-sm-block">
						<img src="https://acceleratenetworks.com/images/scaled/accelerate.png" alt="Accelerate Networks"
							class="img-fluid" style="width: 10rem;" loading="lazy">
					</a>
					<a href="/" class="navbar-brand d-sm-none w-75 mx-auto d-block">
						<img src="https://acceleratenetworks.com/images/scaled/accelerate.png" alt="Accelerate Networks"
							class="img-fluid p-1 ml-3" loading="lazy">
					</a>
				</div>
				<div id="contactButton" class="col-xl-2 col-lg-2 col-md-4 col-sm-4 col-6 text-center">
					<a href="tel:+1-206-858-8757" class="btn btn-lg btn-outline-dark"><small>(206) 858-8757</small></a>
				</div>
			</div>
		</div>
	</div>
	<section class="text-light p-md-5 p-3" style="background-color: #3279B2">
		<div class="container">
			<div class="row">
				<div class="col">
					<h2>Let's get you set up!</h2>
				</div>
			</div>
			<span class="mobile" style="display: none;">
				<div class="row">
					<div class="col">
						<h3 class="maybe-mobile">If you're setting up a mobile device...</h3>
					</div>
				</div>
				<div class="row">
					<div class="col">
						<p>1. Install the Accelerate Networks app from <span id="appstore"><a
									href="https://play.google.com/store/apps/details?id=com.acceleratenetworks.mobile&amp;hl=en_US&amp;gl=US"
									target="_blank" rel="noopener">Google Play</a>, or the <a
									href="https://apps.apple.com/app/apple-store/id6736579700" target="_blank"
									rel="noopener">Apple App Store</a></span></p>
					</div>
					<div class="col">
						<p>2. Paste the URL to this page in the provisioning URL box. You may also scan a QR code below.
						</p>
					</div>
					<div class="col"></div>
				</div>
			</span>
			<span class="desktop">
				<div class="row">
					<div class="col">
						<h3 class="maybe-desktop" style="display: none;">If you're setting up a desktop (or laptop!)
						</h3>
					</div>
				</div>
				<div class="row">
					<div class="col">
						<p>First download and install Linphone Desktop:</p>
					</div>
					<div class="col">
						<a class="btn btn-lg btn-primary"
							href="https://linphone.org/releases/windows/latest_app_win64">Windows
							🪟</a>
						<a class="btn btn-lg btn-secondary" href="https://linphone.org/releases/macosx/latest_app">Mac
							🍎</a>
						<a class="btn btn-lg btn-success" href="https://linphone.org/releases/linux/latest_app">Linux
							🐧</a>
					</div>
				</div>
				<div class="row">
					<div class="col">
						<p>Finally click
							here to configure Linphone with your Accelerate Networks account:</p>
						<a class="btn btn-danger click-to-provision"
							href="linphone-config://acceleratenetworks.sip.callpipe.com/app/linphone/provision/index.php?token=V63AZJJ8A6gXQuYTUZRC">Configure
							⚡</a>
					</div>
				</div>
			</span>
		</div>
	</section>
	<div class="container p-3">
		<div class="row">
			<div class="col">
				<h2>Smartphone Setup</h2>

				<p>1. Install the Accelerate Networks app:
				<div class="appstore"><a class="btn btn-lg btn-success"
						href="https://play.google.com/store/apps/details?id=com.acceleratenetworks.mobile&amp;hl=en_US&amp;gl=US"
						target="_blank" rel="noopener">Google Play ▶️</a>
					<a class="btn btn-lg btn-secondary" href="https://apps.apple.com/app/apple-store/id6736579700"
						target="_blank" rel="noopener">Apple
						App Store 🍎</a>
				</div>
				</p>
				<p>2. Open the app, choose Fetch Remote Configuration. Tap QRCode, scan a QR code below then Fetch
					&amp; Apply.</p>
				<p>3. To verify service, call <a href="tel:4254997999">(425) 499-7999</a> inside the Accelerate
					Networks app and hear music play. You can also call
					our echo line by dialing *9196 to test your microphone.</p>
				<p>If you need troubleshooting help visit our <a href="https://acceleratenetworks.com/Phones/ANMobile"
						target="_blank">support page</a> or
					call <a href="tel:2068588757">(206) 858-8757</a>.</p>
			</div>
			<div class="col-12 col-lg-6">
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

echo "				<div id='provisoning'>\n";
echo "					<img src=\"data:image/jpeg;base64,".base64_encode($image)."\" style='margin-top: 0px; padding: 5px; background: white; max-width: 100%;'><br />\n";
echo "				</div>\n";
?>
			</div>
		</div>
	</div>

	<script>
		var isMobile = navigator.userAgent.match(/(iPhone|iPod|iPad|Android)/);
		var isApple = navigator.userAgent.match(/(iPhone|iPod|iPad)/);

		if (isMobile) {
			document.querySelector('.desktop').style.display = "none";
			document.querySelector('.maybe-mobile').style.display = "none";
			if (isApple) {
				document.getElementById("appstore").innerHTML = '<a class="btn btn-lg btn-secondary" href="https://apps.apple.com/app/apple-store/id6736579700" target="_blank" rel="noopener">Apple App Store 🍎</a>';
				// setTimeout(function() {
				// 	window.location = "https://apps.apple.com/app/apple-store/id6736579700";
				// }, 250);
			} else {
				document.getElementById("appstore").innerHTML = '<a class="btn btn-lg btn-success" href="https://play.google.com/store/apps/details?id=com.acceleratenetworks.mobile&amp;hl=en_US&amp;gl=US" target="_blank" rel="noopener">Google Play ▶️</a>'
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
	<footer class="footer pt-4 pb-4">
		<div class="container">
			<div class="row">
				<div class="col-md-4">
					<div class="row">
						<div class="col-6">
							<a class="d-block mb-3" href="/" width="100%"><img
									src="https://acceleratenetworks.com/images/scaled/accelerate.png"
									alt="Accelerate Networks" width="100%" loading="lazy"></a>
						</div>
					</div>
					<div class="row">
						<div class="col">
							<p class="text-light pb-2 pb-sm-3">Manage calls, voicemails, business texts, and faxes—all
								in one place.</p>
							<a class="btn-social bs-outline bs-facebook bs-light bs-lg me-2 mb-2"
								href="https://www.facebook.com/AccelerateNets/" title="Facebook"><i
									class="ai-facebook"></i></a>
							<a class="btn-social bs-outline bs-twitter bs-light bs-lg me-2 mb-2"
								href="https://www.linkedin.com/company/72476822/" title="LinkedIn"><i
									class="ai-linkedin"></i></a>
							<a class="btn-social bs-outline bs-instagram bs-light bs-lg me-2 mb-2"
								href="https://github.com/AccelerateNetworks" title="Github"><i
									class="ai-github"></i></a>
						</div>
					</div>
				</div>
				<div class="col-md-8">
					<div class="row">
						<div class="offset-lg-3"></div>
						<div class="col">
							<div class="text-light checkmark-list">
								<h4>Product</h4>
								<br>
								<ul>
									<li><a href="https://acceleratenetworks.com/features">Features</a></li>
									<li><a href="https://acceleratenetworks.com/Services">Services</a></li>
									<li><a href="https://acceleratenetworks.com/Hardware">Hardware</a></li>
								</ul>
							</div>
						</div>
						<div class="col">
							<div class="text-light checkmark-list">
								<h4>Support</h4>
								<br>
								<ul>
									<li><a href="/support">Support</a></li>
									<li><a href="tel:206-858-8757">(206) 858-8757</a></li>
								</ul>
							</div>
						</div>
						<div class="col">
							<div class="text-light checkmark-list">
								<h4>Company</h4>
								<br>
								<ul>
									<li><a href="https://acceleratenetworks.com/about">About</a></li>
									<li><a href="https://acceleratenetworks.com/blog">Blog</a></li>
									<li><a href="https://acceleratenetworks.com/careers">Careers</a></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<hr class="my-0 border-light">
			<div class="row align-items-center py-4">
				<div class="col-md-6">
					<p class="mb-0"><span class="text-light">© Accelerate Networks. Proudly made in Seattle.</span></p>
				</div>
				<div class="col-md-6 text-end checkmark-list">
					<ul class="list-inline mb-0">
						<li class="list-inline-item"><a class="nav-link-style nav-link-light"
								href="https://acceleratenetworks.com/privacy">Privacy</a></li>
						<li class="list-inline-item"><a class="nav-link-style nav-link-light"
								href="https://acceleratenetworks.com/support">Support</a></li>
					</ul>
				</div>
			</div>
		</div>
	</footer>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
		crossorigin="anonymous"></script>

</body>
</html>
