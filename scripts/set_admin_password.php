<?php
$dbFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'database.sqlite';
if (! file_exists($dbFile)) {
    fwrite(STDERR, "Database file not found: $dbFile\n");
    exit(1);
}
try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $email = 'admin@example.com';
    $password = 'password';
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('UPDATE users SET password = :hash WHERE email = :email');
    $stmt->execute([':hash' => $hash, ':email' => $email]);
    echo "Updated password for $email\n";
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
    exit(1);
}
