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
if (isset($_COOKIE['detection_method'])) {
    $detection_method = $_COOKIE['detection_method'];
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

    <!----------------------------------------------------------------------------------->
    <div id="choice-popup" class="choice-popup-overlay" style="display: none;">
        <div class="choice-popup-content">
            <h2>Choose Detection Method</h2>
            <div class="choice-options">
                <div class="choice-option">
                    <button onclick="selectChoice('color_blob')">Color Blob Detection</button>
                    <div class="choice-label fast"><b>Very Fast (~1ms)</b></div>
                    <div class="choice-label fast" style="color: #d1d1d1ff"><b>(Recommended)</b></div>
                </div>

                <div class="choice-option">
                    <button onclick="selectChoice('machine_learning')">Machine Learning (Beta)</button>
                    <div class="choice-label slow"><b>Slow</b></div>
                </div>
            </div>

        </div>
    </div>

    <style>
        .choice-option {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .choice-label {
            margin-top: 6px;
            font-size: 14px;
            font-family: Arial, sans-serif;
        }

        .choice-label.fast {
            color: #4caf50;
            /* green */
        }

        .choice-label.slow {
            color: #ff2600ff;
            /* orange/red */
        }


        .choice-popup-overlay {
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

        .choice-popup-content {
            background-color: #1e1e1e;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            border: 1px solid #333;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }

        .choice-popup-content h2 {
            color: #fff;
            margin-bottom: 35px;
            font-family: Arial, sans-serif;
        }

        .choice-options {
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        .choice-options button {
            padding: 15px 30px;
            font-size: 18px;
            cursor: pointer;
            background-color: #d4d4d4ff;
            color: black;
            border: none;
            border-radius: 8px;
        }

        .choice-options button:hover {
            background-color: #ffffffff;
            transform: scale(1.05);
        }

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

        function selectChoice(choice) {
            setCookie('detection_method', choice, 365);
            document.getElementById('choice-popup').style.display = 'none';
            location.reload();
        }

        document.addEventListener("DOMContentLoaded", function () {
            var choice = getCookie('detection_method');
            if (!choice) {
                document.getElementById('choice-popup').style.display = 'flex';
            }
        });
    </script>
    <!----------------------------------------------------------------------------------->
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
            <div class="title">Getting Started
                <?php if ($detection_method == 'Color Blob Detection')
                    echo 'with <span style="color:#BBFF87;">Color Blob Detection</span>';
                else
                    echo 'with <span style="color:#BBFF87;">Machine Learning</span>';
                ?>
            </div>
            <div class="text-container">
                <div class="stext">
                    <h2> > Initial Setup</h2>
                </div>
                <div class="stext">
                    Click below to watch the initial setup tutorial.
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
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var video = document.getElementById('setupVideo');
                        var overlay = document.querySelector('.video-play-overlay');

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
                <div class="stext"><b class="bc">1. Visual Studio Code</b></div>
                <div class="rtext">1. Download <b class="bc">Visual Studio Code</b>. (Recommended)
                    <div class="downloadbtn"><a href="https://code.visualstudio.com/docs/?dv=win64user">Download</a>
                    </div>
                </div>

                <div class="stext">2.<b class="bc"> Download Python 3.7 or newer </b>(tested on Python
                    3.8/3.9/3.10/3.11)</div>
                <div class="rtext">
                    <div class="downloadbtn"><a
                            href="https://www.python.org/ftp/python/3.12.9/python-3.12.9-amd64.exe">Download</a></div>
                </div>
                <div class="stext"><b class="bc">3. Configure Visual Studio Code & Terminal </b></div>
                <div class="rtext">1. Open Folder (This is the folder where you want to have the OpenML project)</div>
                <div class="rtext">2. Open New Terminal</div>
                <div class="rtext"><img src="/assets/ai/terminal.png" width=500 style="border-radius: 10px;"></div>
                <div class="rtext">3. Install the dependencies</div>
                <div class="stext"><b class="bc">4. Dependencies (use pip3 for python3)</b></div>
                <div class="stext">
                    <div class="codee-window">
                        <pre><code class="language-python" >pip install opencv-python ultralytics numpy</code></pre>
                    </div>
                </div>
                <div class="stext">Or</div>
                <div class="stext">
                    <div class="codee-window">
                        <pre><code class="language-python" >pip install opencv-python==4.8.0.76
                                            pip install ultralytics==8.0.196
                                            pip install numpy==1.24.4		</code></pre>
                    </div>
                </div>
                <div class="stext"><u>If OpenCV does not work try to reinstall it using the method above
                        (or retry using the methods below)</u></div>
                <div class="stext">
                    <div class="codee-window">
                        <pre><code class="language-python" >pip install opencv-python </code></pre>
                    </div>
                </div>
                <div class="stext">Example </div>
                <div class="stext"><img src="/assets/ai/show_terminal_2.png" width="600" style="border-radius: 10px;">
                </div>
                <div class="stext">5.<b class="bc"> Hardware Requirements</b></div>
                <div class="rtext">
                    <li>A Webcam connected to the device is required (the code uses cv2.VideoCapture(0), make sure
                        camera index 0 is correct for your configuration.)</li>
                </div>
                <div class="rtext">
                    <li>Sufficient CPU power for real-time inference (a CUDA-enabled graphics card is recommended
                        for better performance) for Testing and Control Hub is also accepted.
                    </li>
                </div>
                <div class="stext"><b class="bc">6. Test Camera Python Script</b></div>
                <div class="rtext">
                    <li>Create a Python script named <b>camera_test.py</b> and add the following code to it</li>
                </div>
                <div class="stext"><a href="/resources/camera_test.py" download><u><b>camera_test.py</b></u></a> (Click
                    to
                    download)</div>
                <div class="stext">
                    <div class="codee-window">
                        <pre><code class="language-python" >import cv2
    cap = cv2.VideoCapture(0) #If it doesn't work, increment the number by 1 until the camera works and appears on the screen
    ret, frame = cap.read()
    print("Camera working:", ret)
    cap.release()</code></pre>
                    </div>
                </div>
                <div class="stext">
                    Then</div>
                <div class="stext">
                    <div class="codee-window">
                        <pre><code class="language-python" >python camera_test.py</pre></code>
                    </div>
                </div>

                <div class="stext">7.<b class="bc">If the camera works, download the ML model from
                        <u>Resources</u></b></div>
                <div class="rtext">
                    <li>If you have a <b class="bc">Very Low / Low</b> quality camera download the <u><a
                                href="/model/<?php echo $season_path; ?>/resources"
                                style="text-decoration: none; color: white;">first ML</a></u>
                    </li>
                </div>
                <div class="rtext">
                    <li>If you have a <b class="bc">Medium / Very Good </b> quality camera download the <u><a
                                href="/model/<?php echo $season_path; ?>/resources"
                                style="text-decoration: none; color: white;">second
                                ML</a></u></li>
                </div>
                <div class="rtext">
                    <li><u>Make sure you download the model with <b class="bc">[Python Testing]</b> for your camera
                            quality
                            [It matters a lot]</u></li>
                </div>

                <div class="stext">8. <b class="bc">Calibrate The Camera</b></div>
                <div class="rtext">
                    <li>Access <u><a href="/model/<?php echo $season_path; ?>/cameracalib"
                                style="text-decoration: none; color: white">Camera
                                Calibration</a></u>, and then return after you have finished calibrating the camera.
                    </li>
                </div>

                <div class="stext">9.🎉If you have <b class="bc">successfully</b> completed all steps, you can proceed
                    to <b class="bc">Python Code For Detection</b> to test the OpenML model 🎉 </div>
                <div class="rtext">
                    <li>Access <u><a href="/model/<?php echo $season_path; ?>/pythonml"
                                style="text-decoration: none; color: white;">Python Code
                                For Detection</a></u></li>
                    </li>
                </div>

                <div class="stext">10.<b class="bc"> Additional Notes</b></div>
                <div class="rtext">
                    <li>The code uses math and cv2 modules for geometric calculations and camera operations. These
                        are included in the dependencies mentioned above.</li>
                </div>
                <div class="rtext">
                    <li>If you encounter CUDA-related errors, ensure you have compatible GPU drivers and PyTorch/CUDA
                        is installed (Ultralytics YOLO usually handles this automatically).</li>
                </div>
                <div class="rtext">
                    <li>Adjust the fov_degrees, first_angle and y values based on your camera calibration.</li>
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
</body>

</html>