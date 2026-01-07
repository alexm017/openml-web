<?php
session_start();
$record_file = fopen("/var/www/html/record_index.txt", "a");
$txt = "python\n";
$txtt = "python";
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
	<link rel="stylesheet" href="/assets/css/model_style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="shortcut icon" type="image/x-icon" href="/assets/images/alphabit.ico" />
	<!-- Highlight.js CSS Theme -->
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.6.0/styles/atom-one-dark.min.css">
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
			<div class="title">Python Code For Detection</div>
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
					<div class="stext"><b class="bc">[!]</b> Camera trebuie să fie neapărat <u>perpendiculară</u> cu
						terenul. <b class="bc">[!]</b></div>
					<div class="stext"><b class="bc">[!]</b> Camera trebuie să ofere o vedere în 2D (Exemplu în <u><a
								href="/model/cameracalib" style="text-decoration:none; color: white;">Camera
								Calibration</a></u>). <b class="bc">[!]</b></div>
					<div class="stext"><b class="bc">[!]</b> Camera trebuie să fie deasupra sample-ului pentru a calcula
						orientarea sample-ului. <b class="bc">[!]</b></div>
					<div class="stext"><b><u>ml_testing.py</u></b></div>
					<div class="code-window">
						<pre><code class="language-python" >#Pentru a opri codul trebuie apasata tasta Q
								import cv2
								from ultralytics import YOLO
								import math

								model = YOLO("C:\\Users\\&lt;USER>\\Desktop\\OpenML\\high.pt") #Modifica aici cu Path-ul tau curent si cu modelul OpenML descarcat

								focal_length_pixels = 600
								object_real_width = 3.8
								camera_angle_from_object = 0
								fov_degrees = 60 #Aici se pune FOV-ul camerei. Uita-te pe Camera Calibration pe a afla cum se calculeaza
								object_width = 0
								object_height = 0

								maxWidth = 0
								minWidth = 3000

								cap = cv2.VideoCapture(0) #Daca nu functioneaza, incrementeaza numarul cu 1 pana cand camera functioneaza si apare pe ecran

								while True:
									ret, frame = cap.read()
									if not ret:
										break

									results = model.predict(frame, conf=0.5)

									frame_with_results = results[0].plot()
									boxes = results[0].boxes
									if len(boxes) > 0:
										x1, y1, x2, y2 = map(int, boxes.xyxy[0])
										object_width = x2 - x1
										object_height = y2 - y1

										middle_of_the_object_pos = (x2 + x1) / 2
										middle_of_the_screen = 640 / 2 #640x480 640 Este distanta orizontala si masoara mijlocul imaginii orizontal. Daca testati pe o rezolutie de 320x240, schimbati in 320 de la 640
										offset = middle_of_the_object_pos - middle_of_the_screen
										camera_angle_from_object = (offset / 640) * fov_degrees #Aici masoara unghiul sample-ului fata de camera orizontal. (Se poate si vertical, acelasi lucru, dar daca rezolutia este 640x480, in loc de 640 este 480

										first_angle = 0.00 #Aici pune cel mai mic unghi calculat matematic cand ai urmati pasii de pe pagina Camera Calibration. Sample-ul este drept arctg(width/height) => unghi
										y = 0.00 #Aici calculezi (max angle - min angle)/90 de grade. Exemplu: (66.32-24.79)/90 => 0.459555
										raw_angle = math.degrees(math.atan(object_width / object_height))
										object_angle = (raw_angle - first_angle) / y
										servo_position = 0.5 + (object_angle * 0.0038) #Calcularea Servo_Pos la fel ca pe pagina Camera Calibration
		
										cv2.putText(frame_with_results, f"Sample Angle: {object_angle:.2f}",
													(x1, y1 + 80),
													cv2.FONT_HERSHEY_SIMPLEX, 0.6, (0, 255, 0), 2)
		
										cv2.putText(frame_with_results, f"Servo Pos: {servo_position:.2f}",
													(x1, y1 + 100),
													cv2.FONT_HERSHEY_SIMPLEX, 0.6, (0, 255, 0), 2)
		
									cv2.imshow('OpenML - Real-time Detection', frame_with_results)

									if cv2.waitKey(1) &amp; 0xFF == ord('q'): # Apasa Q pentru a iesi din cod
										break

								cap.release()
								cv2.destroyAllWindows()
													</code></pre>
					</div>
					<div class="stext">
						Acest cod folosește YOLO pentru a detecta un obiect în timp real folosind o cameră și calculează
						poziția acestuia față de centru, determinând astfel unghiul său și poziția unui servomotor pentru
						urmărire.
					</div>
					<div class="stext">
						<b>Cum funcționează?</b>
					</div>
					<div class="stext">
						<b>1. Inițializare</b>
					</div>
					<div class="rtext">
						<li>Se încarcă modelul YOLO antrenat pentru recunoașterea obiectului dorit.</li>
					</div>
					<div class="rtext">
						<li>Se definește câmpul vizual al camerei și alte variabile necesare pentru calcule.</li>
					</div>
					<div class="rtext">
						<li>Se deschide camera pentru capturarea imaginilor în timp real.</li>
					</div>
					<div class="stext">
						<b>2. Procesare continuă</b>
					</div>
					<div class="rtext">
						<li>Se preia fiecare cadru video și se trimite la modelul YOLO pentru detectarea obiectului.</li>
					</div>
					<div class="rtext">
						<li>Dacă obiectul este găsit, se obține un „bounding box” (coordonatele care încadrează obiectul).
						</li>
					</div>
					<div class="rtext">
						<li>Se calculează lățimea și înălțimea obiectului în pixeli.</li>
					</div>
					<div class="stext">
						<b>3. Calcularea unghiului și a poziției</b>
					</div>
					<div class="rtext">
						<li>Se compară poziția obiectului cu centrul ecranului pentru a determina cât de departe este de axa
							centrală.</li>
					</div>
					<div class="rtext">
						<li>Pe baza dimensiunilor obiectului detectat, se calculează unghiul său real.</li>
					</div>
					<div class="rtext">
						<li>Se ajustează unghiul calculat pentru a obține o valoare corectă a poziției servomotorului.</li>
					</div>
					<div class="stext">
						<b>4. Afișarea rezultatelor</b>
					</div>
					<div class="rtext">
						<li>Se desenează informațiile pe imagine, cum ar fi lățimea, înălțimea și unghiul obiectului.</li>
					</div>
					<div class="rtext">
						<li>Se afișează imaginea procesată într-o fereastră.</li>
					</div>
					<div class="stext">
						<b>5. Ieșire și oprire</b>
					</div>
					<div class="rtext">
						<li>Programul rulează continuu până când utilizatorul apasă tasta 'q'.</li>
					</div>
					<div class="rtext">
						<li>Se închide camera și se eliberează resursele.</li>
					</div>
					<div class="endLine"></div>
					<div class="endD"><a href="https://discord.gg/ZB6vQ62KZT">Support -> Discord</a></div>
					<div class="end"></div>
				<?php else: ?>
					<div class="stext"><b class="bc">[!]</b> The camera must be <u>perpendicular</u> to the
						ground. <b class="bc">[!]</b></div>
					<div class="stext"><b class="bc">[!]</b> The camera must provide a 2D view (Example in <u><a
								href="/model/cameracalib" style="text-decoration:none; color: white;">Camera
								Calibration</a></u>). <b class="bc">[!]</b></div>
					<div class="stext"><b class="bc">[!]</b> The camera must be above the sample to calculate the
						sample orientation. <b class="bc">[!]</b></div>
					<div class="stext"><b><u>ml_testing.py</u></b></div>
					<div class="code-window">
						<pre><code class="language-python" >#To stop the code, press the Q key
								import cv2
								from ultralytics import YOLO
								import math

								model = YOLO("C:\\Users\\&lt;USER>\\Desktop\\OpenML\\high.pt") #Modify here with your current Path and the downloaded OpenML model

								focal_length_pixels = 600
								object_real_width = 3.8
								camera_angle_from_object = 0
								fov_degrees = 60 #Here you put the camera FOV. Look at Camera Calibration to find out how to calculate it
								object_width = 0
								object_height = 0

								maxWidth = 0
								minWidth = 3000

								cap = cv2.VideoCapture(0) #If it doesn't work, increment the number by 1 until the camera works and appears on the screen

								while True:
									ret, frame = cap.read()
									if not ret:
										break

									results = model.predict(frame, conf=0.5)

									frame_with_results = results[0].plot()
									boxes = results[0].boxes
									if len(boxes) > 0:
										x1, y1, x2, y2 = map(int, boxes.xyxy[0])
										object_width = x2 - x1
										object_height = y2 - y1

										middle_of_the_object_pos = (x2 + x1) / 2
										middle_of_the_screen = 640 / 2 #640x480 640 Is the horizontal distance and measures the middle of the image horizontally. If testing on a 320x240 resolution, change to 320 from 640
										offset = middle_of_the_object_pos - middle_of_the_screen
										camera_angle_from_object = (offset / 640) * fov_degrees #Here it measures the sample angle relative to the camera horizontally. (It can also be vertical, same thing, but if the resolution is 640x480, instead of 640 it is 480

										first_angle = 0.00 #Here put the smallest mathematically calculated angle when you followed the steps on the Camera Calibration page. The sample is straight arctan(width/height) => angle
										y = 0.00 #Here you calculate (max angle - min angle)/90 degrees. Example: (66.32-24.79)/90 => 0.459555
										raw_angle = math.degrees(math.atan(object_width / object_height))
										object_angle = (raw_angle - first_angle) / y
										servo_position = 0.5 + (object_angle * 0.0038) #Calculating Servo_Pos same as on the Camera Calibration page
		
										cv2.putText(frame_with_results, f"Sample Angle: {object_angle:.2f}",
													(x1, y1 + 80),
													cv2.FONT_HERSHEY_SIMPLEX, 0.6, (0, 255, 0), 2)
		
										cv2.putText(frame_with_results, f"Servo Pos: {servo_position:.2f}",
													(x1, y1 + 100),
													cv2.FONT_HERSHEY_SIMPLEX, 0.6, (0, 255, 0), 2)
		
									cv2.imshow('OpenML - Real-time Detection', frame_with_results)

									if cv2.waitKey(1) &amp; 0xFF == ord('q'): # Press Q to exit the code
										break

								cap.release()
								cv2.destroyAllWindows()
													</code></pre>
					</div>
					<div class="stext">
						This code uses YOLO to detect an object in real-time using a camera and calculates its
						position relative to the center, thus determining its angle and the position of a servo motor for
						tracking.
					</div>
					<div class="stext">
						<b>How does it work?</b>
					</div>
					<div class="stext">
						<b>1. Initialization</b>
					</div>
					<div class="rtext">
						<li>The trained YOLO model is loaded for recognizing the desired object.</li>
					</div>
					<div class="rtext">
						<li>The camera's field of view and other variables necessary for calculations are defined.</li>
					</div>
					<div class="rtext">
						<li>The camera is opened for capturing images in real-time.</li>
					</div>
					<div class="stext">
						<b>2. Continuous processing</b>
					</div>
					<div class="rtext">
						<li>Each video frame is taken and sent to the YOLO model for object detection.</li>
					</div>
					<div class="rtext">
						<li>If the object is found, a 'bounding box' (coordinates framing the object) is obtained.
						</li>
					</div>
					<div class="rtext">
						<li>The width and height of the object in pixels are calculated.</li>
					</div>
					<div class="stext">
						<b>3. Calculating angle and position</b>
					</div>
					<div class="rtext">
						<li>The object's position is compared with the center of the screen to determine how far it is from
							the central axis.</li>
					</div>
					<div class="rtext">
						<li>Based on the dimensions of the detected object, its real angle is calculated.</li>
					</div>
					<div class="rtext">
						<li>The calculated angle is adjusted to obtain a correct servo motor position value.</li>
					</div>
					<div class="stext">
						<b>4. Displaying results</b>
					</div>
					<div class="rtext">
						<li>Information such as width, height, and object angle is drawn on the image.</li>
					</div>
					<div class="rtext">
						<li>The processed image is displayed in a window.</li>
					</div>
					<div class="stext">
						<b>5. Exit and stop</b>
					</div>
					<div class="rtext">
						<li>The program runs continuously until the user presses the 'q' key.</li>
					</div>
					<div class="rtext">
						<li>The camera is closed and resources are released.</li>
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

				<div class="setup">Detectie Sample 2D</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/sample_2d_math">Ghid de
						initializare</a>
				</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/cameracalib">Calibrarea Camerei</a>
				</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/sample_2d_math">Testare Detectie
						Python</a></div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/sample_2d_math">Implementare Android
						Studio</a></div>

				<div class="docsLine"></div>

				<div class="setup">Detectie Sample 3D</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/sample_2d_math">Ghid de
						initializare</a>
				</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/cameracalib">Calibrarea Camerei</a>
				</div>
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/sample_2d_math">Testare Detectie
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
				<div class="sub-section">
					<p style="color:#c67171;">Cod Python pentru Detecție</p>
				</div>
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
				<div class="sub-section"><a href="/model/<?php echo $season_path; ?>/sample_2d_math">Starter Guide</a></div>
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
				<div class="sub-section">
					<p style="color:#c67171;">Python Code For Detection</p>
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
	<!-- Include Highlight.js library -->
	<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.6.0/highlight.min.js"></script>
	<script>hljs.highlightAll();</script>
</body>

</html>