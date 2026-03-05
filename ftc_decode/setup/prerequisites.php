<?php
$pageRecord = 'decode-prerequisites';
$pageTitleEn = 'Getting Started - Competition Setup and First Validation';
$pageTitleRo = 'Ghid de Initializare - Setup Competitional';
$activePage = 'prerequisites';

$contentEn = <<<'HTML'
<div class="ftext">This setup guide is based on a proven FTC implementation flow. Follow it in order to get stable driving, autonomous artifact handling, and repeatable scoring behavior.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Prepare the Road Runner and Pedro Versions</div>
<div class="rtext"><li>Keep one version with Road Runner 0.5.6 trajectories and one version with Pedro Pathing.</li></div>
<div class="rtext"><li>Use the Road Runner version as a baseline for repeatability and timing checks.</li></div>
<div class="rtext"><li>Use the Pedro version when validating migration and long-term pathing direction.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Hardware Map Names Must Match Code Exactly</div>
<div class="rtext">These names are not optional. Any mismatch breaks init or localization:</div>
<div class="rtext">
    <div class="codee-window">
<pre><code class="language-java">// Drive + localization
"Front_Right", "Back_Right", "Back_Left", "Front_Left"
"imu", "Back_Left", "Back_Right"

// Vision
"AlphaBit_Webcam"

// Mechanisms
"Intake_LeftMotor", "Intake_RightMotor"
"Outtake_LeftMotor", "Outtake_RightMotor"
"LeftTurret", "RightTurret", "AngleTurret"
"BlockArtifact", "PushLastArtifact"
"leftDistanceSensor", "rightDistanceSensor", "colorSensor"</code></pre>
    </div>
</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Verify TeleOp Init Sequence</div>
<div class="rtext">Before start, the op mode sets fail-safe case selection and initializes robot pose and yaw:</div>
<div class="rtext">
    <div class="codee-window">
<pre><code class="language-java">while(opModeInInit()) {
    if(gamepad1.dpad_left)  failSafeCase = failSafeCase + 1;
    if(gamepad1.dpad_up)    failSafeCase = failSafeCase + 2;
    if(gamepad1.dpad_right) VarStorage.autonomous_case = failSafeCase;
}

waitForStart();
artifactControl.initServo();
artifactControl.resetYaw();
artifactControl.initRobotPose();</code></pre>
    </div>
</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Run Engineering Smoke Tests First</div>
<div class="rtext"><li>Run `ChasisEngineering` to verify drivetrain, motor test hooks, and Pinpoint encoder readout.</li></div>
<div class="rtext"><li>Run `TeleOp_Decode` and confirm telemetry updates for turret, pose, flywheel, sensors, and pattern.</li></div>
<div class="rtext"><li>If you use Road Runner 0.5.6, run `AutonomousControl` for full route validation (4 cases).</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Validate Vision Baseline</div>
<div class="rtext"><li>AprilTag pattern IDs must be recognized as 21/22/23.</li></div>
<div class="rtext"><li>Localization IDs 20/24 must report X/Y and bearing through `getRobotPose()`.</li></div>
<div class="rtext"><li>Blob filtering should show stable output with contour area 50-20000 and circularity 0.6-1.0.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Validate Localization Baseline</div>
<div class="rtext"><li>Road Runner two-wheel constants: `PARALLEL_X=-6.1417`, `PARALLEL_Y=2.9960`, multipliers `0.3536`.</li></div>
<div class="rtext"><li>Pedro constants: `forwardTicksToInches=-0.00112149`, `strafeTicksToInches=0.00112149`.</li></div>
<div class="rtext"><li>Run straight-line and square repeatability tests before any scoring-cycle tuning.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Tune In This Order (Do Not Skip)</div>
<div class="rtext"><li>Localization and IMU orientation.</li></div>
<div class="rtext"><li>Drivetrain/follower path stability.</li></div>
<div class="rtext"><li>Turret angle/flywheel compensation.</li></div>
<div class="rtext"><li>Burst timing and auto-intake thresholds.</li></div>
<div class="rtext"><li>AprilTag pose reset gates and match safety checks.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">First Acceptance Criteria</div>
<div class="rtext"><li>Robot finishes one complete cycle: intake -> align -> burst score -> reset.</li></div>
<div class="rtext"><li>No servo jitter under vibration due to deadzone logic.</li></div>
<div class="rtext"><li>Pose remains usable across repeated runs without uncontrolled drift.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Next Step: Continue to Resources for the full file map and tuning checkpoints.</div>
HTML;

$contentRo = '';

require __DIR__ . '/../includes/decode_doc_page.php';
