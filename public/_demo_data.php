<?php
// UI-only demo data (θα αντικατασταθεί από DB αργότερα)

$demoUser = [
  'user_id' => 1,
  'user_name' => 'greg',
  'role' => 'admin', // άλλαξε σε 'user' για να δεις UI ως απλός user
  'is_logged_in' => true, // άλλαξε σε false για να δεις public-only navbar
];

$demoPosts = [
  [
    'post_id' => 101,
    'user_id' => 1,
    'author' => 'greg',
    'title' => 'My first published post',
    'excerpt' => 'Short excerpt for the first post...',
    'status' => 'published',
    'created_at' => '2026-02-20 10:15',
    'updated_at' => '2026-02-20 11:05',
  ],
  [
    'post_id' => 102,
    'user_id' => 1,
    'author' => 'greg',
    'title' => 'Draft post (not visible publicly)',
    'excerpt' => 'This is a draft...',
    'status' => 'draft',
    'created_at' => '2026-02-19 18:00',
    'updated_at' => '2026-02-19 18:10',
  ],
  [
    'post_id' => 103,
    'user_id' => 2,
    'author' => 'maria',
    'title' => 'Another published post',
    'excerpt' => 'Another short excerpt...',
    'status' => 'published',
    'created_at' => '2026-02-18 09:20',
    'updated_at' => '2026-02-18 09:20',
  ],
];

$demoComments = [
  101 => [
    [
      'comment_id' => 9001,
      'author_name' => 'Nikos',
      'author_email' => 'nikos@example.com',
      'body' => 'Nice post!',
      'created_at' => '2026-02-20 12:00',
    ],
    [
      'comment_id' => 9002,
      'author_name' => 'Eleni',
      'author_email' => null,
      'body' => 'Thanks for sharing.',
      'created_at' => '2026-02-20 12:30',
    ],
  ],
  103 => [
    [
      'comment_id' => 9003,
      'author_name' => 'Greg',
      'author_email' => 'greg@example.com',
      'body' => 'Welcome to the blog.',
      'created_at' => '2026-02-18 10:10',
    ],
  ],
];

$demoUsers = [
  ['user_id' => 1, 'user_name' => 'greg', 'email' => 'greg@example.com', 'role' => 'admin', 'created_at' => '2026-02-01'],
  ['user_id' => 2, 'user_name' => 'maria', 'email' => 'maria@example.com', 'role' => 'user', 'created_at' => '2026-02-05'],
];