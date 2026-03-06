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
			<?php
			$lang = isset($_COOKIE['site_lang']) ? $_COOKIE['site_lang'] : 'en';
			if ($lang == 'ro'):
				?>
				<div class="title">Cod Python pentru Antrenament</div>
				<div class="text-container">
					<div class="stext"><b>După ce ați parcurs pașii anteriori, puteți antrena modelul ML.</b></div>
					<div class="stext">Fișierul <u><b>data.yaml</u></b> reprezintă un element esențial în procesul de
						configurare a mediului de antrenare pentru modelul de ML. Acesta centralizează informațiile legate
						de structura setului de date și parametrii necesari pentru ca scriptul de training să ruleze corect.
						În esență, fișierul definește căile către imaginile de antrenare și validare, numărul de clase și
						denumirile acestora, facilitând astfel accesul și organizarea datelor într-o manieră standardizată.
					</div>
					<div class="stext"><u>data.yaml</u> <b>(Simplu) [Recomandat pentru Început]</b>
						<div class="codee-window">
							<pre><code class="language-python">
						path: AI
						train: train/images
						val: val/images
						nc: 3

						names: ['YellowSample', 'BlueSample', 'RedSample']
											</pre></code>
						</div>
					</div>

					<div class="rtext">
						<li><b>train și val: </b>Aceste intrări indică calea către folderele care conțin imaginile pentru
							antrenare și validare. Astfel, scriptul știe exact de unde să preia datele. </li>
					</div>
					<div class="rtext">
						<li><b>nc: </b>Specifică numărul de clase din setul de date. Această informație este vitală pentru
							configurarea corectă a ultimului strat al rețelei neuronale. </li>
					</div>
					<div class="rtext">
						<li><b>names: </b>O listă de etichete (nume de clase) care corespund identificatorilor numerici
							utilizați în fișierele de etichete generate, de exemplu, cu aplicația labelImg. </li>
					</div>
					<br>
					<div class="stext">Sau versiunea [BETA] \/.</div></br>

					<div class="stext"><u>data.yaml</u> <b>(Avansat) [BETA] [Nu foarte stabil] [!Folosiți doar pentru seturi
							mari de date!]</b>
						<div class="codee-window">
							<pre><code class="language-python">
						path: AI
						train: train/images
						val: val/images
						nc: 3

						lr0: 0.001
						lrf: 0.1
						warmup_epochs: 5

						degrees: 2.5
						perspective: 0.0
						scale: 0.01
						fliplr: 0.05
						copy_paste: 0.0

						box: 0.07
						cls: 0.4
						dfl: 1.5

						names: ['YellowSample', 'BlueSample', 'RedSample']
											</pre></code>
						</div>
					</div>
					<div class="stext">Fișierul <u><b>ml_training.py</b></u> este componenta centrală care orchestrează
						întregul proces de antrenare și validare a modelului YOLOv8n, folosind biblioteca Ultralytics și
						infrastructura PyTorch. Iată o descriere tehnică a fiecărei secțiuni și a parametrilor utilizați:
					</div>
					<div class="stext"><b>1. Importul modulelor și setările de bază:</b></div>
					<div class="rtext">La început se importă clasa YOLO din biblioteca Ultralytics, esențială pentru
						manipularea și antrenarea rețelelor YOLO, precum și PyTorch, care gestionează operațiunile
						tensoriale și execuția pe GPU. Variabila ce indică calea către fișierul de configurare (data.yaml)
						conține toate detaliile despre dataset (calea către imaginile de antrenare și validare, numărul de
						clase și denumirile acestora). În plus, setarea dispozitivului la "cuda" asigură faptul că
						antrenarea se va efectua pe GPU, accelerând semnificativ calculele.</div>

					<div class="stext"><b>2. Inițializarea modelului:</b></div>
					<div class="rtext">Modelul este instanțiat folosind un fișier de greutăți preantrenate (yolov8n.pt).
						Această abordare oferă un punct de plecare robust, deoarece rețeaua beneficiază de cunoștințe
						pre-extrase, accelerând procesul de convergență și îmbunătățind performanța inițială.</div>

					<div class="stext"><b>3. Configurarea antrenării:</b></div>
					<div class="stext">Procesul de training este declanșat cu o serie de hiperparametri critici, fiecare
						jucând un rol esențial în optimizarea performanței modelului:</div>
					<div class="rtext">
						<div class="stext">
							<li><b>Dataset-ul: </b>Fișierul data.yaml este folosit pentru a localiza imaginile și etichetele
								aferente, asigurând coerența între datele de antrenare și validare. </li>
						</div>
						<div class="stext">
							<li><b>Epocile (epochs): </b>Setate la 150, acestea reprezintă numărul total de cicluri complete
								prin întregul set de date de antrenare. Fiecare epocă oferă modelului oportunitatea de a
								ajusta greutățile pe baza tuturor datelor disponibile, contribuind la stabilirea
								convergenței. </li>
						</div>
						<div class="stext">
							<li><b>Dimensiunea imaginilor (imgsz): </b>O valoare fixă de 640 indică faptul că toate
								imaginile vor fi redimensionate la 640x640 pixeli, asigurând uniformitate în procesul de
								antrenare și facilitând gestionarea inputului rețelei. </li>
						</div>
						<div class="stext">
							<li><b>Automatic Mixed Precision (amp): </b>Activarea acestei funcționalități permite utilizarea
								combinată a preciziei de 16 și 32 de biți, reducând consumul de memorie și accelerând
								antrenarea, fără a compromite semnificativ acuratețea modelului. </li>
						</div>
						<div class="stext">
							<li><b>Batch size: </b>Cu o valoare de 12, acest parametru definește numărul de imagini
								procesate simultan înainte de actualizarea parametrilor modelului. Un batch size optim ajută
								la stabilizarea gradientelor, oferind un echilibru între performanța de calcul și
								stabilitatea optimizării. </li>
						</div>
						<div class="stext">
							<li><b>Single Class vs. Multi-Class: </b>Parametrul single_cls este setat la fals, indicând
								faptul că modelul este pregătit să distingă între mai multe clase de obiecte, ceea ce
								implică o complexitate mai mare în învățare. </li>
						</div>
						<div class="stext">
							<li><b>Patience: </b>Valoarea de 100 epoci specifică o strategie de early stopping, întrerupând
								antrenarea dacă nu se înregistrează îmbunătățiri pe setul de validare pe o perioadă extinsă,
								contribuind astfel la evitarea overfitting-ului. </li>
						</div>
						<div class="stext">
							<li><b>Optimizator și hiperparametri aferenți: </b>Alegerea optimizatorului Adam, împreună cu
								setările pentru momentum (0.9) și weight decay (0.0005), controlează modul în care se
								ajustează greutățile modelului. Aceste setări sunt esențiale pentru a asigura o convergență
								eficientă și pentru a preveni acumularea unor valori de greutate excesiv de mari. </li>
						</div>
						<div class="stext">
							<li><b>Parametrul specific augmentării (close_mosaic): </b>Această setare ajustează modul de
								aplicare a augmentării de tip mosaic, o tehnică ce combină mai multe imagini pentru a crește
								diversitatea datelor. Închiderea acestei tehnici după un anumit număr de epoci permite
								modelului să se concentreze pe învățarea detaliilor fine odată ce a beneficat de un set
								diversificat de date inițial. </li>
						</div>
					</div>
					<div class="stext"><b><u>ml_training.py</u></b>
						<div class="codee-window">
							<pre><code class="language-python">from ultralytics import YOLO
						import torch

						data_yaml = 'data.yaml' 

						device = 'cuda'

						def main():
							model = YOLO("yolov8n.pt")
							model.train(data='data.yaml', epochs=150, imgsz=640,amp=True, device=device, batch=12, single_cls=False,patience=100, optimizer='Adam', momentum=0.9, weight_decay=0.0005, close_mosaic=25)
							model.val(data=data_yaml)
	
						if __name__ == '__main__': 
							main()			</pre></code>
						</div>
					</div>
					<br></br>
					<div class="stext"><b>Exemple</b></div>
					<div class="stext"><img src="/assets/ai/visual.png" width=750 style="border-radius: 10px;"></div>
					<div class="endLine"></div>
					<div class="endD"><a href="https://discord.gg/ZB6vQ62KZT">Support -> Discord</a></div>
					<div class="end"></div>
				</div>
			<?php else: ?>
				<div class="title">Python Code For Training</div>
				<div class="text-container">
					<div class="stext"><b>After you completed the steps before, you can now train the ML model.</b></div>
					<div class="stext">The <u><b>data.yaml</u></b> file represents an essential element in the process of
						configuring the training environment for the ML model. It centralizes information related to
						the dataset structure and the parameters necessary for the training script to run correctly.
						Essentially, the file defines the paths to the training and validation images, the number of
						classes, and
						their names, thus facilitating data access and organization in a standardized manner.
					</div>
					<div class="stext"><u>data.yaml</u> <b>(Simple) [Recommended For Starting]</b>
						<div class="codee-window">
							<pre><code class="language-python">
						path: AI
						train: train/images
						val: val/images
						nc: 3

						names: ['YellowSample', 'BlueSample', 'RedSample']
											</pre></code>
						</div>
					</div>

					<div class="rtext">
						<li><b>train and val: </b>These entries indicate the path to the folders containing the images for
							training and validation. Thus, the script knows exactly where to retrieve the data from. </li>
					</div>
					<div class="rtext">
						<li><b>nc: </b>Specifies the number of classes in the dataset. This information is vital for
							the correct configuration of the last layer of the neural network. </li>
					</div>
					<div class="rtext">
						<li><b>names: </b>A list of labels (class names) corresponding to the numeric identifiers
							used in the generated label files, for example, with the labelImg application. </li>
					</div>
					<br>
					<div class="stext">Or the [BETA] version \/.</div></br>

					<div class="stext"><u>data.yaml</u> <b>(Advanced) [BETA] [Not really stable] [!Use only for large
							datasets!]</b>
						<div class="codee-window">
							<pre><code class="language-python">
						path: AI
						train: train/images
						val: val/images
						nc: 3

						lr0: 0.001
						lrf: 0.1
						warmup_epochs: 5

						degrees: 2.5
						perspective: 0.0
						scale: 0.01
						fliplr: 0.05
						copy_paste: 0.0

						box: 0.07
						cls: 0.4
						dfl: 1.5

						names: ['YellowSample', 'BlueSample', 'RedSample']
											</pre></code>
						</div>
					</div>
					<div class="stext">The <u><b>ml_training.py</b></u> file is the central component that orchestrates
						the entire training and validation process of the YOLOv8n model, using the Ultralytics library and
						PyTorch infrastructure. Here is a technical description of each section and the parameters used:
					</div>
					<div class="stext"><b>1. Module import and basic settings:</b></div>
					<div class="rtext">At the beginning, the YOLO class is imported from the Ultralytics library, essential
						for
						manipulating and training YOLO networks, as well as PyTorch, which handles tensor operations
						and GPU execution. The variable indicating the path to the configuration file (data.yaml)
						contains all the details about the dataset (path to training and validation images, number of
						classes and their names). Additionally, setting the device to "cuda" ensures that
						training will be performed on the GPU, significantly accelerating calculations.</div>

					<div class="stext"><b>2. Model initialization:</b></div>
					<div class="rtext">The model is instantiated using a pre-trained weights file (yolov8n.pt).
						This approach offers a robust starting point, as the network benefits from pre-extracted knowledge,
						accelerating the convergence process and improving initial performance.</div>

					<div class="stext"><b>3. Training configuration:</b></div>
					<div class="stext">The training process is triggered with a series of critical hyperparameters, each
						playing an essential role in optimizing model performance:</div>
					<div class="rtext">
						<div class="stext">
							<li><b>Dataset: </b>The data.yaml file is used to locate the images and related labels,
								ensuring consistency between training and validation data. </li>
						</div>
						<div class="stext">
							<li><b>Epochs: </b>Set to 150, these represent the total number of complete cycles
								through the entire training dataset. Each epoch gives the model the opportunity to
								adjust weights based on all available data, contributing to establishing
								convergence. </li>
						</div>
						<div class="stext">
							<li><b>Image size (imgsz): </b>A fixed value of 640 indicates that all
								images will be resized to 640x640 pixels, ensuring uniformity in the training process
								and facilitating network input management. </li>
						</div>
						<div class="stext">
							<li><b>Automatic Mixed Precision (amp): </b>Activating this feature allows the combined use
								of 16 and 32-bit precision, reducing memory consumption and accelerating
								training, without significantly compromising model accuracy. </li>
						</div>
						<div class="stext">
							<li><b>Batch size: </b>With a value of 12, this parameter defines the number of images
								processed simultaneously before updating model parameters. An optimal batch size helps
								stabilize gradients, offering a balance between computational performance and
								optimization stability. </li>
						</div>
						<div class="stext">
							<li><b>Single Class vs. Multi-Class: </b>The single_cls parameter is set to false, indicating
								that the model is prepared to distinguish between multiple object classes, which
								implies greater learning complexity. </li>
						</div>
						<div class="stext">
							<li><b>Patience: </b>The value of 100 epochs specifies an early stopping strategy, interrupting
								training if no improvements are recorded on the validation set over an extended period,
								thus contributing to avoiding overfitting. </li>
						</div>
						<div class="stext">
							<li><b>Optimizer and related hyperparameters: </b>Choosing the Adam optimizer, together with
								settings for momentum (0.9) and weight decay (0.0005), controls how
								model weights are adjusted. These settings are essential to ensure efficient convergence
								and to prevent the accumulation of excessively large weight values. </li>
						</div>
						<div class="stext">
							<li><b>Augmentation specific parameter (close_mosaic): </b>This setting adjusts the application
								mode
								of mosaic augmentation, a technique that combines multiple images to increase
								data diversity. Closing this technique after a certain number of epochs allows
								the model to focus on learning fine details once it has benefited from an initially
								diversified dataset. </li>
						</div>
					</div>
					<div class="stext"><b><u>ml_training.py</u></b>
						<div class="codee-window">
							<pre><code class="language-python">from ultralytics import YOLO
						import torch

						data_yaml = 'data.yaml' 

						device = 'cuda'

						def main():
							model = YOLO("yolov8n.pt")
							model.train(data='data.yaml', epochs=150, imgsz=640,amp=True, device=device, batch=12, single_cls=False,patience=100, optimizer='Adam', momentum=0.9, weight_decay=0.0005, close_mosaic=25)
							model.val(data=data_yaml)
	
						if __name__ == '__main__': 
							main()			</pre></code>
						</div>
					</div>
					<br></br>
					<div class="stext"><b>Examples</b></div>
					<div class="stext"><img src="/assets/ai/visual.png" width=750 style="border-radius: 10px;"></div>
					<div class="endLine"></div>
					<div class="endD"><a href="https://discord.gg/ZB6vQ62KZT">Support -> Discord</a></div>
					<div class="end"></div>
				</div>
			<?php endif; ?>
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
						<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/label_tool">Utilitar Etichetare
								Imagini</a></div>
						<div class="sub-section">
							<p style="color:#c67171;">Cod Python pentru Antrenament</p>
						</div>

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
						<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/label_tool">Label Images Tool</a></div>
						<div class="sub-section">
							<p style="color:#c67171;">Python Code For Training</p>
						</div>

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