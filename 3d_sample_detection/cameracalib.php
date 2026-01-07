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
			<div class="title">Camera Calibration</div>
			<div class="text-container">
				<?php
				$lang = isset($_COOKIE['site_lang']) ? $_COOKIE['site_lang'] : 'en';
				if ($lang == 'ro'):
					?>
					<div class="stext"><b class="bc">1.</b> Creează un script python cu numele <u>camera_calibration.py</u>
					</div>
					<div class="stext">
						<div class="codee-window">
							<pre><code class="language-python">import cv2
						from ultralytics import YOLO
						import math

						model = YOLO("C:\\Users\\&lt;USER>\\Desktop\\OpenML\\high.pt") #Modifica aici cu Path-ul tau curent si cu modelul OpenML descarcat

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

								cv2.putText(frame_with_results, f"Obj Width: {object_width:.2f}",
											(x1, y1 + 40),
											cv2.FONT_HERSHEY_SIMPLEX, 0.6, (0, 255, 0), 2)
								cv2.putText(frame_with_results, f"Obj Height: {object_height:.2f}",
											(x1, y1 + 60),
											cv2.FONT_HERSHEY_SIMPLEX, 0.6, (0, 255, 0), 2)
		
							cv2.imshow('OpenML - Real-time Detection', frame_with_results)

							if cv2.waitKey(1) & 0xFF == ord('q'): # Apasa Q pentru a iesi din cod
								break

						cap.release()
						cv2.destroyAllWindows()</pre></code>
						</div>
					</div>
					<div class="stext"><b class="bc">2.</b> Pentru a calcula orientarea la care se află sample-ul trebuie
						măsurat minWidth, maxWidth, minHeight, maxHeight</div>
					<div class="rtext"><b class="bc">1. Pune sample-ul să fie drept în imagine </b></div>
					<div class="rtext"><b class="bc">2.</b> Asigură-te că sample-ul se află în <b class="bc">totalitate</b>
						în imagine. Este neapărat ca sample-ul să se încadreze în imagine și să nu fie jumătate din sample
						afară din imagine</div>
					<div class="rtext">Exemplu: </div>
					<div class="rtext"><img src="/assets/ai/straight_sample.png"
							style="border-radius: 10px; width: 37.5vh;"></div>
					<div class="rtext">
						<li>De aici trebuie notate <u>minWidth</u> care este egal cu <b class="bc">Width</b> și
							<u>minHeight</u> care este egal cu <b class="bc">Height</b>
						</li>
					</div>
					<div class="rtext">&nbsp;</div>
					<div class="rtext"><b class="bc">3.</b> Acum trebuie rotit sample-ul la unghiul de 90 de grade, iar mai
						apoi trebuie notat <u>maxWidth</u>, <u>maxWidth</u>. Asigură-te că sample-ul se află în totalitate
						în imagine</div>
					<div class="rtext">Exemplu: </div>
					<div class="rtext"><img src="/assets/ai/90_degree.png" style="border-radius: 10px; width: 37.5vh;">
					</div>
					<div class="rtext">
						<li>De aici trebuie notate maxWidth care este egal cu Width și maxWidth care este egal cu Height
						</li>
					</div>
					<div class="stext"><b class="bc">2.</b>Aplicarea matematică a măsurătorilor</div>
					<div class="rtext"><b class="bc">1.</b> După ce minWidth, minHeight, maxWidth, maxHeight au fost
						măsurate trebuie să le aplicăm matematic pentru a afla orientarea sample-ului
						<div class="rtext">
							<li>Să ne imaginăm că este o diagonală în acest dreptunghi creat de ML. Noi calculăm unghiul
								dintre diagonală și width.</li>
						</div>
						<div class="rtext">
							<li>De aici putem aplica funcția arctg din matematică pentru calcularea unghiului.</li>
						</div>
						<div class="rtext">
							<li>Împărțim width la height => arctg(width/height) din asta rezultă un unghi.</li>
						</div>
						<div class="rtext">
							<li>Acum facem calculele pentru când sample-ul era drept (minWidth, minHeight).</li>
						</div>
						<div class="rtext">
							<li>
								<div class="codee-window">
									<pre><code class="language-python">math.degrees(math.atan(object_width / object_height))</code></pre>
							</li>
						</div>
						<div class="rtext">
							<li>Unghiul dat de <b class="bc">arctg</b> atunci când sample-ul este <b class="bc">drept</b>
								este de <b class="bc">24.79 de grade</b></li>
						</div>
						<div class="rtext">
							<li>Facem același lucru și pentru când sample-ul este la 90 de grade. (maxWidth, maxHeight)</li>
						</div>
						<div class="rtext">
							<li>Unghiul dat de <b class="bc">arctg</b> atunci când sample-ul este la unghiul de <b
									class="bc">90 de grade</b> este de <b class="bc">66.32 de grade</b></li>
						</div>
					</div>
					<div class="rtext">&nbsp;</div>
					<div class="rtext"><b class="bc">2.</b> După aflarea unghiului atunci când sample-ul este drept și la 90
						de grade trebuie cu o <b class="bc">formulă matematică</b> să aflăm unghiul adevărat al sample-ului
						<div class="rtext">
							<li>Unghiul dat de arctg îl vom scădea cu unghiul inițial atunci când sample-ul era drept
								(unghiul cel mai mic).</li>
						</div>
						<div class="rtext">
							<li>orientation_angle = (arctg(object_width/object_height) - 24.79) / y</li>
						</div>
						<div class="rtext">
							<li>Iar y este unghiul maxim (sample-ul este la 90 de grade) - unghiul minim (sample-ul este
								drept) / 90 de grade</li>
						</div>
						<div class="rtext">
							<li>y = (66.32 - 27.79)/90 => y = 0.45955</li>
						</div>
						<div class="rtext">
							<li>Aplicăm la formula finală și iese (În Python): </li>
						</div>
						<div class="rtext">
							<div class="codee-window">
								<pre><code class="language-python">orientation_angle = (math.degrees(math.atan(object_width/object_height))-24.79)/0.45955</code></pre>
							</div>
						</div>
						<div class="rtext">
							<li>Formula matematică finală: <b
									class="bc">[arctg(object_width/object_height)-24.79]/0.45955</b></li>
						</div>
					</div>
					<div class="rtext">&nbsp;</div>
					<div class="stext"><b class="bc">3. </b>Calcularea poziției Intake-ului bazat pe unghiul sample-ului (<b
							class="bc">Servo Intake</b>)</div>
					<div class="rtext">
						<li>Luăm exemplul că Intake-ul la poziția de 0.5 este perfect drept și poate lua doar sample-uri în
							poziție dreaptă</li>
					</div>
					<div class="rtext">
						<li>Trebuie măsurată poziția Intake-ului când este la 90 de grade în stânga și tot la fel când este
							la 90 de grade în dreapta</li>
					</div>
					<div class="rtext">
						<li>Poziția de 0.15 este unghiul 90 de grade spre stânga al Intake-ului.</li>
					</div>
					<div class="rtext">
						<li>Poziția de 0.85 este unghiul 90 de grade spre dreapta al Intake-ului.</li>
					</div>
					<div class="rtext">
						<li>Astfel noi știm că 0.85-0.5 = 0.35 are o rotație de 90 de grade. Astfel împărțim 0.35 la 90 de
							grade</li>
					</div>
					<div class="rtext">
						<li>0.35/90 de grade = 0.0038 poziție Intake/grad. Deci la fiecare 0.0038 poziție la Intake adăugată
							Intake-ul se mișcă cu un 1 grad spre dreapta</li>
					</div>
					<div class="rtext">
						<li>Formula asta poate fi aplicată și pentru alte Intake-uri cu poziții de servo diferite.</li>
					</div>
					<div class="stext"><b class="bc">După ce ai terminat calibrarea camerei, întoarce-te înapoi la <u><a
									href="/model/<?php echo $season_path; ?>/prerequisites"
									style="text-decoration: none; color: white;">Getting
									Started</a></u></b></div>
					<div class="endLine"></div>
					<div class="endD"><a href="https://discord.gg/ZB6vQ62KZT">Support -> Discord</a></div>
					<div class="end"></div>
				<?php else: ?>
					<div class="stext"><b class="bc">1.</b> Create a python script named <u>camera_calibration.py</u>
					</div>
					<div class="stext">
						<div class="codee-window">
							<pre><code class="language-python">import cv2
						from ultralytics import YOLO
						import math

						model = YOLO("C:\\Users\\&lt;USER>\\Desktop\\OpenML\\high.pt") #Modify here with your current Path and the downloaded OpenML model

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

								cv2.putText(frame_with_results, f"Obj Width: {object_width:.2f}",
											(x1, y1 + 40),
											cv2.FONT_HERSHEY_SIMPLEX, 0.6, (0, 255, 0), 2)
								cv2.putText(frame_with_results, f"Obj Height: {object_height:.2f}",
											(x1, y1 + 60),
											cv2.FONT_HERSHEY_SIMPLEX, 0.6, (0, 255, 0), 2)
		
							cv2.imshow('OpenML - Real-time Detection', frame_with_results)

							if cv2.waitKey(1) & 0xFF == ord('q'): # Press Q to exit the code
								break

						cap.release()
						cv2.destroyAllWindows()</pre></code>
						</div>
					</div>
					<div class="stext"><b class="bc">2.</b> To calculate the orientation of the sample, minWidth, maxWidth,
						minHeight, maxHeight must be measured</div>
					<div class="rtext"><b class="bc">1. Place the sample straight in the image </b></div>
					<div class="rtext"><b class="bc">2.</b> Make sure the sample is <b class="bc">completely</b>
						in the image. It is essential that the sample fits in the image and is not half out of the image
					</div>
					<div class="rtext">Example: </div>
					<div class="rtext"><img src="/assets/ai/straight_sample.png"
							style="border-radius: 10px; width: 37.5vh;"></div>
					<div class="rtext">
						<li>From here, note <u>minWidth</u> which is equal to <b class="bc">Width</b> and
							<u>minHeight</u> which is equal to <b class="bc">Height</b>
						</li>
					</div>
					<div class="rtext">&nbsp;</div>
					<div class="rtext"><b class="bc">3.</b> Now rotate the sample to a 90-degree angle, and then note
						<u>maxWidth</u>, <u>maxWidth</u>. Make sure the sample is completely in the image
					</div>
					<div class="rtext">Example: </div>
					<div class="rtext"><img src="/assets/ai/90_degree.png" style="border-radius: 10px; width: 37.5vh;">
					</div>
					<div class="rtext">
						<li>From here, note maxWidth which is equal to Width and maxWidth which is equal to Height
						</li>
					</div>
					<div class="stext"><b class="bc">2.</b>Mathematical application of measurements</div>
					<div class="rtext"><b class="bc">1.</b> After minWidth, minHeight, maxWidth, maxHeight have been
						measured, we must apply them mathematically to find the sample orientation
						<div class="rtext">
							<li>Let's imagine there is a diagonal in this rectangle created by ML. We calculate the angle
								between the diagonal and the width.</li>
						</div>
						<div class="rtext">
							<li>From here we can apply the arctan function from mathematics to calculate the angle.</li>
						</div>
						<div class="rtext">
							<li>We divide width by height => arctan(width/height) resulting in an angle.</li>
						</div>
						<div class="rtext">
							<li>Now we do the calculations for when the sample was straight (minWidth, minHeight).</li>
						</div>
						<div class="rtext">
							<li>
								<div class="codee-window">
									<pre><code class="language-python">math.degrees(math.atan(object_width / object_height))</code></pre>
							</li>
						</div>
						<div class="rtext">
							<li>The angle given by <b class="bc">arctan</b> when the sample is <b class="bc">straight</b>
								is <b class="bc">24.79 degrees</b></li>
						</div>
						<div class="rtext">
							<li>We do the same for when the sample is at 90 degrees. (maxWidth, maxHeight)</li>
						</div>
						<div class="rtext">
							<li>The angle given by <b class="bc">arctan</b> when the sample is at a <b class="bc">90-degree
									angle</b> is <b class="bc">66.32 degrees</b></li>
						</div>
					</div>
					<div class="rtext">&nbsp;</div>
					<div class="rtext"><b class="bc">2.</b> After finding the angle when the sample is straight and at 90
						degrees, we must use a <b class="bc">mathematical formula</b> to find the true angle of the sample
						<div class="rtext">
							<li>We subtract the initial angle when the sample was straight (the smallest angle) from the
								angle given by arctan.</li>
						</div>
						<div class="rtext">
							<li>orientation_angle = (arctg(object_width/object_height) - 24.79) / y</li>
						</div>
						<div class="rtext">
							<li>And y is the maximum angle (sample is at 90 degrees) - minimum angle (sample is straight) /
								90 degrees</li>
						</div>
						<div class="rtext">
							<li>y = (66.32 - 27.79)/90 => y = 0.45955</li>
						</div>
						<div class="rtext">
							<li>We apply the final formula and it results in (In Python): </li>
						</div>
						<div class="rtext">
							<div class="codee-window">
								<pre><code class="language-python">orientation_angle = (math.degrees(math.atan(object_width/object_height))-24.79)/0.45955</code></pre>
							</div>
						</div>
						<div class="rtext">
							<li>Final mathematical formula: <b
									class="bc">[arctg(object_width/object_height)-24.79]/0.45955</b></li>
						</div>
					</div>
					<div class="rtext">&nbsp;</div>
					<div class="stext"><b class="bc">3. </b>Calculating the Intake position based on the sample angle (<b
							class="bc">Servo Intake</b>)</div>
					<div class="rtext">
						<li>Let's take the example that the Intake at position 0.5 is perfectly straight and can only take
							samples in a straight position</li>
					</div>
					<div class="rtext">
						<li>The Intake position must be measured when it is 90 degrees to the left and similarly when it is
							90 degrees to the right</li>
					</div>
					<div class="rtext">
						<li>Position 0.15 is the 90-degree angle to the left of the Intake.</li>
					</div>
					<div class="rtext">
						<li>Position 0.85 is the 90-degree angle to the right of the Intake.</li>
					</div>
					<div class="rtext">
						<li>Thus we know that 0.85-0.5 = 0.35 has a rotation of 90 degrees. So we divide 0.35 by 90 degrees
						</li>
					</div>
					<div class="rtext">
						<li>0.35/90 degrees = 0.0038 Intake position/degree. So for every 0.0038 added to the Intake
							position, the Intake moves 1 degree to the right</li>
					</div>
					<div class="rtext">
						<li>This formula can also be applied to other Intakes with different servo positions.</li>
					</div>
					<div class="stext"><b class="bc">After you have finished calibrating the camera, go back to <u><a
									href="/model/<?php echo $season_path; ?>/prerequisites"
									style="text-decoration: none; color: white;">Getting
									Started</a></u></b></div>
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
					<div class="sub-section">
						<p style="color:#c67171;">Calibrarea Camerei</p>
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
					<div class="sub-section">
						<p style="color:#c67171;">Camera Calibration</p>
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
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
</body>

</html>