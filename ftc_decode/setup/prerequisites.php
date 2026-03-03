<?php
session_start();
$record_file = fopen("/var/www/html/record_index.txt", "a");
$txt = "prereq\n";
$txtt = "prereq";
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
$lang = isset($_COOKIE['site_lang']) ? $_COOKIE['site_lang'] : 'en';
$detection_method = 'Machine Learning';
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlphaBit - OpenML</title>
    <link rel="stylesheet" href="/assets/css/model_style.css?v=20260304">
    <link rel="stylesheet" href="/assets/css/overview_theme.css?v=20260304">
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

    <style>
        .video-wrapper {
            position: relative;
            width: fit-content;
            display: inline-block;
        }

        .video-play-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 60px;
            color: rgba(255, 255, 255, 0.8);
            pointer-events: none;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
            transition: all 0.3s ease;
        }

        .video-wrapper:hover .video-play-overlay {
            color: #fff;
            transform: translate(-50%, -50%) scale(1.1);
        }
    </style>

    <div class="background-container">
        <div class="alphabit-topleft">
            <a href="#">AlphaBit OpenML</a>
        </div>
        <div class="before_docs">
            <?php echo $season_year; ?>
        </div>
        <div class="ai-star-logo">
            <img src="/assets/images/ai_star_alpha.png" width=50>
        </div>
        <div class="docs">Documentation</div>
        <div class="rbox">
            <div class="title"><?php echo ($lang == 'ro') ? 'Ghid de Initializare (Decode 2026)' : 'Getting Started (Decode 2026)'; ?></div>
            <div class="text-container">
                <?php if ($lang == 'ro'): ?>
                    <div class="stext">
                        <h2>Obiectiv</h2>
                    </div>
                    <div class="rtext">
                        Aceasta pagina te ghideaza de la setup initial pana la primul flux functional pentru sezonul FTC
                        Decode 2026: detectie AprilTag, control autonom si ochire asistata de camera.
                    </div>

                    <div class="stext"><b class="bc">1. Verifica hardware-ul minim</b></div>
                    <div class="rtext">
                        <li>Control Hub / Robot Controller pregatit pentru testare.</li>
                    </div>
                    <div class="rtext">
                        <li>Webcam UVC stabil, montata ferm pe robot.</li>
                    </div>
                    <div class="rtext">
                        <li>Conexiune buna la baterie si iluminare constanta pentru testele de viziune.</li>
                    </div>

                    <div class="stext"><b class="bc">2. Instaleaza mediul de dezvoltare</b></div>
                    <div class="rtext">
                        <li>Instaleaza <b class="bc">Android Studio</b> si configureaza proiectul FTC SDK pentru codul de robot.</li>
                    </div>
                    <div class="rtext">
                        <li>Pentru teste rapide pe laptop, instaleaza Python 3 si OpenCV.</li>
                    </div>
                    <div class="stext">
                        <div class="codee-window">
                            <pre><code class="language-bash">pip install opencv-python numpy</code></pre>
                        </div>
                    </div>

                    <div class="stext"><b class="bc">3. Test optional video setup</b></div>
                    <div class="rtext">
                        Urmareste tutorialul scurt de setup daca vrei un walkthrough vizual.
                    </div>
                    <div class="stext">
                        <div class="video-wrapper">
                            <video id="setupVideo" width="600" controls style="border-radius: 10px;"
                                poster="/ftc_decode/data/initial_setup_thumbnail.png">
                                <source src="/ftc_decode/data/initial_setup.mkv">
                                Browserul tau nu suporta tag-ul video.
                            </video>
                            <i class="fa fa-play-circle video-play-overlay"></i>
                        </div>
                    </div>

                    <div class="stext"><b class="bc">4. Verifica rapid camera</b></div>
                    <div class="rtext">
                        Ruleaza scriptul de test pentru a confirma ca imaginea este citita corect.
                    </div>
                    <div class="stext"><a href="/resources/camera_test.py" download><u><b>camera_test.py</b></u></a> (download)</div>
                    <div class="stext">
                        <div class="codee-window">
                            <pre><code class="language-python">python camera_test.py</code></pre>
                        </div>
                    </div>

                    <div class="stext"><b class="bc">5. Configureaza fluxul AprilTag</b></div>
                    <div class="rtext">
                        <li>Citeste ghidul de baza: <u><a href="/model/<?php echo $season_path; ?>/apriltag"
                                    style="text-decoration:none; color:#ffffff;">AprilTag Getting Started</a></u>.</li>
                    </div>
                    <div class="rtext">
                        <li>Integreaza codul: <u><a href="/model/<?php echo $season_path; ?>/apriltag_code_sample"
                                    style="text-decoration:none; color:#ffffff;">AprilTag Code Sample</a></u>.</li>
                    </div>

                    <div class="stext"><b class="bc">6. Adauga control autonom</b></div>
                    <div class="rtext">
                        Continua catre modulele de deplasare: <u><a href="/model/<?php echo $season_path; ?>/autonomous"
                                style="text-decoration:none; color:#ffffff;">Getting Started</a></u>, <u><a
                                href="/model/<?php echo $season_path; ?>/odometry"
                                style="text-decoration:none; color:#ffffff;">Odometry</a></u>, <u><a
                                href="/model/<?php echo $season_path; ?>/road_runner_056"
                                style="text-decoration:none; color:#ffffff;">Road Runner 0.5.6</a></u>, <u><a
                                href="/model/<?php echo $season_path; ?>/road_runner_10"
                                style="text-decoration:none; color:#ffffff;">Road Runner 1.0</a></u>, <u><a
                                href="/model/<?php echo $season_path; ?>/pedro_pathing"
                                style="text-decoration:none; color:#ffffff;">Pedro Pathing</a></u>.
                    </div>

                    <div class="stext"><b class="bc">7. Activeaza ochirea automata</b></div>
                    <div class="rtext">
                        Alege varianta potrivita: <u><a href="/model/<?php echo $season_path; ?>/auto_aiming_getting_started"
                                style="text-decoration:none; color:#ffffff;">Start</a></u>, <u><a
                                href="/model/<?php echo $season_path; ?>/gyroscope_only"
                                style="text-decoration:none; color:#ffffff;">IMU only</a></u>, <u><a
                                href="/model/<?php echo $season_path; ?>/camera_only"
                                style="text-decoration:none; color:#ffffff;">Webcam only</a></u>, <u><a
                                href="/model/<?php echo $season_path; ?>/gyroscope_and_camera"
                                style="text-decoration:none; color:#ffffff;">IMU + Webcam</a></u>.
                    </div>

                    <div class="stext"><b class="bc">8. Checklist final inainte de field test</b></div>
                    <div class="rtext">
                        <li>Camera are FPS stabil si imagine clara.</li>
                    </div>
                    <div class="rtext">
                        <li>Detectia AprilTag ruleaza constant in telemetrie.</li>
                    </div>
                    <div class="rtext">
                        <li>Traiectoriile autonome au fost testate in siguranta, la viteza mica, apoi crescute gradual.</li>
                    </div>
                <?php else: ?>
                    <div class="stext">
                        <h2>Goal</h2>
                    </div>
                    <div class="rtext">
                        This page takes you from initial setup to a working first Decode 2026 flow: AprilTag detection,
                        autonomous movement, and camera-assisted aiming.
                    </div>

                    <div class="stext"><b class="bc">1. Confirm minimum hardware</b></div>
                    <div class="rtext">
                        <li>Configured Control Hub / Robot Controller environment.</li>
                    </div>
                    <div class="rtext">
                        <li>Stable UVC webcam mounted firmly on the robot.</li>
                    </div>
                    <div class="rtext">
                        <li>Reliable battery power and consistent lighting for vision tests.</li>
                    </div>

                    <div class="stext"><b class="bc">2. Install development tools</b></div>
                    <div class="rtext">
                        <li>Install <b class="bc">Android Studio</b> and prepare your FTC SDK robot project.</li>
                    </div>
                    <div class="rtext">
                        <li>For quick laptop camera tests, install Python 3 and OpenCV.</li>
                    </div>
                    <div class="stext">
                        <div class="codee-window">
                            <pre><code class="language-bash">pip install opencv-python numpy</code></pre>
                        </div>
                    </div>

                    <div class="stext"><b class="bc">3. Optional setup video</b></div>
                    <div class="rtext">
                        Use the short setup walkthrough if you want a visual reference before coding.
                    </div>
                    <div class="stext">
                        <div class="video-wrapper">
                            <video id="setupVideo" width="600" controls style="border-radius: 10px;"
                                poster="/ftc_decode/data/initial_setup_thumbnail.png">
                                <source src="/ftc_decode/data/initial_setup.mkv">
                                Your browser does not support the video tag.
                            </video>
                            <i class="fa fa-play-circle video-play-overlay"></i>
                        </div>
                    </div>

                    <div class="stext"><b class="bc">4. Validate camera input</b></div>
                    <div class="rtext">
                        Run the camera test script to confirm your capture device is detected correctly.
                    </div>
                    <div class="stext"><a href="/resources/camera_test.py" download><u><b>camera_test.py</b></u></a> (download)</div>
                    <div class="stext">
                        <div class="codee-window">
                            <pre><code class="language-python">python camera_test.py</code></pre>
                        </div>
                    </div>

                    <div class="stext"><b class="bc">5. Build your AprilTag baseline</b></div>
                    <div class="rtext">
                        <li>Start with <u><a href="/model/<?php echo $season_path; ?>/apriltag"
                                    style="text-decoration:none; color:#ffffff;">AprilTag Getting Started</a></u>.</li>
                    </div>
                    <div class="rtext">
                        <li>Implement from <u><a href="/model/<?php echo $season_path; ?>/apriltag_code_sample"
                                    style="text-decoration:none; color:#ffffff;">AprilTag Code Sample</a></u>.</li>
                    </div>

                    <div class="stext"><b class="bc">6. Add autonomous control</b></div>
                    <div class="rtext">
                        Continue through movement modules: <u><a href="/model/<?php echo $season_path; ?>/autonomous"
                                style="text-decoration:none; color:#ffffff;">Getting Started</a></u>, <u><a
                                href="/model/<?php echo $season_path; ?>/odometry"
                                style="text-decoration:none; color:#ffffff;">Odometry</a></u>, <u><a
                                href="/model/<?php echo $season_path; ?>/road_runner_056"
                                style="text-decoration:none; color:#ffffff;">Road Runner 0.5.6</a></u>, <u><a
                                href="/model/<?php echo $season_path; ?>/road_runner_10"
                                style="text-decoration:none; color:#ffffff;">Road Runner 1.0</a></u>, <u><a
                                href="/model/<?php echo $season_path; ?>/pedro_pathing"
                                style="text-decoration:none; color:#ffffff;">Pedro Pathing</a></u>.
                    </div>

                    <div class="stext"><b class="bc">7. Enable auto aiming</b></div>
                    <div class="rtext">
                        Choose your implementation path: <u><a href="/model/<?php echo $season_path; ?>/auto_aiming_getting_started"
                                style="text-decoration:none; color:#ffffff;">Start</a></u>, <u><a
                                href="/model/<?php echo $season_path; ?>/gyroscope_only"
                                style="text-decoration:none; color:#ffffff;">IMU only</a></u>, <u><a
                                href="/model/<?php echo $season_path; ?>/camera_only"
                                style="text-decoration:none; color:#ffffff;">Webcam only</a></u>, <u><a
                                href="/model/<?php echo $season_path; ?>/gyroscope_and_camera"
                                style="text-decoration:none; color:#ffffff;">IMU + Webcam</a></u>.
                    </div>

                    <div class="stext"><b class="bc">8. Final checklist before field tests</b></div>
                    <div class="rtext">
                        <li>Camera feed is stable and clearly visible in telemetry.</li>
                    </div>
                    <div class="rtext">
                        <li>AprilTag detection runs consistently with usable pose output.</li>
                    </div>
                    <div class="rtext">
                        <li>Autonomous paths are validated in a safe area at low speed first, then scaled up.</li>
                    </div>
                <?php endif; ?>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var video = document.getElementById('setupVideo');
                        var overlay = document.querySelector('.video-play-overlay');
                        if (!video || !overlay) {
                            return;
                        }

                        video.addEventListener('play', function () {
                            overlay.style.opacity = '0';
                        });

                        video.addEventListener('pause', function () {
                            overlay.style.opacity = '1';
                        });

                        video.addEventListener('ended', function () {
                            overlay.style.opacity = '1';
                        });
                    });
                </script>

                <div class="endLine"></div>
                <div class="endD"><a href="https://discord.gg/ZB6vQ62KZT">Support -> Discord</a></div>
                <div class="end"></div>
            </div>
        </div>
        <div class="docs-container">
            <?php if ($lang == 'ro'): ?>
            <div class="setup">Configurare</div>
            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/overview">Prezentare Generală</a></div>
            <div class="sub-section">
                <p style="color:#c67171;">Initializare Device</p>
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
            <div class="setup">Detectie AprilTag</div>
            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/apriltag">Ghid de
                    initializare</a></div>

            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/apriltag_code_sample">Implementare
                    AprilTag</a></div>

            <div class="docsLine"></div>

            <div class="setup">Control Autonom</div>
            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/autonomous">Ghid de
                    initializare</a></div>
            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/odometry">Odometrie</a></div>
            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/road_runner_056">Implementare Road
                    Runner 0.5.6</a></div>
            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/road_runner_10">Implementare Road
                    Runner 1.0</a></div>
            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/pedro_pathing">Implementare Pedro
                    Pathing</a></div>

            <div class="docsLine"></div>

            <div class="setup">Turela de Ochire Automată</div>
            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/auto_aiming_getting_started">Ghid de
                    initializare</a></div>
            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/gyroscope_only">Implementare
                    Doar IMU</a></div>
            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/camera_only">
                    Implementare Doar Webcam</a>
            </div>
            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/gyroscope_and_camera">Implementare
                    IMU & Webcam</a>
            </div>
            <?php endif; ?>
            <?php else: ?>
            <div class="setup">Setup</div>
            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/overview">Overview</a></div>
            <div class="sub-section">
                <p style="color:#c67171;">Getting Started</p>
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
            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/apriltag_code_sample">AprilTag
                    Implementation</a></div>

            <div class="docsLine"></div>

            <div class="setup">Autonomous Control</div>
            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/autonomous">Getting
                    Started</a></div>
            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/odometry">Odometry
                    Pods</a></div>
            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/road_runner_056">Road Runner 0.5.6
                    Implementation</a></div>
            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/road_runner_10">Road Runner 1.0
                    Implementation</a></div>
            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/pedro_pathing">Pedro Pathing
                    Implementation</a></div>

            <div class="docsLine"></div>

            <div class="setup">Auto Aiming Turret</div>
            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/auto_aiming_getting_started">Getting
                    Started</a></div>
            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/gyroscope_only">IMU
                    Only
                    Implementation</a></div>
            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/camera_only">
                    Webcam Only
                    Implementation</a>
            </div>
            <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/gyroscope_and_camera">IMU &
                    Webcam
                    Implementation</a>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/assets/includes/chat_widget.php'; ?>
</body>

</html>
