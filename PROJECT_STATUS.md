# Council Digital Platforms Mini Lab — Project Status

## Current state

The repository contains a verified local learning implementation and an evidence-led static case study for a fictional council public-highway reporting journey. It is not a production service and is not yet ready for public release.

## Verified implementation

- A single-page Drupal Webform records a fictional public-highway problem and conditionally shows Risk details for the immediate-risk option.
- Scoped server-side postcode validation accepts `BN3 1AA`, rejects `ABC123` and `BN3 1 AA`, and normalises surrounding/repeated whitespace and case before validation.
- The empty-postcode message is `Enter a postcode, for example BN3 1AA.`
- A scoped Twig confirmation presents a static thank-you message, next-step guidance and a Back to form link without submitted values or a dynamic reference.
- A read-only Drupal GET endpoint returns one fixed fictional service-request record.
- A local internal staff preview fetches that fixed record and demonstrates a browser-memory-only status sequence from New to Closed with static next actions. It resets after refresh.
- The repository contains a screenshot-supported local Mendix learning prototype record, fictional SQLite SQL evidence, XML/XPath/mock SOAP evidence, and a Boomi-oriented process-design study.

## Technical boundaries

- The JSON endpoint is fixed, fictional and read-only. It is not connected to Webform submissions or a case-management system.
- The internal preview has a fixed Street Lighting context; it is not authenticated, persistent, dynamically routed or operationally integrated.
- The Mendix source package is not included. There is no Drupal-to-Mendix import, cloud deployment or production use.
- SQLite evidence is fictional and standalone. Temporary-copy execution was used for current verification; the private SQLite binary was removed from the release-facing tree and is ignored.
- XML and XPath were executed for the fictional record. SOAP messages are mock, with no live service, WSDL, XSD validation or authentication.
- XSLT was not implemented.
- Boomi evidence is design-only: no tenant, connector, process execution or deployment occurred.
- Operational evidence records historical local checks only. It is not production DevOps or disaster-recovery evidence.

## Accessibility and persona checks

The evidence records accessibility-focused manual checks of keyboard use, visible focus, validation states, confirmation behaviour, zoom/reflow and narrow viewports. Alex, Priya, Tom and Sam are fictional personas used for focused review. Dedicated end-to-end screen-reader testing was not completed, and no full WCAG-compliance or accessibility-certification claim is made.

## Evidence and release status

- `project.html` uses deterministic public-safe crops for the two Studio Pro Mendix views. The three selected runtime screenshots remain limited to fictional local-prototype evidence; the originals are excluded from public export.
- Evidence records remain subject to their classifications in `evidence/19-final-audit/public-safety-evidence-inventory.md`.
- Phase 9 removed the private SQLite binary from the release-facing tree and confirmed that the current Git history requires a clean-history export rather than direct publication.
- Final independent review of the Phase 9/9A release decision and clean public candidate remains required before any release activity.
