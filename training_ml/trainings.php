<?php
session_start();
$record_file = fopen("/var/www/html/record_index.txt", "a");
$txt = "res\n";
$txtt = "res";
$user_agent = $_SERVER["HTTP_USER_AGENT"];
$ip = $_SERVER["REMOTE_ADDR"];
$date = date('m/d/Y h:i:s a', time());
$txt2 = $txtt . " " . $user_agent . " " . $ip . " " . $date . "\n";
fwrite($record_file, $txt);
fwrite($record_file, $txt2);
fclose($record_file);

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
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>AlphaBit - OpenML</title>
	<link rel="stylesheet" href="/assets/css/model_style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="shortcut icon" type="image/x-icon" href="/assets/images/alphabit.ico" />
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.6.0/styles/atom-one-dark.min.css">
	<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.6.0/highlight.min.js"></script>
	<script>
		document.addEventListener("DOMContentLoaded", () => {
			hljs.highlightAll();
		});
	</script>
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
		<div class="before_docs"><?php echo $season_year; ?></div>
		<div class="ai-star-logo">
			<img src="/assets/images/ai_star_alpha.png" width=50>
		</div>
		<div class="docs">Documentation</div>
		<div class="rbox">
			<div class="title">Training Structure</div>
			<div class="text-container">
				<?php
				$lang = isset($_COOKIE['site_lang']) ? $_COOKIE['site_lang'] : 'en';
				if ($lang == 'ro'):
					?>
					<div class="stext"><b>Structura Directorului Pentru Antrenare Machine Learning</b></div>
					<div class="stext">
						<div class="codee-window">
							<pre><code class="language-python" >OpenML/ #Folderul principal pentru proiectul OpenML
						└── datasets/ #Folderul ce contine training & validation datasets
							└── AI/
								├── train/
								│   ├── images/  #Imagini pentru antrenare ML
								│   └── labels/  #Fiecare imagine este etichetata pentru ML
								└── val/
									├── images/  #Imaginile din val sunt folosite pentru validare
									└── labels/  #Fiecare imagine din val este etichetata pentru ML</code></pre>
						</div>
					</div>
					<div class="stext"><b class="bc">Explicații detaliate:</b></div>
					<div class="rtext">
						<li><b class="bc">Folderul principal "OpenML":</b> Acesta este directorul rădăcină care conține
							toate datele și subfolderele necesare.</li></b>
					</div>
					<div class="rtext">
						<li><b class="bc">"datasets/AI":</b> În acest director se află dataset-ul folosit pentru antrenarea
							modelului AI.</li>
					</div>
					<div class="rtext">
						<li><b class="bc">Subfolderele "train" și "val":</b></li>
						<div class="rtext"><b class="bc">
								<li>train:
							</b>Conține datele pe care modelul le folosește pentru a învăța (ex. imagini și etichete).</li>
						</div>
						<div class="rtext"><b class="bc">
								<li>val:
							</b>Conține datele utilizate pentru validarea modelului, adică pentru a evalua performanța
							acestuia pe date noi, pe care nu le-a văzut în timpul antrenării.</li>
						</div>
					</div>

					<div class="rtext">
						<li><b class="bc">În fiecare folder "train" și "val":</b></li>
						<div class="rtext"><b class="bc">
								<li>images:
							</b>Aici se găsesc imaginile reale training/validare.</li>
						</div>
						<div class="rtext"><b class="bc">
								<li>labels:
							</b>Aici se află fișierele de etichete (annotation files) care descriu obiectele din imagini.
							</li>
						</div>
					</div>

					<div class="stext"><b class="bc">Etichete pentru YOLOv8n</b></div>
					<div class="rtext">Pentru a antrena un model de tip YOLOv8n, fișierele din folderul labels trebuie să
						conțină etichetele în formatul corespunzător:</div>
					<div class="rtext">
						<li><b class="bc">Formatul etichetelor (YOLO format):</b></li>
						<div class="rtext">
							<li>Fiecare linie dintr-un fișier de etichetă reprezintă un obiect detectat.</li>
						</div>
						<div class="rtext">
							<li>Formatul este:</li>
							<div class="stext">
								<div class="codee-window">
									<pre><code class="language-python" >&lt;id_clasa> &lt;x_centru> &lt;y_centru> &lt;lățime> &lt;înălțime></code></pre>
								</div>
							</div>
						</div>
						<div class="rtext">
							<li>Valorile pentru coordonate (x_centru, y_centru, lățime, înălțime) sunt normalizate
								(împărțite la dimensiunile imaginii).</li>
						</div>
						<div class="rtext">
							<li>&lt;id_clasa> este un număr întreg care reprezintă clasa obiectului.</li>
						</div>
					</div>


					<div class="endLine"></div>
					<div class="endD"><a href="https://discord.gg/ZB6vQ62KZT">Support -> Discord</a></div>
					<div class="end"></div>
				<?php else: ?>
					<div class="stext"><b>Directory Structure For Machine Learning Training</b></div>
					<div class="stext">
						<div class="codee-window">
							<pre><code class="language-python" >OpenML/ #Main folder for the OpenML project
						└── datasets/ #Folder containing training & validation datasets
							└── AI/
								├── train/
								│   ├── images/  #Images for ML training
								│   └── labels/  #Each image is labeled for ML
								└── val/
									├── images/  #Images in val are used for validation
									└── labels/  #Each image in val is labeled for ML</code></pre>
						</div>
					</div>
					<div class="stext"><b class="bc">Detailed Explanations:</b></div>
					<div class="rtext">
						<li><b class="bc">Main "OpenML" Folder:</b> This is the root directory containing all necessary data
							and subfolders.</li></b>
					</div>
					<div class="rtext">
						<li><b class="bc">"datasets/AI":</b> This directory contains the dataset used for training the AI
							model.</li>
					</div>
					<div class="rtext">
						<li><b class="bc">"train" and "val" Subfolders:</b></li>
						<div class="rtext"><b class="bc">
								<li>train:
							</b>Contains the data the model uses to learn (e.g., images and labels).</li>
						</div>
						<div class="rtext"><b class="bc">
								<li>val:
							</b>Contains data used for model validation, i.e., to evaluate its performance on new data it
							hasn't seen during training.</li>
						</div>
					</div>

					<div class="rtext">
						<li><b class="bc">In each "train" and "val" folder:</b></li>
						<div class="rtext"><b class="bc">
								<li>images:
							</b>Here are the actual training/validation images.</li>
						</div>
						<div class="rtext"><b class="bc">
								<li>labels:
							</b>Here are the label files (annotation files) describing objects in the images.
							</li>
						</div>
					</div>

					<div class="stext"><b class="bc">Labels for YOLOv8n</b></div>
					<div class="rtext">To train a YOLOv8n model, the files in the labels folder must contain labels in the
						appropriate format:</div>
					<div class="rtext">
						<li><b class="bc">Label Format (YOLO format):</b></li>
						<div class="rtext">
							<li>Each line in a label file represents a detected object.</li>
						</div>
						<div class="rtext">
							<li>The format is:</li>
							<div class="stext">
								<div class="codee-window">
									<pre><code class="language-python" >&lt;class_id> &lt;x_center> &lt;y_center> &lt;width> &lt;height></code></pre>
								</div>
							</div>
						</div>
						<div class="rtext">
							<li>Coordinate values (x_center, y_center, width, height) are normalized (divided by image
								dimensions).</li>
						</div>
						<div class="rtext">
							<li>&lt;class_id> is an integer representing the object class.</li>
						</div>
					</div>


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

				<?php if ($season_cookie != 'Decode'): ?>
					<div class="setup">Detectie Sample 2D</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/2d_start">Ghid de initializare</a>
					</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/2d_cameracalib">Calibrarea Camerei</a>
					</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/2d_python_test">Testare Detectie
							Python</a></div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/2d_android">Implementare Android
							Studio</a></div>

					<div class="docsLine"></div>

					<div class="setup">Detectie Sample 3D</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/3d_start">Ghid de initializare</a>
					</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/3d_cameracalib">Calibrarea Camerei</a>
					</div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/3d_python_test">Testare Detectie
							Python</a></div>
					<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/3d_android">Implementare Android
							Studio</a></div>

					<div class="docsLine"></div>


					<?php if ($detection_method != 'Color Blob Detection'): ?>
						<div class="setup">Antrenare ML</div>
						<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/training">Set de Date Antrenament</a>
						</div>
						<div class="sub-section">
							<p style="color:#c67171;">Structura Antrenamentului</p>
						</div>
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
				<?php endif; ?>
			<?php else: ?>
				<div class="setup">Setup</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/overview">Overview</a></div>
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
						<div class="sub-section">
							<p style="color:#c67171;">Training Structure</p>
						</div>
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
			<?php endif; ?>
		</div>
	</div>
</body>

</html>