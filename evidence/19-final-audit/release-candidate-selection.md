# Phase 9 release-candidate selection

## Scope

This register records the Phase 9 review of `PUBLIC-SAFE CANDIDATE` items used by current public documents or `project.html`. It is a selection decision for a future clean public export, not a publication approval.

## Approved only within the stated context

| Item | Supported context | Selection result |
| --- | --- | --- |
| `evidence/11-persona-testing/alex-mobile-updated-form.png` | Fictional mobile form presentation only. | Approved for that limited context. |
| `evidence/11-persona-testing/alex-mobile-updated-conditional-field.png` | Fictional mobile conditional-risk presentation only. | Approved for that limited context. |
| `evidence/11-persona-testing/tom-plain-language-desktop.png` | Fictional plain-language form copy only. | Approved for that limited context. |
| `evidence/15-interoperability/interoperability-test-note.md`, `field-mapping.md` and `xpath-checks.md` | One fictional JSON-to-XML/XPath/mock-SOAP exercise; no live service, WSDL, XSD or authentication claim. | Approved for that limited context. |
| `evidence/16-sql-data/data-model.md` and `query-results.md` | Fictional standalone SQL exercise; no Drupal-database or production-data claim. | Approved for that limited context. |
| `evidence/18-boomi-design/boomi-design-note.md` | Fictional Boomi-oriented design only; no tenant, process execution, connector, runtime or deployment claim. | Approved for that limited context. |

Each approved item was checked for its stated claim, local paths, private material, stale wording, account or tenant context, and unsupported integration or accessibility claims. The approved items contain fictional data only and retain the listed limitations.

## Not selected for a clean public export

| Item | Reason | Required follow-up |
| --- | --- | --- |
| `evidence/14-mendix/mendix-domain-model.png` and `mendix-advance-status-microflow.png` | Original Studio Pro views retain account/navigation and development-console context. | Excluded from public export; use the deterministic public-safe derivatives. |

## Phase 9A Mendix media correction

| Item | Release decision | Limited claim |
| --- | --- | --- |
| `mendix-domain-model-public-safe.png` | Selected public-safe deterministic derivative. | ServiceRequest model fields in the local learning prototype. |
| `mendix-advance-status-microflow-public-safe.png` | Selected public-safe deterministic derivative. | Status and next-action update microflow in the local learning prototype. |
| `mendix-request-overview.png`, `mendix-status-awaiting-review.png` and `mendix-status-closed.png` | Selected `PUBLIC-SAFE CANDIDATE` items within stated limits. | Fictional local-prototype runtime views only; no source package, import, deployment or production claim. |

The two originals are not selected. `project.html` now uses the two derivatives and the selected runtime images only within their bounded fictional local-prototype context.

## Deterministic derivative method

The derivatives were cropped directly from the original 8-bit RGBA non-interlaced PNGs. No UI was redrawn or generated, no annotation was added, and the output contains only `IHDR`, `IDAT` and `IEND` chunks.

| Derivative | Original dimensions | Crop coordinates (`x`, `y`, `width`, `height`) | Final dimensions | SHA-256 |
| --- | --- | --- | --- | --- |
| `mendix-domain-model-public-safe.png` | 1902 × 957 | 740, 270, 430, 400 | 430 × 400 | `86c65f43c893857975457f411e80449f2901a820f6b283b34bcb712143447dbc` |
| `mendix-advance-status-microflow-public-safe.png` | 1560 × 929 | 450, 170, 1050, 190 | 1050 × 190 | `9d1a77c0235c031ba9033fd6cdb6e5177435c7a32ad2300efa9126982f6c37d6` |

## SQLite decision

The private SQLite binary is not selected for public use. It was removed from the release-facing tree after byte-identical private retention verification. The tracked schema, seed, queries and PHP runner remain the reproducible public source for the fictional SQL claim.

## Release status

This register does not approve publication, GitHub visibility, static deployment or portfolio handoff. Final independent review of the Phase 9/9A decision and clean release candidate remains required.
