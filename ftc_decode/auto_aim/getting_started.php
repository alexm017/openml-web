<?php
$pageRecord = 'decode-autoaim-start';
$pageTitleEn = 'Auto Aiming Turret - Getting Started';
$pageTitleRo = 'Turela Auto Aim - Ghid de Initializare';
$activePage = 'autoaim_start';

$contentEn = <<<'HTML'
<div class="ftext">Auto aim in this implementation is implemented in `ArtifactControl` and tuned for repeatable burst scoring. It combines geometric targeting, polynomial compensation, and strict gating before launch.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Main Auto-Aim Responsibilities in ArtifactControl</div>
<div class="rtext"><li>Estimate basket-relative direction from current pose + alliance side.</li></div>
<div class="rtext"><li>Compute horizontal turret servo target and vertical turret angle.</li></div>
<div class="rtext"><li>Compute flywheel power from distance model with clamp limits.</li></div>
<div class="rtext"><li>Apply deadzone filtering to avoid servo jitter.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Real Distance-Based Models (from code)</div>
<div class="rtext">The turret angle and flywheel power are polynomial-fitted and clamped:</div>
<div class="rtext">
    <div class="codee-window">
<pre><code class="language-java">angleTurretPosition = (0.0000207725 * (basketDistance * basketDistance))
                    - (0.00755001 * basketDistance)
                    + 0.865169;
angleTurretPosition = clamp(angleTurretPosition, 0.25, 0.75);

flyWheelPower = ((-3.15936e-7) * d * d * d)
              + (0.000074273 * d * d)
              - (0.00230794 * d)
              + 0.606381;
flyWheelPower = clamp(flyWheelPower, 0.6, 0.87);</code></pre>
    </div>
</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Horizontal + Vertical Servo Update Pattern</div>
<div class="rtext">Auto aim does not spam servo writes; it updates only when movement exceeds deadzone:</div>
<div class="rtext"><li>`horizontalTurretDeadzone` protects left/right turret servos.</li></div>
<div class="rtext"><li>`verticalTurretDeadzone` protects angle turret servo.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Launch Permission Logic</div>
<div class="rtext">Scoring is gated by area checks and robot state:</div>
<div class="rtext"><li>`areaOfThrowing()` determines legal/valid shooting region.</li></div>
<div class="rtext"><li>Haptic rumble feedback indicates enter/exit allowed zone.</li></div>
<div class="rtext"><li>Auto-shoot options require stationary/valid conditions.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Burst Integration</div>
<div class="rtext">Auto aim is coupled with burst logic (`burstShootingArtifacts`) that manages intake windows, pusher state, timeout recovery, and counter progression.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Manual Safety Philosophy</div>
<div class="rtext"><li>Driver can toggle manual/auto behavior at runtime.</li></div>
<div class="rtext"><li>Pose reset and heading reset are available as fallback controls.</li></div>
<div class="rtext"><li>Subsystem can be fully stopped by operator in emergency case.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Recommended Validation Plan</div>
<div class="rtext"><li>Validate geometry-only aiming before enabling burst sequence.</li></div>
<div class="rtext"><li>Validate polynomial output against real shot groups at multiple ranges.</li></div>
<div class="rtext"><li>Validate deadzone values under full drivetrain vibration.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Mode Breakdown</div>
<div class="rtext"><li>IMU only: deterministic fallback.</li></div>
<div class="rtext"><li>Webcam only: vision-driven corrections.</li></div>
<div class="rtext"><li>IMU + Webcam: robust combined mode for finals.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Next Step: Continue with IMU Only to implement the most stable baseline mode first.</div>
HTML;

$contentRo = '';

require __DIR__ . '/../includes/decode_doc_page.php';
