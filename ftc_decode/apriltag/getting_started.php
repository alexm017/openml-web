<?php
$pageRecord = 'decode-apriltag-start';
$pageTitleEn = 'AprilTag Detection - Getting Started';
$pageTitleRo = 'Detectie AprilTag - Ghid de Initializare';
$activePage = 'apriltag_start';

$contentEn = <<<'HTML'
<div class="ftext">This page documents the exact AprilTag foundation used in the robot code (`drive/ComputerVision/AprilTagIdentification.java`) so teams can reproduce the same localization and pattern-detection workflow.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Decode AprilTag Roles</div>
<div class="rtext"><li>Pattern detection: IDs 21 / 22 / 23 map game pattern states (GPP/PGP/PPG).</li></div>
<div class="rtext"><li>Localization correction: IDs 20 / 24 are used to estimate robot pose and bearing.</li></div>
<div class="rtext"><li>Auto + TeleOp support: same vision source feeds autonomous case selection and pose reset utilities.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Camera Mount and Pose Offsets (from code)</div>
<div class="rtext">The implementation uses camera pose offsets directly inside `AprilTagIdentification`:</div>
<div class="rtext"><li>Position offsets (inch): X=0.0, Y=6.0236, Z=9.8818</li></div>
<div class="rtext"><li>Orientation offsets (deg): Yaw=0.0, Pitch=-70.0, Roll=0.0</li></div>
<div class="rtext">These values must match your physical mount geometry for reliable robot pose output.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Real Initialization Snippet</div>
<div class="rtext">
    <div class="codee-window">
<pre><code class="language-java">aprilTagProcessor = new AprilTagProcessor.Builder()
    .setDrawTagID(true)
    .setDrawTagOutline(true)
    .setDrawAxes(true)
    .setDrawCubeProjection(true)
    .setOutputUnits(DistanceUnit.INCH, AngleUnit.DEGREES)
    .setCameraPose(cameraPosition, cameraOrientation)
    .build();

VisionPortal.Builder builder = new VisionPortal.Builder();
builder.setCamera(hwdmap.get(WebcamName.class, "AlphaBit_Webcam"));
builder.setCameraResolution(new Size(640, 480));
builder.addProcessor(aprilTagProcessor);
visionPortal = builder.build();</code></pre>
    </div>
</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Pattern and Pose Readout Logic</div>
<div class="rtext">Pattern and localization are filtered by tag IDs inside the same class:</div>
<div class="rtext">
    <div class="codee-window">
<pre><code class="language-java">public int getPatternId(){
    for (AprilTagDetection d : aprilTagProcessor.getDetections()) {
        if (d.metadata != null && (d.id == 21 || d.id == 22 || d.id == 23)) {
            detectionId = d.id;
        }
    }
    return detectionId;
}

public void getRobotPose(){
    locTagFound = false;
    for (AprilTagDetection d : aprilTagProcessor.getDetections()) {
        if (d.metadata != null && (d.id == 20 || d.id == 24)) {
            robotPose_x = d.robotPose.getPosition().x;
            robotPose_y = d.robotPose.getPosition().y;
            bearingAngle = d.ftcPose.bearing;
            locTagFound = true;
        }
    }
}</code></pre>
    </div>
</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Integration Points in Main Robot Flow</div>
<div class="rtext"><li>`ArtifactControl` calls `updateAprilTag()` and `updateArtifactPose()` each loop.</li></div>
<div class="rtext"><li>`AutonomousControl` checks pattern tags during `opModeInInit()` to lock pattern before match start.</li></div>
<div class="rtext"><li>Pose reset utilities use tag bearing + alliance offsets to re-anchor heading and pose.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Tuning and Reliability Checklist</div>
<div class="rtext"><li>Confirm camera name exactly matches `AlphaBit_Webcam` in hardware config.</li></div>
<div class="rtext"><li>Validate tag scale and mount angle before autonomous testing.</li></div>
<div class="rtext"><li>Reject low-confidence detections when robot is moving aggressively.</li></div>
<div class="rtext"><li>Prefer pose reset only when stationary and inside valid zone checks.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Optional Color-Blob Companion Pipeline</div>
<div class="rtext">The same class initializes `ColorBlobLocatorProcessor` for purple artifact detection with contour area and circularity filtering. This helps runtime artifact awareness alongside AprilTag field localization.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Next Step: Open AprilTag Implementation for a full integration template with autonomous and turret subsystems.</div>
HTML;

$contentRo = '';

require __DIR__ . '/../includes/decode_doc_page.php';
