-- database/seed.sql
-- Demo data to make the project immediately testable after importing schema.sql.
-- IMPORTANT: This file is for local/demo use only.

USE database_labo;

-- Re-run safe: wipe in dependency order
DELETE FROM comments;
DELETE FROM posts;
DELETE FROM users;

-- Default accounts
-- Admin credentials (for demo):
--   username: admin
--   password: Admin123!
-- Normal user credentials (for demo):
--   username: user
--   password: User123!
-- Extra demo users (super simple):
--   username: user2 / user3 / user4
--   password: password

-- Admin
INSERT INTO users (user_name, email, phone, role, password_hash)
VALUES
  ('admin', 'admin@example.com', NULL, 'admin', '$2y$10$crz4bec6iD/XuQTBq8uy/ufwhUkr76aUjKN1RSKoVY0EPlPMWpxJq');
SET @admin_id = LAST_INSERT_ID();

-- Normal user
INSERT INTO users (user_name, email, phone, role, password_hash)
VALUES
  ('user', 'user@example.com', NULL, 'user', '$2y$10$XdtSTwZujMlqzChewCvtoe0aaZnbjrwaAjNHOek08QHIJ8seN15zy');
SET @user_id = LAST_INSERT_ID();

-- Extra users (password_hash('password') generated with bcrypt / PHP-compatible prefix)
INSERT INTO users (user_name, email, phone, role, password_hash)
VALUES
  ('user2', 'user2@example.com', NULL, 'user', '$2y$10$RaRfiD8NUJThJnOdiSo.EuEEZtz7fUva2xRuzYgoyPPlhSHh9.kqy');
SET @user2_id = LAST_INSERT_ID();

INSERT INTO users (user_name, email, phone, role, password_hash)
VALUES
  ('user3', 'user3@example.com', NULL, 'user', '$2y$10$vcVIQctD9Lb.Q6hLeI4LdekbzWz32UhsrbLB7o5fuipQsoMNItMPW');
SET @user3_id = LAST_INSERT_ID();

INSERT INTO users (user_name, email, phone, role, password_hash)
VALUES
  ('user4', 'user4@example.com', NULL, 'user', '$2y$10$2pnWkyY8L/wMKh0Z/XNhfuIBCx1FTW2zNC1B1Weo.a8wGxRL/H0jW');
SET @user4_id = LAST_INSERT_ID();


-- Posts: keep enough published posts for pagination (perPage=6) + 1 draft.
-- Use different published_at timestamps to avoid non-deterministic ordering.
INSERT INTO posts (user_id, post_title, post_body, status, published_at)
VALUES
  (
    @admin_id,
    'Welcome to DATA Labo',
    'Καλωσήρθες στο DATA Labo!\n'
    'Αυτό το mini blog είναι ένα καθαρό παράδειγμα MVC-ish δομής με PHP.\n'
    'Θα δεις public λίστα posts με pagination και αναλυτική σελίδα post.\n'
    'Τα σχόλια είναι ανοιχτά χωρίς login για να εξασκηθείς σε validation.\n'
    'Το admin/dashboard έχει CRUD για posts και publish/draft.\n'
    'Στόχος: καθαρός κώδικας, σωστά redirects και ασφαλή queries (PDO prepared statements).',
    'published',
    NOW() - INTERVAL 60 MINUTE
  ),
  (
    @user_id,
    'PDO prepared statements: το βασικό safety net',
    'Όταν χτίζεις SQL με string concatenation, ρισκάρεις SQL injection.\n'
    'Με PDO prepared statements περνάς δεδομένα ως params (bind) και όχι ως SQL.\n'
    'Κράτα ERRMODE_EXCEPTION για να μην κρύβεις προβλήματα.\n'
    'Να επιστρέφεις μόνο τα πεδία που χρειάζεσαι, όχι SELECT *.\n'
    'Για writes που έχουν πολλά βήματα, βάλε transactions.\n'
    'Και πάντα validation/escaping στην έξοδο (HTML).',
    'published',
    NOW() - INTERVAL 55 MINUTE
  ),
  (
    @admin_id,
    'Draft example',
    'Αυτό είναι draft και δεν πρέπει να φαίνεται στο public.\n'
    'Χρησιμοποίησέ το για να τεστάρεις το publish flow.\n'
    'Στο dashboard, κάνε edit και άλλαξε status σε published.\n'
    'Έλεγξε ότι το public query σου φέρνει ΜΟΝΟ published.\n'
    'Κράτα τον κώδικα απλό: status enum/string και published_at NULL για drafts.\n'
    'Μετά το publish, κάνε redirect + flash message.',
    'draft',
    NULL
  ),
  (
    @user_id,
    'Pagination demo #1 — Σταθερό ORDER BY',
    'Τα duplicates στην pagination σχεδόν πάντα είναι θέμα ordering.\n'
    'Αν πολλά rows έχουν ίδιο created_at, το ORDER BY δεν είναι deterministic.\n'
    'Λύση: ORDER BY created_at DESC, post_id DESC.\n'
    'Και το OFFSET πρέπει να είναι (page - 1) * perPage.\n'
    'Το COUNT query πρέπει να έχει ίδιο WHERE με το SELECT.\n'
    'Έτσι κάθε σελίδα δείχνει μοναδικά rows.',
    'published',
    NOW() - INTERVAL 50 MINUTE
  ),
  (
    @user_id,
    'Pagination demo #2 — COUNT και filters',
    'Αν στο public δείχνεις μόνο published, τότε και το COUNT πρέπει να μετράει μόνο published.\n'
    'Διαφορετικά θα έχεις λάθος totalPages και περίεργο pagination UI.\n'
    'Κράτα τα φίλτρα σε μία κοινή συνάρτηση/μεταβλητή για να μην ξεφεύγουν.\n'
    'Πρόσεξε το search: ίδιο WHERE σε COUNT/SELECT.\n'
    'Να κάνεις sanitize το query string και να κρατάς τα params.\n'
    'Έτσι αποφεύγεις “σπασμένα” links.',
    'published',
    NOW() - INTERVAL 45 MINUTE
  ),
  (
    @user_id,
    'Pagination demo #3 — Performance basics',
    'Για λίγα δεδομένα όλα φαίνονται γρήγορα, αλλά θες σωστή βάση.\n'
    'Βάλε index σε status, created_at, user_id (ανάλογα με queries).\n'
    'Μην φορτώνεις τεράστια bodies στη λίστα αν δεν τα χρειάζεσαι.\n'
    'Μπορείς να δείχνεις excerpt (π.χ. πρώτες 200 chars).\n'
    'Χρησιμοποίησε LIMIT/OFFSET σωστά.\n'
    'Και κράτα τα query params prepared.',
    'published',
    NOW() - INTERVAL 40 MINUTE
  ),
  (
    @user_id,
    'Pagination demo #4 — Bootstrap UI hygiene',
    'Το Bootstrap βοηθάει να έχεις γρήγορα αξιοπρεπές UI.\n'
    'Κράτα consistent spacing (container, row, col) και όχι inline styles παντού.\n'
    'Βάλε ένα app.css και φόρτωσέ το από το _header.php.\n'
    'Χρησιμοποίησε components: alerts για flash messages, cards για posts.\n'
    'Για forms: proper labels, validation hints, small help text.\n'
    'Η καθαρή UI δομή κάνει και το debugging ευκολότερο.',
    'published',
    NOW() - INTERVAL 35 MINUTE
  ),
  (
    @user_id,
    'Pagination demo #5 — Forms & validation',
    'Στα σχόλια χωρίς login, το validation είναι must.\n'
    'Έλεγξε required πεδία, μέγιστο μήκος, και email format όπου υπάρχει.\n'
    'Κάνε trim σε inputs και απόφυγε “κενά” σχόλια.\n'
    'Για output, κάνε escaping (htmlspecialchars) για XSS προστασία.\n'
    'Μην αποθηκεύεις HTML αν δεν το χρειάζεσαι.\n'
    'Κράτα τα error messages φιλικά και συγκεκριμένα.',
    'published',
    NOW() - INTERVAL 30 MINUTE
  ),
  (
    @user_id,
    'Pagination demo #6 — Auth & roles',
    'Login σημαίνει session management και σωστά redirects.\n'
    'Μετά το login, κάνε redirect στο dashboard και δείξε flash success.\n'
    'Για authorization: user μπορεί edit/delete μόνο τα δικά του posts.\n'
    'Admin έχει επιπλέον εργαλεία: delete comments και create users/admin.\n'
    'Μην εμπιστεύεσαι hidden fields για user_id—πάρε το από τη session.\n'
    'Και πάντα έλεγξε role πριν εκτελέσεις admin actions.',
    'published',
    NOW() - INTERVAL 25 MINUTE
  ),

  -- New posts for user2
  (
    @user2_id,
    'REST APIs: τα 5 πράγματα που μετράνε',
    'REST δεν είναι “μόνο JSON”. Είναι κανόνες που βοηθούν στο design.\n'
    'Χρησιμοποίησε σωστά HTTP verbs: GET/POST/PATCH/DELETE.\n'
    'Κράτα consistent status codes (200/201/204/400/401/403/404/422).\n'
    'Βάλε validation errors σε σταθερό format.\n'
    'Κάνε pagination σε endpoints με limit/offset ή cursor.\n'
    'Και τεκμηρίωση (OpenAPI/Swagger) από νωρίς.',
    'published',
    NOW() - INTERVAL 20 MINUTE
  ),
  (
    @user2_id,
    'Docker basics για local development',
    'Ο στόχος του Docker εδώ είναι απλός: ίδια βάση/ρυθμίσεις σε κάθε PC.\n'
    'Ένα docker-compose με mysql + phpmyadmin συνήθως αρκεί.\n'
    'Μην βάζεις credentials hardcoded σε repo—χρησιμοποίησε .env.\n'
    'Κράτα volumes για να μη χάνεις δεδομένα σε restart.\n'
    'Δώσε ξεκάθαρες οδηγίες στο README (up, down, migrate/seed).\n'
    'Έτσι μειώνεις “δουλεύει μόνο στο δικό μου μηχάνημα”.',
    'published',
    NOW() - INTERVAL 19 MINUTE
  ),

  -- New posts for user3
  (
    @user3_id,
    'Git commits που βοηθάνε πραγματικά',
    'Το commit message είναι documentation.\n'
    'Χρησιμοποίησε προστακτική: “Add …”, “Fix …”, “Refactor …”.\n'
    'Μην ανακατεύεις formatting με logic αλλαγές στο ίδιο commit.\n'
    'Κράτα τα commits μικρά ώστε να μπορείς να κάνεις revert εύκολα.\n'
    'Πριν push, τρέξε τα βασικά checks (lint/tests αν υπάρχουν).\n'
    'Το history σου γίνεται εργαλείο, όχι βάρος.',
    'published',
    NOW() - INTERVAL 18 MINUTE
  ),
  (
    @user3_id,
    'SQL indexes: πότε αξίζουν',
    'Index δεν είναι “πάντα καλό”. Έχει κόστος σε inserts/updates.\n'
    'Βάλε index εκεί που φιλτράρεις συχνά: status, user_id, created_at.\n'
    'Για sorting/pagination, ένα index στο (created_at, post_id) βοηθάει.\n'
    'Χρησιμοποίησε EXPLAIN για να δεις αν το query κάνει full scan.\n'
    'Μην κάνεις over-indexing χωρίς λόγο.\n'
    'Στόχος: γρήγορα reads χωρίς να σκοτώνεις τα writes.',
    'published',
    NOW() - INTERVAL 17 MINUTE
  ),

  -- New posts for user4
  (
    @user4_id,
    'Frontend: μικρές συνήθειες που ανεβάζουν ποιότητα',
    'Βάλε semantic HTML (header/main/article/footer) για καλύτερη δομή.\n'
    'Κράτα components/partials καθαρά: navbar, footer, alerts.\n'
    'Μην αντιγράφεις markup—χρησιμοποίησε partials.\n'
    'Πρόσεξε contrast και μεγέθη κειμένου.\n'
    'Τα forms να έχουν labels και σωστά names.\n'
    'Η προσβασιμότητα δεν είναι “extra”—είναι baseline.',
    'published',
    NOW() - INTERVAL 16 MINUTE
  ),
  (
    @user4_id,
    'Security checklist για mini projects',
    'Πάντα prepared statements για DB.\n'
    'Escape στην έξοδο (htmlspecialchars) για XSS.\n'
    'CSRF tokens σε POST actions (create/edit/delete).\n'
    'Sessions: regenerate id μετά το login.\n'
    'Role checks σε κάθε protected route.\n'
    'Και μην εμφανίζεις raw errors σε production (αλλά log).',
    'published',
    NOW() - INTERVAL 15 MINUTE
  );


-- Grab some post ids for comments (use deterministic ordering where relevant)
SELECT post_id INTO @p_welcome
FROM posts
WHERE post_title = 'Welcome to DATA Labo'
LIMIT 1;

SELECT post_id INTO @p_pdo
FROM posts
WHERE post_title = 'PDO prepared statements: το βασικό safety net'
LIMIT 1;

SELECT post_id INTO @p_rest
FROM posts
WHERE post_title = 'REST APIs: τα 5 πράγματα που μετράνε'
LIMIT 1;

SELECT post_id INTO @p_git
FROM posts
WHERE post_title = 'Git commits που βοηθάνε πραγματικά'
LIMIT 1;

-- A published post id (latest) for generic demo comments
SELECT post_id INTO @latest_published_post_id
FROM posts
WHERE status = 'published'
ORDER BY created_at DESC, post_id DESC
LIMIT 1;


-- Comments: no login required, but we'll simulate users commenting on others' posts
INSERT INTO comments (post_id, author_name, author_email, comment_body)
VALUES
  (@p_welcome, 'Visitor', 'visitor@example.com',
   'Ωραίο setup. Μου αρέσει που ξεχωρίζει public από dashboard και κρατάει απλά routes.'),
  (@p_welcome, 'User2', 'user2@example.com',
   'Το publish/draft flow είναι πολύ καλό για να μάθεις redirect + flash messages.'),
  (@p_pdo, 'User3', 'user3@example.com',
   'Συμφωνώ: prepared statements παντού και προσοχή στο escaping στην έξοδο.'),
  (@p_rest, 'User4', 'user4@example.com',
   'Καλή υπενθύμιση για status codes και consistent error format.'),
  (@p_git, 'User2', 'user2@example.com',
   'Τα μικρά commits σώζουν χρόνο όταν κάνεις revert ή ψάχνεις bug.'),
  (@latest_published_post_id, 'Another visitor', NULL,
   'Nice post! Καθαρό κείμενο και πρακτικές συμβουλές.');