<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

function encodeJson($payload): ?string
{
    $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    if (!is_string($json)) {
        return null;
    }

    return $json;
}

function respond(array $payload, int $status = 200): void
{
    http_response_code($status);
    $json = encodeJson($payload);
    if ($json === null) {
        echo '{"success":false,"error":"Failed to encode JSON response."}';
        exit;
    }

    echo $json;
    exit;
}

function loadOpenAiKey(): string
{
    $fromEnv = trim((string) getenv('openai_token'));
    if ($fromEnv !== '') {
        return $fromEnv;
    }

    $sourcePath = __DIR__ . '/../../../financial_history_2026/api/chat.php';
    if (!is_readable($sourcePath)) {
        return '';
    }

    $content = file_get_contents($sourcePath);
    if (!is_string($content) || $content === '') {
        return '';
    }

    if (preg_match('/\$apiKey\s*=\s*[\"\']([^\"\']+)[\"\']\s*;/', $content, $matches) !== 1) {
        return '';
    }

    $candidate = trim((string) $matches[1]);
    if ($candidate === '' || stripos($candidate, 'REDACTED') !== false) {
        return '';
    }

    return $candidate;
}

function truncateText(string $text, int $maxLength): string
{
    if ($maxLength <= 0 || $text === '') {
        return '';
    }

    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($text, 'UTF-8') <= $maxLength) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, $maxLength - 3, 'UTF-8')) . '...';
    }

    if (strlen($text) <= $maxLength) {
        return $text;
    }

    return rtrim(substr($text, 0, $maxLength - 3)) . '...';
}

function extractVisibleText(string $content): string
{
    $withoutPhp = preg_replace('/<\?(?:php|=)?[\s\S]*?\?>/i', ' ', $content);
    $withoutScripts = preg_replace('/<script\b[^>]*>[\s\S]*?<\/script>/i', ' ', (string) $withoutPhp);
    $withoutStyles = preg_replace('/<style\b[^>]*>[\s\S]*?<\/style>/i', ' ', (string) $withoutScripts);
    $plain = strip_tags((string) $withoutStyles);
    $decoded = html_entity_decode($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $collapsed = preg_replace('/\s+/', ' ', $decoded);

    return trim((string) $collapsed);
}

function extractPageSnippet(string $projectRoot, string $relativePath, int $maxLength = 420): string
{
    $fullPath = realpath($projectRoot . '/' . ltrim($relativePath, '/'));
    if (!is_string($fullPath) || !is_readable($fullPath)) {
        return '';
    }

    if (strpos($fullPath, $projectRoot) !== 0) {
        return '';
    }

    $content = file_get_contents($fullPath);
    if (!is_string($content) || $content === '') {
        return '';
    }

    $plainText = extractVisibleText($content);
    return truncateText($plainText, $maxLength);
}

function buildWebsiteKnowledgeContext(): string
{
    static $cachedContext = null;
    if (is_string($cachedContext)) {
        return $cachedContext;
    }

    $projectRoot = realpath(__DIR__ . '/..');
    if (!is_string($projectRoot) || $projectRoot === '') {
        $cachedContext = '';
        return $cachedContext;
    }

    $pages = [
        ['label' => 'Main landing page + contact form', 'route' => '/', 'file' => 'index.php'],
        ['label' => 'IntoTheDeep model overview', 'route' => '/model/intothedeep/overview', 'file' => 'setup/overview.php'],
        ['label' => 'IntoTheDeep prerequisites', 'route' => '/model/intothedeep/prerequisites', 'file' => 'setup/prerequisites.php'],
        ['label' => 'IntoTheDeep resources', 'route' => '/model/intothedeep/resources', 'file' => 'setup/resources.php'],
        ['label' => 'Training datasets page', 'route' => '/model/intothedeep/training', 'file' => 'training_ml/traningdata.php'],
        ['label' => 'Online ML training page', 'route' => '/model/intothedeep/online_training_ml', 'file' => 'training_ml/online_training_ml.php'],
        ['label' => 'Online ML training guide', 'route' => '/model/intothedeep/training_ml', 'file' => 'training_ml/pythontraining.php'],
        ['label' => 'Training data structure guide', 'route' => '/model/intothedeep/training_structure', 'file' => 'training_ml/trainings.php'],
        ['label' => 'Label tool guide', 'route' => '/model/intothedeep/label_tool', 'file' => 'training_ml/label_tool.php'],
        ['label' => 'Python ML integration example', 'route' => '/model/intothedeep/pythonml', 'file' => 'examples/pythonml.php'],
        ['label' => 'Android Studio integration example', 'route' => '/model/intothedeep/android_studio', 'file' => 'examples/android_studio.php'],
        ['label' => 'Robot control example', 'route' => '/model/intothedeep/robot_control', 'file' => 'examples/robot_control.php'],
        ['label' => 'Decode model overview', 'route' => '/model/decode/overview', 'file' => 'ftc_decode/setup/overview.php'],
        ['label' => 'Decode prerequisites', 'route' => '/model/decode/prerequisites', 'file' => 'ftc_decode/setup/prerequisites.php'],
        ['label' => 'Decode resources', 'route' => '/model/decode/resources', 'file' => 'ftc_decode/setup/resources.php'],
        ['label' => 'Decode AprilTag getting started', 'route' => '/model/decode/apriltag', 'file' => 'ftc_decode/apriltag/getting_started.php'],
    ];

    $lines = [
        'Website context for AlphaBit OpenML (FTC robotics + machine learning platform).',
        'Key support routes: /register, /login, /profile, and /#contact.',
        'For requests needing manual team intervention, direct users to the contact form at /#contact and mention the team will respond as quickly as possible.',
    ];

    foreach ($pages as $page) {
        $snippet = extractPageSnippet($projectRoot, $page['file']);
        if ($snippet === '') {
            continue;
        }

        $lines[] = $page['label'] . ' [' . $page['route'] . ' | ' . $page['file'] . ']: ' . $snippet;
    }

    $cachedContext = implode("\n", $lines);
    return $cachedContext;
}

function normalizeConversationHistory($rawHistory): array
{
    if (!is_array($rawHistory)) {
        return [];
    }

    $normalized = [];
    foreach ($rawHistory as $entry) {
        if (!is_array($entry)) {
            continue;
        }

        $role = strtolower(trim((string) ($entry['role'] ?? '')));
        if ($role === 'ai') {
            $role = 'assistant';
        }

        if ($role !== 'assistant' && $role !== 'user') {
            continue;
        }

        $content = trim((string) ($entry['content'] ?? ''));
        if ($content === '') {
            continue;
        }

        $normalized[] = [
            'role' => $role,
            'content' => truncateText($content, 1200),
        ];
    }

    if (count($normalized) > 20) {
        $normalized = array_slice($normalized, -20);
    }

    return $normalized;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    respond([
        'success' => false,
        'error' => 'Method not allowed. Use POST.',
    ], 405);
}

$raw = file_get_contents('php://input');
$input = json_decode((string) $raw, true);
if (!is_array($input)) {
    respond([
        'success' => false,
        'error' => 'Invalid JSON payload.',
    ], 400);
}

$userMessage = trim((string) ($input['message'] ?? ''));
if ($userMessage === '') {
    respond([
        'success' => false,
        'error' => 'No message provided.',
    ], 400);
}

$history = normalizeConversationHistory($input['history'] ?? null);
$websiteContext = buildWebsiteKnowledgeContext();
$systemPrompt = 'You are AlphaBit Assistant for the AlphaBit OpenML FTC robotics platform. Use the provided website context to guide users through navigation, setup pages, training datasets, examples, and model workflows. Keep responses practical and concise with concrete next steps and route paths when useful. If a request needs manual team intervention (account-specific actions, unresolved bugs, custom requests, partnerships, or anything requiring a human), direct the user to the contact form at /#contact and mention the team will respond as quickly as possible. If safety-critical robotics behavior is discussed, remind users to validate changes on a controlled test field. Always reply in the same language as the user.';

$messages = [
    [
        'role' => 'system',
        'content' => $systemPrompt,
    ],
];

if ($websiteContext !== '') {
    $messages[] = [
        'role' => 'system',
        'content' => 'Website knowledge base from local files: ' . $websiteContext,
    ];
}

foreach ($history as $item) {
    $messages[] = $item;
}

$lastHistoryMessage = $history[count($history) - 1] ?? null;
if (
    !is_array($lastHistoryMessage) ||
    ($lastHistoryMessage['role'] ?? '') !== 'user' ||
    trim((string) ($lastHistoryMessage['content'] ?? '')) !== $userMessage
) {
    $messages[] = [
        'role' => 'user',
        'content' => $userMessage,
    ];
}

$apiKey = loadOpenAiKey();
if ($apiKey === '') {
    respond([
        'success' => false,
        'error' => 'OpenAI API key is missing. Configure OPENAI_API_KEY or keep the financial_history_2026 key source available.',
    ], 500);
}

$payload = [
    'model' => 'gpt-4o-mini',
    'messages' => $messages,
    'temperature' => 0.4,
    'max_tokens' => 420,
];
$payloadJson = encodeJson($payload);
if ($payloadJson === null) {
    respond([
        'success' => false,
        'error' => 'Failed to build OpenAI JSON payload.',
    ], 500);
}

$apiUrl = 'https://api.openai.com/v1/chat/completions';
$responseBody = '';
$statusCode = 0;

if (function_exists('curl_init')) {
    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ],
        CURLOPT_POSTFIELDS => $payloadJson,
        CURLOPT_CONNECTTIMEOUT => 12,
        CURLOPT_TIMEOUT => 30,
    ]);

    $responseBody = (string) curl_exec($ch);
    $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($responseBody === '' && $curlError !== '') {
        respond([
            'success' => false,
            'error' => 'OpenAI request failed: ' . $curlError,
        ], 502);
    }
} else {
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n" .
                'Authorization: Bearer ' . $apiKey . "\r\n",
            'content' => $payloadJson,
            'timeout' => 30,
            'ignore_errors' => true,
        ],
    ]);

    $response = @file_get_contents($apiUrl, false, $context);
    if ($response === false) {
        $lastError = error_get_last();
        respond([
            'success' => false,
            'error' => 'OpenAI request failed: ' . ($lastError['message'] ?? 'Unknown stream error'),
        ], 502);
    }

    $responseBody = (string) $response;

    if (isset($http_response_header) && is_array($http_response_header)) {
        foreach ($http_response_header as $headerLine) {
            if (preg_match('/^HTTP\/[0-9.]+\s+(\d{3})/', $headerLine, $matches) === 1) {
                $statusCode = (int) $matches[1];
                break;
            }
        }
    }
}

if ($statusCode >= 400 && $responseBody === '') {
    respond([
        'success' => false,
        'error' => 'OpenAI request failed with HTTP ' . $statusCode,
    ], 502);
}

$responseData = json_decode($responseBody, true);
if (!is_array($responseData)) {
    respond([
        'success' => false,
        'error' => 'Received invalid JSON from OpenAI API.',
    ], 502);
}

$reply = trim((string) ($responseData['choices'][0]['message']['content'] ?? ''));
if ($reply !== '') {
    respond([
        'success' => true,
        'reply' => $reply,
    ]);
}

$apiError = (string) ($responseData['error']['message'] ?? 'Could not generate a reply.');
respond([
    'success' => false,
    'error' => $apiError,
], 502);
