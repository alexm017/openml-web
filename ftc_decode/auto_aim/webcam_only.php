<?php
$pageRecord = 'decode-autoaim-webcam';
$pageTitleEn = 'Auto Aim with Webcam Only';
$pageTitleRo = 'Auto Aim cu Doar Webcam';
$activePage = 'webcam_only';

$contentEn = <<<'HTML'
<div class="ftext">Webcam-driven aiming in this codebase is built around `AprilTagIdentification` plus optional color-blob artifact processing. It is powerful, but must be filtered and validated for real match conditions.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Vision Stack Used in Code</div>
<div class="rtext"><li>`AprilTagProcessor` for tag IDs, pose, and bearing.</li></div>
<div class="rtext"><li>`ColorBlobLocatorProcessor` for purple artifact blob filtering.</li></div>
<div class="rtext"><li>`VisionPortal` streams camera output and processor data.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Real Webcam + Blob Configuration Snippet</div>
<div class="rtext">
    <div class="codee-window">
<pre><code class="language-java">colorBlobLocatorProcessor = new ColorBlobLocatorProcessor.Builder()
    .setTargetColorRange(ColorRange.ARTIFACT_PURPLE)
    .setContourMode(ColorBlobLocatorProcessor.ContourMode.EXTERNAL_ONLY)
    .setRoi(ImageRegion.asUnityCenterCoordinates(-0.75, 0.75, 0.75, -0.75))
    .setBlurSize(5)
    .setDilateSize(15)
    .setErodeSize(15)
    .setMorphOperationType(ColorBlobLocatorProcessor.MorphOperationType.CLOSING)
    .build();

VisionPortal.Builder builder = new VisionPortal.Builder();
builder.setCameraResolution(new Size(640, 480));
builder.addProcessor(aprilTagProcessor);
builder.addProcessor(colorBlobLocatorProcessor);
visionPortal = builder.build();</code></pre>
    </div>
</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Artifact Blob Filtering (from code)</div>
<div class="rtext">The pipeline filters by contour area and circularity before telemetry output:</div>
<div class="rtext"><li>Area range: 50 to 20000</li></div>
<div class="rtext"><li>Circularity range: 0.6 to 1.0</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Practical Use in Aiming Workflows</div>
<div class="rtext"><li>AprilTag gives field-relative confidence for pose correction windows.</li></div>
<div class="rtext"><li>Blob feed supports artifact awareness and intake timing context.</li></div>
<div class="rtext"><li>Webcam-only can be used for tuning and controlled demonstrations.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Limelight Upgrade Note</div>
<div class="rtext">For high-level benchmarking, this webcam-oriented pipeline was compared with a Limelight (Raspberry Pi based) setup to evaluate consistency under match lighting.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Limitations You Must Handle</div>
<div class="rtext"><li>Lighting shifts can destabilize detections if camera settings are not controlled.</li></div>
<div class="rtext"><li>Fast robot motion can reduce confidence due to blur and rolling artifacts.</li></div>
<div class="rtext"><li>Field occlusions can temporarily remove tags from view.</li></div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Deployment Recommendation</div>
<div class="rtext">Treat webcam-only mode as a subsystem and testing mode. For highest match robustness, combine it with IMU-based heading logic.</div>

<div class="stext" style="color: white !important; font-size: 26px !important; font-weight: 500 !important; margin-top: 26px !important; margin-bottom: 14px !important; line-height: 1.1 !important;">Next Step: Continue with IMU + Webcam Fusion for full competition-grade aiming behavior.</div>
HTML;

$contentRo = '';

require __DIR__ . '/../includes/decode_doc_page.php';
