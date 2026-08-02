# SQL query results

## Open requests

| reference | request_type | responsible_service | status |
| --- | --- | --- | --- |
| `LSR-DEMO-10482` | `faulty_streetlight` | `Street Lighting` | `New` |
| `LSR-DEMO-10483` | `pothole` | `Highways` | `Awaiting review` |
| `LSR-DEMO-10484` | `fallen_branch` | `Parks and Trees` | `Assigned` |
| `LSR-DEMO-10485` | `blocked_drain` | `Drainage` | `In progress` |

## Immediate safety-risk requests

| reference | request_type | postcode | status | created_at |
| --- | --- | --- | --- | --- |
| `LSR-DEMO-10483` | `pothole` | `BN1 4AB` | `Awaiting review` | `2026-07-02T10:20:00+01:00` |
| `LSR-DEMO-10484` | `fallen_branch` | `BN2 5CD` | `Assigned` | `2026-07-03T08:45:00+01:00` |

## Activity JOIN for LSR-DEMO-10482

| reference | activity_type | activity_note | created_at |
| --- | --- | --- | --- |
| `LSR-DEMO-10482` | `created` | `Fictional request record created` | `2026-07-01T09:15:00+01:00` |
| `LSR-DEMO-10482` | `review` | `Initial streetlight review queued` | `2026-07-01T10:00:00+01:00` |

## Counts by status

| status | request_count |
| --- | --- |
| `Assigned` | `1` |
| `Awaiting review` | `1` |
| `Closed` | `1` |
| `In progress` | `1` |
| `New` | `1` |

## Counts by responsible service

| responsible_service | request_count |
| --- | --- |
| `Drainage` | `1` |
| `Highways` | `1` |
| `Parks and Trees` | `1` |
| `Street Lighting` | `1` |
| `Traffic and Signs` | `1` |

## Prepared reference lookup

| reference | request_type | postcode | status | next_action |
| --- | --- | --- | --- | --- |
| `LSR-DEMO-10484` | `fallen_branch` | `BN2 5CD` | `Assigned` | `Arrange inspection` |

## Committed transaction result

| reference | status | next_action | updated_at |
| --- | --- | --- | --- |
| `LSR-DEMO-10483` | `Assigned` | `Arrange inspection` | `2026-07-10T09:00:00+01:00` |

## Rollback verification

| reference | status | next_action |
| --- | --- | --- |
| `LSR-DEMO-10484` | `Assigned` | `Arrange inspection` |

