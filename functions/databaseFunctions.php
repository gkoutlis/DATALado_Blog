<?php
// functions/databaseFunctions.php

function dbConfig(): array
{
    // Μπορείς να αλλάζεις εύκολα χωρίς να πειράζεις κώδικα.
    // Αργότερα μπορείς να τα πάρεις από env vars.
    return [
        'host' => 'localhost',
        'user' => 'root',
        'pass' => '',
        'name' => 'database_labo',
        'port' => 3306,
    ];
}

function connectToDatabase(): mysqli
{
    $cfg = dbConfig();

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $conn = new mysqli($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['name'], $cfg['port']);
    $conn->set_charset('utf8mb4');

    return $conn;
}

/**
 * Infer mysqli bind_param types for common PHP values.
 * i = int, d = double, s = string, b = blob
 */
function inferParamTypes(array $params): string
{
    $types = '';
    foreach ($params as $p) {
        if (is_int($p)) $types .= 'i';
        elseif (is_float($p)) $types .= 'd';
        elseif (is_null($p)) $types .= 's';      // NULL will be sent as string; ok for most cases
        else $types .= 's';
    }
    return $types;
}

/**
 * Execute a SELECT and return all rows as array<array>.
 */
function dbSelectAll(string $sql, array $params = [], ?string $types = null): array
{
    if (trim($sql) === '') {
        throw new InvalidArgumentException("Empty SELECT query.");
    }

    $conn = connectToDatabase();
    $stmt = $conn->prepare($sql);

    if (!empty($params)) {
        $types = $types ?? inferParamTypes($params);
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    $stmt->close();
    $conn->close();

    return $rows;
}

/**
 * Execute a SELECT and return a single row or null.
 */
function dbSelectOne(string $sql, array $params = [], ?string $types = null): ?array
{
    $rows = dbSelectAll($sql, $params, $types);
    return $rows[0] ?? null;
}

/**
 * Execute INSERT/UPDATE/DELETE.
 * Returns affected rows count.
 */
function dbExecute(string $sql, array $params = [], ?string $types = null): int
{
    if (trim($sql) === '') {
        throw new InvalidArgumentException("Empty query.");
    }

    $conn = connectToDatabase();
    $stmt = $conn->prepare($sql);

    if (!empty($params)) {
        $types = $types ?? inferParamTypes($params);
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $affected = $stmt->affected_rows;

    $stmt->close();
    $conn->close();

    return $affected;
}

/**
 * Legacy helper (avoid for new code).
 * Kept so your old seminar code doesn't break.
 */
function selectFromDb(string $sql): array
{
    return dbSelectAll($sql);
}