<?php
$pageRecord = 'decode-resources';
$pageTitleEn = 'Resources - File Map, References, and Setup Checkpoints';
$pageTitleRo = 'Resurse - Fisiere si Referinte';
$activePage = 'resources';

$contentEn = <<<'HTML'
<div class="ftext">This page indexes the exact classes and references behind the Decode documentation. Use it as your technical map when implementing, debugging, or onboarding new programmers.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Primary Robot Code Files (Read First)</div>
<div class="rtext"><li>TeleOp runtime: `drive/OpModes/TeleOp_Decode.java`</li></div>
<div class="rtext"><li>Core mechanism control: `drive/Structure/ArtifactControl.java`</li></div>
<div class="rtext"><li>Vision subsystem: `drive/ComputerVision/AprilTagIdentification.java`</li></div>
<div class="rtext"><li>Tunable constants: `drive/Skeletal_Structures/VarStorage.java`, `AutoStorage.java`</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Road Runner 0.5.6 Autonomous</div>
<div class="rtext"><li>Autonomous routes: `drive/Autonomous/AutonomousControl.java`</li></div>
<div class="rtext"><li>RR drive layer: `RoadRunner/drive/SampleMecanumDrive.java`</li></div>
<div class="rtext"><li>Two-wheel localizer: `RoadRunner/drive/opmode/TwoWheelTrackingLocalizer.java`</li></div>
<div class="rtext"><li>Implementation summary: `description.txt` (programming notes and subsystem behavior).</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Pedro Pathing</div>
<div class="rtext"><li>Follower + localizer constants: `pedroPathing/Constants.java`</li></div>
<div class="rtext"><li>Tuning support OpModes: `pedroPathing/Tuning.java`</li></div>
<div class="rtext"><li>Runtime pose usage in shooter subsystem: `ArtifactControl` (`drive.getPose()`, `drive.setPose(...)`).</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">External References Used for Architecture</div>
<div class="rtext"><li><a href="https://learnroadrunner.com/" target="_blank" rel="noopener" style="text-decoration:none; color:#ffffff;">learnroadrunner.com</a> for RR trajectory and localization concepts.</li></div>
<div class="rtext"><li>Pedro Pathing official documentation/repository for follower migration and tuning patterns.</li></div>
<div class="rtext"><li>FTC SDK vision docs for AprilTag and VisionPortal behavior.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Constants Worth Auditing Before Events</div>
<div class="rtext"><li>Localization: pod geometry, ticks-to-inch multipliers, IMU orientation.</li></div>
<div class="rtext"><li>Shooter: `targetFlyWheelSpeed`, min/max flywheel power clamps.</li></div>
<div class="rtext"><li>Turret: servo bounds, deadzones, basket angle model constants.</li></div>
<div class="rtext"><li>Automation: intake timings, timeout windows, distance thresholds, light threshold.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Example Diagnostic Telemetry Block</div>
<div class="rtext">Expose these values each run so tuning decisions are evidence-based:</div>
<div class="rtext">
    <div class="codee-window">
<pre><code class="language-java">telemetrys.addData("[Artifact] Basket distance", artifactControl.getBasketDistance(0,0,false,false));
telemetrys.addData("[Artifact] FlyWheel Power", artifactControl.getFlyWheelPower(0,0,false,false));
telemetrys.addData("[Artifact] AprilTag Robot Pose X", artifactControl.calculatedRobotPose_X);
telemetrys.addData("[Artifact] AprilTag Robot Pose Y", artifactControl.calculatedRobotPose_Y);
telemetrys.addData("[Artifact] Robot Stationary", artifactControl.isRobotStationary);
telemetrys.addData("[Artifact] Robot Auto Shoot toggle", artifactControl.robotAutoShootToggle);</code></pre>
    </div>
</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Quick Repository Search Commands</div>
<div class="rtext">Use these commands to find implementation points quickly:</div>
<div class="rtext">
    <div class="codee-window">
<pre><code class="language-bash">rg -n "getRobotPose|getPatternId|setCameraPose" robot_code
rg -n "trajectorySequenceBuilder|addTemporalMarker" robot_code
rg -n "createFollower|TwoWheelConstants|setPose\(" robot_code</code></pre>
    </div>
</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Limelight Benchmark Note</div>
<div class="rtext">The documentation keeps webcam and AprilTag implementation as baseline, and also notes a Limelight (Raspberry Pi based) comparison for high-level consistency testing.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Next Step: Continue to AprilTag Getting Started and implement the detection + pose section before integrating full autonomous cycles.</div>
HTML;

$contentRo = '';

require __DIR__ . '/../includes/decode_doc_page.php';
