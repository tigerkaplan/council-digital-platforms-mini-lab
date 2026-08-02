PRAGMA foreign_keys = ON;

CREATE TABLE service_request (
  id INTEGER PRIMARY KEY,
  reference TEXT NOT NULL UNIQUE,
  request_type TEXT NOT NULL,
  location_description TEXT NOT NULL,
  postcode TEXT NOT NULL,
  asset_reference TEXT,
  immediate_safety_risk INTEGER NOT NULL CHECK (immediate_safety_risk IN (0, 1)),
  contact_name TEXT,
  contact_email TEXT,
  responsible_service TEXT NOT NULL,
  assigned_team TEXT,
  status TEXT NOT NULL,
  next_action TEXT,
  created_at TEXT NOT NULL,
  updated_at TEXT NOT NULL
);

CREATE TABLE service_request_activity (
  id INTEGER PRIMARY KEY,
  service_request_id INTEGER NOT NULL,
  activity_type TEXT NOT NULL,
  activity_note TEXT NOT NULL,
  created_at TEXT NOT NULL,
  FOREIGN KEY (service_request_id) REFERENCES service_request(id) ON DELETE CASCADE
);

CREATE INDEX idx_service_request_status
  ON service_request (status);

CREATE INDEX idx_service_request_responsible_service
  ON service_request (responsible_service);

CREATE INDEX idx_service_request_activity_request_id
  ON service_request_activity (service_request_id);
