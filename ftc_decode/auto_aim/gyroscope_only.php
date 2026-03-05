<?php
$pageRecord = 'decode-autoaim-imu';
$pageTitleEn = 'Auto Aim with IMU Only';
$pageTitleRo = 'Auto Aim cu Doar IMU';
$activePage = 'imu_only';

$contentEn = <<<'HTML'
<div class="ftext">IMU-only aiming is the deterministic fallback path used when camera confidence is low. In this implementation it relies on heading from `GyroscopeBHIMU` and pose from localizer, then computes basket-relative turret commands.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Real Code Flow</div>
<div class="rtext"><li>`headingAngle = gyroscope.getHeading();`</li></div>
<div class="rtext"><li>Pose X/Y comes from localizer (`drive.getPoseEstimate()` in the Road Runner flow, `drive.getPose()` in the Pedro flow).</li></div>
<div class="rtext"><li>`dynamicTargetAngle()` + `getBasketDirection()` compute turn direction and magnitude.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Core Direction Logic (from ArtifactControl)</div>
<div class="rtext">
    <div class="codee-window">
<pre><code class="language-java">double calculatedAngle = Math.abs(Math.toDegrees(
    Math.atan2(positive_x_position, positive_y_position)
));

if (isRedAlliance) targetAngle = calculatedAngle;
else               targetAngle = 360 - calculatedAngle;

if (targetAngle - headingAngle > 0) {
    basketAngle = targetAngle - headingAngle;
    rotateToLeft = basketAngle < 180;
} else {
    basketAngle = 360 - Math.abs(headingAngle - targetAngle);
    rotateToLeft = basketAngle < 180;
}
if (basketAngle >= 180) basketAngle = 360 - basketAngle;</code></pre>
    </div>
</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Why IMU-Only Is Important</div>
<div class="rtext"><li>Works even with temporary camera blindness or heavy motion blur.</li></div>
<div class="rtext"><li>Provides stable heading continuity during aggressive robot movement.</li></div>
<div class="rtext"><li>Gives a predictable fallback for driver-first safety strategy.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Integration with Turret and Flywheel</div>
<div class="rtext"><li>`getTurretPosition()` converts target angle into servo command.</li></div>
<div class="rtext"><li>`getTurretAngle()` and `getFlyWheelPower()` apply distance compensation.</li></div>
<div class="rtext"><li>`updateShooter()` applies deadzone-filtered servo updates.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Required Calibration</div>
<div class="rtext"><li>IMU orientation and angle offset calibration (alliance-aware).</li></div>
<div class="rtext"><li>Field anchor coordinates for basket targets.</li></div>
<div class="rtext"><li>Servo center offsets for left/right turret mechanism.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Launch Safety Gates</div>
<div class="rtext"><li>Only fire in allowed region (`areaOfThrowing`).</li></div>
<div class="rtext"><li>Require burst subsystem readiness and timing checks.</li></div>
<div class="rtext"><li>Allow immediate stop/reset via operator controls.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Practical Strength</div>
<div class="rtext">IMU-only mode is usually the most repeatable backup in finals when camera conditions are unstable. Keep this mode tuned even if fusion is your primary strategy.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Next Step: Compare with Webcam Only and then enable IMU + Webcam Fusion.</div>
HTML;

$contentRo = '';

require __DIR__ . '/../includes/decode_doc_page.php';
