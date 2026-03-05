<?php
$pageRecord = 'decode-apriltag-implementation';
$pageTitleEn = 'AprilTag Implementation - Code Samples';
$pageTitleRo = 'Implementare AprilTag - Exemple de Cod';
$activePage = 'apriltag_impl';

$contentEn = <<<'HTML'
<div class="ftext">This page provides practical code blocks adapted from the active robot code so teams can replicate the AprilTag workflow in TeleOp and Autonomous.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Vision Subsystem Class Skeleton</div>
<div class="rtext">Use a dedicated class (as in `AprilTagIdentification`) to isolate camera init, pattern readout, and robot-pose updates.</div>
<div class="rtext">
    <div class="codee-window">
<pre><code class="language-java">public class AprilTagIdentification {
    AprilTagProcessor aprilTagProcessor;
    VisionPortal visionPortal;
    public int detectionId = 0;
    public double robotPose_x = 0.0;
    public double robotPose_y = 0.0;
    public double bearingAngle = 0.0;
    public boolean locTagFound = false;

    public void init(HardwareMap hwdmap, MultipleTelemetry telemetrys) {
        // Build processor + portal here
    }
}</code></pre>
    </div>
</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Pattern Selection in Autonomous Init</div>
<div class="rtext">Reference autonomous uses AprilTags in `opModeInInit()` to lock pattern before start:</div>
<div class="rtext">
    <div class="codee-window">
<pre><code class="language-java">if (!nearBasket) {
    currentId = artifactControl.getCurrentTag();
    if (currentId != 0) {
        switch (currentId) {
            case 21: currentPattern = ObeliskPattern.GPP; break;
            case 22: currentPattern = ObeliskPattern.PGP; break;
            case 23: currentPattern = ObeliskPattern.PPG; break;
        }
        patternFound = true;
    }
}</code></pre>
    </div>
</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Pose Refresh Integration in ArtifactControl</div>
<div class="rtext">Tag pose is pulled and applied when safe (stationary + valid context). This is a key anti-drift pattern:</div>
<div class="rtext">
    <div class="codee-window">
<pre><code class="language-java">if (aprilTagIdentification.locTagFound) {
    calculatedRobotPose_X = aprilTagIdentification.robotPose_x;
    calculatedRobotPose_Y = aprilTagIdentification.robotPose_y;
    robotAngleAprilTag = aprilTagIdentification.bearingAngle;

    gyroscope.resetHeading();
    gyroscope.setAngleOffset(36.5 - robotAngleAprilTag);
    drive.setPose(new Pose(
        calculatedRobotPose_X,
        calculatedRobotPose_Y,
        Math.toRadians(126.5 - robotAngleAprilTag)
    ));
}</code></pre>
    </div>
</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Telemetry Block for Validation</div>
<div class="rtext">Expose enough telemetry to verify detections, pose, and bearing quality:</div>
<div class="rtext">
    <div class="codee-window">
<pre><code class="language-java">telemetrys.addData("[Artifact] AprilTag Robot Pose X", artifactControl.calculatedRobotPose_X);
telemetrys.addData("[Artifact] AprilTag Robot Pose Y", artifactControl.calculatedRobotPose_Y);
telemetrys.addData("[Artifact] AprilTag Robot Angle", artifactControl.robotAngleAprilTag);
telemetrys.addData("[->] Pattern", artifactControl.artifactPattern);
</code></pre>
    </div>
</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Recommended Implementation Steps</div>
<div class="rtext"><li>Create `AprilTagIdentification` with camera init + ID filters first.</li></div>
<div class="rtext"><li>Integrate `getPatternId()` into autonomous init for case/pattern logic.</li></div>
<div class="rtext"><li>Add `getRobotPose()` and test pose output while stationary.</li></div>
<div class="rtext"><li>Only then enable pose reset hooks inside your control subsystem.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Defensive Checks You Should Keep</div>
<div class="rtext"><li>Require `detection.metadata != null` before using tag data.</li></div>
<div class="rtext"><li>Limit pose reset to valid IDs (20/24) and safe robot state.</li></div>
<div class="rtext"><li>Keep manual fallback in case camera confidence drops mid-match.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Production Tips</div>
<div class="rtext"><li>Log pattern and pose snapshots in practice runs to catch bad tags.</li></div>
<div class="rtext"><li>Re-check camera pose offsets after every mount adjustment.</li></div>
<div class="rtext"><li>Benchmark with match lighting, not only lab lighting.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Next Step: Continue to Autonomous Control to connect vision decisions with full trajectory execution.</div>
HTML;

$contentRo = '';

require __DIR__ . '/../includes/decode_doc_page.php';
