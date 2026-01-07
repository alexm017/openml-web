<?php
session_start();
$record_file = fopen("/var/www/html/record_index.txt", "a");
$txt = "andro\n";
$txtt = "andro";
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
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.6.0/styles/atom-one-dark.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="/assets/images/alphabit.ico" />
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
            <div class="title">Android Studio Implementation</div>
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
                    <div class="stext"><b>Pentru Modelul Machine Learning Cameră Calitate Scăzută (low.tflite) (Va fi
                            adăugat suport
                            pentru high.tflite astăzi)</b></div>
                    <div class="code-window">
                        <pre><code class="language-java" >package org.firstinspires.ftc.teamcode.drive.opmodes;

                            import android.content.res.AssetFileDescriptor;
                            import android.content.res.AssetManager;
                            import com.qualcomm.robotcore.eventloop.opmode.LinearOpMode;
                            import com.qualcomm.robotcore.eventloop.opmode.TeleOp;
                            import org.firstinspires.ftc.robotcore.external.hardware.camera.WebcamName;
                            import org.firstinspires.ftc.robotcore.internal.camera.calibration.CameraCalibration;
                            import org.firstinspires.ftc.vision.VisionPortal;
                            import org.firstinspires.ftc.vision.VisionProcessor;
                            import org.opencv.core.Core;
                            import org.opencv.core.CvType;
                            import org.opencv.core.Mat;
                            import org.opencv.imgproc.Imgproc;
                            import org.tensorflow.lite.Interpreter;

                            import android.graphics.Canvas;
                            import android.util.Size;
                            import java.io.FileInputStream;
                            import java.io.IOException;
                            import java.nio.ByteBuffer;
                            import java.nio.ByteOrder;
                            import java.nio.channels.FileChannel;
                            import java.util.ArrayList;
                            import java.util.List;

                            @TeleOp(name = "AI Vision", group = "AITesting")
                            public class AIVision extends LinearOpMode {

                                // Configuration
                                private static final String MODEL_FILE = "best_float32.tflite";
                                private static final int MODEL_SIZE = 320;
                                private static final float CONF_THRESHOLD = 0.6f;
                                private static final float IOU_THRESHOLD = 0.5f;

                                private VisionPortal visionPortal;

                                @Override
                                public void runOpMode() {
                                    YoloProcessor frameProcessor = new YoloProcessor(hardwareMap.appContext.getAssets());

                                    visionPortal = new VisionPortal.Builder()
                                            .setCamera(hardwareMap.get(WebcamName.class, "AICam"))
                                            .setStreamFormat(VisionPortal.StreamFormat.YUY2)
                                            .setCameraResolution(new Size(320, 240))
                                            .addProcessor(frameProcessor)
                                            .build();

                                    waitForStart();

                                    try {
                                        while (opModeIsActive()) {
                                            List<Detection> detections = frameProcessor.getLatestDetections();
                                            telemetry.addData("Detections", detections.size());
                                            int bestConfidence = -1;
                                            double confCounter=0;
                                            for(int i=0; i<detections.size(); i++){
                                                Detection d = detections.get(i);
                                                if(confCounter < (d.confidence * 100)){
                                                    confCounter = (d.confidence * 100);
                                                    bestConfidence = i;
                                                }
                                            }
                                            if(detections.size() > 0){
                                                Detection d = detections.get(bestConfidence);
                                                double angle = Math.toDegrees(Math.atan(d.height/d.width));
                                                double x = 6.4131;
                                                double y = 0.7223;
                                                double final_angle = (angle - x)/y;
                                                telemetry.addData(String.format("Obj %d", bestConfidence),
                                                        "%s %.1f%% | W: %.1f H: %.1f Angle: %.1f",
                                                        d.className(),
                                                        d.confidence * 100,
                                                        d.width,
                                                        d.height,
                                                        final_angle);
                                            }

                                            //!!! This one is to show all the detections. The one from above is to get only the detection with the highest confidence out of all.
                                            /*for (int i = 0; i < detections.size(); i++) {
                                                Detection d = detections.get(i);
                                                telemetry.addData(String.format("Obj %d", i),
                                                        "%s %.1f%% | W: %.1f H: %.1f",
                                                        d.className(),
                                                        d.confidence * 100,
                                                        d.width,
                                                        d.height);
                                            }*/
                                            telemetry.update();
                                        }
                                    } finally {
                                        visionPortal.close();
                                        frameProcessor.close();
                                    }
                                }

                                static class YoloProcessor implements VisionProcessor {
                                    private final Interpreter tflite;
                                    private final Object syncLock = new Object();
                                    private List<Detection> detections = new ArrayList<>();

                                    // Modified output buffer for [1,7,2100] shape
                                    private final float[][][] outputBuffer = new float[1][7][2100];  // CHANGED HERE
                                    private final ByteBuffer inputBuffer;
                                    private final Mat resizedMat = new Mat();
                                    private final Mat rgbMat = new Mat();

                                    public YoloProcessor(AssetManager assets) {
                                        try {
                                            Interpreter.Options options = new Interpreter.Options();
                                            options.setNumThreads(4);
                                            options.setUseXNNPACK(true);
                                            tflite = new Interpreter(loadModelFile(assets), options);

                                            inputBuffer = ByteBuffer.allocateDirect(MODEL_SIZE * MODEL_SIZE * 3 * 4);
                                            inputBuffer.order(ByteOrder.nativeOrder());
                                        } catch (IOException e) {
                                            throw new RuntimeException("Model loading failed", e);
                                        }
                                    }

                                    private ByteBuffer loadModelFile(AssetManager assets) throws IOException {
                                        try (AssetFileDescriptor afd = assets.openFd(MODEL_FILE)) {
                                            try (FileInputStream fis = new FileInputStream(afd.getFileDescriptor())) {
                                                FileChannel channel = fis.getChannel();
                                                return channel.map(FileChannel.MapMode.READ_ONLY, afd.getStartOffset(), afd.getDeclaredLength());
                                            }
                                        }
                                    }

                                    @Override
                                    public void init(int width, int height, CameraCalibration calibration) {}

                                    @Override
                                    public Object processFrame(Mat frame, long captureTimeNanos) {
            
                                        Mat rotated = new Mat();
                                        Core.rotate(frame, rotated, Core.ROTATE_90_CLOCKWISE);

                                        Imgproc.resize(rotated  , resizedMat, new org.opencv.core.Size(MODEL_SIZE, MODEL_SIZE));
                                        Imgproc.cvtColor(resizedMat, rgbMat, Imgproc.COLOR_BGR2RGB);
                                        rgbMat.convertTo(rgbMat, CvType.CV_32FC3, 1.0 / 255.0);

                                        float[] floatBuffer = new float[MODEL_SIZE * MODEL_SIZE * 3];
                                        rgbMat.get(0, 0, floatBuffer);
                                        inputBuffer.rewind();
                                        inputBuffer.asFloatBuffer().put(floatBuffer);

            
                                        tflite.run(inputBuffer, outputBuffer);

                                        List<Detection> newDetections = processOutput(frame.width(), frame.height());
                                        synchronized (syncLock) {
                                            detections = newDetections;
                                        }
                                        return null;
                                    }

                                    private List<Detection> processOutput(int frameWidth, int frameHeight) {
                                        List<Detection> rawDetections = new ArrayList<>();
                                        final float[] xCenterArray = outputBuffer[0][0];
                                        final float[] yCenterArray = outputBuffer[0][1];
                                        final float[] widthArray = outputBuffer[0][2];
                                        final float[] heightArray = outputBuffer[0][3];
                                        final float[] class0Array = outputBuffer[0][4];
                                        final float[] class1Array = outputBuffer[0][5];
                                        final float[] class2Array = outputBuffer[0][6];

            
                                        for (int j = 0; j < 2100; j++) {  
                                            float maxScore = Math.max(class0Array[j], Math.max(class1Array[j], class2Array[j]));
                                            if (maxScore < CONF_THRESHOLD) continue;

                                            int classId = 0;
                                            if (maxScore == class1Array[j]) classId = 1;
                                            else if (maxScore == class2Array[j]) classId = 2;

                                            rawDetections.add(new Detection(
                                                    xCenterArray[j] * frameWidth,
                                                    yCenterArray[j] * frameHeight,
                                                    widthArray[j] * frameWidth,
                                                    heightArray[j] * frameHeight,
                                                    maxScore,
                                                    classId
                                            ));
                                        }
                                        return nms(rawDetections);
                                    }

        
                                    private List<Detection> nms(List<Detection> detections) {
                                        List<Detection> results = new ArrayList<>(10);
                                        detections.sort((d1, d2) -> Float.compare(d2.confidence, d1.confidence));

                                        while (!detections.isEmpty()) {
                                            Detection best = detections.remove(0);
                                            results.add(best);
                                            detections.removeIf(d -> iou(best, d) > IOU_THRESHOLD);
                                        }
                                        return results;
                                    }

                                    private float iou(Detection a, Detection b) {
                                        float intersectionLeft = Math.max(a.x1, b.x1);
                                        float intersectionTop = Math.max(a.y1, b.y1);
                                        float intersectionRight = Math.min(a.x2, b.x2);
                                        float intersectionBottom = Math.min(a.y2, b.y2);

                                        if (intersectionRight < intersectionLeft || intersectionBottom < intersectionTop)
                                            return 0.0f;

                                        float intersectionArea = (intersectionRight - intersectionLeft) * (intersectionBottom - intersectionTop);
                                        float areaA = a.width * a.height;
                                        float areaB = b.width * b.height;

                                        return intersectionArea / (areaA + areaB - intersectionArea);
                                    }

                                    @Override
                                    public void onDrawFrame(Canvas canvas, int width, int height, float scale, float density, Object tag) {}

                                    public List<Detection> getLatestDetections() {
                                        synchronized (syncLock) {
                                            return new ArrayList<>(detections);
                                        }
                                    }

                                    public void close() {
                                        tflite.close();
                                        resizedMat.release();
                                        rgbMat.release();
                                    }
                                }

                                static class Detection {
                                    final float x1, y1, x2, y2;
                                    final float confidence;
                                    final int classId;
                                    public final float width;
                                    public final float height;

                                    public Detection(float cx, float cy, float w, float h, float conf, int cls) {
                                        width = w;
                                        height = h;
                                        x1 = cx - width/2;
                                        y1 = cy - height/2;
                                        x2 = cx + width/2;
                                        y2 = cy + height/2;
                                        confidence = conf;
                                        classId = cls;
                                    }

                                    String className() {
                                        switch (classId) {
                                            case 0: return "Yellow";
                                            case 1: return "Blue";
                                            case 2: return "Red";
                                            default: return "Unknown";
                                        }
                                    }
                                }
                            }
                                                </code></pre>
                    </div>
                    <div class="stext">Acest cod implementează un sistem de viziune bazat pe AI pentru un robot FTC,
                        folosind un model YOLOv8 rulat pe un TensorFlow Lite (TFLite) interpreter. Acesta utilizează o
                        cameră pentru a detecta și clasifica obiecte, oferind informații despre poziția și dimensiunile
                        fiecărui obiect detectat.</div>
                    <div class="stext">
                        <b>Fluxul de funcționare:</b>
                    </div>
                    <div class="stext">
                        <b>Inițializarea camerei și a modelului AI</b>
                    </div>
                    <div class="stext">
                        <b>1. Inițializarea camerei și a modelului AI</b>
                    </div>
                    <div class="rtext">
                        <li>Se creează un obiect VisionPortal care inițializează camera „AICam”.</li>
                    </div>
                    <div class="rtext">
                        <li>Se configurează rezoluția camerei la 320x240.</li>
                    </div>
                    <div class="rtext">
                        <li>Se atașează un procesor personalizat (YoloProcessor) care se ocupă de inferența AI.</li>
                    </div>
                    <div class="stext">
                        <b>2. Preprocesarea imaginii</b>
                    </div>
                    <div class="rtext">
                        <li>Se rotește imaginea la 90° pentru aliniere.</li>
                    </div>
                    <div class="rtext">
                        <li>Se redimensionează la 320x320, dimensiunea cerută de model.</li>
                    </div>
                    <div class="rtext">
                        <li>Se convertește imaginea la format RGB și se normalizează valorile pixelilor la intervalul [0,1].
                        </li>
                    </div>
                    <div class="rtext">
                        <li>Se copiază datele într-un buffer care va fi folosit pentru inferență.</li>
                    </div>
                    <div class="stext">
                        <b>3. Inferența AI</b>
                    </div>
                    <div class="rtext">
                        <li>Modelul YOLOv8 este rulat pe imaginea preprocesată.</li>
                    </div>
                    <div class="rtext">
                        <li>Se obține un output cu coordonatele și scorurile de încredere pentru mai multe obiecte
                            detectate.</li>
                    </div>
                    <div class="rtext">
                        <li>Se filtrează rezultatele astfel încât să se păstreze doar obiectele cu un scor de încredere
                            peste 60%.</li>
                    </div>
                    <div class="stext">
                        <b>4. Post-procesare și eliminarea suprapunerilor</b>
                    </div>
                    <div class="rtext">
                        <li>Se aplică un algoritm Non-Maximum Suppression (NMS) pentru a elimina detectările duplicate sau
                            redundante.</li>
                    </div>
                    <div class="rtext">
                        <li>Se determină cel mai bun obiect detectat, adică cel cu cea mai mare încredere.</li>
                    </div>
                    <div class="rtext">
                        <li>Se calculează unghiul obiectului pe baza raportului dintre înălțime și lățime cu arctg.</li>
                    </div>
                    <div class="rtext">
                        <li>Se face o ajustare liniară a unghiului folosind niște constante empirice.</li>
                    </div>
                    <div class="stext">
                        <b>5. Afișarea rezultatelor</b>
                    </div>
                    <div class="rtext">
                        <li>Se trimit datele despre obiectele detectate către telemetry pentru a putea fi vizualizate pe
                            Driver Station.</li>
                    </div>
                    <div class="rtext">
                        <li>Se închide VisionPortal și resursele alocate atunci când opmode-ul este oprit.</li>
                    </div>
                    <br></br>
                    <div class="stext">
                        <b>Aspecte importante:</b>
                    </div>
                    <div class="rtext">
                        <li>Modelul YOLOv8 folosește o ieșire de dimensiune [1, 7, 2100], ceea ce înseamnă că returnează
                            până la 2100 de detectări per cadru.</li>
                    </div>
                    <div class="rtext">
                        <li>Algoritmul Non-Maximum Suppression elimină detectările redundante pe baza unui prag de IoU de
                            50%.</li>
                    </div>
                    <div class="rtext">
                        <li>Clasele detectate sunt limitate la trei categorii: „Yellow”, „Blue” și „Red”.</li>
                    </div>
                    <div class="endLine"></div>
                    <div class="endD"><a href="https://discord.gg/ZB6vQ62KZT">Support -> Discord</a></div>
                    <div class="end"></div>
                <?php else: ?>
                    <div class="stext"><b>For Low Quality Camera Machine Learning Model (low.tflite) (Will be added support
                            for high.tflite today)</b></div>
                    <div class="code-window">
                        <pre><code class="language-java" >package org.firstinspires.ftc.teamcode.drive.opmodes;

                            import android.content.res.AssetFileDescriptor;
                            import android.content.res.AssetManager;
                            import com.qualcomm.robotcore.eventloop.opmode.LinearOpMode;
                            import com.qualcomm.robotcore.eventloop.opmode.TeleOp;
                            import org.firstinspires.ftc.robotcore.external.hardware.camera.WebcamName;
                            import org.firstinspires.ftc.robotcore.internal.camera.calibration.CameraCalibration;
                            import org.firstinspires.ftc.vision.VisionPortal;
                            import org.firstinspires.ftc.vision.VisionProcessor;
                            import org.opencv.core.Core;
                            import org.opencv.core.CvType;
                            import org.opencv.core.Mat;
                            import org.opencv.imgproc.Imgproc;
                            import org.tensorflow.lite.Interpreter;

                            import android.graphics.Canvas;
                            import android.util.Size;
                            import java.io.FileInputStream;
                            import java.io.IOException;
                            import java.nio.ByteBuffer;
                            import java.nio.ByteOrder;
                            import java.nio.channels.FileChannel;
                            import java.util.ArrayList;
                            import java.util.List;

                            @TeleOp(name = "AI Vision", group = "AITesting")
                            public class AIVision extends LinearOpMode {

                                // Configuration
                                private static final String MODEL_FILE = "best_float32.tflite";
                                private static final int MODEL_SIZE = 320;
                                private static final float CONF_THRESHOLD = 0.6f;
                                private static final float IOU_THRESHOLD = 0.5f;

                                private VisionPortal visionPortal;

                                @Override
                                public void runOpMode() {
                                    YoloProcessor frameProcessor = new YoloProcessor(hardwareMap.appContext.getAssets());

                                    visionPortal = new VisionPortal.Builder()
                                            .setCamera(hardwareMap.get(WebcamName.class, "AICam"))
                                            .setStreamFormat(VisionPortal.StreamFormat.YUY2)
                                            .setCameraResolution(new Size(320, 240))
                                            .addProcessor(frameProcessor)
                                            .build();

                                    waitForStart();

                                    try {
                                        while (opModeIsActive()) {
                                            List<Detection> detections = frameProcessor.getLatestDetections();
                                            telemetry.addData("Detections", detections.size());
                                            int bestConfidence = -1;
                                            double confCounter=0;
                                            for(int i=0; i<detections.size(); i++){
                                                Detection d = detections.get(i);
                                                if(confCounter < (d.confidence * 100)){
                                                    confCounter = (d.confidence * 100);
                                                    bestConfidence = i;
                                                }
                                            }
                                            if(detections.size() > 0){
                                                Detection d = detections.get(bestConfidence);
                                                double angle = Math.toDegrees(Math.atan(d.height/d.width));
                                                double x = 6.4131;
                                                double y = 0.7223;
                                                double final_angle = (angle - x)/y;
                                                telemetry.addData(String.format("Obj %d", bestConfidence),
                                                        "%s %.1f%% | W: %.1f H: %.1f Angle: %.1f",
                                                        d.className(),
                                                        d.confidence * 100,
                                                        d.width,
                                                        d.height,
                                                        final_angle);
                                            }

                                            //!!! This one is to show all the detections. The one from above is to get only the detection with the highest confidence out of all.
                                            /*for (int i = 0; i < detections.size(); i++) {
                                                Detection d = detections.get(i);
                                                telemetry.addData(String.format("Obj %d", i),
                                                        "%s %.1f%% | W: %.1f H: %.1f",
                                                        d.className(),
                                                        d.confidence * 100,
                                                        d.width,
                                                        d.height);
                                            }*/
                                            telemetry.update();
                                        }
                                    } finally {
                                        visionPortal.close();
                                        frameProcessor.close();
                                    }
                                }

                                static class YoloProcessor implements VisionProcessor {
                                    private final Interpreter tflite;
                                    private final Object syncLock = new Object();
                                    private List<Detection> detections = new ArrayList<>();

                                    // Modified output buffer for [1,7,2100] shape
                                    private final float[][][] outputBuffer = new float[1][7][2100];  // CHANGED HERE
                                    private final ByteBuffer inputBuffer;
                                    private final Mat resizedMat = new Mat();
                                    private final Mat rgbMat = new Mat();

                                    public YoloProcessor(AssetManager assets) {
                                        try {
                                            Interpreter.Options options = new Interpreter.Options();
                                            options.setNumThreads(4);
                                            options.setUseXNNPACK(true);
                                            tflite = new Interpreter(loadModelFile(assets), options);

                                            inputBuffer = ByteBuffer.allocateDirect(MODEL_SIZE * MODEL_SIZE * 3 * 4);
                                            inputBuffer.order(ByteOrder.nativeOrder());
                                        } catch (IOException e) {
                                            throw new RuntimeException("Model loading failed", e);
                                        }
                                    }

                                    private ByteBuffer loadModelFile(AssetManager assets) throws IOException {
                                        try (AssetFileDescriptor afd = assets.openFd(MODEL_FILE)) {
                                            try (FileInputStream fis = new FileInputStream(afd.getFileDescriptor())) {
                                                FileChannel channel = fis.getChannel();
                                                return channel.map(FileChannel.MapMode.READ_ONLY, afd.getStartOffset(), afd.getDeclaredLength());
                                            }
                                        }
                                    }

                                    @Override
                                    public void init(int width, int height, CameraCalibration calibration) {}

                                    @Override
                                    public Object processFrame(Mat frame, long captureTimeNanos) {
            
                                        Mat rotated = new Mat();
                                        Core.rotate(frame, rotated, Core.ROTATE_90_CLOCKWISE);

                                        Imgproc.resize(rotated  , resizedMat, new org.opencv.core.Size(MODEL_SIZE, MODEL_SIZE));
                                        Imgproc.cvtColor(resizedMat, rgbMat, Imgproc.COLOR_BGR2RGB);
                                        rgbMat.convertTo(rgbMat, CvType.CV_32FC3, 1.0 / 255.0);

                                        float[] floatBuffer = new float[MODEL_SIZE * MODEL_SIZE * 3];
                                        rgbMat.get(0, 0, floatBuffer);
                                        inputBuffer.rewind();
                                        inputBuffer.asFloatBuffer().put(floatBuffer);

            
                                        tflite.run(inputBuffer, outputBuffer);

                                        List<Detection> newDetections = processOutput(frame.width(), frame.height());
                                        synchronized (syncLock) {
                                            detections = newDetections;
                                        }
                                        return null;
                                    }

                                    private List<Detection> processOutput(int frameWidth, int frameHeight) {
                                        List<Detection> rawDetections = new ArrayList<>();
                                        final float[] xCenterArray = outputBuffer[0][0];
                                        final float[] yCenterArray = outputBuffer[0][1];
                                        final float[] widthArray = outputBuffer[0][2];
                                        final float[] heightArray = outputBuffer[0][3];
                                        final float[] class0Array = outputBuffer[0][4];
                                        final float[] class1Array = outputBuffer[0][5];
                                        final float[] class2Array = outputBuffer[0][6];

            
                                        for (int j = 0; j < 2100; j++) {  
                                            float maxScore = Math.max(class0Array[j], Math.max(class1Array[j], class2Array[j]));
                                            if (maxScore < CONF_THRESHOLD) continue;

                                            int classId = 0;
                                            if (maxScore == class1Array[j]) classId = 1;
                                            else if (maxScore == class2Array[j]) classId = 2;

                                            rawDetections.add(new Detection(
                                                    xCenterArray[j] * frameWidth,
                                                    yCenterArray[j] * frameHeight,
                                                    widthArray[j] * frameWidth,
                                                    heightArray[j] * frameHeight,
                                                    maxScore,
                                                    classId
                                            ));
                                        }
                                        return nms(rawDetections);
                                    }

        
                                    private List<Detection> nms(List<Detection> detections) {
                                        List<Detection> results = new ArrayList<>(10);
                                        detections.sort((d1, d2) -> Float.compare(d2.confidence, d1.confidence));

                                        while (!detections.isEmpty()) {
                                            Detection best = detections.remove(0);
                                            results.add(best);
                                            detections.removeIf(d -> iou(best, d) > IOU_THRESHOLD);
                                        }
                                        return results;
                                    }

                                    private float iou(Detection a, Detection b) {
                                        float intersectionLeft = Math.max(a.x1, b.x1);
                                        float intersectionTop = Math.max(a.y1, b.y1);
                                        float intersectionRight = Math.min(a.x2, b.x2);
                                        float intersectionBottom = Math.min(a.y2, b.y2);

                                        if (intersectionRight < intersectionLeft || intersectionBottom < intersectionTop)
                                            return 0.0f;

                                        float intersectionArea = (intersectionRight - intersectionLeft) * (intersectionBottom - intersectionTop);
                                        float areaA = a.width * a.height;
                                        float areaB = b.width * b.height;

                                        return intersectionArea / (areaA + areaB - intersectionArea);
                                    }

                                    @Override
                                    public void onDrawFrame(Canvas canvas, int width, int height, float scale, float density, Object tag) {}

                                    public List<Detection> getLatestDetections() {
                                        synchronized (syncLock) {
                                            return new ArrayList<>(detections);
                                        }
                                    }

                                    public void close() {
                                        tflite.close();
                                        resizedMat.release();
                                        rgbMat.release();
                                    }
                                }

                                static class Detection {
                                    final float x1, y1, x2, y2;
                                    final float confidence;
                                    final int classId;
                                    public final float width;
                                    public final float height;

                                    public Detection(float cx, float cy, float w, float h, float conf, int cls) {
                                        width = w;
                                        height = h;
                                        x1 = cx - width/2;
                                        y1 = cy - height/2;
                                        x2 = cx + width/2;
                                        y2 = cy + height/2;
                                        confidence = conf;
                                        classId = cls;
                                    }

                                    String className() {
                                        switch (classId) {
                                            case 0: return "Yellow";
                                            case 1: return "Blue";
                                            case 2: return "Red";
                                            default: return "Unknown";
                                        }
                                    }
                                }
                            }
                                                </code></pre>
                    </div>
                    <div class="stext">This code implements an AI-based vision system for an FTC robot,
                        using a YOLOv8 model run on a TensorFlow Lite (TFLite) interpreter. It uses a
                        camera to detect and classify objects, providing information about the position and dimensions
                        of each detected object.</div>
                    <div class="stext">
                        <b>Workflow:</b>
                    </div>
                    <div class="stext">
                        <b>Camera and AI model initialization</b>
                    </div>
                    <div class="stext">
                        <b>1. Camera and AI model initialization</b>
                    </div>
                    <div class="rtext">
                        <li>A VisionPortal object is created which initializes the 'AICam' camera.</li>
                    </div>
                    <div class="rtext">
                        <li>The camera resolution is configured to 320x240.</li>
                    </div>
                    <div class="rtext">
                        <li>A custom processor (YoloProcessor) is attached to handle AI inference.</li>
                    </div>
                    <div class="stext">
                        <b>2. Image preprocessing</b>
                    </div>
                    <div class="rtext">
                        <li>The image is rotated 90° for alignment.</li>
                    </div>
                    <div class="rtext">
                        <li>It is resized to 320x320, the size required by the model.</li>
                    </div>
                    <div class="rtext">
                        <li>The image is converted to RGB format and pixel values are normalized to the [0,1] range.
                        </li>
                    </div>
                    <div class="rtext">
                        <li>Data is copied into a buffer that will be used for inference.</li>
                    </div>
                    <div class="stext">
                        <b>3. AI Inference</b>
                    </div>
                    <div class="rtext">
                        <li>The YOLOv8 model is run on the preprocessed image.</li>
                    </div>
                    <div class="rtext">
                        <li>An output with coordinates and confidence scores for multiple detected objects is obtained.</li>
                    </div>
                    <div class="rtext">
                        <li>Results are filtered to keep only objects with a confidence score over 60%.</li>
                    </div>
                    <div class="stext">
                        <b>4. Post-processing and overlap removal</b>
                    </div>
                    <div class="rtext">
                        <li>A Non-Maximum Suppression (NMS) algorithm is applied to remove duplicate or redundant
                            detections.</li>
                    </div>
                    <div class="rtext">
                        <li>The best detected object is determined, i.e., the one with the highest confidence.</li>
                    </div>
                    <div class="rtext">
                        <li>The object angle is calculated based on the height-to-width ratio using arctan.</li>
                    </div>
                    <div class="rtext">
                        <li>A linear adjustment of the angle is made using some empirical constants.</li>
                    </div>
                    <div class="stext">
                        <b>5. Displaying results</b>
                    </div>
                    <div class="rtext">
                        <li>Data about detected objects is sent to telemetry to be viewed on the Driver Station.</li>
                    </div>
                    <div class="rtext">
                        <li>VisionPortal and allocated resources are closed when the opmode is stopped.</li>
                    </div>
                    <br></br>
                    <div class="stext">
                        <b>Important aspects:</b>
                    </div>
                    <div class="rtext">
                        <li>The YOLOv8 model uses an output size of [1, 7, 2100], which means it returns up to 2100
                            detections per frame.</li>
                    </div>
                    <div class="rtext">
                        <li>The Non-Maximum Suppression algorithm removes redundant detections based on an IoU threshold of
                            50%.</li>
                    </div>
                    <div class="rtext">
                        <li>Detected classes are limited to three categories: 'Yellow', 'Blue', and 'Red'.</li>
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
                <?php endif; ?>


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
                    <div class="sub-section">
                        <p style="color:#c67171;">Control Colectare cu OpenML</p>
                    </div>
                    <div class="sub-section">
                        <p style="color:#c67171;">Implementare ML Autonom</p>
                    </div>
                    <div class="sub-section">
                        <p style="color:#c67171;">Implementare ML TeleOp</p>
                    </div>
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
                    <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/sample_2d_math">Starter Guide</a></div>
                    <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/cameracalib">Camera Calibration</a>
                    </div>
                    <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/sample_2d_math">Python Detection
                            Testing</a></div>
                    <div class="sub-section">
                        <p style="color:#c67171;">Android Studio Implementation</p>
                    </div>

                    <div class="docsLine"></div>

                    <div class="setup">3D Sample Detection</div>
                    <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/sample_2d_math">Starter Guide</a></div>
                    <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/sample_2d_math">Camera Calibration</a>
                    </div>
                    <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/sample_2d_math">Python Detection
                            Testing</a></div>
                    <div class="sub-section"><a href="/model/<?php echo $season_path; ?>/sample_2d_math">Android Studio
                            Implementation</a></div>

                    <div class="docsLine"></div>
                <?php endif; ?>

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
                <div class="sub-section">
                    <p style="color:#c67171;">Android Studio Implementation</p>
                </div>
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
</body>

</html>