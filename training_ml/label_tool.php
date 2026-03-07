<?php
session_start();

$season_cookie = isset($_COOKIE['season_choice']) ? $_COOKIE['season_choice'] : 'Decode';
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
			<a href="/">AlphaBit OpenML</a>
		</div>
		<div class="before_docs"><?php echo $season_year; ?></div>
		<div class="ai-star-logo">
			<img src="/assets/images/ai_star_alpha.png" width=50>
		</div>
		<div class="docs">Documentation</div>
		<div class="rbox">
			<div class="title">Label Image Tool</div>
			<div class="text-container">
				<?php
				$lang = isset($_COOKIE['site_lang']) ? $_COOKIE['site_lang'] : 'en';
				if ($lang == 'ro'):
					?>
					<div class="stext">
						<li><b>LabelImg Tool <img src="/assets/images/labelimg.png" width=30> <a
									href="https://github.com/HumanSignal/labelImg"><u>Github Repository</u></a></b></li>
					</div>
					<div class="downloadbtn"><a href="/assets/ai/windows_v1.8.1.zip">Descarcă</a></div>
					<div class="stext"><b><u>LabelImg</u></b> este o aplicație open-source de etichetare a imaginilor,
						esențială în pregătirea seturilor de date pentru recunoașterea obiectelor, în special atunci când se
						utilizează modele de tip YOLO, cum este YOLOv8n. Mai jos găsiți o descriere detaliată a aplicației
						și modul în care se integrează în procesul de dezvoltare al unui model performant:</div>
					<div class="rtext">
						<div class="stext">
							<li><b>Interfață Intuitivă și Ușurință în Utilizare</b><br>LabelImg oferă o interfață grafică
								prietenoasă, care permite utilizatorilor să deschidă imagini, să le vizualizeze și să
								traseze cutii de delimitare (bounding boxes) în jurul obiectelor de interes. Acest proces
								manual de etichetare este simplu și rapid, facilitând lucrul chiar și pentru cei care nu
								sunt familiarizați cu tehnologiile de procesare a imaginilor.</li>
						</div>
						<div class="stext">
							<li><b>Compatibilitate cu Formatul YOLO</b></li>Aplicația salvează etichetele într-un format
							compatibil cu YOLO, ceea ce este deosebit de important atunci când se antrenează modele precum
							YOLOv8n. Fiecare cutie de delimitare este asociată cu un label corespunzător clasei obiectului
							(de exemplu, „obiect”, „persoană”, „vehicul”), permițând modelului să învețe recunoașterea
							corectă a acestora.
						</div>
						<div class="stext">
							<li><b>Flexibilitate și Precizie în Etichetare</b></li>LabelImg suportă o gamă variată de
							formate de imagine și oferă funcționalități avansate, cum ar fi redimensionarea și ajustarea
							precisă a cutiilor de delimitare. Acest lucru asigură că etichetările sunt cât mai exacte, un
							aspect critic în obținerea unor rezultate bune la antrenarea modelului YOLOv8n.
						</div>
						<div class="stext">
							<li><b>Flux de Lucru Eficient</b></li>Prin organizarea sistematică a imaginilor și etichetelor,
							LabelImg facilitează gestionarea unor seturi de date mari, reducând timpul de pregătire și
							minimizând erorile umane. Utilizatorii pot naviga ușor între imagini, pot corecta rapid
							etichetele și pot asigura consistența denumirilor, toate acestea contribuind la un proces de
							antrenare mai eficient.
						</div>
						<div class="stext">
							<li><b>Integrare Directă în Proiecte de Inteligență Artificială</b></li>În contextul utilizării
							modelului YOLOv8n, calitatea etichetărilor realizate cu LabelImg are un impact direct asupra
							performanței modelului. Datele etichetate precis ajută la crearea unui set de date robust, care
							duce la antrenarea unui model de detecție a obiectelor cu o acuratețe sporită și o eficiență
							ridicată în medii reale.
						</div>
					</div>
					<br></br>
					<div class="stext"><b>Exemple</b></div>
					<div class="stext"><img src="/assets/ai/label.png" style="border-radius: 10px; width: 52vh;"></div>
					<div class="endLine"></div>
					<div class="endD"><a href="https://discord.gg/ZB6vQ62KZT">Support -> Discord</a></div>
					<div class="end"></div>
				<?php else: ?>
					<div class="stext">
						<li><b>LabelImg Tool <img src="/assets/images/labelimg.png" width=30> <a
									href="https://github.com/HumanSignal/labelImg"><u>Github Repository</u></a></b></li>
					</div>
					<div class="downloadbtn"><a href="/assets/ai/windows_v1.8.1.zip">Download</a></div>
					<div class="stext"><b><u>LabelImg</u></b> is an open-source image labeling application, essential in
						preparing datasets for object recognition, especially when using YOLO-type models, such as YOLOv8n.
						Below you will find a detailed description of the application and how it integrates into the
						development process of a high-performance model:</div>
					<div class="rtext">
						<div class="stext">
							<li><b>Intuitive Interface and Ease of Use</b><br>LabelImg offers a user-friendly graphical
								interface that allows users to open images, view them, and draw bounding boxes around
								objects of interest. This manual labeling process is simple and fast, facilitating work even
								for those unfamiliar with image processing technologies.</li>
						</div>
						<div class="stext">
							<li><b>Compatibility with YOLO Format</b></li>The application saves labels in a format
							compatible with YOLO, which is particularly important when training models like YOLOv8n. Each
							bounding box is associated with a label corresponding to the object class (e.g., "object",
							"person", "vehicle"), allowing the model to learn their correct recognition.
						</div>
						<div class="stext">
							<li><b>Flexibility and Precision in Labeling</b></li>LabelImg supports a wide range of image
							formats and offers advanced features such as resizing and precise adjustment of bounding boxes.
							This ensures that labelings are as accurate as possible, a critical aspect in achieving good
							results when training the YOLOv8n model.
						</div>
						<div class="stext">
							<li><b>Efficient Workflow</b></li>By systematically organizing images and labels, LabelImg
							facilitates the management of large datasets, reducing preparation time and minimizing human
							errors. Users can easily navigate between images, quickly correct labels, and ensure naming
							consistency, all contributing to a more efficient training process.
						</div>
						<div class="stext">
							<li><b>Direct Integration into Artificial Intelligence Projects</b></li>In the context of using
							the YOLOv8n model, the quality of labelings made with LabelImg has a direct impact on the
							model's performance. Precisely labeled data helps create a robust dataset, leading to the
							training of an object detection model with increased accuracy and high efficiency in real-world
							environments.
						</div>
					</div>
					<br></br>
					<div class="stext"><b>Examples</b></div>
					<div class="stext"><img src="/assets/ai/label.png" style="border-radius: 10px; width: 52vh;"></div>
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
						<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/training_structure">Structura
								Antrenamentului</a></div>
						<div class="sub-section">
							<p style="color:#c67171;">Utilitar Etichetare Imagini</p>
						</div>
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
						<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/training_structure">Training
								Structure</a></div>
						<div class="sub-section">
							<p style="color:#c67171;">Label Images Tool</p>
						</div>
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

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/assets/includes/chat_widget.php'; ?>
</body>

</html>