<?php
session_start();
$record_file = fopen("/var/www/html/record_index.txt", "a");
$txt = "res\n";
$text2= "test";
$txtt = "res";
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
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>AlphaBit - OpenML</title>
	<link rel="stylesheet" href="../assets/css/model_style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="shortcut icon" type="image/x-icon" href="/assets/images/alphabit.ico" />
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
	<div class="background-container">
		<div class="alphabit-topleft">
			<a href="#">AlphaBit OpenML</a>
		</div>
		<div class="ai-star-logo">
			<img src="../assets/images/ai_star_alpha.png" width=50>
		</div>
		<div class="docs">Documentation</div>
		<div class="rbox">
			<div class="title">Sample Math</div>
			<div class="text-container">
				<?php
				$lang = isset($_COOKIE['site_lang']) ? $_COOKIE['site_lang'] : 'en';
				$season_cookie = isset($_COOKIE['season_choice']) ? $_COOKIE['season_choice'] : 'IntoTheDeep';
				$season_year = ($season_cookie == 'Decode') ? '2026' : '2025';
				$season_path = ($season_cookie == 'Decode') ? 'decode' : 'intothedeep';
				if (isset($_COOKIE['detection_method'])) {
					$detection_method = $_COOKIE['detection_method'];
				} else {
					$detection_method = 'machine_learning';
				}
				if ($detection_method == 'color_blob') {
					$detection_method = 'Color Blob Detection';
				}
				if ($detection_method == 'machine_learning') {
					$detection_method = 'Machine Learning';
				}
				if ($lang == 'ro'):
					?>
					<div class="stext">Va fi adăugat în curând</div>
					<div class="stext">Alătură-te serverului de Discord pentru a vedea toate funcționalitățile noi ce vor fi
						adăugate 🎉</div>
					<div class="endLine"></div>
					<div class="endD"><a href="https://discord.gg/ZB6vQ62KZT">Support -> Discord</a></div>
					<div class="end"></div>
				<?php else: ?>
					<div class="stext">To be added soon</div>
					<div class="stext">Join the discord to see all the new features that will be added 🎉</div>
					<div class="endLine"></div>
					<div class="endD"><a href="https://discord.gg/ZB6vQ62KZT">Support -> Discord</a></div>
					<div class="end"></div>
				<?php endif; ?>
			</div>
		</div>
		<div class="docs-container">
			<?php if ($lang == 'ro'): ?>
				<div class="setup">Configurare</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/overview">Prezentare Generală</a></div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/prerequisites">Initializare Device</a>
				</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/resources">Resurse</a></div>
				<div class="docsLine"></div>

				<div class="setup">Detecție Mostre 2D</div>
				<div class="sub-section">
					<p style="color:#c67171;">Ghid de initializare</p>
				</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/cameracalib">Calibrarea Camerei</a>
				</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/sample_2d_math">Testare Detecție
						Python</a></div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/sample_2d_math">Implementare Android
						Studio</a></div>

				<div class="docsLine"></div>

				<div class="setup">Detecție Mostre 3D</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/sample_2d_math">Ghid de
						initializare</a>
				</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/cameracalib">Calibrarea Camerei</a>
				</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/sample_2d_math">Testare Detecție
						Python</a></div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/sample_2d_math">Implementare Android
						Studio</a></div>

				<div class="docsLine"></div>


				<?php if ($detection_method != 'Color Blob Detection'): ?>
					<div class="setup">Antrenare ML</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/training">Set de Date Antrenament</a>
					</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/training_structure">Structura
							Antrenamentului</a></div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/label_tool">Utilitar Etichetare
							Imagini</a></div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/training_ml">Cod Python pentru
							Antrenament</a></div>

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
			<?php else: ?>
				<div class="setup">Setup</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/overview">Overview</a></div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/prerequisites">Getting Started</a>
				</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/resources">Resources</a></div>
				<div class="docsLine"></div>

				<div class="setup">2D Sample Detection</div>
				<div class="sub-section">
					<p style="color:#c67171;">Starter Guide</p>
				</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/cameracalib">Camera Calibration</a>
				</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/sample_2d_math">Python Detection
						Testing</a></div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/sample_2d_math">Android Studio
						Implementation</a></div>

				<div class="docsLine"></div>

				<div class="setup">3D Sample Detection</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/sample_2d_math">Starter Guide</a></div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/cameracalib">Camera Calibration</a>
				</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/sample_2d_math">Python Detection
						Testing</a></div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/sample_2d_math">Android Studio
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
			<?php endif; ?>
		</div>
	</div>
</body>

</html>
