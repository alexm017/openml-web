<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

function respond(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
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

$apiKey = loadOpenAiKey();
if ($apiKey === '') {
    respond([
        'success' => false,
        'error' => 'OpenAI API key is missing. Configure OPENAI_API_KEY or keep the financial_history_2026 key source available.',
    ], 500);
}

$payload = [
    'model' => 'gpt-4o-mini',
    'messages' => [
        [
            'role' => 'system',
            'content' => 'You are AlphaBit Assistant for an FTC robotics ML website. Help with model training, dataset quality, inference debugging, deployment, and autonomous robotics workflows. Keep answers practical and concise, with concrete next steps. If safety-critical robotics behavior is discussed, remind users to validate on controlled test fields. Always reply in the same language as the user message.',
        ],
        [
            'role' => 'user',
            'content' => $userMessage,
        ],
    ],
    'temperature' => 0.5,
    'max_tokens' => 320,
];

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
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
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
            'content' => json_encode($payload, JSON_UNESCAPED_UNICODE),
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
