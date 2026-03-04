<?php
declare(strict_types=1);

function alphabit_model_routes_slugify(string $value): string
{
    $slug = strtolower(trim($value));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    return trim((string) $slug, '-');
}

function alphabit_model_builtin_routes(): array
{
    return [
        [
            'season' => 'intothedeep',
            'slug' => 'overview',
            'file' => 'setup/overview.php',
            'title_en' => 'Overview',
            'title_ro' => 'Prezentare Generala',
        ],
        [
            'season' => 'decode',
            'slug' => 'overview',
            'file' => 'ftc_decode/setup/overview.php',
            'title_en' => 'Overview',
            'title_ro' => 'Prezentare Generala',
        ],
        [
            'season' => 'intothedeep',
            'slug' => 'prerequisites',
            'file' => 'setup/prerequisites.php',
            'title_en' => 'Getting Started',
            'title_ro' => 'Initializare Device',
        ],
        [
            'season' => 'decode',
            'slug' => 'prerequisites',
            'file' => 'ftc_decode/setup/prerequisites.php',
            'title_en' => 'Getting Started',
            'title_ro' => 'Initializare Device',
        ],
        [
            'season' => 'intothedeep',
            'slug' => 'resources',
            'file' => 'setup/resources.php',
            'title_en' => 'Resources',
            'title_ro' => 'Resurse',
        ],
        [
            'season' => 'decode',
            'slug' => 'resources',
            'file' => 'ftc_decode/setup/resources.php',
            'title_en' => 'Resources',
            'title_ro' => 'Resurse',
        ],
        [
            'season' => 'intothedeep',
            'slug' => '2d-cameracalib',
            'file' => '2d_sample_detection/cameracalib.php',
            'title_en' => '2D Camera Calibration',
            'title_ro' => 'Calibrare Camera 2D',
        ],
        [
            'season' => 'decode',
            'slug' => '2d-cameracalib',
            'file' => '2d_sample_detection/cameracalib.php',
            'title_en' => '2D Camera Calibration',
            'title_ro' => 'Calibrare Camera 2D',
        ],
        [
            'season' => 'intothedeep',
            'slug' => '3d-cameracalib',
            'file' => '3d_sample_detection/cameracalib.php',
            'title_en' => '3D Camera Calibration',
            'title_ro' => 'Calibrare Camera 3D',
        ],
        [
            'season' => 'decode',
            'slug' => '3d-cameracalib',
            'file' => '3d_sample_detection/cameracalib.php',
            'title_en' => '3D Camera Calibration',
            'title_ro' => 'Calibrare Camera 3D',
        ],
        [
            'season' => 'intothedeep',
            'slug' => 'training',
            'file' => 'training_ml/traningdata.php',
            'title_en' => 'Training Data',
            'title_ro' => 'Date de Antrenament',
        ],
        [
            'season' => 'decode',
            'slug' => 'training',
            'file' => 'training_ml/traningdata.php',
            'title_en' => 'Training Data',
            'title_ro' => 'Date de Antrenament',
        ],
        [
            'season' => 'intothedeep',
            'slug' => 'training-structure',
            'file' => 'training_ml/trainings.php',
            'title_en' => 'Training Structure',
            'title_ro' => 'Structura Antrenare',
        ],
        [
            'season' => 'decode',
            'slug' => 'training-structure',
            'file' => 'training_ml/trainings.php',
            'title_en' => 'Training Structure',
            'title_ro' => 'Structura Antrenare',
        ],
        [
            'season' => 'intothedeep',
            'slug' => 'training-ml',
            'file' => 'training_ml/pythontraining.php',
            'title_en' => 'Python Training Code',
            'title_ro' => 'Cod Python Antrenare',
        ],
        [
            'season' => 'decode',
            'slug' => 'training-ml',
            'file' => 'training_ml/pythontraining.php',
            'title_en' => 'Python Training Code',
            'title_ro' => 'Cod Python Antrenare',
        ],
        [
            'season' => 'intothedeep',
            'slug' => 'online-training-ml',
            'file' => 'training_ml/online_training_ml.php',
            'title_en' => 'Online Training ML',
            'title_ro' => 'Antrenare ML Online',
        ],
        [
            'season' => 'decode',
            'slug' => 'online-training-ml',
            'file' => 'training_ml/online_training_ml.php',
            'title_en' => 'Online Training ML',
            'title_ro' => 'Antrenare ML Online',
        ],
        [
            'season' => 'intothedeep',
            'slug' => 'label-tool',
            'file' => 'training_ml/label_tool.php',
            'title_en' => 'Label Tool',
            'title_ro' => 'Tool Etichetare',
        ],
        [
            'season' => 'decode',
            'slug' => 'label-tool',
            'file' => 'training_ml/label_tool.php',
            'title_en' => 'Label Tool',
            'title_ro' => 'Tool Etichetare',
        ],
        [
            'season' => 'intothedeep',
            'slug' => 'pythonml',
            'file' => 'examples/pythonml.php',
            'title_en' => 'Python Detection Example',
            'title_ro' => 'Exemplu Detectie Python',
        ],
        [
            'season' => 'decode',
            'slug' => 'pythonml',
            'file' => 'examples/pythonml.php',
            'title_en' => 'Python Detection Example',
            'title_ro' => 'Exemplu Detectie Python',
        ],
        [
            'season' => 'intothedeep',
            'slug' => 'android-studio',
            'file' => 'examples/android_studio.php',
            'title_en' => 'Android Studio Example',
            'title_ro' => 'Exemplu Android Studio',
        ],
        [
            'season' => 'decode',
            'slug' => 'android-studio',
            'file' => 'examples/android_studio.php',
            'title_en' => 'Android Studio Example',
            'title_ro' => 'Exemplu Android Studio',
        ],
        [
            'season' => 'intothedeep',
            'slug' => 'robot-control',
            'file' => 'examples/robot_control.php',
            'title_en' => 'Robot Control Example',
            'title_ro' => 'Exemplu Control Robot',
        ],
        [
            'season' => 'decode',
            'slug' => 'robot-control',
            'file' => 'examples/robot_control.php',
            'title_en' => 'Robot Control Example',
            'title_ro' => 'Exemplu Control Robot',
        ],
        [
            'season' => 'decode',
            'slug' => 'apriltag',
            'file' => 'ftc_decode/apriltag/getting_started.php',
            'title_en' => 'AprilTag Getting Started',
            'title_ro' => 'AprilTag Getting Started',
        ],
        [
            'season' => 'decode',
            'slug' => 'apriltag-code-sample',
            'file' => 'ftc_decode/apriltag/apriltag_code_sample.php',
            'title_en' => 'AprilTag Code Sample',
            'title_ro' => 'AprilTag Code Sample',
        ],
        [
            'season' => 'decode',
            'slug' => 'apriltag-implementation',
            'file' => 'ftc_decode/apriltag/apriltag_code_sample.php',
            'title_en' => 'AprilTag Implementation',
            'title_ro' => 'AprilTag Implementation',
        ],
        [
            'season' => 'decode',
            'slug' => 'autonomous',
            'file' => 'ftc_decode/auto_control/getting_started.php',
            'title_en' => 'Autonomous Control',
            'title_ro' => 'Control Autonom',
        ],
        [
            'season' => 'decode',
            'slug' => 'odometry',
            'file' => 'ftc_decode/auto_control/odometry_pods.php',
            'title_en' => 'Odometry Pods',
            'title_ro' => 'Odometry Pods',
        ],
        [
            'season' => 'decode',
            'slug' => 'road-runner-056',
            'file' => 'ftc_decode/auto_control/rr_05.php',
            'title_en' => 'Road Runner 0.5.6',
            'title_ro' => 'Road Runner 0.5.6',
        ],
        [
            'season' => 'decode',
            'slug' => 'road-runner-10',
            'file' => 'ftc_decode/auto_control/rr_10.php',
            'title_en' => 'Road Runner 1.0',
            'title_ro' => 'Road Runner 1.0',
        ],
        [
            'season' => 'decode',
            'slug' => 'pedro-pathing',
            'file' => 'ftc_decode/auto_control/pedro_pathing.php',
            'title_en' => 'Pedro Pathing',
            'title_ro' => 'Pedro Pathing',
        ],
        [
            'season' => 'decode',
            'slug' => 'auto-aiming-getting-started',
            'file' => 'ftc_decode/auto_aim/getting_started.php',
            'title_en' => 'Auto Aiming Getting Started',
            'title_ro' => 'Auto Aiming Getting Started',
        ],
        [
            'season' => 'decode',
            'slug' => 'gyroscope-only',
            'file' => 'ftc_decode/auto_aim/gyroscope_only.php',
            'title_en' => 'Gyroscope Only',
            'title_ro' => 'Gyroscope Only',
        ],
        [
            'season' => 'decode',
            'slug' => 'camera-only',
            'file' => 'ftc_decode/auto_aim/webcam_only.php',
            'title_en' => 'Camera Only',
            'title_ro' => 'Camera Only',
        ],
        [
            'season' => 'decode',
            'slug' => 'gyroscope-and-camera',
            'file' => 'ftc_decode/auto_aim/gyroscope_and_webcam.php',
            'title_en' => 'Gyroscope + Camera',
            'title_ro' => 'Gyroscope + Camera',
        ],
    ];
}

function alphabit_model_builtin_key(string $season, string $slug): string
{
    return strtolower(trim($season)) . '::' . alphabit_model_routes_slugify($slug);
}

function alphabit_model_builtin_map(): array
{
    static $map = null;
    if (is_array($map)) {
        return $map;
    }

    $map = [];
    foreach (alphabit_model_builtin_routes() as $route) {
        $season = strtolower((string) ($route['season'] ?? ''));
        $slug = alphabit_model_routes_slugify((string) ($route['slug'] ?? ''));
        if ($season === '' || $slug === '') {
            continue;
        }

        $normalized = $route;
        $normalized['season'] = $season;
        $normalized['slug'] = $slug;
        $map[alphabit_model_builtin_key($season, $slug)] = $normalized;
    }

    return $map;
}

function alphabit_model_builtin_find(string $season, string $slug): ?array
{
    $key = alphabit_model_builtin_key($season, $slug);
    $map = alphabit_model_builtin_map();
    return $map[$key] ?? null;
}

function alphabit_model_builtin_for_season(string $season): array
{
    $season = strtolower(trim($season));
    $result = [];
    foreach (alphabit_model_builtin_map() as $route) {
        if (($route['season'] ?? '') === $season) {
            $result[] = $route;
        }
    }

    usort($result, static function (array $a, array $b): int {
        return strcmp((string) ($a['slug'] ?? ''), (string) ($b['slug'] ?? ''));
    });

    return $result;
}

function alphabit_model_is_builtin_slug(string $season, string $slug): bool
{
    return is_array(alphabit_model_builtin_find($season, $slug));
}
