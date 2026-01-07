<?php
session_start();
$record_file = fopen("/var/www/html/record_index.txt", "a");
$txt = "index\n";
$txtt = "index";
$user_agent = $_SERVER["HTTP_USER_AGENT"];
$ip = $_SERVER["REMOTE_ADDR"];
$date = date('m/d/Y h:i:s a', time());
$txt2 = $txtt . " " . $user_agent . " " . $ip . " " . $date . "\n";
fwrite($record_file, $txt);
fwrite($record_file, $txt2);
fclose($record_file);
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title>AlphaBit - OpenML</title>
	<link rel="stylesheet" href="assets/css/style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="shortcut icon" type="image/x-icon" href="assets/images/alphabit.ico" />
</head>

<body>
	<div id="language-popup" class="language-popup-overlay" style="display: none;">
		<div class="language-popup-content">
			<h2>Choose Language / Alege Limba</h2>
			<div class="language-options">
				<button onclick="selectLanguage('ro')">🇷🇴 Română</button>
				<button onclick="selectLanguage('en')">🇬🇧 English</button>
			</div>
		</div>
	</div>

	<style>
		.language-popup-overlay {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: rgba(0, 0, 0, 0.9);
			z-index: 9999;
			display: flex;
			justify-content: center;
			align-items: center;
		}

		.language-popup-content {
			background-color: #1e1e1e;
			padding: 40px;
			border-radius: 15px;
			text-align: center;
			border: 1px solid #333;
			box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
		}

		.language-popup-content h2 {
			color: #fff;
			margin-bottom: 35px;
			font-family: Arial, sans-serif;
		}

		.language-options {
			display: flex;
			gap: 20px;
			justify-content: center;
		}

		.language-options button {
			padding: 15px 30px;
			font-size: 18px;
			cursor: pointer;
			background-color: #d4d4d4ff;
			color: black;
			border: none;
			border-radius: 8px;
		}

		.language-options button:hover {
			background-color: #ffffffff;
			transform: scale(1.05);
		}
	</style>

	<script>
		function setCookie(name, value, days) {
			var expires = "";
			if (days) {
				var date = new Date();
				date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
				expires = "; expires=" + date.toUTCString();
			}
			document.cookie = name + "=" + (value || "") + expires + "; path=/";
		}

		function getCookie(name) {
			var nameEQ = name + "=";
			var ca = document.cookie.split(';');
			for (var i = 0; i < ca.length; i++) {
				var c = ca[i];
				while (c.charAt(0) == ' ') c = c.substring(1, c.length);
				if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
			}
			return null;
		}

		function selectLanguage(lang) {
			setCookie('site_lang', lang, 365);
			document.getElementById('language-popup').style.display = 'none';
			location.reload();
		}

		document.addEventListener("DOMContentLoaded", function () {
			var lang = getCookie('site_lang');
			if (!lang) {
				document.getElementById('language-popup').style.display = 'flex';
			}
		});
	</script>
	<noscript>You need to enable JavaScript to run this website.</noscript>
	<?php
	$lang = isset($_COOKIE['site_lang']) ? $_COOKIE['site_lang'] : 'en';
	$season_cookie = isset($_COOKIE['season_choice']) ? $_COOKIE['season_choice'] : 'IntoTheDeep';
	$season_year = ($season_cookie == 'Decode') ? '2026' : '2025';
	$season_path = ($season_cookie == 'Decode') ? 'decode' : 'intothedeep';
	if ($lang == 'ro'):
		?>
		<div class="background-container">
			<div class="alphabit-topleft">
				<a href="#">AlphaBit OpenML</a>
			</div>
			<div class="alphabit-news">
				🎉 Descoperă cel mai nou model ML open source lansat pentru detecția obiectelor și autonomie (Into The
				Deep🌊)
			</div>
			<div class="scroll"></div>
			<?php
			session_start();
			if ($_SESSION["loggedIn"] != "userLoggedIn") {
				echo '<div class="alphabit-signup">
						<a href="register">Înregistrare</a>
				</div>
				<div class="alphabit-login">
					<a href="login">Autentificare</a>
				</div>';
			} else {
				echo '<div class="alphabit-profile-teamname"><a href="profile">Salut, ' . $_SESSION["teamname"] . '!</a></div>';
				echo '<div class="alphabit-profile-teamname-pic"><img src="assets/images/user3.png" width=45></div>';
			}
			?>
			<div class="alphabit-training">
				<a href="model/<?php echo $season_path; ?>/training">Date de Antrenament</a>
			</div>
			<div class="alphabit-ml-model">
				<a href="model/<?php echo $season_path; ?>/overview">Model ML</a>
			</div>
			<div class="alphabit-fwelcome">
				Bine ați venit la Hub-ul ML Alphabit
			</div>
			<div class="alphabit-swelcome">
				Unde Robotica Întâlnește Machine Learning-ul!
			</div>
			<div class="alphabit-welcome-text">
				Suntem o echipă de robotică FTC care împinge limitele inovației cu sistemul nostru de machine learning de
				ultimă
				generație. Tehnologia noastră detectează automat elementele de joc, le calculează pozițiile și orientările
				și
				permite robotului nostru să le colecteze autonom. Descoperă cum redefinim viitorul roboticii competitive!
			</div>
			<div class="alphabit-learn">
				<a href="model/<?php echo $season_path; ?>/overview">Obține Modelul OpenML ✨</a>
			</div>
			<div class="ai-star-logo">
				<img src="assets/images/ai_star_alpha.png" width=55>
			</div>
		</div>


		<div class="fpage">
			<div class="ftext-box"></div>
			<div class="fimage-info-box"></div>
			<div class="fpage-ftext">Machine learning nu este doar un termen la modă, ci un instrument transformator care
				oferă
				roboților capacitatea de a învăța, de a se adapta și de a excela în timp real. Prin integrarea ML în
				proiectele
				voastre FTC, puteți valorifica informații bazate pe date pentru a optimiza performanța și a inova pe teren.
				Platforma noastră deschisă de machine learning este concepută pentru a ajuta echipe ca a voastră să
				depășească
				limitele roboticii.
			</div>
			<div class="fimage-box"></div>
			<div class="simage-box"></div>
			<img class="fpage-fimage" src="assets/images/simage-ml.jpeg" width=450>
			<img class="fpage-simage" src="assets/images/fimage-ml.jpeg" width=450>
			<div class="fimage-info">Acuratețea noastră medie pentru detectarea mostrelor este de peste 90% datorită
				modelului
				nostru de detectare a obiectelor de ultimă generație și setului său de date de antrenament.</div>
			<div class="fpage-learn"><a href="model/<?php echo $season_path; ?>/overview">Obține Modelul OpenML ✨</a></div>
			<div class="fpage-news">Încearcă cel mai nou Model ML pentru detecția obiectelor și autonomie 🎉</div>
		</div>


		<div class="cpage">

			<div class="alphabit-contact-details">
				DETALII CONTACT
			</div>
			<div class="contact-box">
				<div class="alphabit-contact">
					<img src="assets/images/hmm.png">
				</div>
				<div class="contact-name-text">
					Nume
				</div>
				<div class="contact-name-email">
					Adresă E-mail
				</div>
				<div class="contact-name-teamname">
					Nume Echipă
				</div>
				<div class="contact-name-message">
					Mesaj
				</div>
			</div>
			<form id="contact-form" action="index.php" method="post">
				<input type="text" name="name" placeholder="" class="contact-name" required></input>
				<input type="email" name="name" placeholder="" class="contact-email" required></input>
				<input type="text" name="name" placeholder="" class="contact-teamname" required></input>
				<input type="text" name="name" placeholder="" class="contact-message" required></input>
				<button type="submit" name="submit" placeholder="TRIMITE" class="contact-submit">TRIMITE</button>
			</form>
			<a href="https://www.linkedin.com/in/team-alphabit-b0b0b333a/" class="fa fa-linkedin"></a>
			<a href="https://www.youtube.com/@alphabit-ro1378" class="fa fa-youtube"></a>
			<a href="https://www.facebook.com/AlphaBitPetrosani" class="fa fa-facebook"></a>
			<a href="https://www.instagram.com/alphabit137/" class="fa fa-instagram"></a>
			<div class="mapouter">
				<div class="gmap_canvas"><iframe class="gmap_iframe" frameborder="0" scrolling="no" marginheight="0"
						marginwidth="0"
						src="https://maps.google.com/maps?width=650&amp;height=450&amp;hl=en&amp;q=Strada 1 Decembrie 1918 7, Petroșani, Romania&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe><a
						href="https://sprunkin.com/">Sprunki</a></div>
				<style>
					.mapouter {
						position: absolute;
						top: 30%;
						left: 55%;
						text-align: right;
						width: 650px;
						height: 450px;
					}

					.gmap_canvas {
						overflow: hidden;
						background: none !important;
						width: 650px;
						height: 450px;
					}

					.gmap_iframe {
						width: 650px !important;
						height: 450px !important;
					}
				</style>
			</div>

			<div class="contact-details-email">
				Adresă E-mail: <a href="mailto:support@alphabit.ro">support@alphabit.ro</a>
			</div>
			<div class="contact-details-address">
				Adresă fizică: Romania, Hunedoara, Petrosani, Strada 1 Decembrie 1918 7
			</div>
			<a href="#" class="fa fa-copyright"></a>
			<div class="copyright-text">
				2025 AlphaBit. Toate drepturile rezervate.
			</div>
		</div>
	<?php else: ?>
		<div class="background-container">
			<div class="alphabit-topleft">
				<a href="#">AlphaBit OpenML</a>
			</div>
			<div class="alphabit-news">
				🎉 Check out our newest open source ML model released for object detection and autonomy (Into The Deep🌊)
			</div>
			<div class="scroll"></div>
			<?php
			session_start();
			if ($_SESSION["loggedIn"] != "userLoggedIn") {
				echo '<div class="alphabit-signup">
						<a href="register">Sign Up</a>
				</div>
				<div class="alphabit-login">
					<a href="login">Login</a>
				</div>';
			} else {
				echo '<div class="alphabit-profile-teamname"><a href="profile">Hello, ' . $_SESSION["teamname"] . '!</a></div>';
				echo '<div class="alphabit-profile-teamname-pic"><img src="assets/images/user3.png" width=45></div>';
			}
			?>
			<div class="alphabit-training">
				<a href="model/<?php echo $season_path; ?>/training">Training Data</a>
			</div>
			<div class="alphabit-ml-model">
				<a href="model/<?php echo $season_path; ?>/overview">ML Model</a>
			</div>
			<div class="alphabit-fwelcome">
				Welcome to Alphabit’s ML Hub
			</div>
			<div class="alphabit-swelcome">
				Where Robotics Meets Machine Learning!
			</div>
			<div class="alphabit-welcome-text">
				We’re an FTC robotics team pushing the boundaries of innovation with our state-of-the-art machine learning
				system. Our technology automatically detects game elements, calculates their positions and orientations, and
				empowers our robot to collect them autonomously. Dive in and explore how we’re redefining the future of
				competitive robotics!
			</div>
			<div class="alphabit-learn">
				<a href="model/<?php echo $season_path; ?>/overview">Get OpenML Model ✨</a>
			</div>
			<div class="ai-star-logo">
				<img src="assets/images/ai_star_alpha.png" width=55>
			</div>
		</div>


		<div class="fpage">
			<div class="ftext-box"></div>
			<div class="fimage-info-box"></div>
			<div class="fpage-ftext">Machine learning isn’t just a buzzword—it’s a transformative tool that gives robots the
				ability to learn, adapt, and excel in real-time. By integrating ML into your FTC projects, you can harness
				data-driven insights to optimize performance and innovate on the field. Our open machine learning platform
				is designed to empower teams like yours to push the boundaries of robotics.
			</div>
			<div class="fimage-box"></div>
			<div class="simage-box"></div>
			<img class="fpage-fimage" src="assets/images/simage-ml.jpeg" width=450>
			<img class="fpage-simage" src="assets/images/fimage-ml.jpeg" width=450>
			<div class="fimage-info">Our average accuracy for detecting samples is over 90% thanks to our state-of-the-art
				object detection model and it's training dataset.</div>
			<div class="fpage-learn"><a href="model/<?php echo $season_path; ?>/overview">Get OpenML Model ✨</a></div>
			<div class="fpage-news">Try our newest ML Model for object detection and autonomy 🎉</div>
		</div>


		<div class="cpage">

			<div class="alphabit-contact-details">
				CONTACT DETAILS
			</div>
			<div class="contact-box">
				<div class="alphabit-contact">
					<img src="assets/images/hmm.png">
				</div>
				<div class="contact-name-text">
					Name
				</div>
				<div class="contact-name-email">
					E-mail Address
				</div>
				<div class="contact-name-teamname">
					Team name
				</div>
				<div class="contact-name-message">
					Message
				</div>
			</div>
			<form id="contact-form" action="index.php" method="post">
				<input type="text" name="name" placeholder="" class="contact-name" required></input>
				<input type="email" name="name" placeholder="" class="contact-email" required></input>
				<input type="text" name="name" placeholder="" class="contact-teamname" required></input>
				<input type="text" name="name" placeholder="" class="contact-message" required></input>
				<button type="submit" name="submit" placeholder="SUBMIT" class="contact-submit">SUBMIT</button>
			</form>
			<a href="https://www.linkedin.com/in/team-alphabit-b0b0b333a/" class="fa fa-linkedin"></a>
			<a href="https://www.youtube.com/@alphabit-ro1378" class="fa fa-youtube"></a>
			<a href="https://www.facebook.com/AlphaBitPetrosani" class="fa fa-facebook"></a>
			<a href="https://www.instagram.com/alphabit137/" class="fa fa-instagram"></a>
			<div class="mapouter">
				<div class="gmap_canvas"><iframe class="gmap_iframe" frameborder="0" scrolling="no" marginheight="0"
						marginwidth="0"
						src="https://maps.google.com/maps?width=650&amp;height=450&amp;hl=en&amp;q=Strada 1 Decembrie 1918 7, Petroșani, Romania&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe><a
						href="https://sprunkin.com/">Sprunki</a></div>
				<style>
					.mapouter {
						position: absolute;
						top: 30%;
						left: 55%;
						text-align: right;
						width: 650px;
						height: 450px;
					}

					.gmap_canvas {
						overflow: hidden;
						background: none !important;
						width: 650px;
						height: 450px;
					}

					.gmap_iframe {
						width: 650px !important;
						height: 450px !important;
					}
				</style>
			</div>

			<div class="contact-details-email">
				E-mail address: <a href="mailto:support@alphabit.ro">support@alphabit.ro</a>
			</div>
			<div class="contact-details-address">
				Physical address: Romania, Hunedoara, Petrosani, Strada 1 Decembrie 1918 7
			</div>
			<a href="#" class="fa fa-copyright"></a>
			<div class="copyright-text">
				2025 AlphaBit. All rights reserved.
			</div>
		</div>
	<?php endif; ?>
</body>

</html>