<?php
session_start();
$record_file = fopen("/var/www/html/record_index.txt", "a");
$txt = "modelo\n";
$txtt = "modelo";
$user_agent = $_SERVER["HTTP_USER_AGENT"];
$ip = $_SERVER["REMOTE_ADDR"];
$date = date('m/d/Y h:i:s a', time());
$txt2 = $txtt . " " . $user_agent . " " . $ip . " " . $date . "\n";
fwrite($record_file, $txt);
fwrite($record_file, $txt2);
fclose($record_file);

$season_cookie = isset($_COOKIE['season_choice']) ? $_COOKIE['season_choice'] : 'Decode';
$season_year = ($season_cookie == 'Decode') ? '2026' : '2025';
$season_path = ($season_cookie == 'Decode') ? 'decode' : 'intothedeep';
$detection_method = isset($_COOKIE['detection_method']) ? $_COOKIE['detection_method'] : 'machine_learning';
if ($detection_method == 'color_blob') {
	$detection_method = 'Color Blob Detection';
}
if ($detection_method == 'machine_learning') {
	$detection_method = 'Machine Learning';
}
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>AlphaBit - OpenML</title>
	<link rel="stylesheet" href="/assets/css/model_style.css?v=20260304">
	<link rel="stylesheet" href="/assets/css/overview_theme.css?v=20260306j">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="shortcut icon" type="image/x-icon" href="/assets/images/alphabit.ico" />
</head>

<body>
	<div id="language-popup" class="language-popup-overlay" style="display: none;">
		<div class="language-popup-content">
			<h2>Choose Language / Alege Limba</h2>
			<div class="language-options">
				<button onclick="selectLanguage('ro')">Română</button>
				<button onclick="selectLanguage('en')">English</button>
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
			background-color: rgba(0, 0, 0, 0.8);
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
			window.location.reload();
		}

		function selectChoice(choice) {
			setCookie('detection_method', choice, 365);
			document.getElementById('choice-popup').style.display = 'none';
			location.reload();
		}

		document.addEventListener("DOMContentLoaded", function () {
			var lang = getCookie('site_lang');
			var season_choice = getCookie('season_choice');
			var detection_method = getCookie('detection_method');

			// 1. Language Popup
			if (!lang) {
				document.getElementById('language-popup').style.display = 'flex';
				return; // Stop here, wait for reload
			}

			// 2. Default season to Decode on first visit
			if (!season_choice) {
				setCookie('season_choice', 'Decode', 365);
				season_choice = 'Decode';
			}

			// 3. Detection Method Popup (Into The Deep only)
			if (season_choice !== 'Decode' && !detection_method) {
				document.getElementById('choice-popup').style.display = 'flex';
				return; // Stop here, wait for reload
			}
		});
	</script>

	<!------------------------------------------------------------------------>

	<div id="choice-popup" class="choice-popup-overlay" style="display: none;">
		<div class="choice-popup-content">
			<?php if ($lang == 'ro'): ?>
				<h2>Alege Metoda de Detectie</h2>
				<div class="choice-options">
					<div class="choice-option">
						<button onclick="selectChoice('color_blob')">Detectie Culoare (Blob)</button>
						<div class="choice-label fast"><b>Foarte Rapid (~1ms)</b></div>
						<div class="choice-label fast" style="color: #d1d1d1ff"><b>(Recomandat)</b></div>
					</div>

					<div class="choice-option">
						<button onclick="selectChoice('machine_learning')">Machine Learning (Beta)</button>
						<div class="choice-label slow"><b>Lent</b></div>
					</div>
				</div>
			<?php else: ?>
				<h2>Choose Detection Method</h2>
				<div class="choice-options">
					<div class="choice-option">
						<button onclick="selectChoice('color_blob')">Color Blob Detection</button>
						<div class="choice-label fast"><b>Very Fast (~1ms)</b></div>
						<div class="choice-label fast" style="color: #d1d1d1ff"><b>(Recommended)</b></div>
					</div>

					<div class="choice-option">
						<button onclick="selectChoice('machine_learning')">Machine Learning (Beta)</button>
						<div class="choice-label slow"><b>Slow</b></div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<style>
		.choice-option {
			display: flex;
			flex-direction: column;
			align-items: center;
		}

		.choice-label {
			margin-top: 6px;
			font-size: 14px;
			font-family: Arial, sans-serif;
		}

		.choice-label.fast {
			color: #4caf50;
			/* green */
		}

		.choice-label.slow {
			color: #ff2600ff;
			/* orange/red */
		}


		.choice-popup-overlay {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: rgba(0, 0, 0, 0.8);
			z-index: 9999;
			display: flex;
			justify-content: center;
			align-items: center;
		}

		.choice-popup-content {
			background-color: #1e1e1e;
			padding: 40px;
			border-radius: 15px;
			text-align: center;
			border: 1px solid #333;
			box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
		}

		.choice-popup-content h2 {
			color: #fff;
			margin-bottom: 35px;
			font-family: Arial, sans-serif;
		}

		.choice-options {
			display: flex;
			gap: 20px;
			justify-content: center;
		}

		.choice-options button {
			padding: 15px 30px;
			font-size: 18px;
			cursor: pointer;
			background-color: #d4d4d4ff;
			color: black;
			border: none;
			border-radius: 8px;
		}

		.choice-options button:hover {
			background-color: #ffffffff;
			transform: scale(1.05);
		}
	</style>

	<!------------------------------------------------------------------------>

	<div class="background-container">
		<div class="alphabit-topleft">
			<a href="/">AlphaBit OpenML</a>
		</div>
		<div class="before_docs"><?php echo $season_year; ?></div>
		<div class="ai-star-logo">
			<img src="/assets/images/ai_star_alpha.png" width=50>
		</div>
		<div class="docs">Documentation</div>
		<div class="rbox">
			<div class="title">Introduction</div>
			<div style="margin-left: 20vh; margin-bottom: -90px;margin-top:180px;">
				<iframe src="https://www.youtube.com/embed/WIGv4dXdv54" title="OpenML Introduction" width="720"
					height="360" frameborder="0"
					allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
					allowfullscreen>
				</iframe>
			</div>
			<div class="text-container">
				<?php
				$lang = isset($_COOKIE['site_lang']) ? $_COOKIE['site_lang'] : 'en';
				if ($lang == 'ro'):
					?>
					<div class="ftext">
						<b>Urmează pașii din -> </b><b class="bc"><u><a
									href="/model/<?php echo $season_path; ?>/prerequisites"
									style="text-decoration:none; color: #ffffffff">Getting Started</a></u></b> pentru a-ți
						pregăti dispozitivul pentru folosirea modelului de Machine Learning.
					</div>
					<div class="stext">Bine ați venit la documentația de dezvoltare a OpenML pentru robotul FTC – o
						platformă open-source inovatoare dezvoltată de echipa AlphaBit Machine Learning. Proiectul nostru
						este conceput pentru a aduce cele mai avansate tehnologii de machine learning direct în arena
						roboticii FTC, facilitând accesul gratuit la o soluție completă, pre-antrenată și pregătită
						matematic pentru orice echipă interesată de performanță și inovație.</div>

					<div class="stext">Într-o lume în care competiția este din ce în ce mai acerbă, integrarea inteligenței
						artificiale și a algoritmilor avansați în sistemele robotice devine esențială. De aceea, am
						dezvoltat OpenML pentru robotul FTC astfel încât să puteți beneficia de:</div>
					<div class="rtext">
						<li><b class="bc">Modele pre-antrenate</b>: Soluția noastră vine echipată cu modele de machine
							learning deja antrenate, optimizate pentru recunoașterea obiectelor, navigație autonomă și alte
							funcții critice în timpul competițiilor FTC.</li>
					</div>
					<div class="rtext">
						<li><b class="bc">Detectarea orientării și a unghiurilor</b>: OpenML integrează algoritmi matematici
							ce permit detectarea precisă a orientării și a unghiurilor de la camera de bord. Acest aspect
							este esențial pentru navigarea autonomă și performanța în terenul de competiție.</li>
					</div>
					<div class="rtext">
						<li><b class="bc">Arhitectură modulară</b>: Fiecare componentă a platformei poate fi personalizată
							și extinsă, permițând echipelor să adauge funcționalități specifice sau să integreze noi module
							în funcție de strategia lor de competiție.</li>
					</div>
					<div class="rtext">
						<li><b class="bc">Open-Source</b>: Toate resursele sunt disponibile gratuit pentru orice echipă
							interesată. Indiferent dacă sunteți o echipă nouă sau una consacrată, puteți beneficia de aceste
							tehnologii.</li>
					</div>
					<div class="stext">
						Acest proiect este destinat tuturor echipelor – fie că sunteți la început de drum sau aveți deja
						experiență în domeniu. Prin deschiderea resurselor noastre, dorim să stimulăm inovația, să
						îmbunătățim performanțele în competițiile FTC și să creăm o comunitate colaborativă în care fiecare
						contribuție contează.
						<br><br>
						Vă invităm să explorați în detaliu fiecare componentă a acestei platforme și să descoperiți modul în
						care OpenML poate transforma strategia și execuția echipei voastre pe terenul de competiție. Fiecare
						secțiune a documentației este gândită pentru a vă oferi suportul necesar în implementarea rapidă și
						eficientă a tehnologiilor de machine learning în robotica FTC.
						<br><br>
						Bucurați-vă de această experiență inovatoare și nu ezitați să contribuiți la dezvoltarea continuă a
						proiectului!
					</div>
				<?php else: ?>
					<div class="ftext">
						<b>Follow the steps in -> </b><b class="bc"><u><a
									href="/model/<?php echo $season_path; ?>/prerequisites"
									style="text-decoration:none; color: #ffffffff">Getting Started</a></u></b> to prepare
						your
						device for using the <u>Machine Learning</u> model or the <u>Color Blob Detection</u> model.
					</div>
					<div class="stext">Welcome to the OpenML development documentation for the FTC robot – an innovative
						open-source platform developed by the AlphaBit Machine Learning team. Our project is designed to
						bring
						the most advanced machine learning technologies directly into the FTC robotics arena, facilitating
						free
						access to a complete, pre-trained, and mathematically prepared solution for any team interested in
						performance and innovation.</div>

					<div class="stext">In a world where competition is increasingly fierce, integrating artificial
						intelligence
						and advanced algorithms into robotic systems becomes essential. That's why we developed OpenML for
						the
						FTC robot so you can benefit from:</div>
					<div class="rtext">
						<li><b class="bc">Pre-trained Models</b>: Our solution comes equipped with already trained machine
							learning models, optimized for object recognition, autonomous navigation, and other critical
							functions during FTC competitions.</li>
					</div>
					<div class="rtext">
						<li><b class="bc">Orientation and Angle Detection</b>: OpenML integrates mathematical algorithms
							that
							allow precise detection of orientation and angles from the onboard camera. This aspect is
							essential
							for autonomous navigation and performance on the competition field.</li>
					</div>
					<div class="rtext">
						<li><b class="bc">Modular Architecture</b>: Each component of the platform can be customized and
							extended, allowing teams to add specific functionalities or integrate new modules according to
							their
							competition strategy.</li>
					</div>
					<div class="rtext">
						<li><b class="bc">Open-Source</b>: All resources are available for free to any interested team.
							Whether
							you are a new team or an established one, you can benefit from these technologies.</li>
					</div>
					<div class="stext">
						This project is intended for all teams – whether you are just starting out or already have
						experience in
						the field. By opening up our resources, we aim to stimulate innovation, improve performance in FTC
						competitions, and create a collaborative community where every contribution counts.
						<br><br>
						We invite you to explore each component of this platform in detail and discover how OpenML can
						transform
						your team's strategy and execution on the competition field. Each section of the documentation is
						designed to provide you with the necessary support for the rapid and efficient implementation of
						machine
						learning technologies in FTC robotics.
						<br><br>
						Enjoy this innovative experience and do not hesitate to contribute to the continuous development of
						the
						project!
					</div>
				<?php endif; ?>
				<div class="endLine"></div>
				<div class="endD"><a href="https://discord.gg/ZB6vQ62KZT">Support -> Discord</a></div>
				<div class="end"></div>
			</div>
		</div>

		<div class="docs-container">
			<?php if ($lang == 'ro'): ?>
				<div class="setup">Configurare</div>
				<div class="sub-section">
					<p style="color:#c67171;">Prezentare Generala</p>
				</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/prerequisites">Initializare Device</a>
				</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/resources">Resurse</a></div>
				<div class="docsLine"></div>

				<?php if ($season_cookie != 'Decode'): ?>
					<div class="setup">Detectie Sample 2D</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/2d_start">Ghid de initializare</a>
					</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/2d_cameracalib">Calibrarea Camerei</a>
					</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/2d_python_test">Testare Detecție
							Python</a></div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/2d_android">Implementare Android
							Studio</a></div>

					<div class="docsLine"></div>

					<div class="setup">Detectie Sample 3D</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/3d_start">Ghid de initializare</a>
					</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/3d_cameracalib">Calibrarea Camerei</a>
					</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/3d_python_test">Testare Detecție
							Python</a></div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/3d_android">Implementare Android
							Studio</a></div>

					<div class="docsLine"></div>


					<?php if ($detection_method != 'Color Blob Detection'): ?>
						<div class="setup">Antrenare ML</div>
						<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/training">Set de Date Antrenament</a>
						</div>
						<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/training_structure">Structura
								Antrenare ML</a></div>
						<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/label_tool">LabelImg Etichetare
								Imagini</a></div>
						<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/training_ml">Cod Python pentru
								Antrenare ML</a></div>

						<div class="docsLine"></div>
					<?php endif; ?>

					<div class="setup">Exemple</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/pythonml">Cod Python pentru
							Detecție</a></div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/android_studio">Implementare Android
							Studio</a></div>
					<?php if ($detection_method != 'Color Blob Detection'): ?>
						<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/robot_control">Control Colectare cu
								OpenML</a></div>
						<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/robot_control">Implementare ML
								Autonom</a></div>
						<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/robot_control">Implementare ML
								TeleOp</a></div>
					<?php endif; ?>
				<?php endif; ?>
			<?php else: ?>
				<div class="setup">Setup</div>
				<div class="sub-section">
					<p style="color:#c67171;">Overview</p>
				</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/prerequisites">Getting Started</a>
				</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/resources">Resources</a></div>
				<div class="docsLine"></div>

				<?php if ($season_cookie != 'Decode'): ?>
					<div class="setup">2D Sample Detection</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/2d_start">Starter Guide</a></div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/2d_cameracalib">Camera Calibration</a>
					</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/2d_python_test">Python Detection
							Testing</a></div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/2d_android">Android Studio
							Implementation</a></div>

					<div class="docsLine"></div>

					<div class="setup">3D Sample Detection</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/3d_start">Starter Guide</a></div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/3d_cameracalib">Camera Calibration</a>
					</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/3d_python_test">Python Detection
							Testing</a></div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/3d_android">Android Studio
							Implementation</a></div>

					<div class="docsLine"></div>


					<?php if ($detection_method != 'Color Blob Detection'): ?>
						<div class="setup">Training ML</div>
						<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/training">Training Dataset</a></div>
						<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/training_structure">Training
								Structure</a></div>
						<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/label_tool">Label Images Tool</a></div>
						<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/training_ml">Python Code For
								Training</a></div>

						<div class="docsLine"></div>
					<?php endif; ?>

					<div class="setup">Examples</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/pythonml">Python Code For Detection</a>
					</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/android_studio">Android Studio
							Implementation</a></div>
					<?php if ($detection_method != 'Color Blob Detection'): ?>
						<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/robot_control">Control Intake Using The
								OpenML</a></div>
						<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/robot_control">Autonomous ML
								Implementation</a></div>
						<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/robot_control">TeleOp ML
								Implementation</a></div>
					<?php endif; ?>
				<?php else: ?>
					<div class="setup">AprilTag Detection</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/apriltag">Getting
							Started</a></div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/apriltag">AprilTag
							Implementation</a></div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/apriltag">AprilTag
							Code Sample</a></div>

					<div class="docsLine"></div>

					<div class="setup">Autonomous Control</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/apriltag">Getting
							Started</a></div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/apriltag">Odometry
							Pods</a></div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/apriltag">Road Runner 0.5.6
							Implementation</a></div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/apriltag">Road Runner 1.0
							Implementation</a></div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/apriltag">Pedro Pathing
							Implementation</a></div>

					<div class="docsLine"></div>

					<div class="setup">Auto Artifact Pick-up (beta)</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/apriltag">Getting
							Started</a></div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/apriltag">Detection Method
							Implementation</a></div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/apriltag">Sample Code For Pick-up</a>
					</div>

				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/assets/includes/chat_widget.php'; ?>
</body>

</html>
