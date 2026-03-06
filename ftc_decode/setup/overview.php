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
$lang = isset($_COOKIE['site_lang']) ? $_COOKIE['site_lang'] : 'en';
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

        document.addEventListener("DOMContentLoaded", function () {
            var lang = getCookie('site_lang');
            if (!lang) {
                document.getElementById('language-popup').style.display = 'flex';
            }
            if (!getCookie('season_choice')) {
                setCookie('season_choice', 'Decode', 365);
            }
        });
    </script>

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
            <div class="title"><?php echo ($lang == 'ro') ? 'Prezentare Generala - Decode 2026' : 'Overview - Decode 2026'; ?></div>
            <div class="text-container">
                <?php if ($lang == 'ro'): ?>
                    <div class="ftext">
                        OpenML Decode este documentatia pentru sezonul FTC Decode 2026. Daca este prima ta vizita, incepe cu
                        <a href="/model/<?php echo $season_path; ?>/prerequisites"
                            style="text-decoration:none; color:#ffffff;">Getting Started</a>.
                    </div>

                    <div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Ce include aceasta sectiune</div>
                    <div class="rtext">
                        <li>Configurare AprilTag pentru localizare si orientare pe teren.</li>
                    </div>
                    <div class="rtext">
                        <li>Control autonom cu odometrie, Road Runner si Pedro Pathing.</li>
                    </div>
                    <div class="rtext">
                        <li>Auto aiming cu IMU, camera sau combinatie IMU + camera.</li>
                    </div>
                    <div class="rtext">
                        <li>Exemple de implementare pentru integrare rapida pe robot.</li>
                    </div>

                    <div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Parcurs recomandat</div>
                    <div class="rtext">
                        <li>1. Finalizeaza setup-ul in <a href="/model/<?php echo $season_path; ?>/prerequisites"
                                style="text-decoration:none; color:#ffffff;">Getting Started</a>.</li>
                    </div>
                    <div class="rtext">
                        <li>2. Configureaza detectia in <a href="/model/<?php echo $season_path; ?>/apriltag"
                                style="text-decoration:none; color:#ffffff;">AprilTag</a>.</li>
                    </div>
                    <div class="rtext">
                        <li>3. Construieste fluxul de miscare in <a href="/model/<?php echo $season_path; ?>/autonomous"
                                style="text-decoration:none; color:#ffffff;">Autonomous Control</a>.</li>
                    </div>
                    <div class="rtext">
                        <li>4. Activeaza ochirea asistata in <a href="/model/<?php echo $season_path; ?>/auto_aiming_getting_started"
                                style="text-decoration:none; color:#ffffff;">Auto Aiming</a>.</li>
                    </div>

                    <div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">
                        Rezultatul dorit: un workflow stabil, usor de testat si gata pentru iteratii rapide in meciurile Decode.
                    </div>
                <?php else: ?>
                    <div class="ftext">
                        OpenML Decode is the documentation hub for FTC Decode 2026. If this is your first visit, start with
                        <a href="/model/<?php echo $season_path; ?>/prerequisites"
                            style="text-decoration:none; color:#ffffff;">Getting Started</a>.
                    </div>

                    <div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">What this section covers</div>
                    <div class="rtext">
                        <li>AprilTag setup for field localization and robot orientation.</li>
                    </div>
                    <div class="rtext">
                        <li>Autonomous control with odometry, Road Runner, and Pedro Pathing.</li>
                    </div>
                    <div class="rtext">
                        <li>Auto aiming using IMU-only, camera-only, or hybrid IMU + camera.</li>
                    </div>
                    <div class="rtext">
                        <li>Implementation examples you can adapt directly to your robot codebase.</li>
                    </div>

                    <div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Recommended path</div>
                    <div class="rtext">
                        <li>1. Complete setup in <a href="/model/<?php echo $season_path; ?>/prerequisites"
                                style="text-decoration:none; color:#ffffff;">Getting Started</a>.</li>
                    </div>
                    <div class="rtext">
                        <li>2. Configure detection in <a href="/model/<?php echo $season_path; ?>/apriltag"
                                style="text-decoration:none; color:#ffffff;">AprilTag</a>.</li>
                    </div>
                    <div class="rtext">
                        <li>3. Build movement logic in <a href="/model/<?php echo $season_path; ?>/autonomous"
                                style="text-decoration:none; color:#ffffff;">Autonomous Control</a>.</li>
                    </div>
                    <div class="rtext">
                        <li>4. Add assisted aiming in <a href="/model/<?php echo $season_path; ?>/auto_aiming_getting_started"
                                style="text-decoration:none; color:#ffffff;">Auto Aiming</a>.</li>
                    </div>

                <?php endif; ?>
                <div class="endLine"></div>
                <div class="endD"><a href="https://discord.gg/ZB6vQ62KZT">Support -> Discord</a></div>
                <div class="end"></div>
            </div>
        </div>

        <div class="docs-container">
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


                <div class="setup">Training ML</div>
                <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/training">Training Dataset</a></div>
                <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/training_structure">Training
                        Structure</a></div>
                <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/label_tool">Label Images Tool</a></div>
                <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/training_ml">Python Code For
                        Training</a></div>

                <div class="docsLine"></div>

                <div class="setup">Examples</div>
                <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/pythonml">Python Code For Detection</a>
                </div>
                <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/android_studio">Android Studio
                        Implementation</a></div>
                <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/robot_control">Control Intake Using The
                        OpenML</a></div>
                <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/robot_control">Autonomous ML
                        Implementation</a></div>
                <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/robot_control">TeleOp ML
                        Implementation</a></div>
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
        </div>
    </div>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/assets/includes/chat_widget.php'; ?>
</body>

</html>
