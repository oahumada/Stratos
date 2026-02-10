<?php
// Compact using existing llm_response without requiring Composer/vendor.
$root = realpath(__DIR__ . '/../../');

function parseEnvFile($path) {
    $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (! $lines) return [];
    $data = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' ) continue;
        if (strpos($line, '=') === false) continue;
        list($k,$v) = explode('=', $line, 2);
        $k = trim($k);
        $v = trim($v);
        $v = preg_replace('/^\"|\"$/', '', $v);
        $v = preg_replace("/^'|'$/", '', $v);
        $data[$k] = $v;
    }
    return $data;
}

$env = [];
if (file_exists($root . '/.env')) {
    $env = parseEnvFile($root . '/.env');
} elseif (file_exists($root . '/.env.example')) {
    $env = parseEnvFile($root . '/.env.example');
} elseif (file_exists($root . '/src/.env')) {
    $env = parseEnvFile($root . '/src/.env');
} elseif (file_exists($root . '/src/.env.example')) {
    $env = parseEnvFile($root . '/src/.env.example');
}

$dbConn = getenv('DB_CONNECTION') ?: ($env['DB_CONNECTION'] ?? 'mysql');
$dbHost = getenv('DB_HOST') ?: ($env['DB_HOST'] ?? '127.0.0.1');
$dbPort = getenv('DB_PORT') ?: ($env['DB_PORT'] ?? ($dbConn === 'mysql' ? '3306' : ($dbConn === 'pgsql' ? '5432' : null)));
$dbName = getenv('DB_DATABASE') ?: ($env['DB_DATABASE'] ?? null);
$dbUser = getenv('DB_USERNAME') ?: ($env['DB_USERNAME'] ?? null);
$dbPass = getenv('DB_PASSWORD') ?: ($env['DB_PASSWORD'] ?? null);

if (! $dbName) {
    fwrite(STDERR, "No DB_DATABASE found in .env; cannot continue.\n");
    exit(1);
}

try {
    if ($dbConn === 'sqlite') {
        $path = $dbName;
        if (! preg_match('#^/#', $path)) {
            $path = $root . '/' . $path;
        }
        $dsn = "sqlite:" . $path;
        $pdo = new PDO($dsn);
    } elseif ($dbConn === 'pgsql') {
        $dsn = "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName}";
        $pdo = new PDO($dsn, $dbUser, $dbPass);
    } else {
        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
} catch (Exception $e) {
    fwrite(STDERR, "DB connection failed: " . $e->getMessage() . "\n");
    exit(1);
}

$genId = 59;

$stmt = $pdo->prepare('SELECT llm_response, metadata FROM scenario_generations WHERE id = ?');
$stmt->execute([$genId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (! $row) {
    fwrite(STDERR, "ScenarioGeneration $genId not found.\n");
    exit(1);
}

$llmRaw = $row['llm_response'];
$metaRaw = $row['metadata'];

// Determine JSON text to compact
$jsonText = null;
if ($llmRaw === null || $llmRaw === '') {
    fwrite(STDERR, "No llm_response present for generation $genId.\n");
    exit(1);
}

$decoded = json_decode($llmRaw, true);
if (json_last_error() === JSON_ERROR_NONE) {
    $jsonText = json_encode($decoded, JSON_UNESCAPED_UNICODE);
} else {
    // llm_response might already be a plain string; use as-is
    $jsonText = $llmRaw;
}

$encoded = base64_encode($jsonText);

$cntStmt = $pdo->prepare('SELECT COUNT(*) as c FROM generation_chunks WHERE scenario_generation_id = ?');
$cntStmt->execute([$genId]);
$cntRow = $cntStmt->fetch(PDO::FETCH_ASSOC);
$chunkCount = intval($cntRow['c'] ?? 0);

$meta = null;
if ($metaRaw) {
    $m = json_decode($metaRaw, true);
    $meta = (json_last_error() === JSON_ERROR_NONE && is_array($m)) ? $m : [];
} else {
    $meta = [];
}

$meta['compacted'] = $encoded;
$meta['chunk_count'] = $chunkCount;
$meta['compacted_at'] = date('Y-m-d H:i:s');


    // Move compacted blob to dedicated column and keep metadata for other fields
    $meta['chunk_count'] = $chunkCount;
    $meta['compacted_at'] = date('Y-m-d H:i:s');
    $metaJson = json_encode($meta, JSON_UNESCAPED_UNICODE);

    $up = $pdo->prepare('UPDATE scenario_generations SET metadata = ?, compacted = ?, chunk_count = ?, compacted_at = ? WHERE id = ?');
    $up->execute([$metaJson, $encoded, $chunkCount, $meta['compacted_at'], $genId]);
echo "compacted base64 len: " . strlen($encoded) . "\n";
echo "decoded len: " . strlen($jsonText) . "\n";

exit(0);
