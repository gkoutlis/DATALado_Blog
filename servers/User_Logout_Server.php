<?php
// servers/User_Logout_Server.php
// POST: logout; destroy session.
require_once __DIR__ . '/../functions/genericFunctions.php';
require_once __DIR__ . '/../functions/userFunctions.php';

startSession();

if (!isRequestMethodPost()) {
  setError('Invalid request method.');
  redirectTo('/errorPage.php');
}

csrf_verify_or_die();

logUserOut();

setSuccess('You have been logged out.');
redirectTo('/Posts_List.php');
