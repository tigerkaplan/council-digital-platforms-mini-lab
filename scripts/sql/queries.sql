-- 1. Basic filtered SELECT: open requests where status is not Closed.
SELECT reference, request_type, responsible_service, status
FROM service_request
WHERE status <> 'Closed'
ORDER BY created_at;

-- 2. WHERE plus ORDER BY: immediate-safety-risk requests by created date.
SELECT reference, request_type, postcode, status, created_at
FROM service_request
WHERE immediate_safety_risk = 1
ORDER BY created_at;

-- 3. JOIN: activity entries for a selected request.
SELECT sr.reference, sra.activity_type, sra.activity_note, sra.created_at
FROM service_request sr
JOIN service_request_activity sra ON sra.service_request_id = sr.id
WHERE sr.reference = 'LSR-DEMO-10482'
ORDER BY sra.created_at;

-- 4. GROUP BY and COUNT: request counts by status.
SELECT status, COUNT(*) AS request_count
FROM service_request
GROUP BY status
ORDER BY status;

-- 5. GROUP BY service: request counts by responsible service.
SELECT responsible_service, COUNT(*) AS request_count
FROM service_request
GROUP BY responsible_service
ORDER BY responsible_service;

-- 6. Parameterised reference lookup is executed from PHP with a prepared PDO statement.

-- 7. Transactional UPDATE is executed from PHP.

-- 8. Rollback demonstration is executed from PHP.

-- 9. Foreign-key validation is executed from PHP.
