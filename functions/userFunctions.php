<?php
// functions/userFunctions.php

require_once __DIR__ . '/genericFunctions.php'; // για startSession(), redirectTo(), setError()

function existsActiveUserSession(): bool
{
    return session_status() === PHP_SESSION_ACTIVE;
}

function ensureSession(): void
{
    if (!existsActiveUserSession()) {
        startSession();
    }
}

function existsLoggedUser(): bool
{
    ensureSession();
    return isset($_SESSION['user_id'], $_SESSION['loggedUserName'], $_SESSION['loggedUserRole']);
}

function currentUserId(): ?int
{
    return existsLoggedUser() ? (int)$_SESSION['user_id'] : null;
}

function currentUserName(): ?string
{
    return existsLoggedUser() ? (string)$_SESSION['loggedUserName'] : null;
}

function currentUserRole(): ?string
{
    return existsLoggedUser() ? (string)$_SESSION['loggedUserRole'] : null;
}

function isUserAdmin(): bool
{
    return currentUserRole() === 'admin';
}

function logUserIn(int $userId, string $userName, string $userRole): void
{
    ensureSession();

    // Prevent session fixation
    session_regenerate_id(true);

    $_SESSION['user_id'] = $userId;
    $_SESSION['loggedUserName'] = $userName;
    $_SESSION['loggedUserRole'] = $userRole;
}

/**
 * Proper logout: clears session array and destroys session cookie + session.
 */
function logUserOut(): void
{
    ensureSession();

    $_SESSION = [];

    // delete session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

/**
 * Guards (use these at top of protected pages)
 */
function requireLogin(): void
{
    if (!existsLoggedUser()) {
        setError('Please login first.');
        redirectTo('/User_Login.php');
    }
}

function requireAdmin(): void
{
    requireLogin();
    if (!isUserAdmin()) {
        setError('Admin access required.');
        redirectTo('/errorPage.php');
    }
}