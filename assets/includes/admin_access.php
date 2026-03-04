<?php
declare(strict_types=1);

function alphabit_normalize_email(string $email): string
{
    return strtolower(trim($email));
}

function alphabit_admin_allowed_emails(): array
{
    static $emails = null;
    if (is_array($emails)) {
        return $emails;
    }

    $emails = [
        'test@test.test' => true,
    ];

    $envValue = getenv('OPENML_ADMIN_EMAILS');
    if (is_string($envValue) && trim($envValue) !== '') {
        $parts = explode(',', $envValue);
        foreach ($parts as $part) {
            $candidate = alphabit_normalize_email($part);
            if ($candidate === '' || !filter_var($candidate, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            $emails[$candidate] = true;
        }
    }

    return array_keys($emails);
}

function alphabit_is_admin_email(string $email): bool
{
    $normalized = alphabit_normalize_email($email);
    if ($normalized === '') {
        return false;
    }

    return in_array($normalized, alphabit_admin_allowed_emails(), true);
}

function alphabit_current_user_email(): string
{
    if (!isset($_SESSION['user_email']) || !is_string($_SESSION['user_email'])) {
        return '';
    }

    return alphabit_normalize_email($_SESSION['user_email']);
}

function alphabit_session_is_admin(): bool
{
    return alphabit_is_admin_email(alphabit_current_user_email());
}
