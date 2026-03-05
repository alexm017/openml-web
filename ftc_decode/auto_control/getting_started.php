<?php
$pageRecord = 'decode-autonomous-start';
$pageTitleEn = 'Autonomous Control - Getting Started';
$pageTitleRo = 'Control Autonom - Ghid de Initializare';
$activePage = 'autonomous_start';

$contentEn = <<<'HTML'
<div class="ftext">This guide is based on the real competition code from the Road Runner 0.5.6 implementation and documents how autonomous is structured for repeatable artifact pickup and scoring.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Reference Autonomous Structure (Road Runner 0.5.6)</div>
<div class="rtext"><li>Main class: drive/Autonomous/AutonomousControl.java</li></div>
<div class="rtext"><li>Four routes: Red Basket, Blue Basket, Red Audience, Blue Audience.</li></div>
<div class="rtext"><li>Shared mechanism logic through ArtifactControl temporal markers.</li></div>
<div class="rtext"><li>Pattern-aware audience behavior using AprilTag IDs 21/22/23.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Real Autonomous Case Selection (from code)</div>
<div class="rtext">Before start, D-pad selects alliance/field side and auto case:</div>
<div class="rtext">
    <div class="codee-window">
<pre><code class="language-java">while(opModeInInit()) {
    if (gamepad1.dpad_left) { /* toggle blue/red */ }
    if (gamepad1.dpad_up)   { /* toggle basket/audience */ }

    if(!nearBasket){
        currentId = artifactControl.getCurrentTag();
        if(currentId == 21) currentPattern = ObeliskPattern.GPP;
        if(currentId == 22) currentPattern = ObeliskPattern.PGP;
        if(currentId == 23) currentPattern = ObeliskPattern.PPG;
    }
}

switch(autoCase){
    case 0: drive.setPoseEstimate(startPose_RedAudience);
            drive.followTrajectorySequenceAsync(trajectoryRedAudience); break;
    case 1: drive.setPoseEstimate(startPose_BlueAudience);
            drive.followTrajectorySequenceAsync(trajectoryBlueAudience); break;
    case 2: drive.setPoseEstimate(startPose_RedBasket);
            drive.followTrajectorySequenceAsync(trajectoryRedBasket); break;
    case 3: drive.setPoseEstimate(startPose_BlueBasket);
            drive.followTrajectorySequenceAsync(trajectoryBlueBasket); break;
}</code></pre>
    </div>
</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Why This Works in Matches</div>
<div class="rtext"><li>Route and mechanism are coupled by temporal markers, not ad-hoc delays.</li></div>
<div class="rtext"><li>Shooter and intake are synchronized with path phase changes.</li></div>
<div class="rtext"><li>Pattern detection can alter audience-side decisions without rewriting full route logic.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Reference-to-Advanced Transition</div>
<div class="rtext">Road Runner routes use Road Runner 0.5.6. In the Pedro version, pathing moved to Pedro follower architecture while keeping the same ArtifactControl principles (auto intake, burst scoring, pose correction logic).</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Required Subsystems</div>
<div class="rtext"><li>Localization: odometry + IMU baseline.</li></div>
<div class="rtext"><li>Vision: AprilTag for pattern + optional pose refresh.</li></div>
<div class="rtext"><li>Mechanism control: turret, flywheel, intake, pusher sequencing.</li></div>
<div class="rtext"><li>Safety: geofence + stationary checks for launch windows.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Competition Tuning Order</div>
<div class="rtext"><li>Tune localization first, then path accuracy, then mechanism timing.</li></div>
<div class="rtext"><li>Validate each route independently before multi-cycle routines.</li></div>
<div class="rtext"><li>Always keep a reduced fallback autonomous profile.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">What to Reproduce for Similar Outcomes</div>
<div class="rtext"><li>Keep route constants centralized (see `AutoStorage`).</li></div>
<div class="rtext"><li>Use explicit state gates (ready-to-fire, intake-confirmed, timeout reset).</li></div>
<div class="rtext"><li>Log pose, burst counters, and pattern state on each run.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Next Step: Continue with Odometry Pods for localization setup and hardware-specific recommendations.</div>
HTML;

$contentRo = '';

require __DIR__ . '/../includes/decode_doc_page.php';
