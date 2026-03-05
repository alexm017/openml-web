<?php
$pageRecord = 'decode-rr-056';
$pageTitleEn = 'Road Runner 0.5.6 Implementation';
$pageTitleRo = 'Implementare Road Runner 0.5.6';
$activePage = 'rr056';

$contentEn = <<<'HTML'
<div class="ftext">Road Runner 0.5.6 remains a proven trajectory-sequence approach for FTC autonomous routines that need reliable timing and repeatable cycle control.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Where the Real Implementation Lives</div>
<div class="rtext"><li>Main autonomous: drive/Autonomous/AutonomousControl.java</li></div>
<div class="rtext"><li>Drive layer: RoadRunner/drive/SampleMecanumDrive.java</li></div>
<div class="rtext"><li>Path definitions: `trajectorySequenceBuilder(...)` with temporal markers.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Real Trajectory Pattern (from AutonomousControl)</div>
<div class="rtext">The routines combine trajectory segments and mechanism markers in one sequence:</div>
<div class="rtext">
    <div class="codee-window">
<pre><code class="language-java">trajectoryRedBasket = drive.trajectorySequenceBuilder(startPose_RedBasket)
    .lineToLinearHeading(new Pose2d(RB_mainShooting_X, RB_mainShooting_Y, Math.toRadians(90)))
    .addTemporalMarker(() -> artifactControl.setAutonomousShooter(...))
    .addTemporalMarker(() -> artifactControl.setAutonomousThrowFlags())
    .waitSeconds(3.65)
    .addTemporalMarker(() -> artifactControl.setAutonomousResetFlags())
    .lineTo(new Vector2d(RB_startPickupMiddlePattern_X, RB_startPickupMiddlePattern_Y))
    .addTemporalMarker(() -> artifactControl.getArtifacts(false))
    .lineTo(new Vector2d(RB_getMiddlePattern_X, RB_getMiddlePattern_Y))
    .build();</code></pre>
    </div>
</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Why Temporal Markers Matter</div>
<div class="rtext"><li>They align intake, turret, and flywheel events to movement progress.</li></div>
<div class="rtext"><li>They reduce manual timing drift compared to timer-only orchestration.</li></div>
<div class="rtext"><li>They keep scoring cycle logic reproducible across runs.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Pose Initialization Pattern</div>
<div class="rtext">Each selected case sets start pose before asynchronous follow:</div>
<div class="rtext">
    <div class="codee-window">
<pre><code class="language-java">drive.setPoseEstimate(startPose_RedBasket);
drive.followTrajectorySequenceAsync(trajectoryRedBasket);

while(opModeIsActive()) {
    drive.update();
    // mechanism control + telemetry
}</code></pre>
    </div>
</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Localization Dependency</div>
<div class="rtext">Road Runner path quality depends on odometry + IMU stability. Two-wheel localization and IMU heading should be tuned together before route-level timing changes.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Tuning Sequence for Similar Results</div>
<div class="rtext"><li>Calibrate odometry multipliers and IMU orientation first.</li></div>
<div class="rtext"><li>Tune drivetrain velocity behavior (feedforward/PID profile).</li></div>
<div class="rtext"><li>Only then tune temporal marker timing for burst/pickup actions.</li></div>
<div class="rtext"><li>Finalize with full-cycle stress runs, not isolated path tests.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Production Checklist</div>
<div class="rtext"><li>Verify `AutoStorage` constants against field-side measurements.</li></div>
<div class="rtext"><li>Revalidate all start poses after drivetrain servicing.</li></div>
<div class="rtext"><li>Keep one conservative backup route for match-day recovery.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Migration Context</div>
<div class="rtext">This RR 0.5.6 flow is a strong baseline. Pedro Pathing can be introduced while preserving subsystem behavior and targeting logic.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Next Step: Open Pedro Pathing Implementation to see how the follower and localizer are configured for migration.</div>
HTML;

$contentRo = '';

require __DIR__ . '/../includes/decode_doc_page.php';
