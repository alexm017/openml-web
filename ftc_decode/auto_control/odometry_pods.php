<?php
$pageRecord = 'decode-odometry-pods';
$pageTitleEn = 'Odometry Pods and Localization Strategy';
$pageTitleRo = 'Odometry Pods si Strategie de Localizare';
$activePage = 'odometry';

$contentEn = <<<'HTML'
<div class="ftext">This page combines odometry constants and localization patterns from Road Runner and Pedro Pathing so teams can build stable localization with measurable repeatability.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Localization Implementations</div>
<div class="rtext"><li>Road Runner option: `RoadRunner/drive/opmode/TwoWheelTrackingLocalizer.java` + IMU heading.</li></div>
<div class="rtext"><li>Pedro option: `pedroPathing/Constants.java` with `TwoWheelConstants` and `FollowerBuilder`.</li></div>
<div class="rtext"><li>Diagnostics: `ChasisEngineering` reads `GoBildaPinpointDriver` raw encoders.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Two-Wheel Parameters (Road Runner)</div>
<div class="rtext">From `TwoWheelTrackingLocalizer`:</div>
<div class="rtext">
    <div class="codee-window">
<pre><code class="language-java">public static double TICKS_PER_REV = 2000;
public static double WHEEL_RADIUS = 0.944;
public static double PARALLEL_X = -6.1417;
public static double PARALLEL_Y = 2.9960;
public static double PERPENDICULAR_X = -6.1417;
public static double PERPENDICULAR_Y = -2.9763;
public static double X_MULTIPLIER = 0.3536;
public static double Y_MULTIPLIER = 0.3536;

parallelEncoder = new Encoder(hardwareMap.get(DcMotorEx.class, "Back_Left"));
perpendicularEncoder = new Encoder(hardwareMap.get(DcMotorEx.class, "Back_Right"));</code></pre>
    </div>
</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Pedro Localization Setup</div>
<div class="rtext">From `pedroPathing/Constants`:</div>
<div class="rtext">
    <div class="codee-window">
<pre><code class="language-java">public static TwoWheelConstants localizerConstants = new TwoWheelConstants()
    .forwardEncoder_HardwareMapName("Back_Left")
    .strafeEncoder_HardwareMapName("Back_Right")
    .forwardPodY(2.9960)
    .strafePodX(-6.1417)
    .forwardTicksToInches(-0.00112149)
    .strafeTicksToInches(0.00112149)
    .IMU_HardwareMapName("imu");</code></pre>
    </div>
</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Pod Options You Can Use</div>
<div class="rtext"><li>goBILDA dead-wheel pods: common FTC standard, easy replacement and predictable geometry.</li></div>
<div class="rtext"><li>Custom two-wheel pod mounts: compact for tight drivetrain packaging.</li></div>
<div class="rtext"><li>Pinpoint-backed setups: useful for independent encoder diagnostics and verification.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Control Hub IMU Note</div>
<div class="rtext">The Control Hub platform includes integrated IMU support (hardware name `imu`). For two-wheel localization this is critical, because heading quality directly impacts translational pose accuracy.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Two-Wheel vs Three-Wheel Practical Comparison</div>
<div class="rtext"><li>2-wheel + IMU: simpler, lighter, and easier to maintain.</li></div>
<div class="rtext"><li>3-wheel dead wheel: stronger pure odometry independence, more mechanical overhead.</li></div>
<div class="rtext"><li>Hybrid approach: two-wheel + IMU + AprilTag refresh gives strong competition resilience.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Calibration Workflow for Reproducible Results</div>
<div class="rtext"><li>Set encoder signs and hardware names correctly first.</li></div>
<div class="rtext"><li>Tune ticks-to-inch multipliers using measured runs.</li></div>
<div class="rtext"><li>Validate heading stability before tuning path follower gains.</li></div>
<div class="rtext"><li>Re-check after drivetrain rebuild, wheel change, or mount shift.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Validation Tests You Should Run</div>
<div class="rtext"><li>Straight 2m forward/backward repeatability.</li></div>
<div class="rtext"><li>Square path endpoint error (position + heading).</li></div>
<div class="rtext"><li>Burst-launch vibration test to detect pod contact issues.</li></div>
<div class="rtext"><li>Pre-match short stress loop with telemetry logging.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Next Step: Continue with Road Runner 0.5.6 trajectory architecture, then compare to Pedro Pathing migration.</div>
HTML;

$contentRo = '';

require __DIR__ . '/../includes/decode_doc_page.php';
