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
            <div class="title">Resources</div>
            <div class="text-container">
                <div class="stext">1. If you have <b class="bc">Low Quality Camera</b> and want to test the ML Model in
                    <b class="bc">Python</b> Download This Model:
                </div>
                <div class="rtext">
                    <div class="stext">
                        <li><b>Machine Learning Model <u>[Low Quality Camera]</b></u> <b class="bc"><u>[For Python
                                    Testing]</u></li></b>
                    </div>
                    <div class="downloadbtn"><a href="/assets/ai/low.pt">Download</a></div>
                </div><br></br>
                <div class="stext">2. If you have <b class="bc">Medium -> High Quality Camera</b> and want to test the
                    ML Model in <b class="bc">Python</b> Download This Model:</div>
                <div class="rtext">
                    <div class="stext">
                        <li><b>Machine Learning Model <u>[Normal Webcams]</u> <b class="bc"><u>[For Python
                                        Testing]</u></b></li>
                    </div>
                    <div class="downloadbtn"><a href="/assets/ai/high.pt">Download</a></div>
                </div>
                <div class="endLinee"></div>
                <br></br>
                <br></br>
                <div class="stext">3. If you have <b class="bc">Low Quality Camera</b> and want to test the ML Model on
                    the <b class="bc">Robot</b> Download This Model:</div>
                <div class="rtext">
                    <div class="ftext">
                        <li><b>Machine Learning <u>[Low Quality Camera]</u> <b class="bc"><u>[Robot (Control Hub)]</u>
                        </li></b>
                    </div>
                    <div class="downloadbtn"><a href="/assets/ai/low.tflite">Download</a></div><br></br>
                </div>
                <div class="stext">4. If you have <b class="bc">Medium -> High Quality Camera</b> and want to test the
                    ML Model on the <b class="bc">Robot</b> Download This Model:</div>
                <div class="rtext">
                    <div class="stext">
                        <li><b>Machine Learning Model <u>[Normal Webcam]</u> <b class="bc"><u>[Robot (Control
                                        Hub)]</u></b></li>
                    </div>
                    <div class="downloadbtn"><a href="/assets/ai/high.tflite">Download</a></div>
                </div>
                <div class="endLine"></div>
                <div class="endD"><a href="https://discord.gg/ZB6vQ62KZT">Support -> Discord</a></div>
                <div class="end"></div>
            </div>
        </div>
        <div class="docs-container">
            <?php if ($lang == 'ro'): ?>
                <div class="setup">Configurare</div>
                <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/overview">Prezentare Generală</a></div>
                <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/prerequisites">Initializare Device</a>
                </div>
                <div class="sub-section">
                    <p style="color:#c67171;">Resurse</p>
                </div>
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
                <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/prerequisites">Getting Started</a>
                </div>
                <div class="sub-section">
                    <p style="color:#c67171;">Resources</p>
                </div>
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
</body>

</html>