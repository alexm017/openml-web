<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../assets/includes/admin_access.php';
mysqli_report(MYSQLI_REPORT_OFF);

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== 'userLoggedIn') {
    header('Location: /login');
    exit;
}

function profile_email_normalize(string $email): string
{
    return strtolower(trim($email));
}

function profile_create_token(): string
{
    try {
        return bin2hex(random_bytes(16));
    } catch (Throwable $exception) {
        return hash('sha256', uniqid('profile-csrf', true));
    }
}

function profile_upload_error_text(int $errorCode): string
{
    if ($errorCode === UPLOAD_ERR_INI_SIZE || $errorCode === UPLOAD_ERR_FORM_SIZE) {
        return 'Image is too large.';
    }
    if ($errorCode === UPLOAD_ERR_PARTIAL) {
        return 'Image upload was interrupted. Please try again.';
    }
    if ($errorCode === UPLOAD_ERR_NO_FILE) {
        return 'Choose an image first.';
    }
    return 'Image upload failed.';
}

function profile_safe_image_path(string $path, string $fallback): string
{
    if (preg_match('#^/assets/uploads/team_profiles/[a-zA-Z0-9._-]+$#', $path) === 1) {
        return $path;
    }
    return $fallback;
}

if (!isset($_SESSION['profile_flash']) || !is_array($_SESSION['profile_flash'])) {
    $_SESSION['profile_flash'] = ['success' => [], 'error' => []];
}

if (!isset($_SESSION['profile_csrf']) || !is_string($_SESSION['profile_csrf']) || $_SESSION['profile_csrf'] === '') {
    $_SESSION['profile_csrf'] = profile_create_token();
}

$csrfToken = $_SESSION['profile_csrf'];
$flash = $_SESSION['profile_flash'];
$_SESSION['profile_flash'] = ['success' => [], 'error' => []];

$dbServer = 'localhost';
$dbUser = '<REDACTED>';
$dbPassword = '<REDACTED>';
$dbName = '<REDACTED>';

$teamName = isset($_SESSION['teamname']) ? trim((string) $_SESSION['teamname']) : 'AlphaBit Member';
$seasonCookie = isset($_COOKIE['season_choice']) ? (string) $_COOKIE['season_choice'] : 'Decode';
$seasonPath = ($seasonCookie === 'Decode') ? 'decode' : 'intothedeep';
$isAdmin = alphabit_session_is_admin();
$isLoggedIn = isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === 'userLoggedIn';

$currentUserEmail = alphabit_current_user_email();
if ($currentUserEmail === '' && isset($_SESSION['user_email']) && is_string($_SESSION['user_email'])) {
    $currentUserEmail = profile_email_normalize((string) $_SESSION['user_email']);
}

$conn = new mysqli($dbServer, $dbUser, $dbPassword, $dbName);
$dbReady = !$conn->connect_error;
if ($dbReady) {
    $conn->set_charset('utf8mb4');
}

if ($dbReady && $currentUserEmail === '' && $teamName !== '') {
    $lookupStmt = $conn->prepare('SELECT email FROM users WHERE teamname = ? ORDER BY email ASC LIMIT 1');
    if ($lookupStmt) {
        $lookupStmt->bind_param('s', $teamName);
        $lookupStmt->execute();
        $lookupStmt->bind_result($foundEmail);
        if ($lookupStmt->fetch()) {
            $currentUserEmail = profile_email_normalize((string) $foundEmail);
            $_SESSION['user_email'] = $currentUserEmail;
        }
        $lookupStmt->close();
    }
}

$profileDescription = '';
$profileImagePath = '/assets/images/user3.png';
$profileVerified = false;
$profileUpdatedAt = '';
$profileFtcNumber = '';
$profileContactEmail = ($currentUserEmail !== '') ? $currentUserEmail : '';

$myTickets = [];
$allTickets = [];
$verifiedTeams = [];
$directoryTeams = [];
$pendingTeams = [];
$adminVerifiedTeams = [];

$isPost = isset($_SERVER['REQUEST_METHOD']) && strtoupper((string) $_SERVER['REQUEST_METHOD']) === 'POST';
$redirectAnchor = '';

if ($dbReady) {
    $schemaStatements = [
        "CREATE TABLE IF NOT EXISTS team_profiles (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_email VARCHAR(190) NOT NULL,
            team_name VARCHAR(190) NOT NULL,
            description TEXT NULL,
            image_path VARCHAR(255) NULL,
            contact_email VARCHAR(190) NULL,
            ftc_team_number VARCHAR(32) NULL,
            is_verified TINYINT(1) NOT NULL DEFAULT 0,
            verified_by VARCHAR(190) NULL,
            verified_at DATETIME NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_team_profiles_email (user_email),
            KEY idx_team_profiles_verified (is_verified),
            KEY idx_team_profiles_team_name (team_name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        "CREATE TABLE IF NOT EXISTS support_tickets (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_email VARCHAR(190) NOT NULL,
            team_name VARCHAR(190) NOT NULL,
            subject VARCHAR(160) NOT NULL,
            message TEXT NOT NULL,
            priority ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
            status ENUM('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY idx_support_tickets_user (user_email),
            KEY idx_support_tickets_status (status),
            KEY idx_support_tickets_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    ];

    foreach ($schemaStatements as $schemaSql) {
        if (!$conn->query($schemaSql)) {
            $dbReady = false;
            $flash['error'][] = 'Database initialization failed on profile page.';
            break;
        }
    }
}

if ($dbReady && $currentUserEmail !== '') {
    $syncStmt = $conn->prepare(
        'INSERT INTO team_profiles (user_email, team_name, contact_email, description)
         VALUES (?, ?, ?, \'\')
         ON DUPLICATE KEY UPDATE
             team_name = VALUES(team_name),
             contact_email = COALESCE(NULLIF(contact_email, \'\'), VALUES(contact_email))'
    );
    if ($syncStmt) {
        $syncStmt->bind_param('sss', $currentUserEmail, $teamName, $currentUserEmail);
        $syncStmt->execute();
        $syncStmt->close();
    }
}

if ($isPost) {
    $tokenFromPost = isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '';
    $redirectAnchor = isset($_POST['redirect_anchor']) ? (string) $_POST['redirect_anchor'] : '';
    $redirectAnchor = preg_replace('/[^a-zA-Z0-9_-]/', '', $redirectAnchor);

    if (!hash_equals($csrfToken, $tokenFromPost)) {
        $_SESSION['profile_flash']['error'][] = 'Security token mismatch. Refresh and try again.';
    } elseif (!$dbReady || $currentUserEmail === '') {
        $_SESSION['profile_flash']['error'][] = 'Profile actions are unavailable right now. Try logging in again.';
    } else {
        $action = isset($_POST['profile_action']) ? (string) $_POST['profile_action'] : '';

        if ($action === 'update_description') {
            $description = trim((string) ($_POST['team_description'] ?? ''));
            $ftcNumber = trim((string) ($_POST['ftc_team_number'] ?? ''));
            $contactEmail = profile_email_normalize((string) ($_POST['contact_email'] ?? ''));
            $descriptionLength = function_exists('mb_strlen') ? mb_strlen($description, 'UTF-8') : strlen($description);

            if ($descriptionLength > 1200) {
                $_SESSION['profile_flash']['error'][] = 'Description is too long (max 1200 characters).';
            } elseif ($contactEmail !== '' && !filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['profile_flash']['error'][] = 'Contact email is invalid.';
            } elseif (preg_match('/[^0-9A-Za-z -]/', $ftcNumber) === 1 || strlen($ftcNumber) > 32) {
                $_SESSION['profile_flash']['error'][] = 'FTC team number format is invalid.';
            } else {
                $updateStmt = $conn->prepare(
                    'UPDATE team_profiles
                     SET team_name = ?, description = ?, ftc_team_number = ?, contact_email = ?
                     WHERE user_email = ?'
                );
                if ($updateStmt) {
                    $updateStmt->bind_param('sssss', $teamName, $description, $ftcNumber, $contactEmail, $currentUserEmail);
                    if ($updateStmt->execute()) {
                        $_SESSION['profile_flash']['success'][] = 'Team description updated.';
                    } else {
                        $_SESSION['profile_flash']['error'][] = 'Could not update description.';
                    }
                    $updateStmt->close();
                } else {
                    $_SESSION['profile_flash']['error'][] = 'Could not prepare description update.';
                }
            }
        } elseif ($action === 'upload_profile_picture') {
            if (!isset($_FILES['team_image']) || !is_array($_FILES['team_image'])) {
                $_SESSION['profile_flash']['error'][] = 'No image upload found.';
            } else {
                $upload = $_FILES['team_image'];
                $uploadError = isset($upload['error']) ? (int) $upload['error'] : UPLOAD_ERR_NO_FILE;
                if ($uploadError !== UPLOAD_ERR_OK) {
                    $_SESSION['profile_flash']['error'][] = profile_upload_error_text($uploadError);
                } else {
                    $tmpPath = isset($upload['tmp_name']) ? (string) $upload['tmp_name'] : '';
                    $fileSize = isset($upload['size']) ? (int) $upload['size'] : 0;
                    if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
                        $_SESSION['profile_flash']['error'][] = 'Invalid uploaded image.';
                    } elseif ($fileSize < 1 || $fileSize > (3 * 1024 * 1024)) {
                        $_SESSION['profile_flash']['error'][] = 'Image must be between 1 byte and 3 MB.';
                    } else {
                        $allowedMime = [
                            'image/jpeg' => 'jpg',
                            'image/png' => 'png',
                            'image/webp' => 'webp',
                            'image/gif' => 'gif',
                        ];
                        $mime = '';
                        if (function_exists('finfo_open')) {
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            if ($finfo !== false) {
                                $mime = (string) finfo_file($finfo, $tmpPath);
                                finfo_close($finfo);
                            }
                        }
                        if (!isset($allowedMime[$mime])) {
                            $imageMeta = @getimagesize($tmpPath);
                            if (is_array($imageMeta) && isset($imageMeta['mime']) && is_string($imageMeta['mime'])) {
                                $mime = $imageMeta['mime'];
                            }
                        }

                        if (!isset($allowedMime[$mime])) {
                            $_SESSION['profile_flash']['error'][] = 'Only JPG, PNG, WEBP, or GIF files are accepted.';
                        } else {
                            $uploadDir = __DIR__ . '/../assets/uploads/team_profiles';
                            if (!is_dir($uploadDir)) {
                                @mkdir($uploadDir, 0777, true);
                            }
                            if (is_dir($uploadDir) && !is_writable($uploadDir)) {
                                @chmod($uploadDir, 0777);
                            }

                            if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
                                $_SESSION['profile_flash']['error'][] = 'Upload directory is unavailable or not writable.';
                            } else {
                                try {
                                    $randomPart = bin2hex(random_bytes(10));
                                } catch (Throwable $exception) {
                                    $randomPart = uniqid('img', true);
                                }
                                $filename = 'team_' . preg_replace('/[^a-z0-9]+/i', '-', $currentUserEmail) . '_' . $randomPart . '.' . $allowedMime[$mime];
                                $targetAbsolute = $uploadDir . '/' . $filename;
                                $targetWebPath = '/assets/uploads/team_profiles/' . $filename;

                                if (!move_uploaded_file($tmpPath, $targetAbsolute)) {
                                    $_SESSION['profile_flash']['error'][] = 'Failed to store uploaded image.';
                                } else {
                                    @chmod($targetAbsolute, 0664);
                                    $oldPath = '';
                                    $oldStmt = $conn->prepare('SELECT image_path FROM team_profiles WHERE user_email = ? LIMIT 1');
                                    if ($oldStmt) {
                                        $oldStmt->bind_param('s', $currentUserEmail);
                                        $oldStmt->execute();
                                        $oldStmt->bind_result($currentPath);
                                        if ($oldStmt->fetch()) {
                                            $oldPath = is_string($currentPath) ? $currentPath : '';
                                        }
                                        $oldStmt->close();
                                    }

                                    $saveStmt = $conn->prepare('UPDATE team_profiles SET image_path = ? WHERE user_email = ?');
                                    if ($saveStmt) {
                                        $saveStmt->bind_param('ss', $targetWebPath, $currentUserEmail);
                                        if ($saveStmt->execute()) {
                                            $_SESSION['profile_flash']['success'][] = 'Team profile picture updated.';
                                            $_SESSION['profile_image_path'] = $targetWebPath;
                                            if (
                                                $oldPath !== '' &&
                                                $oldPath !== $targetWebPath &&
                                                preg_match('#^/assets/uploads/team_profiles/[a-zA-Z0-9._-]+$#', $oldPath) === 1
                                            ) {
                                                $oldAbsolute = __DIR__ . '/..' . $oldPath;
                                                if (is_file($oldAbsolute)) {
                                                    @unlink($oldAbsolute);
                                                }
                                            }
                                        } else {
                                            $_SESSION['profile_flash']['error'][] = 'Could not save new image path.';
                                        }
                                        $saveStmt->close();
                                    } else {
                                        $_SESSION['profile_flash']['error'][] = 'Could not prepare image update query.';
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } elseif ($action === 'create_ticket') {
            $subject = trim((string) ($_POST['ticket_subject'] ?? ''));
            $message = trim((string) ($_POST['ticket_message'] ?? ''));
            $priority = isset($_POST['ticket_priority']) ? (string) $_POST['ticket_priority'] : 'medium';
            $allowedPriorities = ['low', 'medium', 'high'];
            if (!in_array($priority, $allowedPriorities, true)) {
                $priority = 'medium';
            }
            $subjectLength = function_exists('mb_strlen') ? mb_strlen($subject, 'UTF-8') : strlen($subject);
            $messageLength = function_exists('mb_strlen') ? mb_strlen($message, 'UTF-8') : strlen($message);

            if ($subjectLength < 4 || $subjectLength > 160) {
                $_SESSION['profile_flash']['error'][] = 'Ticket subject must be between 4 and 160 characters.';
            } elseif ($messageLength < 8 || $messageLength > 4000) {
                $_SESSION['profile_flash']['error'][] = 'Ticket message must be between 8 and 4000 characters.';
            } else {
                $ticketStmt = $conn->prepare(
                    'INSERT INTO support_tickets (user_email, team_name, subject, message, priority, status)
                     VALUES (?, ?, ?, ?, ?, \'open\')'
                );
                if ($ticketStmt) {
                    $ticketStmt->bind_param('sssss', $currentUserEmail, $teamName, $subject, $message, $priority);
                    if ($ticketStmt->execute()) {
                        $_SESSION['profile_flash']['success'][] = 'Support ticket created.';
                    } else {
                        $_SESSION['profile_flash']['error'][] = 'Could not create support ticket.';
                    }
                    $ticketStmt->close();
                } else {
                    $_SESSION['profile_flash']['error'][] = 'Could not prepare support ticket query.';
                }
            }
        } elseif ($action === 'verify_team') {
            if (!$isAdmin) {
                $_SESSION['profile_flash']['error'][] = 'Only admins can verify teams.';
            } else {
                $targetEmail = profile_email_normalize((string) ($_POST['target_email'] ?? ''));
                $verifyValue = isset($_POST['verify_value']) ? (string) $_POST['verify_value'] : '0';
                $verifyFlag = ($verifyValue === '1') ? 1 : 0;
                if (!filter_var($targetEmail, FILTER_VALIDATE_EMAIL)) {
                    $_SESSION['profile_flash']['error'][] = 'Invalid target email for verification.';
                } else {
                    $targetTeamName = '';
                    $teamLookup = $conn->prepare('SELECT teamname FROM users WHERE email = ? LIMIT 1');
                    if ($teamLookup) {
                        $teamLookup->bind_param('s', $targetEmail);
                        $teamLookup->execute();
                        $teamLookup->bind_result($teamNameLookup);
                        if ($teamLookup->fetch()) {
                            $targetTeamName = trim((string) $teamNameLookup);
                        }
                        $teamLookup->close();
                    }
                    if ($targetTeamName === '') {
                        $targetTeamName = 'FTC Team';
                    }

                    $verifiedBy = ($verifyFlag === 1) ? $currentUserEmail : null;
                    $verifiedAt = ($verifyFlag === 1) ? date('Y-m-d H:i:s') : null;

                    $verifyStmt = $conn->prepare(
                        'INSERT INTO team_profiles (user_email, team_name, description, is_verified, verified_by, verified_at)
                         VALUES (?, ?, \'\', ?, ?, ?)
                         ON DUPLICATE KEY UPDATE
                             team_name = VALUES(team_name),
                             is_verified = VALUES(is_verified),
                             verified_by = VALUES(verified_by),
                             verified_at = VALUES(verified_at)'
                    );
                    if ($verifyStmt) {
                        $verifyStmt->bind_param('ssiss', $targetEmail, $targetTeamName, $verifyFlag, $verifiedBy, $verifiedAt);
                        if ($verifyStmt->execute()) {
                            $_SESSION['profile_flash']['success'][] = ($verifyFlag === 1)
                                ? 'Team marked as verified FTC.'
                                : 'Team verification removed.';
                        } else {
                            $_SESSION['profile_flash']['error'][] = 'Could not update team verification.';
                        }
                        $verifyStmt->close();
                    } else {
                        $_SESSION['profile_flash']['error'][] = 'Could not prepare verification update.';
                    }
                }
            }
        } elseif ($action === 'update_ticket_status') {
            if (!$isAdmin) {
                $_SESSION['profile_flash']['error'][] = 'Only admins can update ticket status.';
            } else {
                $ticketId = isset($_POST['ticket_id']) ? (int) $_POST['ticket_id'] : 0;
                $ticketStatus = isset($_POST['ticket_status']) ? (string) $_POST['ticket_status'] : 'open';
                $allowedStatuses = ['open', 'in_progress', 'resolved', 'closed'];
                if ($ticketId < 1 || !in_array($ticketStatus, $allowedStatuses, true)) {
                    $_SESSION['profile_flash']['error'][] = 'Invalid ticket status update request.';
                } else {
                    $statusStmt = $conn->prepare('UPDATE support_tickets SET status = ? WHERE id = ?');
                    if ($statusStmt) {
                        $statusStmt->bind_param('si', $ticketStatus, $ticketId);
                        if ($statusStmt->execute()) {
                            $_SESSION['profile_flash']['success'][] = 'Ticket status updated.';
                        } else {
                            $_SESSION['profile_flash']['error'][] = 'Could not update ticket status.';
                        }
                        $statusStmt->close();
                    } else {
                        $_SESSION['profile_flash']['error'][] = 'Could not prepare ticket status update.';
                    }
                }
            }
        } else {
            $_SESSION['profile_flash']['error'][] = 'Unsupported profile action.';
        }
    }

    $redirectTo = '/profile';
    if ($redirectAnchor !== '') {
        $redirectTo .= '#' . $redirectAnchor;
    }
    header('Location: ' . $redirectTo);
    exit;
}

if ($dbReady && $currentUserEmail !== '') {
    $profileStmt = $conn->prepare(
        'SELECT team_name, description, image_path, contact_email, ftc_team_number, is_verified, updated_at
         FROM team_profiles
         WHERE user_email = ?
         LIMIT 1'
    );
    if ($profileStmt) {
        $profileStmt->bind_param('s', $currentUserEmail);
        $profileStmt->execute();
        $profileStmt->bind_result($dbTeamName, $dbDescription, $dbImagePath, $dbContactEmail, $dbFtcNumber, $dbVerified, $dbUpdatedAt);
        if ($profileStmt->fetch()) {
            if (is_string($dbTeamName) && trim($dbTeamName) !== '') {
                $teamName = trim($dbTeamName);
                $_SESSION['teamname'] = $teamName;
            }
            $profileDescription = is_string($dbDescription) ? trim($dbDescription) : '';
            $profileImagePath = profile_safe_image_path((string) $dbImagePath, '/assets/images/user3.png');
            $profileContactEmail = is_string($dbContactEmail) && trim($dbContactEmail) !== ''
                ? trim($dbContactEmail)
                : $profileContactEmail;
            $profileFtcNumber = is_string($dbFtcNumber) ? trim($dbFtcNumber) : '';
            $profileVerified = ((int) $dbVerified) === 1;
            $profileUpdatedAt = is_string($dbUpdatedAt) ? $dbUpdatedAt : '';
        }
        $profileStmt->close();
    }

    $ticketStmt = $conn->prepare(
        'SELECT id, subject, message, priority, status, created_at, updated_at
         FROM support_tickets
         WHERE user_email = ?
         ORDER BY created_at DESC
         LIMIT 40'
    );
    if ($ticketStmt) {
        $ticketStmt->bind_param('s', $currentUserEmail);
        $ticketStmt->execute();
        $ticketResult = $ticketStmt->get_result();
        if ($ticketResult) {
            while ($row = $ticketResult->fetch_assoc()) {
                $myTickets[] = $row;
            }
        }
        $ticketStmt->close();
    }

    if ($isAdmin) {
        $adminTicketsResult = $conn->query(
            "SELECT id, team_name, user_email, subject, priority, status, created_at
             FROM support_tickets
             ORDER BY
                 CASE status
                     WHEN 'open' THEN 0
                     WHEN 'in_progress' THEN 1
                     WHEN 'resolved' THEN 2
                     ELSE 3
                 END,
                 created_at DESC
             LIMIT 100"
        );
        if ($adminTicketsResult) {
            while ($row = $adminTicketsResult->fetch_assoc()) {
                $allTickets[] = $row;
            }
            $adminTicketsResult->free();
        }
    }

    $verifiedStmt = $conn->prepare(
        'SELECT team_name, user_email, description, image_path, contact_email, ftc_team_number
         FROM team_profiles
         WHERE is_verified = 1 AND user_email <> ?
         ORDER BY team_name ASC
         LIMIT 60'
    );
    if ($verifiedStmt) {
        $verifiedStmt->bind_param('s', $currentUserEmail);
        $verifiedStmt->execute();
        $verifiedResult = $verifiedStmt->get_result();
        if ($verifiedResult) {
            while ($row = $verifiedResult->fetch_assoc()) {
                $row['image_path'] = profile_safe_image_path((string) ($row['image_path'] ?? ''), '/assets/images/user3.png');
                $verifiedTeams[] = $row;
            }
        }
        $verifiedStmt->close();
    }

    $directoryResult = $conn->query(
        "SELECT
            u.teamname AS team_name,
            LOWER(TRIM(u.email)) AS user_email,
            COALESCE(p.description, '') AS description,
            COALESCE(p.image_path, '') AS image_path,
            COALESCE(p.contact_email, LOWER(TRIM(u.email))) AS contact_email,
            COALESCE(p.ftc_team_number, '') AS ftc_team_number,
            COALESCE(p.is_verified, 0) AS is_verified
         FROM users u
         LEFT JOIN team_profiles p ON BINARY p.user_email = BINARY LOWER(TRIM(u.email))
         WHERE u.teamname IS NOT NULL
           AND TRIM(u.teamname) <> ''
           AND COALESCE(p.is_verified, 0) = 1
         ORDER BY COALESCE(p.is_verified, 0) DESC, u.teamname ASC
         LIMIT 120"
    );
    if ($directoryResult) {
        while ($row = $directoryResult->fetch_assoc()) {
            $row['image_path'] = profile_safe_image_path((string) ($row['image_path'] ?? ''), '/assets/images/user3.png');
            $directoryTeams[] = $row;
        }
        $directoryResult->free();
    }

    if ($isAdmin) {
        $pendingResult = $conn->query(
            "SELECT
                u.teamname AS team_name,
                LOWER(TRIM(u.email)) AS user_email,
                COALESCE(p.description, '') AS description,
                COALESCE(p.image_path, '') AS image_path,
                COALESCE(p.contact_email, LOWER(TRIM(u.email))) AS contact_email,
                COALESCE(p.ftc_team_number, '') AS ftc_team_number,
                COALESCE(p.is_verified, 0) AS is_verified
             FROM users u
             LEFT JOIN team_profiles p ON BINARY p.user_email = BINARY LOWER(TRIM(u.email))
             WHERE u.teamname IS NOT NULL
               AND TRIM(u.teamname) <> ''
               AND COALESCE(p.is_verified, 0) <> 1
             ORDER BY u.teamname ASC
             LIMIT 120"
        );
        if ($pendingResult) {
            while ($row = $pendingResult->fetch_assoc()) {
                $row['image_path'] = profile_safe_image_path((string) ($row['image_path'] ?? ''), '/assets/images/user3.png');
                $pendingTeams[] = $row;
            }
            $pendingResult->free();
        }

        $verifiedAdminResult = $conn->query(
            "SELECT
                u.teamname AS team_name,
                LOWER(TRIM(u.email)) AS user_email,
                COALESCE(p.description, '') AS description,
                COALESCE(p.image_path, '') AS image_path,
                COALESCE(p.contact_email, LOWER(TRIM(u.email))) AS contact_email,
                COALESCE(p.ftc_team_number, '') AS ftc_team_number,
                COALESCE(p.is_verified, 0) AS is_verified
             FROM users u
             LEFT JOIN team_profiles p ON BINARY p.user_email = BINARY LOWER(TRIM(u.email))
             WHERE u.teamname IS NOT NULL
               AND TRIM(u.teamname) <> ''
               AND COALESCE(p.is_verified, 0) = 1
             ORDER BY u.teamname ASC
             LIMIT 120"
        );
        if ($verifiedAdminResult) {
            while ($row = $verifiedAdminResult->fetch_assoc()) {
                $row['image_path'] = profile_safe_image_path((string) ($row['image_path'] ?? ''), '/assets/images/user3.png');
                $adminVerifiedTeams[] = $row;
            }
            $verifiedAdminResult->free();
        }
    }
}

$profileDescriptionDisplay = ($profileDescription !== '')
    ? $profileDescription
    : 'Add a short description for your FTC team so others know your strengths and goals.';
$profileUpdatedLabel = ($profileUpdatedAt !== '')
    ? date('M j, Y H:i', strtotime($profileUpdatedAt))
    : 'not yet updated';
if ($isLoggedIn) {
    $_SESSION['profile_image_path'] = profile_safe_image_path($profileImagePath, '/assets/images/user3.png');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>AlphaBit - Profile</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="/assets/images/alphabit.ico">
    <style>
        :root {
            --pf-ink: #f5f5f4;
            --pf-muted: #d0d0cc;
            --pf-surface: rgba(12, 12, 12, 0.9);
            --pf-surface-strong: rgba(8, 8, 8, 0.93);
            --pf-border: rgba(255, 255, 255, 0.14);
            --pf-cta-bg: #f1f1ef;
            --pf-cta-bg-hover: #ffffff;
            --pf-cta-ink: #0f0f0f;
            --pf-shadow: 0 18px 38px rgba(0, 0, 0, 0.4);
            --ok-bg: rgba(88, 214, 141, 0.16);
            --ok-border: rgba(88, 214, 141, 0.52);
            --ok-ink: #ccf7dd;
            --bad-bg: rgba(240, 128, 128, 0.16);
            --bad-border: rgba(240, 128, 128, 0.52);
            --bad-ink: #ffdede;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Manrope', 'Montserrat', sans-serif;
            color: var(--pf-ink);
            background: linear-gradient(165deg, #040404 0%, #090909 52%, #101010 100%);
        }

        body::before,
        body::after {
            content: '';
            position: fixed;
            border-radius: 999px;
            pointer-events: none;
            z-index: -1;
        }

        body::before {
            width: 32rem;
            height: 32rem;
            top: -12rem;
            right: -11rem;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.04), rgba(255, 255, 255, 0));
        }

        body::after {
            width: 26rem;
            height: 26rem;
            bottom: -9rem;
            left: -9rem;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.03), rgba(255, 255, 255, 0));
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .site-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 90;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.9rem;
            padding: 0.78rem clamp(1rem, 3vw, 3.5rem);
            background: rgba(8, 8, 8, 0.84);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.34);
        }

        .brand-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 800;
            letter-spacing: 0.01em;
            font-size: clamp(1.02rem, 1.3vw, 1.2rem);
        }

        .brand-logo {
            width: 1.85rem;
            height: 1.85rem;
            object-fit: contain;
            transform: translateY(2px);
        }

        .nav-links,
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            flex-wrap: wrap;
        }

        .nav-actions {
            margin-right: 2.4rem;
        }

        .nav-link,
        .logout-btn,
        .primary-btn,
        .secondary-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.92rem;
            border-radius: 999px;
            border: 1px solid rgba(0, 0, 0, 0.18);
            background: linear-gradient(135deg, var(--pf-cta-bg), #e7e7e4);
            color: var(--pf-cta-ink);
            font-size: 0.9rem;
            font-weight: 700;
            transition: transform 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
            cursor: pointer;
        }

        .nav-link:hover,
        .logout-btn:hover,
        .primary-btn:hover,
        .secondary-btn:hover {
            transform: translateY(-1px);
            background: linear-gradient(135deg, var(--pf-cta-bg-hover), #f0f0ee);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .secondary-btn {
            background: rgba(16, 16, 16, 0.9);
            border: 1px solid var(--pf-border);
            color: var(--pf-ink);
        }

        .secondary-btn:hover {
            background: rgba(28, 28, 28, 0.96);
        }

        .profile-shell {
            width: min(1120px, 100%);
            margin: 0 auto;
            padding: 6.3rem 1rem 2rem;
            display: grid;
            gap: 1rem;
        }

        .flash-stack {
            display: grid;
            gap: 0.55rem;
        }

        .flash-msg {
            border-radius: 0.8rem;
            padding: 0.72rem 0.9rem;
            border: 1px solid;
            font-size: 0.93rem;
        }

        .flash-msg.ok {
            background: var(--ok-bg);
            border-color: var(--ok-border);
            color: var(--ok-ink);
        }

        .flash-msg.bad {
            background: var(--bad-bg);
            border-color: var(--bad-border);
            color: var(--bad-ink);
        }

        .profile-card,
        .panel-card {
            border-radius: 1.15rem;
            border: 1px solid var(--pf-border);
            background: var(--pf-surface);
            box-shadow: var(--pf-shadow);
        }

        .profile-card {
            position: relative;
            padding: clamp(1rem, 3vw, 1.45rem);
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 1rem;
            align-items: center;
            padding-right: clamp(1rem, 10vw, 9rem);
            padding-bottom: 3.1rem;
        }

        .profile-avatar-upload {
            margin: 0;
            width: fit-content;
        }

        .profile-avatar-input {
            display: none;
        }

        .profile-avatar-trigger {
            position: relative;
            border: none;
            background: none;
            padding: 0;
            margin: 0;
            cursor: pointer;
            border-radius: 999px;
            line-height: 0;
        }

        .profile-avatar {
            width: clamp(80px, 13vw, 108px);
            height: clamp(80px, 13vw, 108px);
            border-radius: 999px;
            border: 1px solid var(--pf-border);
            object-fit: cover;
            background: rgba(255, 255, 255, 0.06);
        }

        .profile-avatar-trigger:hover .profile-avatar {
            border-color: rgba(255, 255, 255, 0.34);
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.1);
        }

        .avatar-upload-hint {
            position: absolute;
            right: -0.15rem;
            bottom: -0.2rem;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.28);
            background: rgba(20, 20, 20, 0.92);
            color: #f5f5f2;
            font-size: 0.66rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            line-height: 1;
            padding: 0.28rem 0.44rem;
            pointer-events: none;
            transform: translateY(0);
            transition: transform 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
            white-space: nowrap;
        }

        .profile-avatar-trigger:hover .avatar-upload-hint,
        .profile-avatar-trigger:focus-visible .avatar-upload-hint {
            transform: translateY(-1px);
            border-color: rgba(255, 255, 255, 0.48);
            background: rgba(32, 32, 32, 0.96);
        }

        .profile-avatar-save {
            position: absolute;
            right: 0.95rem;
            bottom: 0.95rem;
            display: none;
        }

        .profile-avatar-save.is-visible {
            display: inline-flex;
        }

        .profile-settings-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            z-index: 2;
            transition: box-shadow 0.18s ease, background-color 0.18s ease, border-color 0.18s ease, color 0.18s ease;
        }

        .profile-settings-toggle[aria-expanded='true'] {
            background: rgba(34, 34, 34, 0.98);
            color: #f3f3ef;
            border-color: rgba(255, 255, 255, 0.32);
        }

        .profile-settings-toggle:hover,
        .profile-settings-toggle:focus-visible {
            transform: translateY(-50%);
        }

        .profile-name {
            margin: 0;
            font-size: clamp(1.25rem, 2.3vw, 1.65rem);
            line-height: 1.18;
            color: var(--pf-ink);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .team-number-inline {
            color: #d8d8d2;
            font-size: clamp(0.95rem, 1.6vw, 1.1rem);
            font-weight: 700;
        }

        .verify-pill {
            border-radius: 999px;
            border: 1px solid var(--pf-border);
            padding: 0.2rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .verify-pill.ok {
            border-color: rgba(88, 214, 141, 0.65);
            color: #c9f7d8;
            background: rgba(88, 214, 141, 0.2);
        }

        .verify-pill.pending {
            border-color: rgba(255, 255, 255, 0.22);
            color: #dfdfdb;
            background: rgba(255, 255, 255, 0.08);
        }

        .profile-subtext {
            margin: 0.4rem 0 0;
            color: var(--pf-muted);
            font-size: 0.96rem;
            line-height: 1.45;
            max-width: 72ch;
        }

        .profile-meta {
            margin-top: 0.75rem;
            color: #b8b8b3;
            font-size: 0.84rem;
        }

        .panel-card {
            padding: 1rem;
            display: grid;
            gap: 0.85rem;
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.7rem;
            flex-wrap: wrap;
        }

        .panel-title {
            margin: 0;
            font-size: 1.05rem;
            line-height: 1.2;
        }

        .quick-links-row {
            display: flex;
            gap: 0.55rem;
            flex-wrap: wrap;
        }

        .chip-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 0.8rem;
            border-radius: 999px;
            border: 1px solid var(--pf-border);
            background: rgba(16, 16, 16, 0.9);
            color: var(--pf-muted);
            font-size: 0.9rem;
            font-weight: 600;
        }

        .chip-link:hover {
            color: var(--pf-ink);
            border-color: rgba(255, 255, 255, 0.26);
        }

        .forms-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.8rem;
        }

        .forms-grid.single {
            grid-template-columns: 1fr;
        }

        .is-hidden {
            display: none !important;
        }

        .profile-settings-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            z-index: 1400;
        }

        .profile-settings-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1410;
            width: min(760px, calc(100vw - 2rem));
            max-height: calc(100vh - 2rem);
            overflow: auto;
            box-shadow: 0 24px 54px rgba(0, 0, 0, 0.55);
        }

        .profile-settings-modal .panel-header {
            align-items: center;
        }

        .settings-close-btn {
            min-width: 74px;
        }

        body.settings-modal-open {
            overflow: hidden;
        }

        body.settings-modal-open .site-navbar,
        body.settings-modal-open .profile-shell > *:not(#profile-settings-backdrop):not(#profile-edit) {
            filter: blur(3px);
            pointer-events: none;
            user-select: none;
        }

        .form-card {
            border: 1px solid var(--pf-border);
            border-radius: 0.9rem;
            padding: 0.85rem;
            background: var(--pf-surface-strong);
            display: grid;
            gap: 0.6rem;
        }

        .form-card label {
            font-size: 0.85rem;
            color: #d4d4cf;
            font-weight: 600;
        }

        .form-card input[type='text'],
        .form-card input[type='email'],
        .form-card input[type='file'],
        .form-card textarea,
        .form-card select {
            width: 100%;
            border-radius: 0.7rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(22, 22, 22, 0.96);
            color: #f4f4f1;
            font: inherit;
            padding: 0.58rem 0.65rem;
            outline: none;
        }

        .form-card textarea {
            min-height: 130px;
            resize: vertical;
            line-height: 1.45;
        }

        .avatar-preview {
            width: 96px;
            height: 96px;
            border-radius: 999px;
            object-fit: cover;
            border: 1px solid var(--pf-border);
            background: rgba(255, 255, 255, 0.05);
        }

        .ticket-list,
        .team-grid {
            display: grid;
            gap: 0.7rem;
        }

        .ticket-item,
        .team-item {
            border: 1px solid var(--pf-border);
            border-radius: 0.9rem;
            padding: 0.8rem;
            background: rgba(12, 12, 12, 0.72);
            display: grid;
            gap: 0.45rem;
        }

        .ticket-head,
        .team-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.6rem;
            flex-wrap: wrap;
        }

        .ticket-subject,
        .team-name {
            margin: 0;
            font-size: 0.97rem;
            line-height: 1.2;
        }

        .ticket-meta,
        .team-meta {
            color: #b9b9b4;
            font-size: 0.84rem;
        }

        .ticket-message {
            margin: 0;
            color: #e2e2de;
            font-size: 0.89rem;
            line-height: 1.45;
            white-space: pre-wrap;
            text-align: left;
        }

        .team-description {
            margin: 0;
            color: #e2e2de;
            font-size: 0.89rem;
            line-height: 1.45;
            white-space: normal;
            text-align: left;
        }

        .status-pill {
            border-radius: 999px;
            padding: 0.18rem 0.52rem;
            font-size: 0.75rem;
            font-weight: 700;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.07);
            color: #ecece7;
        }

        .status-pill.open {
            border-color: rgba(248, 216, 140, 0.55);
            color: #ffe9c0;
            background: rgba(248, 216, 140, 0.16);
        }

        .status-pill.in_progress {
            border-color: rgba(130, 193, 255, 0.55);
            color: #cde9ff;
            background: rgba(130, 193, 255, 0.16);
        }

        .status-pill.resolved,
        .status-pill.closed {
            border-color: rgba(88, 214, 141, 0.55);
            color: #cff8de;
            background: rgba(88, 214, 141, 0.16);
        }

        .directory-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.7rem;
        }

        .team-item {
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 0.8rem;
        }

        .team-item > div {
            text-align: left;
            justify-self: start;
        }

        .team-avatar {
            width: 54px;
            height: 54px;
            border-radius: 999px;
            object-fit: cover;
            border: 1px solid var(--pf-border);
            background: rgba(255, 255, 255, 0.05);
        }

        .team-item.is-verified {
            border-color: rgba(88, 214, 141, 0.44);
            background: rgba(88, 214, 141, 0.08);
        }

        .admin-grid {
            display: grid;
            gap: 0.6rem;
        }

        .admin-item {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 0.65rem;
            align-items: center;
            border: 1px solid var(--pf-border);
            border-radius: 0.8rem;
            padding: 0.65rem;
            background: rgba(10, 10, 10, 0.74);
        }

        .empty-state {
            margin: 0;
            padding: 0.75rem;
            border-radius: 0.8rem;
            border: 1px dashed rgba(255, 255, 255, 0.2);
            color: #d4d4cf;
            background: rgba(255, 255, 255, 0.04);
            font-size: 0.9rem;
        }

        @media (max-width: 980px) {
            .site-navbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .nav-links,
            .nav-actions {
                width: 100%;
            }

            .nav-actions {
                margin-right: 0;
            }

            .nav-link,
            .logout-btn {
                width: 100%;
            }

            .profile-card {
                grid-template-columns: 1fr;
                text-align: center;
                justify-items: center;
                padding-right: 1rem;
                padding-bottom: 1rem;
            }

            .forms-grid,
            .directory-grid {
                grid-template-columns: 1fr;
            }

            .profile-settings-toggle {
                position: static;
                transform: none;
                margin-top: 0.65rem;
            }

            .profile-settings-toggle:hover,
            .profile-settings-toggle:focus-visible {
                transform: none;
            }

            .profile-avatar-save {
                position: static;
                margin-top: 0.8rem;
                justify-self: center;
            }

            .profile-settings-modal {
                width: calc(100vw - 1rem);
                max-height: calc(100vh - 1rem);
            }

            .team-item {
                grid-template-columns: auto 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="site-navbar">
        <a class="brand-link" href="/">
            <span>AlphaBit OpenML</span>
            <img class="brand-logo" src="/assets/images/ai_star_alpha.png" alt="AlphaBit logo">
        </a>
        <nav class="nav-links" aria-label="Profile navigation">
            <a class="nav-link" href="/model/<?php echo htmlspecialchars($seasonPath, ENT_QUOTES, 'UTF-8'); ?>/overview">ML Model</a>
            <a class="nav-link" href="/model/<?php echo htmlspecialchars($seasonPath, ENT_QUOTES, 'UTF-8'); ?>/training">Training Data</a>
            <a class="nav-link" href="/model/<?php echo htmlspecialchars($seasonPath, ENT_QUOTES, 'UTF-8'); ?>/online_training_ml">Online Training ML</a>
            <?php if ($isAdmin): ?>
                <a class="nav-link" href="/admin/model-pages">Admin Panel</a>
            <?php endif; ?>
        </nav>
        <div class="nav-actions">
            <a class="logout-btn" href="/logout">Logout</a>
        </div>
    </header>

    <main class="profile-shell">
        <?php if (!$dbReady): ?>
            <section class="flash-stack">
                <p class="flash-msg bad">Database connection failed. Profile save features are currently unavailable.</p>
            </section>
        <?php endif; ?>

        <?php if (is_array($flash) && (count($flash['success']) > 0 || count($flash['error']) > 0)): ?>
            <section class="flash-stack">
                <?php foreach ($flash['success'] as $message): ?>
                    <p class="flash-msg ok"><?php echo htmlspecialchars((string) $message, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endforeach; ?>
                <?php foreach ($flash['error'] as $message): ?>
                    <p class="flash-msg bad"><?php echo htmlspecialchars((string) $message, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>

        <section class="profile-card" id="profile-top-card">
            <form id="profile-avatar-upload-form" class="profile-avatar-upload" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="profile_action" value="upload_profile_picture">
                <input type="hidden" name="redirect_anchor" value="profile-top-card">
                <input id="profile-avatar-input" class="profile-avatar-input" type="file" name="team_image" accept=".jpg,.jpeg,.png,.webp,.gif">
                <button id="profile-avatar-trigger" class="profile-avatar-trigger" type="button" aria-label="Upload profile picture">
                    <img id="profile-avatar-image" class="profile-avatar" src="<?php echo htmlspecialchars($profileImagePath, ENT_QUOTES, 'UTF-8'); ?>" alt="Team profile picture">
                    <span id="profile-avatar-hint" class="avatar-upload-hint">Upload</span>
                </button>
                <button id="profile-avatar-save-btn" class="primary-btn profile-avatar-save" type="submit">Save Changes</button>
            </form>
            <div>
                <h1 class="profile-name">
                    <?php echo htmlspecialchars($teamName, ENT_QUOTES, 'UTF-8'); ?>
                    <?php if ($profileFtcNumber !== ''): ?>
                        <span class="team-number-inline"># FTC <?php echo htmlspecialchars($profileFtcNumber, ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php endif; ?>
                    <?php if ($profileVerified): ?>
                        <span class="verify-pill ok">Verified FTC Team</span>
                    <?php else: ?>
                        <span class="verify-pill pending">Verification Pending</span>
                    <?php endif; ?>
                </h1>
                <p class="profile-subtext"><?php echo htmlspecialchars($profileDescriptionDisplay, ENT_QUOTES, 'UTF-8'); ?></p>
                <p class="profile-meta">
                    Account: <?php echo htmlspecialchars($currentUserEmail, ENT_QUOTES, 'UTF-8'); ?> |
                    Last profile update: <?php echo htmlspecialchars($profileUpdatedLabel, ENT_QUOTES, 'UTF-8'); ?>
                </p>
            </div>
            <button
                id="profile-settings-toggle"
                class="secondary-btn profile-settings-toggle"
                type="button"
                aria-expanded="false"
                aria-controls="profile-edit">Settings</button>
        </section>

        <section class="panel-card">
            <div class="panel-header">
                <h2 class="panel-title">Quick Actions</h2>
            </div>
            <div class="quick-links-row">
                <a id="open-settings-link" class="chip-link" href="#profile-edit">Edit Team Profile</a>
                <a class="chip-link" href="#support-create">Create Support Ticket</a>
                <a class="chip-link" href="#support-list">View Support Tickets</a>
                <a class="chip-link" href="#verified-teams">Verified FTC Teams</a>
            </div>
        </section>

        <div id="profile-settings-backdrop" class="profile-settings-backdrop is-hidden" aria-hidden="true"></div>
        <section class="panel-card profile-settings-modal is-hidden" id="profile-edit" role="dialog" aria-modal="true" aria-labelledby="profile-settings-title">
            <div class="panel-header">
                <h2 class="panel-title" id="profile-settings-title">Team Profile Settings</h2>
                <button id="profile-settings-close" class="secondary-btn settings-close-btn" type="button" aria-label="Close settings">Close</button>
            </div>
            <div class="forms-grid single">
                <form class="form-card" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="profile_action" value="update_description">
                    <input type="hidden" name="redirect_anchor" value="profile-edit">

                    <label for="team-description">Team Description</label>
                    <textarea id="team-description" name="team_description" maxlength="1200"><?php echo htmlspecialchars($profileDescription, ENT_QUOTES, 'UTF-8'); ?></textarea>

                    <label for="ftc-team-number">FTC Team Number (optional)</label>
                    <input id="ftc-team-number" type="text" name="ftc_team_number" value="<?php echo htmlspecialchars($profileFtcNumber, ENT_QUOTES, 'UTF-8'); ?>" maxlength="32">

                    <label for="contact-email">Contact Email (for team connection)</label>
                    <input id="contact-email" type="email" name="contact_email" value="<?php echo htmlspecialchars($profileContactEmail, ENT_QUOTES, 'UTF-8'); ?>" maxlength="190">

                    <button class="primary-btn" type="submit">Save Changes</button>
                </form>
            </div>
        </section>

        <section class="panel-card" id="support-create">
            <div class="panel-header">
                <h2 class="panel-title">Support Ticket System</h2>
            </div>
            <form class="form-card" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="profile_action" value="create_ticket">
                <input type="hidden" name="redirect_anchor" value="support-list">

                <label for="ticket-subject">Ticket Subject</label>
                <input id="ticket-subject" type="text" name="ticket_subject" maxlength="160" required>

                <label for="ticket-priority">Priority</label>
                <select id="ticket-priority" name="ticket_priority">
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="low">Low</option>
                </select>

                <label for="ticket-message">Issue Details</label>
                <textarea id="ticket-message" name="ticket_message" maxlength="4000" required></textarea>

                <div class="quick-links-row">
                    <button class="primary-btn" type="submit">Create Support Ticket</button>
                    <a class="secondary-btn" href="#support-list">View Support Tickets</a>
                </div>
            </form>
        </section>

        <section class="panel-card" id="support-list">
            <div class="panel-header">
                <h2 class="panel-title">My Support Tickets</h2>
            </div>
            <?php if (count($myTickets) === 0): ?>
                <p class="empty-state">No support tickets yet. Create one when your team needs help.</p>
            <?php else: ?>
                <div class="ticket-list">
                    <?php foreach ($myTickets as $ticket): ?>
                        <?php
                        $ticketStatus = isset($ticket['status']) ? (string) $ticket['status'] : 'open';
                        $ticketPriority = isset($ticket['priority']) ? strtoupper((string) $ticket['priority']) : 'MEDIUM';
                        ?>
                        <article class="ticket-item">
                            <div class="ticket-head">
                                <h3 class="ticket-subject">
                                    #<?php echo (int) ($ticket['id'] ?? 0); ?> -
                                    <?php echo htmlspecialchars((string) ($ticket['subject'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                </h3>
                                <span class="status-pill <?php echo htmlspecialchars($ticketStatus, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars(strtoupper(str_replace('_', ' ', $ticketStatus)), ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </div>
                            <p class="ticket-meta">
                                Priority: <?php echo htmlspecialchars($ticketPriority, ENT_QUOTES, 'UTF-8'); ?> |
                                Created: <?php echo htmlspecialchars((string) ($ticket['created_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?> |
                                Updated: <?php echo htmlspecialchars((string) ($ticket['updated_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                            <p class="ticket-message"><?php echo htmlspecialchars((string) ($ticket['message'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="panel-card" id="verified-teams">
            <div class="panel-header">
                <h2 class="panel-title">Verified FTC Teams (Connect Suggestions)</h2>
            </div>
            <?php if (count($verifiedTeams) === 0): ?>
                <p class="empty-state">No verified FTC teams are visible yet.</p>
            <?php else: ?>
                <div class="team-grid">
                    <?php foreach ($verifiedTeams as $team): ?>
                        <?php
                        $teamConnectEmail = (string) ($team['contact_email'] ?? '');
                        $connectIsValid = filter_var($teamConnectEmail, FILTER_VALIDATE_EMAIL);
                        ?>
                        <article class="team-item is-verified">
                            <img class="team-avatar" src="<?php echo htmlspecialchars((string) $team['image_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="Verified team avatar">
                            <div>
                                <h3 class="team-name"><?php echo htmlspecialchars((string) ($team['team_name'] ?? 'FTC Team'), ENT_QUOTES, 'UTF-8'); ?></h3>
                                <p class="team-meta">
                                    <?php
                                    $ftcLabel = trim((string) ($team['ftc_team_number'] ?? ''));
                                    echo ($ftcLabel !== '')
                                        ? 'FTC #' . htmlspecialchars($ftcLabel, ENT_QUOTES, 'UTF-8')
                                        : 'FTC team profile';
                                    ?>
                                </p>
                                <p class="team-description">
                                    <?php
                                    $teamDesc = trim((string) ($team['description'] ?? ''));
                                    echo htmlspecialchars($teamDesc !== '' ? $teamDesc : 'No description shared yet.', ENT_QUOTES, 'UTF-8');
                                    ?>
                                </p>
                            </div>
                            <?php if ($connectIsValid): ?>
                                <a class="secondary-btn" href="mailto:<?php echo htmlspecialchars($teamConnectEmail, ENT_QUOTES, 'UTF-8'); ?>">Connect</a>
                            <?php else: ?>
                                <span class="status-pill">No Contact</span>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="panel-card" id="team-directory">
            <div class="panel-header">
                <h2 class="panel-title">Registered FTC Teams Directory</h2>
            </div>
            <?php if (count($directoryTeams) === 0): ?>
                <p class="empty-state">No teams are currently registered.</p>
            <?php else: ?>
                <div class="directory-grid">
                    <?php foreach ($directoryTeams as $team): ?>
                        <?php
                        $isVerifiedTeam = ((int) ($team['is_verified'] ?? 0) === 1);
                        $directoryEmail = (string) ($team['contact_email'] ?? '');
                        ?>
                        <article class="team-item <?php echo $isVerifiedTeam ? 'is-verified' : ''; ?>">
                            <img class="team-avatar" src="<?php echo htmlspecialchars((string) $team['image_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="Team avatar">
                            <div>
                                <h3 class="team-name"><?php echo htmlspecialchars((string) ($team['team_name'] ?? 'FTC Team'), ENT_QUOTES, 'UTF-8'); ?></h3>
                                <p class="team-meta">
                                    <?php if ($isVerifiedTeam): ?>
                                        Verified FTC team
                                    <?php else: ?>
                                        Verification pending
                                    <?php endif; ?>
                                    <?php if (!empty($team['ftc_team_number'])): ?>
                                        | FTC #<?php echo htmlspecialchars((string) $team['ftc_team_number'], ENT_QUOTES, 'UTF-8'); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <?php if (filter_var($directoryEmail, FILTER_VALIDATE_EMAIL) && $directoryEmail !== $currentUserEmail): ?>
                                <a class="secondary-btn" href="mailto:<?php echo htmlspecialchars($directoryEmail, ENT_QUOTES, 'UTF-8'); ?>">Connect</a>
                            <?php else: ?>
                                <span class="status-pill"><?php echo $isVerifiedTeam ? 'Visible' : 'Pending'; ?></span>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <?php if ($isAdmin): ?>
            <section class="panel-card" id="verification-admin">
                <div class="panel-header">
                    <h2 class="panel-title">Admin Team Verification</h2>
                </div>
                <?php if (count($pendingTeams) === 0): ?>
                    <p class="empty-state">All listed teams are already verified.</p>
                <?php else: ?>
                    <div class="admin-grid">
                        <?php foreach ($pendingTeams as $pending): ?>
                            <article class="admin-item">
                                <div>
                                    <strong><?php echo htmlspecialchars((string) ($pending['team_name'] ?? 'FTC Team'), ENT_QUOTES, 'UTF-8'); ?></strong><br>
                                    <span class="team-meta"><?php echo htmlspecialchars((string) ($pending['user_email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <form method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="profile_action" value="verify_team">
                                    <input type="hidden" name="redirect_anchor" value="verification-admin">
                                    <input type="hidden" name="target_email" value="<?php echo htmlspecialchars((string) ($pending['user_email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="verify_value" value="1">
                                    <button class="primary-btn" type="submit">Mark Verified</button>
                                </form>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="panel-header" style="margin-top: 0.35rem;">
                    <h3 class="panel-title" style="font-size: 0.95rem;">Verified Teams (Deverify)</h3>
                </div>
                <?php if (count($adminVerifiedTeams) === 0): ?>
                    <p class="empty-state">No verified teams to deverify.</p>
                <?php else: ?>
                    <div class="admin-grid">
                        <?php foreach ($adminVerifiedTeams as $verifiedTeam): ?>
                            <article class="admin-item">
                                <div>
                                    <strong><?php echo htmlspecialchars((string) ($verifiedTeam['team_name'] ?? 'FTC Team'), ENT_QUOTES, 'UTF-8'); ?></strong><br>
                                    <span class="team-meta">
                                        <?php echo htmlspecialchars((string) ($verifiedTeam['user_email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                        <?php if (!empty($verifiedTeam['ftc_team_number'])): ?>
                                            | FTC #<?php echo htmlspecialchars((string) $verifiedTeam['ftc_team_number'], ENT_QUOTES, 'UTF-8'); ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <form method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="profile_action" value="verify_team">
                                    <input type="hidden" name="redirect_anchor" value="verification-admin">
                                    <input type="hidden" name="target_email" value="<?php echo htmlspecialchars((string) ($verifiedTeam['user_email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="verify_value" value="0">
                                    <button class="secondary-btn" type="submit">Deverify</button>
                                </form>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <section class="panel-card" id="admin-tickets">
                <div class="panel-header">
                    <h2 class="panel-title">Admin Ticket Queue</h2>
                </div>
                <?php if (count($allTickets) === 0): ?>
                    <p class="empty-state">No support tickets in queue.</p>
                <?php else: ?>
                    <div class="ticket-list">
                        <?php foreach ($allTickets as $ticket): ?>
                            <article class="ticket-item">
                                <div class="ticket-head">
                                    <h3 class="ticket-subject">
                                        #<?php echo (int) ($ticket['id'] ?? 0); ?> -
                                        <?php echo htmlspecialchars((string) ($ticket['subject'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                    </h3>
                                    <span class="status-pill <?php echo htmlspecialchars((string) ($ticket['status'] ?? 'open'), ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars(strtoupper(str_replace('_', ' ', (string) ($ticket['status'] ?? 'open'))), ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </div>
                                <p class="ticket-meta">
                                    Team: <?php echo htmlspecialchars((string) ($ticket['team_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?> |
                                    <?php echo htmlspecialchars((string) ($ticket['user_email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?> |
                                    Priority: <?php echo htmlspecialchars(strtoupper((string) ($ticket['priority'] ?? 'medium')), ENT_QUOTES, 'UTF-8'); ?> |
                                    Created: <?php echo htmlspecialchars((string) ($ticket['created_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                                <form class="quick-links-row" method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="profile_action" value="update_ticket_status">
                                    <input type="hidden" name="redirect_anchor" value="admin-tickets">
                                    <input type="hidden" name="ticket_id" value="<?php echo (int) ($ticket['id'] ?? 0); ?>">
                                    <select name="ticket_status" style="max-width: 210px;">
                                        <?php
                                        $statusOptions = ['open', 'in_progress', 'resolved', 'closed'];
                                        $currentStatus = (string) ($ticket['status'] ?? 'open');
                                        foreach ($statusOptions as $statusOption):
                                            $selected = ($currentStatus === $statusOption) ? 'selected' : '';
                                            ?>
                                            <option value="<?php echo htmlspecialchars($statusOption, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selected; ?>>
                                                <?php echo htmlspecialchars(strtoupper(str_replace('_', ' ', $statusOption)), ENT_QUOTES, 'UTF-8'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="secondary-btn" type="submit">Update Status</button>
                                </form>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>

    <script>
        (function () {
            var settingsToggle = document.getElementById('profile-settings-toggle');
            var settingsPanel = document.getElementById('profile-edit');
            var settingsBackdrop = document.getElementById('profile-settings-backdrop');
            var settingsCloseButton = document.getElementById('profile-settings-close');
            var openSettingsLink = document.getElementById('open-settings-link');
            var avatarTrigger = document.getElementById('profile-avatar-trigger');
            var avatarInput = document.getElementById('profile-avatar-input');
            var avatarImage = document.getElementById('profile-avatar-image');
            var avatarSaveButton = document.getElementById('profile-avatar-save-btn');
            var avatarHint = document.getElementById('profile-avatar-hint');

            function showSettingsPanel() {
                if (!settingsPanel || !settingsToggle) {
                    return;
                }
                settingsPanel.classList.remove('is-hidden');
                if (settingsBackdrop) {
                    settingsBackdrop.classList.remove('is-hidden');
                }
                document.body.classList.add('settings-modal-open');
                settingsToggle.setAttribute('aria-expanded', 'true');
                settingsToggle.textContent = 'Hide Settings';
            }

            function hideSettingsPanel() {
                if (!settingsPanel || !settingsToggle) {
                    return;
                }
                settingsPanel.classList.add('is-hidden');
                if (settingsBackdrop) {
                    settingsBackdrop.classList.add('is-hidden');
                }
                document.body.classList.remove('settings-modal-open');
                settingsToggle.setAttribute('aria-expanded', 'false');
                settingsToggle.textContent = 'Settings';
            }

            if (settingsToggle && settingsPanel) {
                settingsToggle.addEventListener('click', function () {
                    if (settingsPanel.classList.contains('is-hidden')) {
                        showSettingsPanel();
                    } else {
                        hideSettingsPanel();
                    }
                });
            }

            if (openSettingsLink) {
                openSettingsLink.addEventListener('click', function (event) {
                    if (!settingsPanel) {
                        return;
                    }
                    event.preventDefault();
                    showSettingsPanel();
                });
            }

            if (settingsBackdrop) {
                settingsBackdrop.addEventListener('click', hideSettingsPanel);
            }

            if (settingsCloseButton) {
                settingsCloseButton.addEventListener('click', hideSettingsPanel);
            }

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && settingsPanel && !settingsPanel.classList.contains('is-hidden')) {
                    hideSettingsPanel();
                }
            });

            if (window.location.hash === '#profile-edit') {
                showSettingsPanel();
            }

            if (avatarSaveButton) {
                avatarSaveButton.classList.remove('is-visible');
            }

            if (avatarTrigger && avatarInput) {
                avatarTrigger.addEventListener('click', function () {
                    avatarInput.click();
                });
            }

            if (avatarInput) {
                avatarInput.addEventListener('change', function () {
                    var file = avatarInput.files && avatarInput.files[0] ? avatarInput.files[0] : null;
                    if (!file) {
                        if (avatarSaveButton) {
                            avatarSaveButton.classList.remove('is-visible');
                        }
                        if (avatarHint) {
                            avatarHint.textContent = 'Upload';
                        }
                        return;
                    }

                    if (avatarHint) {
                        avatarHint.textContent = 'Ready';
                    }
                    if (avatarSaveButton) {
                        avatarSaveButton.classList.add('is-visible');
                    }
                    if (avatarImage && window.URL && typeof window.URL.createObjectURL === 'function') {
                        avatarImage.src = window.URL.createObjectURL(file);
                    }
                });
            }
        })();
    </script>

    <?php include_once __DIR__ . '/../assets/includes/season_switcher.php'; ?>
</body>
</html>
