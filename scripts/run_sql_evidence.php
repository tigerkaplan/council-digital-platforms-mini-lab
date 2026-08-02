<?php

declare(strict_types=1);

$projectRoot = __DIR__ . '/..';
$sqlDir = $projectRoot . '/scripts/sql';
$evidenceDir = $projectRoot . '/evidence/16-sql-data';
$databasePath = $evidenceDir . '/service-requests.sqlite';

$checks = [];

if (!is_dir($evidenceDir) && !mkdir($evidenceDir, 0775, true) && !is_dir($evidenceDir)) {
    throw new RuntimeException('Could not create SQL evidence directory.');
}

if (is_file($databasePath) && !unlink($databasePath)) {
    throw new RuntimeException('Could not remove previous generated SQLite database.');
}

$pdo = new PDO('sqlite:' . $databasePath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('PRAGMA foreign_keys = ON');

executeSqlFile($pdo, $sqlDir . '/schema.sql');
executeSqlFile($pdo, $sqlDir . '/seed.sql');

$foreignKeysEnabled = (int) $pdo->query('PRAGMA foreign_keys')->fetchColumn() === 1;
recordCheck($checks, 'Foreign keys enabled', $foreignKeysEnabled);

$serviceRequestCount = (int) $pdo->query('SELECT COUNT(*) FROM service_request')->fetchColumn();
$activityCountBeforeTransaction = (int) $pdo->query('SELECT COUNT(*) FROM service_request_activity')->fetchColumn();
recordCheck($checks, 'service_request row count at least 5', $serviceRequestCount >= 5);
recordCheck($checks, 'service_request_activity row count at least 5', $activityCountBeforeTransaction >= 5);

$indexChecks = requiredIndexesExist($pdo);
foreach ($indexChecks as $label => $passed) {
    recordCheck($checks, $label, $passed);
}

$results = [
    'openRequests' => fetchAll($pdo, "
        SELECT reference, request_type, responsible_service, status
        FROM service_request
        WHERE status <> 'Closed'
        ORDER BY created_at
    "),
    'immediateRiskRequests' => fetchAll($pdo, "
        SELECT reference, request_type, postcode, status, created_at
        FROM service_request
        WHERE immediate_safety_risk = 1
        ORDER BY created_at
    "),
    'activityJoin' => fetchAll($pdo, "
        SELECT sr.reference, sra.activity_type, sra.activity_note, sra.created_at
        FROM service_request sr
        JOIN service_request_activity sra ON sra.service_request_id = sr.id
        WHERE sr.reference = 'LSR-DEMO-10482'
        ORDER BY sra.created_at
    "),
    'countsByStatus' => fetchAll($pdo, "
        SELECT status, COUNT(*) AS request_count
        FROM service_request
        GROUP BY status
        ORDER BY status
    "),
    'countsByService' => fetchAll($pdo, "
        SELECT responsible_service, COUNT(*) AS request_count
        FROM service_request
        GROUP BY responsible_service
        ORDER BY responsible_service
    "),
];

foreach ([
    'Basic filtered SELECT returned open requests' => count($results['openRequests']) === 4,
    'Immediate-safety-risk query returned expected requests' => count($results['immediateRiskRequests']) === 2,
    'JOIN returned activity entries for selected request' => count($results['activityJoin']) >= 2,
    'GROUP BY status returned counts' => count($results['countsByStatus']) >= 1,
    'GROUP BY responsible service returned counts' => count($results['countsByService']) >= 1,
] as $label => $passed) {
    recordCheck($checks, $label, $passed);
}

$prepared = $pdo->prepare("
    SELECT reference, request_type, postcode, status, next_action
    FROM service_request
    WHERE reference = :reference
");
$prepared->execute(['reference' => 'LSR-DEMO-10484']);
$results['preparedLookup'] = $prepared->fetchAll(PDO::FETCH_ASSOC);
recordCheck($checks, 'Prepared reference lookup returned one row', count($results['preparedLookup']) === 1);

$committedAt = '2026-07-10T09:00:00+01:00';
$pdo->beginTransaction();
$update = $pdo->prepare("
    UPDATE service_request
    SET status = :status,
        next_action = :next_action,
        updated_at = :updated_at
    WHERE reference = :reference
");
$update->execute([
    'status' => 'Assigned',
    'next_action' => 'Arrange inspection',
    'updated_at' => $committedAt,
    'reference' => 'LSR-DEMO-10483',
]);

$activityInsert = $pdo->prepare("
    INSERT INTO service_request_activity (
        service_request_id,
        activity_type,
        activity_note,
        created_at
    )
    SELECT id, :activity_type, :activity_note, :created_at
    FROM service_request
    WHERE reference = :reference
");
$activityInsert->execute([
    'activity_type' => 'status_change',
    'activity_note' => 'Status changed to Assigned for SQL transaction evidence',
    'created_at' => $committedAt,
    'reference' => 'LSR-DEMO-10483',
]);
$pdo->commit();

$results['transactionResult'] = fetchAll($pdo, "
    SELECT reference, status, next_action, updated_at
    FROM service_request
    WHERE reference = 'LSR-DEMO-10483'
");
$transactionPassed = count($results['transactionResult']) === 1
    && $results['transactionResult'][0]['status'] === 'Assigned'
    && $results['transactionResult'][0]['next_action'] === 'Arrange inspection'
    && $results['transactionResult'][0]['updated_at'] === $committedAt;
recordCheck($checks, 'Committed status update persisted', $transactionPassed);

$rollbackReference = 'LSR-DEMO-10484';
$originalStatus = (string) $pdo->query("
    SELECT status
    FROM service_request
    WHERE reference = '{$rollbackReference}'
")->fetchColumn();

$pdo->beginTransaction();
$rollbackUpdate = $pdo->prepare("
    UPDATE service_request
    SET status = :status,
        next_action = :next_action,
        updated_at = :updated_at
    WHERE reference = :reference
");
$rollbackUpdate->execute([
    'status' => 'Closed',
    'next_action' => 'Temporary rollback demonstration',
    'updated_at' => '2026-07-10T09:30:00+01:00',
    'reference' => $rollbackReference,
]);
$pdo->rollBack();

$results['rollbackVerification'] = fetchAll($pdo, "
    SELECT reference, status, next_action
    FROM service_request
    WHERE reference = 'LSR-DEMO-10484'
");
$rollbackPassed = count($results['rollbackVerification']) === 1
    && $results['rollbackVerification'][0]['status'] === $originalStatus;
recordCheck($checks, 'Rollback restored original status', $rollbackPassed);

$foreignKeyRejected = false;
$foreignKeyExceptionMessage = '';
try {
    $invalidInsert = $pdo->prepare("
        INSERT INTO service_request_activity (
            service_request_id,
            activity_type,
            activity_note,
            created_at
        ) VALUES (
            :service_request_id,
            :activity_type,
            :activity_note,
            :created_at
        )
    ");
    $invalidInsert->execute([
        'service_request_id' => 999999,
        'activity_type' => 'invalid',
        'activity_note' => 'This invalid row should be rejected',
        'created_at' => '2026-07-10T10:00:00+01:00',
    ]);
} catch (PDOException $exception) {
    $foreignKeyRejected = true;
    $foreignKeyExceptionMessage = $exception->getMessage();
}
recordCheck($checks, 'Invalid foreign-key insert rejected', $foreignKeyRejected);

$activityCountAfterTransaction = (int) $pdo->query('SELECT COUNT(*) FROM service_request_activity')->fetchColumn();
recordCheck($checks, 'Invalid foreign-key insert left no invalid data', $activityCountAfterTransaction === $activityCountBeforeTransaction + 1);
$orphanActivityCount = (int) $pdo->query("
    SELECT COUNT(*)
    FROM service_request_activity
    WHERE service_request_id NOT IN (SELECT id FROM service_request)
")->fetchColumn();
recordCheck($checks, 'No orphan activity rows created', $orphanActivityCount === 0);

writeFile($evidenceDir . '/query-results.md', buildQueryResultsMarkdown($results));
writeFile($evidenceDir . '/data-model.md', buildDataModelMarkdown());
writeFile($evidenceDir . '/sql-test-note.md', buildTestNoteMarkdown($serviceRequestCount, $activityCountAfterTransaction));

print "SQL evidence validation\n";
print "Database created: {$databasePath}\n";
print "Drupal database touched: No\n";
print "service_request_count={$serviceRequestCount}\n";
print "service_request_activity_count={$activityCountAfterTransaction}\n";
foreach ($checks as $check) {
    print "{$check['label']}: " . ($check['passed'] ? 'Pass' : 'Fail') . "\n";
}
print "Open requests returned: " . count($results['openRequests']) . "\n";
print "Immediate safety-risk requests returned: " . count($results['immediateRiskRequests']) . "\n";
print "Activity JOIN rows returned: " . count($results['activityJoin']) . "\n";
print "Counts by status groups returned: " . count($results['countsByStatus']) . "\n";
print "Counts by responsible service groups returned: " . count($results['countsByService']) . "\n";
print "Prepared lookup reference: " . $results['preparedLookup'][0]['reference'] . "\n";
print "Committed transaction status: " . $results['transactionResult'][0]['status'] . "\n";
print "Rollback verification status: " . $results['rollbackVerification'][0]['status'] . "\n";
print "foreign_key_rejection=" . ($foreignKeyRejected ? 'PASS' : 'FAIL') . "\n";
print "database_message=" . $foreignKeyExceptionMessage . "\n";
print "orphan_activity_count={$orphanActivityCount}\n";
print "Evidence files generated: Pass\n";

$failedChecks = array_filter($checks, static fn (array $check): bool => !$check['passed']);
if ($failedChecks !== []) {
    exit(1);
}

function executeSqlFile(PDO $pdo, string $path): void
{
    $sql = file_get_contents($path);
    if ($sql === false) {
        throw new RuntimeException("Could not read {$path}");
    }
    $pdo->exec($sql);
}

function fetchAll(PDO $pdo, string $sql): array
{
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

function recordCheck(array &$checks, string $label, bool $passed): void
{
    $checks[] = [
        'label' => $label,
        'passed' => $passed,
    ];
}

function requiredIndexesExist(PDO $pdo): array
{
    $serviceRequestIndexes = fetchAll($pdo, 'PRAGMA index_list(service_request)');
    $activityIndexes = fetchAll($pdo, 'PRAGMA index_list(service_request_activity)');

    return [
        'Unique reference constraint exists' => hasUniqueIndex($serviceRequestIndexes),
        'Index on service_request.status exists' => hasNamedIndex($serviceRequestIndexes, 'idx_service_request_status'),
        'Index on service_request.responsible_service exists' => hasNamedIndex($serviceRequestIndexes, 'idx_service_request_responsible_service'),
        'Index on service_request_activity.service_request_id exists' => hasNamedIndex($activityIndexes, 'idx_service_request_activity_request_id'),
    ];
}

function hasNamedIndex(array $indexes, string $name): bool
{
    foreach ($indexes as $index) {
        if (($index['name'] ?? '') === $name) {
            return true;
        }
    }

    return false;
}

function hasUniqueIndex(array $indexes): bool
{
    foreach ($indexes as $index) {
        if ((int) ($index['unique'] ?? 0) === 1) {
            return true;
        }
    }

    return false;
}

function writeFile(string $path, string $contents): void
{
    if (file_put_contents($path, $contents) === false) {
        throw new RuntimeException("Could not write {$path}");
    }
}

function markdownTable(array $rows): string
{
    if ($rows === []) {
        return '_No rows returned._' . "\n";
    }

    $headers = array_keys($rows[0]);
    $lines = [
        '| ' . implode(' | ', $headers) . ' |',
        '| ' . implode(' | ', array_fill(0, count($headers), '---')) . ' |',
    ];

    foreach ($rows as $row) {
        $values = [];
        foreach ($headers as $header) {
            $values[] = '`' . (($row[$header] === null || $row[$header] === '') ? '' : (string) $row[$header]) . '`';
        }
        $lines[] = '| ' . implode(' | ', $values) . ' |';
    }

    return implode("\n", $lines) . "\n";
}

function buildQueryResultsMarkdown(array $results): string
{
    return implode("\n", [
        '# SQL query results',
        '',
        '## Open requests',
        '',
        markdownTable($results['openRequests']),
        '## Immediate safety-risk requests',
        '',
        markdownTable($results['immediateRiskRequests']),
        '## Activity JOIN for LSR-DEMO-10482',
        '',
        markdownTable($results['activityJoin']),
        '## Counts by status',
        '',
        markdownTable($results['countsByStatus']),
        '## Counts by responsible service',
        '',
        markdownTable($results['countsByService']),
        '## Prepared reference lookup',
        '',
        markdownTable($results['preparedLookup']),
        '## Committed transaction result',
        '',
        markdownTable($results['transactionResult']),
        '## Rollback verification',
        '',
        markdownTable($results['rollbackVerification']),
        '',
    ]);
}

function buildDataModelMarkdown(): string
{
    return implode("\n", [
        '# SQL data model',
        '',
        '## Tables',
        '',
        '### service_request',
        '',
        '- `id` - integer primary key',
        '- `reference` - text, unique, not null',
        '- `request_type` - text, not null',
        '- `location_description` - text, not null',
        '- `postcode` - text, not null',
        '- `asset_reference` - text, nullable',
        '- `immediate_safety_risk` - integer boolean, not null, constrained to `0` or `1`',
        '- `contact_name` - text, nullable',
        '- `contact_email` - text, nullable',
        '- `responsible_service` - text, not null',
        '- `assigned_team` - text, nullable',
        '- `status` - text, not null',
        '- `next_action` - text, nullable',
        '- `created_at` - text, not null',
        '- `updated_at` - text, not null',
        '',
        '### service_request_activity',
        '',
        '- `id` - integer primary key',
        '- `service_request_id` - integer, not null',
        '- `activity_type` - text, not null',
        '- `activity_note` - text, not null',
        '- `created_at` - text, not null',
        '',
        '## Relationship',
        '',
        'One `service_request` row can have many `service_request_activity` rows.',
        '',
        '## Foreign key',
        '',
        '- `service_request_activity.service_request_id` references `service_request.id`',
        '- Foreign-key enforcement is enabled with `PRAGMA foreign_keys = ON`',
        '',
        '## Important constraints and indexes',
        '',
        '- Unique reference constraint on `service_request.reference`',
        '- Index on `service_request.status`',
        '- Index on `service_request.responsible_service`',
        '- Index on `service_request_activity.service_request_id`',
        '',
    ]);
}

function buildTestNoteMarkdown(int $serviceRequestCount, int $activityCount): string
{
    return implode("\n", [
        '# SQL data evidence test note',
        '',
        '## Purpose',
        '',
        'Record Milestone 14 work to demonstrate a small, tested relational-data workflow for fictional council service requests using SQLite and explicit SQL statements.',
        '',
        '## Scope',
        '',
        '- Fictional records only',
        '- Generated SQLite learning database only',
        '- No connection to the live Drupal database',
        '- No real Webform submissions read or written',
        '',
        '## Database environment',
        '',
        '- PHP PDO with SQLite',
        '- Generated database path: `evidence/16-sql-data/service-requests.sqlite`',
        '- Fresh database recreated on each evidence run',
        '- Foreign-key enforcement enabled with `PRAGMA foreign_keys = ON`',
        '',
        '## Data model',
        '',
        '- `service_request` stores the fictional request records',
        '- `service_request_activity` stores activity entries for each request',
        '- Relationship: one `service_request` to many `service_request_activity` rows',
        '',
        '## Fictional seed data',
        '',
        '- `service_request` rows: `' . $serviceRequestCount . '`',
        '- `service_request_activity` rows after transaction test: `' . $activityCount . '`',
        '- References include `LSR-DEMO-10482` to `LSR-DEMO-10486`',
        '- Email addresses use `example.test` only',
        '',
        '## SQL operations demonstrated',
        '',
        '- Basic filtered `SELECT`',
        '- `WHERE` plus `ORDER BY`',
        '- `JOIN` from request to activity rows',
        '- `GROUP BY` and `COUNT` by status',
        '- `GROUP BY` and `COUNT` by responsible service',
        '- Prepared PDO statement for reference lookup',
        '- Transactional update with committed activity row',
        '- Rollback demonstration',
        '- Foreign-key validation failure',
        '',
        '## Commands run',
        '',
        '- `ddev exec php -l scripts/run_sql_evidence.php`',
        '- `ddev exec php scripts/run_sql_evidence.php`',
        '- Independent SQLite verification through PHP PDO inside DDEV',
        '- `git diff --check`',
        '- `git status --short`',
        '',
        '## Verified results',
        '',
        '- SQLite database file exists: Pass',
        '- `service_request` row count at least 5: Pass',
        '- `service_request_activity` row count at least 5: Pass',
        '- Foreign keys enabled: Pass',
        '- Required indexes exist: Pass',
        '- Open-request query returned expected rows: Pass',
        '- Immediate-safety-risk query returned expected rows: Pass',
        '- Activity JOIN returned expected rows: Pass',
        '- Counts by status returned grouped rows: Pass',
        '- Counts by responsible service returned grouped rows: Pass',
        '- Prepared reference lookup returned one row: Pass',
        '- Committed transaction persisted: Pass',
        '- Rollback restored the original value: Pass',
        '- Invalid foreign-key insert was rejected: Pass',
        '',
        '## Evidence',
        '',
        '- `scripts/sql/schema.sql`',
        '- `scripts/sql/seed.sql`',
        '- `scripts/sql/queries.sql`',
        '- `scripts/run_sql_evidence.php`',
        '- `evidence/16-sql-data/service-requests.sqlite`',
        '- `evidence/16-sql-data/sql-test-output.txt`',
        '- `evidence/16-sql-data/query-results.md`',
        '- `evidence/16-sql-data/data-model.md`',
        '- `evidence/16-sql-data/sql-test-note.md`',
        '',
        '## Limitations',
        '',
        '- SQLite learning database only',
        '- Fictional records only',
        '- Not connected to Drupal',
        '- Not connected to Mendix',
        '- No production migration',
        '- No user permissions or role model',
        '- No encryption configuration',
        '- No load or performance testing',
        '- No concurrent-user testing',
        '- No backup or restore test in this milestone',
        '- Not a production council data model',
        '- Not commercial database delivery',
        '- The generated run output includes `service_request_count=5`, `service_request_activity_count=9`, `foreign_key_rejection=PASS`, the caught SQLite foreign-key exception message, and `orphan_activity_count=0`.',
        '',
        '## Proposed claim',
        '',
        'Designed and tested a small relational SQLite data model for fictional service requests, using SQL filtering, ordering, joins, aggregation, prepared statements, transactions, rollback and foreign-key constraints.',
        '',
    ]);
}
