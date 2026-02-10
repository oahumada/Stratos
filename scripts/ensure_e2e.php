<?php
// Script to ensure an E2E admin user and a minimal scenario exist in the sqlite DB.
$dbFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'database.sqlite';
if (! file_exists($dbFile)) {
    fwrite(STDERR, "Database file not found: $dbFile\n");
    exit(1);
}

try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $now = date('Y-m-d H:i:s');
    $passwordHash = password_hash('password', PASSWORD_BCRYPT);

    // Ensure organization id=1 exists (if organizations table exists)
    $hasOrgs = (bool) $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='organizations'")->fetch();
    if ($hasOrgs) {
        $pdo->exec("INSERT OR IGNORE INTO organizations (id, name, created_at, updated_at) VALUES (1, 'E2E Test Org', '$now', '$now')");
    }

    // Ensure user
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => 'admin@example.com']);
    $userId = $stmt->fetchColumn();
    if (! $userId) {
        $insert = $pdo->prepare("INSERT INTO users (name, email, password, organization_id, role, created_at, updated_at) VALUES (:name, :email, :password, :org, :role, :now, :now)");
        $insert->execute([
            ':name' => 'E2E Admin',
            ':email' => 'admin@example.com',
            ':password' => $passwordHash,
            ':org' => 1,
            ':role' => 'admin',
            ':now' => $now,
        ]);
        $userId = $pdo->lastInsertId();
        echo "created user id=$userId\n";
    } else {
        echo "user exists id=$userId\n";
    }

    // Ensure minimal scenario
    $stmt = $pdo->query("SELECT id FROM scenarios WHERE code = 'e2e-1' LIMIT 1");
    $scId = $stmt->fetchColumn();
    if (! $scId) {
        $sql = "INSERT INTO scenarios (organization_id, name, code, start_date, end_date, horizon_months, fiscal_year, scope_type, owner_user_id, created_by, version_number, is_current_version, f, current_step, decision_status, execution_status, created_at, updated_at) VALUES (1, 'E2E Scenario', 'e2e-1', '2026-02-01', '2027-02-01', 12, 2026, 'organization_wide', :owner, :created_by, 1, 1, 'organization', 1, 'draft', 'not_started', :now, :now)";
        $ins = $pdo->prepare($sql);
        $ins->execute([':owner' => $userId, ':created_by' => $userId, ':now' => $now]);
        echo "inserted scenario id=" . $pdo->lastInsertId() . "\n";
    } else {
        echo "scenario exists id=$scId\n";
    }

    exit(0);

} catch (Throwable $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
    exit(1);
}
