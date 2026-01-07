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
			<div class="title">Training Dataset</div>
			<div class="text-container">
				<?php
				$lang = isset($_COOKIE['site_lang']) ? $_COOKIE['site_lang'] : 'en';
				if ($lang == 'ro'):
					?>
					<div class="ftext">
						<li><b>Set de Date Antrenament [3 Mostre] <u>[Set mare]</u> (3671 Imagini Etichetate) (343
								Fundaluri)
						</li></b>
					</div>
					<div class="downloadbtn"><a href="/assets/ai/large_dataset.rar">Descarcă</a></div>
					<div class="stext">
						<li><b>Set de Date Antrenament [3 Mostre] <u>[Set mediu]</u> (2653 Imagini Etichetate) (317
								Fundaluri)
								[Recomandat pentru prima antrenare]</li></b>
					</div>

					<div class="downloadbtn"><a href="/assets/ai/medium_dataset.rar">Descarcă</a></div>

					<div class="stext">Acest set de date a fost creat special pentru antrenarea modelelor de învățare
						automată și este disponibil în mod open-source. Imaginile din set sunt organizate în categorii
						(clase) bine definite, pentru a asigura o structură clară și ușor de utilizat în procesul de
						antrenare.></div>
					<div class="stext"><u><b>Puncte cheie:</b></u></li>
					</div>
					<div class="rtext">
						<li><b>Echilibru între clase</b>: Pentru a preveni instabilitatea în timpul antrenării modelului,
							este esențial ca diferența în numărul de imagini dintre clase să nu depășească 5%. Un
							dezechilibru mai mare poate duce la o performanță suboptimală și la probleme de generalizare.
						</li>
					</div>
					<div class="rtext">
						<li><b>Verificare și validare</b>: La finalul documentației, este inclus un script Python care
							parcurge toate imaginile din set și afișează numărul de imagini pentru fiecare clasă. Acest
							instrument de verificare ajută la menținerea integrității setului de date și asigură respectarea
							cerinței de echilibru.</li>
					</div>
					<div class="stext"><b>Repartiția datelor și importanța validării în antrenarea unui AI</b></li>
					</div>
					<div class="stext"><u>La antrenarea unui AI este esențială împărțirea setului de date în două părți
							principale:</u></div>
					<div class="rtext">
						<li><b>Date de antrenare (training data)</b>: Aceasta reprezintă 80% sau 90% din totalul datelor.
							Modelul "învață" din aceste date, adică identifică tipare și relații relevante pentru sarcina
							dată.</li>
					</div>
					<div class="rtext">
						<li><b>Date de validare (validation data)</b>: Acestea reprezintă restul de 20% sau 10% din date.
							Ele sunt folosite pentru a evalua performanța modelului pe date pe care acesta nu le-a văzut în
							timpul antrenării.</li>
					</div>

					<div class="stext"><b><u>De ce este importantă validarea?</u></b></li>
					</div>
					<div class="rtext">
						<li><b>Evitarea overfitting-ului</b>: Validarea permite detectarea situațiilor în care modelul se
							potrivește prea bine datelor de antrenare, dar nu reușește să generalizeze pe date noi.</li>
					</div>
					<div class="rtext">
						<li><b>Alegerea parametrilor optimi</b>: Evaluând performanța pe setul de validare, poți ajusta
							hiperparametrii modelului pentru a îmbunătăți acuratețea și robustețea.</li>
					</div>
					<div class="rtext">
						<li><b>Evaluare obiectivă</b>: Setul de validare oferă o estimare realistă a performanței modelului
							în situații reale, pe date necunoscute.</li>
					</div>

					<div class="stext"><b><u>Alegerea între 80/20 și 90/10:</u></b></li>
					</div>
					<div class="rtext">
						<li><b>80% antrenare / 20% validare</b>: Se recomandă atunci când dispui de un set de date suficient
							de mare. Un set de validare mai mare te ajută să evaluezi mai precis performanța modelului.</li>
					</div>
					<div class="rtext">
						<li><b>90% antrenare / 10% validare</b>: Este preferabil când setul de date este mai restrâns.
							Astfel, modelul beneficiază de mai multe exemple pentru învățare, însă evaluarea se face pe un
							set de validare mai mic, ceea ce poate oferi o imagine puțin mai puțin robustă a performanței.
						</li>
					</div>

					<div class="stext"><b>Python Script Pentru Verificarea Echilibrului Intre Clase</b></div>
					<div class="stext">
						<div class="codee-window">
							<pre><code class="language-python" >import os
									import xml.etree.ElementTree as ET
									from termcolor import colored

									voc_labels_dir = "datasets/AI/train/images"  # Path to your VOC XML label files
									yolo_labels_dir = "datasets/AI/train/labels"  # Path to save YOLO format label files
									image_dir = "datasets/AI/train/images"  # Path to your images

									os.makedirs(yolo_labels_dir, exist_ok=True)

									def convert_bbox(size, box):
										dw = 1.0 / size[0]
										dh = 1.0 / size[1]
										x = (box[0] + box[1]) / 2.0
										y = (box[2] + box[3]) / 2.0
										w = box[1] - box[0]
										h = box[3] - box[2]
										return (x * dw, y * dh, w * dw, h * dh)

									class_mapping = {
										"YellowSample": 0,
										"BlueSample": 1,
										"RedSample": 2
									}

									yellow_count = 0
									blue_count = 0
									red_count = 0
									total_labeled_images = 0
									duplicates = 0
									lastFile = ""
									hy = 0
									hb = 0
									hr = 0
									maxl = 0
									b_differece = 0
									r_differece = 0
									y_differece = 0

									for file in os.listdir(voc_labels_dir):
										if file.endswith(".xml"):
											xml_path = os.path.join(voc_labels_dir, file)
											tree = ET.parse(xml_path)
											root = tree.getroot()

											yolo_path = os.path.join(yolo_labels_dir, file.replace(".xml", ".txt"))
											with open(yolo_path, "w") as f:
												for obj in root.findall("object"):
													class_name = obj.find("name").text

													if class_name in class_mapping:
														class_id = class_mapping[class_name]
														if class_id == 0:
															yellow_count += 1
														elif class_id == 1:
															blue_count += 1
														elif class_id == 2:
															red_count += 1
					
														total_labeled_images += 1
														if lastFile == file:
															duplicates += 1
													else:
														print(f"Warning: Unknown class '{class_name}' in {file}")
				
													lastFile = file

									if yellow_count > blue_count and yellow_count > red_count:
										maxl = yellow_count
									elif blue_count > yellow_count and blue_count > red_count:
										maxl = blue_count
									elif red_count > yellow_count and red_count > blue_count:
										maxl = red_count

									if maxl == yellow_count:
										b_differece = maxl - blue_count
										r_differece = maxl - red_count
									elif maxl == blue_count:
										y_differece = maxl - yellow_count
										r_differece = maxl - red_count
									elif maxl == red_count:
										y_differece = maxl - yellow_count
										b_differece = maxl - blue_count

									if maxl * 0.05 &lt; b_differece or maxl * 0.05 &lt; r_differece or maxl * 0.05 &lt; y_differece:
										print(colored("Warning: There is a difference of more than 5% between the classes.", "red"))
										print(colored("Please check the labeled images and make sure that the classes are balanced.", "red"))
									else:
										print(colored("Classes are balanced.", "green"))

									print("")
									print("Total labeled images: " + colored(str(total_labeled_images-duplicates), "green"))
									print(colored("Yellow samples: ", "yellow") + colored(str(yellow_count), "green") + " | [Difference]: " + colored(str(y_differece), "red"))
									print(colored("Blue samples: ", "blue") + colored(str(blue_count), "green") + " | [Difference]: " + colored(str(b_differece), "red"))
									print(colored("Red samples: ", "red") + colored(str(red_count), "green") + " | [Difference]: " + colored(str(r_differece), "red"))
													</pre></code>
						</div>
					</div>
					<br></br>

					<div class="endLine"></div>
					<div class="endD"><a href="https://discord.gg/jYR3fyRRjd">Support -> Discord</a></div>
					<div class="end"></div>
				<?php else: ?>
					<div class="ftext">
						<li><b>Training Dataset [3 Samples] <u>[Larger dataset]</u> (3671 Labeled Images) (343 Backgrounds)
						</li></b>
					</div>
					<div class="downloadbtn"><a href="/assets/ai/large_dataset.rar">Download</a></div>
					<div class="stext">
						<li><b>Training Dataset [3 Samples] <u>[Medium dataset]</u> (2653 Labeled Images) (317 Backgrounds)
								[Recommended for your first time training]</li></b>
					</div>

					<div class="downloadbtn"><a href="/assets/ai/medium_dataset.rar">Download</a></div>

					<div class="stext">This dataset was created specifically for training machine learning models and is
						available open-source. The images in the set are organized into well-defined categories (classes) to
						ensure a clear structure that is easy to use in the training process.</div>
					<div class="stext"><u><b>Key Points:</b></u></li>
					</div>
					<div class="rtext">
						<li><b>Class Balance</b>: To prevent instability during model training, it is essential that the
							difference in the number of images between classes does not exceed 5%. A larger imbalance can
							lead to suboptimal performance and generalization issues.
						</li>
					</div>
					<div class="rtext">
						<li><b>Verification and Validation</b>: At the end of the documentation, a Python script is included
							that iterates through all images in the set and displays the number of images for each class.
							This verification tool helps maintain dataset integrity and ensures compliance with the balance
							requirement.</li>
					</div>
					<div class="stext"><b>Data Distribution and the Importance of Validation in AI Training</b></li>
					</div>
					<div class="stext"><u>When training an AI, it is essential to split the dataset into two main parts:</u>
					</div>
					<div class="rtext">
						<li><b>Training Data</b>: This represents 80% or 90% of the total data. The model "learns" from this
							data, identifying patterns and relationships relevant to the given task.</li>
					</div>
					<div class="rtext">
						<li><b>Validation Data</b>: This represents the remaining 20% or 10% of the data. It is used to
							evaluate the model's performance on data it has not seen during training.</li>
					</div>

					<div class="stext"><b><u>Why is Validation Important?</u></b></li>
					</div>
					<div class="rtext">
						<li><b>Avoiding Overfitting: Validation allows detection of situations where the model fits the
								training data too well but fails to generalize to new data.</li>
					</div>
					<div class="rtext">
						<li><b>Choosing Optimal Parameters</b>: By evaluating performance on the validation set, you can
							adjust the model's hyperparameters to improve accuracy and robustness.</li>
					</div>
					<div class="rtext">
						<li><b>Objective Evaluation</b>: The validation set offers a realistic estimate of the model's
							performance in real-world situations, on unknown data.</li>
					</div>

					<div class="stext"><b><u>Choosing Between 80/20 and 90/10:</u></b></li>
					</div>
					<div class="rtext">
						<li><b>80% Training / 20% Validation</b>: Recommended when you have a sufficiently large dataset. A
							larger validation set helps you evaluate model performance more precisely.</li>
					</div>
					<div class="rtext">
						<li><b>90% Training / 10% Validation</b>: Preferable when the dataset is smaller. Thus, the model
							benefits from more examples for learning, but evaluation is done on a smaller validation set,
							which may offer a slightly less robust picture of performance.
						</li>
					</div>

					<div class="stext"><b>Python Script For Checking Class Balance</b></div>
					<div class="stext">
						<div class="codee-window">
							<pre><code class="language-python" >import os
									import xml.etree.ElementTree as ET
									from termcolor import colored

									voc_labels_dir = "datasets/AI/train/images"  # Path to your VOC XML label files
									yolo_labels_dir = "datasets/AI/train/labels"  # Path to save YOLO format label files
									image_dir = "datasets/AI/train/images"  # Path to your images

									os.makedirs(yolo_labels_dir, exist_ok=True)

									def convert_bbox(size, box):
										dw = 1.0 / size[0]
										dh = 1.0 / size[1]
										x = (box[0] + box[1]) / 2.0
										y = (box[2] + box[3]) / 2.0
										w = box[1] - box[0]
										h = box[3] - box[2]
										return (x * dw, y * dh, w * dw, h * dh)

									class_mapping = {
										"YellowSample": 0,
										"BlueSample": 1,
										"RedSample": 2
									}

									yellow_count = 0
									blue_count = 0
									red_count = 0
									total_labeled_images = 0
									duplicates = 0
									lastFile = ""
									hy = 0
									hb = 0
									hr = 0
									maxl = 0
									b_differece = 0
									r_differece = 0
									y_differece = 0

									for file in os.listdir(voc_labels_dir):
										if file.endswith(".xml"):
											xml_path = os.path.join(voc_labels_dir, file)
											tree = ET.parse(xml_path)
											root = tree.getroot()

											yolo_path = os.path.join(yolo_labels_dir, file.replace(".xml", ".txt"))
											with open(yolo_path, "w") as f:
												for obj in root.findall("object"):
													class_name = obj.find("name").text

													if class_name in class_mapping:
														class_id = class_mapping[class_name]
														if class_id == 0:
															yellow_count += 1
														elif class_id == 1:
															blue_count += 1
														elif class_id == 2:
															red_count += 1
					
														total_labeled_images += 1
														if lastFile == file:
															duplicates += 1
													else:
														print(f"Warning: Unknown class '{class_name}' in {file}")
				
													lastFile = file

									if yellow_count > blue_count and yellow_count > red_count:
										maxl = yellow_count
									elif blue_count > yellow_count and blue_count > red_count:
										maxl = blue_count
									elif red_count > yellow_count and red_count > blue_count:
										maxl = red_count

									if maxl == yellow_count:
										b_differece = maxl - blue_count
										r_differece = maxl - red_count
									elif maxl == blue_count:
										y_differece = maxl - yellow_count
										r_differece = maxl - red_count
									elif maxl == red_count:
										y_differece = maxl - yellow_count
										b_differece = maxl - blue_count

									if maxl * 0.05 &lt; b_differece or maxl * 0.05 &lt; r_differece or maxl * 0.05 &lt; y_differece:
										print(colored("Warning: There is a difference of more than 5% between the classes.", "red"))
										print(colored("Please check the labeled images and make sure that the classes are balanced.", "red"))
									else:
										print(colored("Classes are balanced.", "green"))

									print("")
									print("Total labeled images: " + colored(str(total_labeled_images-duplicates), "green"))
									print(colored("Yellow samples: ", "yellow") + colored(str(yellow_count), "green") + " | [Difference]: " + colored(str(y_differece), "red"))
									print(colored("Blue samples: ", "blue") + colored(str(blue_count), "green") + " | [Difference]: " + colored(str(b_differece), "red"))
									print(colored("Red samples: ", "red") + colored(str(red_count), "green") + " | [Difference]: " + colored(str(r_differece), "red"))
													</pre></code>
						</div>
					</div>
					<br></br>

					<div class="endLine"></div>
					<div class="endD"><a href="https://discord.gg/jYR3fyRRjd">Support -> Discord</a></div>
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
						<div class="sub-section">
							<p style="color:#c67171;">Set de Date Antrenament</p>
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
						<div class="sub-section">
							<p style="color:#c67171;">Training Dataset</p>
						</div>
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
			<?php endif; ?>
		</div>
	</div>
</body>

</html>