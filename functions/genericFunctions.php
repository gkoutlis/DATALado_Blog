<?php
function startSession(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}


function setFlash(string $type, string $message): void
{
    startSession();
    // type: 'success' | 'error' | 'info' | 'warning'
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    startSession();
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

// convenience wrappers
function setError(string $message): void
{
    setFlash('error', $message);
}

function setSuccess(string $message): void
{
    setFlash('success', $message);
}

function isRequestMethodPost(): bool {
    return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
}


function redirectTo(string $destinationFile): void
{
    // If it's already an absolute URL (http/https), keep as-is
    if (preg_match('~^https?://~i', $destinationFile)) {
        header("Location: {$destinationFile}");
        exit();
    }

    $script = $_SERVER['SCRIPT_NAME'] ?? '';

    // Compute base path up to "/public" (works for /DATALaboBlog/public/...)
    $publicPos = strpos($script, '/public/');
    if ($publicPos !== false) {
        $base = substr($script, 0, $publicPos + strlen('/public'));
    } else {
        // Fallback: directory of current script
        $base = rtrim(dirname($script), '/');
    }

    // Normalize destination
    if ($destinationFile === '') {
        $destinationFile = '/';
    }

    // Build final location
    if ($destinationFile[0] === '/') {
        $location = $base . $destinationFile;
    } else {
        $location = $base . '/' . $destinationFile;
    }

    // Avoid accidental double slashes (except after "http(s)://", which we already handled)
    $location = preg_replace('~/{2,}~', '/', $location);

    header("Location: {$location}");
    exit();
}



// ... (τα υπόλοιπα functions σου)

function csrf_token(): string
{
    startSession();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify_or_die(): void
{
    startSession();

    $sent = $_POST['csrf_token'] ?? '';
    $real = $_SESSION['csrf_token'] ?? '';

    if ($sent === '' || $real === '' || !hash_equals($real, $sent)) {
        http_response_code(403);
        setError('Security check failed (CSRF). Please try again.');
        redirectTo('/errorPage.php');
    }
}