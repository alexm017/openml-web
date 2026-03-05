<?php
$pageRecord = 'decode-pedro-pathing';
$pageTitleEn = 'Pedro Pathing Implementation (Current Direction)';
$pageTitleRo = 'Implementare Pedro Pathing (Directia Curenta)';
$activePage = 'pedro';

$contentEn = <<<'HTML'
<div class="ftext">This page describes the Pedro Pathing implementation flow. The core configuration is in `pedroPathing/Constants.java` and runtime usage is in `ArtifactControl` through a `Follower` instance.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Core Entry Point</div>
<div class="rtext"><li>Path constants + follower builder: pedroPathing/Constants.java</li></div>
<div class="rtext"><li>Runtime integration: drive/Structure/ArtifactControl.java (`Follower drive;`)</li></div>
<div class="rtext"><li>Tuning suite: pedroPathing/Tuning.java (localization, path, and velocity diagnostics).</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Real Follower Configuration Snippet</div>
<div class="rtext">
    <div class="codee-window">
<pre><code class="language-java">public static TwoWheelConstants localizerConstants = new TwoWheelConstants()
    .forwardEncoder_HardwareMapName("Back_Left")
    .strafeEncoder_HardwareMapName("Back_Right")
    .forwardPodY(2.9960)
    .strafePodX(-6.1417)
    .forwardTicksToInches(-0.00112149)
    .strafeTicksToInches(0.00112149)
    .IMU_HardwareMapName("imu");

public static Follower createFollower(HardwareMap hardwareMap) {
    return new FollowerBuilder(followerConstants, hardwareMap)
        .pathConstraints(pathConstraints)
        .mecanumDrivetrain(driveConstants)
        .twoWheelLocalizer(localizerConstants)
        .build();
}</code></pre>
    </div>
</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">ArtifactControl Migration Pattern</div>
<div class="rtext">In the Pedro version, Road Runner pose calls are replaced with Pedro equivalents:</div>
<div class="rtext"><li>Road Runner equivalent: `drive.setPoseEstimate(...)`</li></div>
<div class="rtext"><li>Pedro calls: `drive.setStartingPose(...)` for init and `drive.setPose(...)` for runtime resets</li></div>
<div class="rtext">
    <div class="codee-window">
<pre><code class="language-java">drive = Constants.createFollower(hwdmap);

switch(VarStorage.autonomous_case){
  case 0: drive.setStartingPose(endPose_RedAudience); break;
  case 1: drive.setStartingPose(endPose_BlueAudience); break;
}

drive.update();
</code></pre>
    </div>
</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Why This Transition Works</div>
<div class="rtext"><li>Keeps proven ArtifactControl mechanics and burst logic intact.</li></div>
<div class="rtext"><li>Moves only follower/localizer layer to Pedro constructs.</li></div>
<div class="rtext"><li>Preserves alliance pose reset and AprilTag correction workflows.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Tuning Workflow (Recommended)</div>
<div class="rtext"><li>Run Pedro localization tests first (Tuning OpModes).</li></div>
<div class="rtext"><li>Validate straight and strafe multipliers under real battery voltage.</li></div>
<div class="rtext"><li>Tune path constraints for control quality before speed optimization.</li></div>
<div class="rtext"><li>Then reconnect mechanism timing and scoring gates.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Engineering Checklist for Match Use</div>
<div class="rtext"><li>Verify motor names/directions exactly match `driveConstants` mapping.</li></div>
<div class="rtext"><li>Re-check IMU orientation values after hub remount.</li></div>
<div class="rtext"><li>Ensure follower pose is initialized before first mechanism action.</li></div>
<div class="rtext"><li>Keep IMU-only fallback aiming path available if vision degrades.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Practical Outcome</div>
<div class="rtext">This migration preserves what already worked (pickup + burst mechanism routines) while modernizing the path-following backend for performance-focused development and further scaling.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Next Step: Continue with Auto Aiming - Getting Started to connect pathing and scoring alignment.</div>
HTML;

$contentRo = '';

require __DIR__ . '/../includes/decode_doc_page.php';
