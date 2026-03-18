<?php
declare(strict_types=1);

// public/servers/*.php
// Thin web-accessible wrapper for the real handler in /servers.
// These exist because the dev server runs with: php -S 127.0.0.1:8000 -t public

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$map = [
    'user_login.php'    => 'User_Login_Server.php',
    'user_logout.php'   => 'User_Logout_Server.php',
    'post_create.php'   => 'Post_Create_Server.php',
    'post_update.php'   => 'Post_Update_Server.php',
    'post_delete.php'   => 'Post_Delete_Server.php',
    'post_publish.php'  => 'Post_Publish_Server.php',
    'comment_create.php'=> 'Comment_Create_Server.php',
    'comment_delete.php'=> 'Comment_Delete_Server.php',
    'user_create.php'   => 'User_Create_Server.php',
];

$base = basename(__FILE__);
if (!isset($map[$base])) {
    http_response_code(500);
    exit('Handler mapping not found');
}

require __DIR__ . '/../../servers/' . $map[$base];
exit;
