INSERT INTO service_request (
  reference,
  request_type,
  location_description,
  postcode,
  asset_reference,
  immediate_safety_risk,
  contact_name,
  contact_email,
  responsible_service,
  assigned_team,
  status,
  next_action,
  created_at,
  updated_at
) VALUES
  (
    'LSR-DEMO-10482',
    'faulty_streetlight',
    'Outside number 24, Example Street',
    'BN3 1AA',
    'LP-418',
    0,
    'Alex Morgan',
    'alex@example.test',
    'Street Lighting',
    'Street Lighting',
    'New',
    'Review request details',
    '2026-07-01T09:15:00+01:00',
    '2026-07-01T09:15:00+01:00'
  ),
  (
    'LSR-DEMO-10483',
    'pothole',
    'Near the bus stop on Sample Road',
    'BN1 4AB',
    NULL,
    1,
    'Jamie Patel',
    'jamie@example.test',
    'Highways',
    'Highways inspection',
    'Awaiting review',
    'Confirm hazard location',
    '2026-07-02T10:20:00+01:00',
    '2026-07-02T10:20:00+01:00'
  ),
  (
    'LSR-DEMO-10484',
    'fallen_branch',
    'Beside the entrance to Example Park',
    'BN2 5CD',
    NULL,
    1,
    'Sam Taylor',
    'sam@example.test',
    'Parks and Trees',
    'Tree works',
    'Assigned',
    'Arrange inspection',
    '2026-07-03T08:45:00+01:00',
    '2026-07-03T08:45:00+01:00'
  ),
  (
    'LSR-DEMO-10485',
    'blocked_drain',
    'At the corner of Demo Avenue and Test Lane',
    'BN3 2EF',
    'DR-221',
    0,
    'Priya Shah',
    'priya@example.test',
    'Drainage',
    'Drainage',
    'In progress',
    'Clear blocked drain',
    '2026-07-04T14:30:00+01:00',
    '2026-07-04T14:30:00+01:00'
  ),
  (
    'LSR-DEMO-10486',
    'damaged_sign',
    'Opposite 10 Fictional Close',
    'BN1 8GH',
    'SN-509',
    0,
    'Tom Evans',
    'tom@example.test',
    'Traffic and Signs',
    'Signs',
    'Closed',
    'No further action',
    '2026-07-05T11:10:00+01:00',
    '2026-07-05T11:10:00+01:00'
  );

INSERT INTO service_request_activity (
  service_request_id,
  activity_type,
  activity_note,
  created_at
)
SELECT id, 'created', 'Fictional request record created', created_at
FROM service_request;

INSERT INTO service_request_activity (
  service_request_id,
  activity_type,
  activity_note,
  created_at
)
SELECT id, 'review', 'Initial streetlight review queued', '2026-07-01T10:00:00+01:00'
FROM service_request
WHERE reference = 'LSR-DEMO-10482';

INSERT INTO service_request_activity (
  service_request_id,
  activity_type,
  activity_note,
  created_at
)
SELECT id, 'risk_review', 'Immediate safety risk flagged for inspection', '2026-07-02T11:00:00+01:00'
FROM service_request
WHERE reference = 'LSR-DEMO-10483';

INSERT INTO service_request_activity (
  service_request_id,
  activity_type,
  activity_note,
  created_at
)
SELECT id, 'assignment', 'Tree works team assigned', '2026-07-03T09:30:00+01:00'
FROM service_request
WHERE reference = 'LSR-DEMO-10484';
