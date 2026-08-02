# SQL data model

## Tables

### service_request

- `id` - integer primary key
- `reference` - text, unique, not null
- `request_type` - text, not null
- `location_description` - text, not null
- `postcode` - text, not null
- `asset_reference` - text, nullable
- `immediate_safety_risk` - integer boolean, not null, constrained to `0` or `1`
- `contact_name` - text, nullable
- `contact_email` - text, nullable
- `responsible_service` - text, not null
- `assigned_team` - text, nullable
- `status` - text, not null
- `next_action` - text, nullable
- `created_at` - text, not null
- `updated_at` - text, not null

### service_request_activity

- `id` - integer primary key
- `service_request_id` - integer, not null
- `activity_type` - text, not null
- `activity_note` - text, not null
- `created_at` - text, not null

## Relationship

One `service_request` row can have many `service_request_activity` rows.

## Foreign key

- `service_request_activity.service_request_id` references `service_request.id`
- Foreign-key enforcement is enabled with `PRAGMA foreign_keys = ON`

## Important constraints and indexes

- Unique reference constraint on `service_request.reference`
- Index on `service_request.status`
- Index on `service_request.responsible_service`
- Index on `service_request_activity.service_request_id`
