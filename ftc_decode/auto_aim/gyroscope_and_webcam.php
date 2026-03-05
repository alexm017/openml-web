<?php
$pageRecord = 'decode-autoaim-fusion';
$pageTitleEn = 'Auto Aim with IMU + Webcam Fusion';
$pageTitleRo = 'Auto Aim cu Fuziune IMU + Webcam';
$activePage = 'imu_webcam';

$contentEn = <<<'HTML'
<div class="ftext">Fusion mode combines inertial heading stability with camera-based correction. In this implementation, the practical fusion anchor is implemented through AprilTag-assisted pose reset and alliance-aware heading offset logic.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Fusion Components in Real Code</div>
<div class="rtext"><li>IMU source: `GyroscopeBHIMU` (`imu` hardware map).</li></div>
<div class="rtext"><li>Vision source: `AprilTagIdentification` (`getRobotPose`, `bearingAngle`, `locTagFound`).</li></div>
<div class="rtext"><li>Pose layer: Road Runner pose (Road Runner 0.5.6) or Pedro pose (Pedro Pathing).</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Real Fusion Reset Pattern (ArtifactControl)</div>
<div class="rtext">When stationary and valid, vision updates pose and IMU offset together:</div>
<div class="rtext">
    <div class="codee-window">
<pre><code class="language-java">if (allowedToShoot && !manualControl && isRobotStationary) {
    aprilTagIdentification.getRobotPose();
    if (aprilTagIdentification.locTagFound) {
        calculatedRobotPose_X = aprilTagIdentification.robotPose_x;
        calculatedRobotPose_Y = aprilTagIdentification.robotPose_y;
        robotAngleAprilTag = aprilTagIdentification.bearingAngle;

        gyroscope.resetHeading();
        if (isRedAlliance) {
            gyroscope.setAngleOffset(36.5 - robotAngleAprilTag);
            drive.setPose(new Pose(calculatedRobotPose_X, calculatedRobotPose_Y,
                    Math.toRadians(126.5 - robotAngleAprilTag)));
        } else {
            gyroscope.setAngleOffset(-36.5 - robotAngleAprilTag);
            drive.setPose(new Pose(calculatedRobotPose_X, calculatedRobotPose_Y,
                    Math.toRadians(-126.5 - robotAngleAprilTag)));
        }
    }
}</code></pre>
    </div>
</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Why This Is Effective</div>
<div class="rtext"><li>IMU keeps high-rate heading continuity during motion.</li></div>
<div class="rtext"><li>Vision provides absolute correction when trustworthy.</li></div>
<div class="rtext"><li>Pose and heading are corrected in one controlled operation.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Recommended Fusion Policy</div>
<div class="rtext"><li>Use IMU as baseline continuously.</li></div>
<div class="rtext"><li>Apply vision correction only in safe windows (stationary + valid tags).</li></div>
<div class="rtext"><li>Keep manual override and fail-safe reset available at all times.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Operational Safeguards</div>
<div class="rtext"><li>Do not run pose reset while robot is accelerating/rotating quickly.</li></div>
<div class="rtext"><li>Require valid tag metadata and allowed ID set before correction.</li></div>
<div class="rtext"><li>Log corrected pose and angle offsets for post-match diagnostics.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Validation Sequence</div>
<div class="rtext"><li>Validate IMU-only aiming first.</li></div>
<div class="rtext"><li>Validate vision pose output while robot is parked.</li></div>
<div class="rtext"><li>Enable fusion correction gates and test under full scoring cycle load.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Match-Day Recommendation</div>
<div class="rtext">Run fusion as primary mode, but keep IMU-only fallback one button away. This preserves scoring continuity if camera confidence drops mid-match.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Final Step: Re-run telemetry checks in TeleOp (`TeleOp_Decode`) before each event block.</div>
HTML;

$contentRo = '';

require __DIR__ . '/../includes/decode_doc_page.php';
