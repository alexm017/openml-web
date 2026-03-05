<?php
$pageRecord = 'decode-rr-10';
$pageTitleEn = 'Road Runner 1.0 Transition Notes';
$pageTitleRo = 'Note de Tranzitie Road Runner 1.0';
$activePage = 'rr10';

$contentEn = <<<'HTML'
<div class="ftext">Road Runner 1.0 is included here as a migration guide for teams that currently run Road Runner 0.5.6 and want to evaluate a structured transition path.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Current Repository Reality</div>
<div class="rtext"><li>Road Runner 0.5.6 code path contains active RR 0.5.6 autonomous routes.</li></div>
<div class="rtext"><li>Pedro Pathing code path contains active Pedro follower constants and integration.</li></div>
<div class="rtext"><li>Some codebases may not yet include a full standalone RR 1.0 autonomous implementation.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Why Keep RR 1.0 Notes Anyway</div>
<div class="rtext"><li>Teams may want a mid-term modernization path while preserving old route logic.</li></div>
<div class="rtext"><li>Useful for codebase planning when comparing maintenance cost vs performance gains.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Migration Strategy That Minimizes Risk</div>
<div class="rtext"><li>Freeze a known-good RR 0.5.6 baseline first.</li></div>
<div class="rtext"><li>Port one short route and benchmark repeatability against baseline.</li></div>
<div class="rtext"><li>Reattach mechanism markers only after path endpoints are stable.</li></div>
<div class="rtext"><li>Keep fallback route available until full match simulation passes.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">KPI Matrix for Migration Decisions</div>
<div class="rtext"><li>Endpoint error (position + heading) over repeated runs.</li></div>
<div class="rtext"><li>Cycle time variance for pickup -> launch loops.</li></div>
<div class="rtext"><li>Intervention count (manual resets, aborted cycles, timeout recoveries).</li></div>
<div class="rtext"><li>Maintainability cost (new bugs / week, tuning effort / route).</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Component Separation Rule</div>
<div class="rtext">No matter the path framework, preserve strict boundaries:</div>
<div class="rtext"><li>Localization layer (odometry + IMU + optional tag refresh).</li></div>
<div class="rtext"><li>Path follower layer (RR/Pedro abstraction).</li></div>
<div class="rtext"><li>Mechanism state machine (ArtifactControl and launch logic).</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">What Not to Migrate Blindly</div>
<div class="rtext"><li>Do not copy constants without re-measuring robot behavior.</li></div>
<div class="rtext"><li>Do not assume temporal marker timing survives framework changes.</li></div>
<div class="rtext"><li>Do not switch full competition workflow during final pre-event days.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Recommended Decision Rule</div>
<div class="rtext">Adopt RR 1.0 only if it beats your baseline in measurable repeatability and lowers long-term maintenance burden. Otherwise continue with the proven workflow for the current season.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Next Step: See Pedro Pathing Implementation for a practical migration target and tuning structure.</div>
HTML;

$contentRo = '';

require __DIR__ . '/../includes/decode_doc_page.php';
