# Evidence Register

This register is a public navigation guide to the project’s supported claims. It is intentionally selective: it links only to evidence and documentation suitable for public navigation. “No direct public link” means that the available record is source-supported, awaiting a later context decision, or otherwise not appropriate for public navigation.

| Area | Claim supported | Public evidence | Verification type | Boundary/limitation | Status |
| --- | --- | --- | --- | --- | --- |
| Drupal Webform | A fictional public-highway reporting form exists. | [Case-study Webform section](project.html#webform-title) | Current source and local runtime | Single-page form only; not a live council service. | Verified locally |
| Conditional logic | Immediate-safety-risk details are conditionally shown and required where configured. | [Case-study validation section](project.html#validation-title) | Current configuration and runtime | Do not infer that hidden values are cleared. | Verified locally |
| PHP postcode validation | `BN3 1AA` is accepted; `ABC123` and `BN3 1 AA` are invalid; the empty message is recorded. | [Invalid-postcode view](evidence/11-persona-testing/priya-invalid-postcode-error-public-view.png) and [empty-postcode view](evidence/11-persona-testing/priya-empty-postcode-error-public-view.png) | Source, controlled runtime and public-safe visual evidence | Normalisation is trim, uppercase and repeated-whitespace collapse only; no address lookup. | Verified locally |
| YAML configuration | The Webform configuration is exported in the repository. | No direct public link | Source inspection | Exported configuration is technical context, not standalone public evidence. | Source-supported |
| JSON endpoint | One fixed fictional service-request record is returned by a read-only GET endpoint. | [Fictional JSON sample](evidence/06-json-output/sample-request.json) | Current source and runtime JSON parsing | No live submissions, writes or production API. | Verified locally |
| Twig confirmation | A scoped confirmation presents static content and a Back to form link. | [Desktop confirmation](evidence/13-twig-confirmation/twig-confirmation-desktop.png) and [mobile confirmation](evidence/13-twig-confirmation/twig-confirmation-mobile.png) | Source, runtime and public-safe visual evidence | No submitted values or dynamic reference. | Verified locally |
| Internal staff preview | A fixed fictional record is displayed in a local staff-preview demonstration. | [Case-study workflow section](project.html#workflow-title) | Current source and manual browser check | Fixed Street Lighting context; no role access, persistence or live workflow. | Verified locally |
| Alex persona | The narrow-viewport form presentation was reviewed for a fictional mobile persona. | No standalone public link | Manual persona review | Candidate media requires later context review; this is not formal research. | Bounded |
| Priya persona | Keyboard, focus, errors, confirmation and zoom/reflow were manually checked within scope. | No standalone public link | Technical inspection and manual browser check | No end-to-end screen-reader test or full WCAG-compliance claim. | Verified within scope |
| Tom persona | Plain-language form wording was reviewed for a fictional persona. | No standalone public link | Manual persona review | Candidate media requires later context review; this is not formal research. | Bounded |
| Sam persona | A non-persistent internal workflow was reviewed through the fictional staff preview. | [Closed state](evidence/12-workflow/workflow-closed-status.png) | Manual browser check and public-safe visual evidence | Browser-memory-only status demonstration; refresh and reset return to the initial state. | Verified within scope |
| Mendix | A local learning prototype was manually demonstrated with fictional entered data. | [Case-study Mendix section](project.html#mendix-title) | Deterministic public-safe crops and selected runtime screenshots | Source package is not included; no import, integration, deployment or production claim. The two original Studio Pro screenshots are excluded; the three runtime screenshots remain selected only for the bounded fictional local-prototype context. | Selected within stated limits |
| SQL and SQLite | A fictional SQLite model demonstrates joins, aggregation, transactions and constraints. | No direct public link | Source and temporary-copy execution | Standalone fictional data; the private SQLite binary was removed from the release-facing tree. Reproducibility uses the tracked schema, seed, queries and runner in an isolated temporary copy. | Verified within scope |
| XML | Well-formed XML was generated from the fictional service-request record. | No direct public link | Existing script and temporary-copy execution | One fictional record; no XSD validation. | Verified within scope |
| XPath | XPath checks returned expected fictional values. | No direct public link | Existing script and temporary-copy execution | Limited to the generated fictional XML record. | Verified within scope |
| Mock SOAP | Well-formed mock SOAP 1.1 request and response messages were generated. | No direct public link | Existing script and temporary-copy execution | No external endpoint, live service, WSDL or authentication. | Verified within scope |
| XSLT status | XSLT was not implemented in the verified repository state. | No artefact to link | Repository-wide source inspection | Absence is not an XSLT capability claim. | Verified absence |
| Boomi design study | A Boomi-oriented process-design study was produced. | No direct public link | Documentation review | No Boomi tenant process, connector, execution or deployment. | Bounded |
| Local operational checks | Sanitised historical local runtime and runbook checks are retained. | No direct public link | Historical operational record | Not production operations, monitoring, high availability, disaster recovery or live support. | Bounded |
| Static case study | The project has an evidence-led static case study. | [Static case study](project.html) | Static HTML, link and manual browser checks | It is project documentation, not a deployed public service. | Verified locally |

## Reading the evidence safely

- Screenshot-supported evidence establishes only what is visible in the recorded fictional state.
- Source-supported evidence should not be treated as a current runtime result without a recorded runtime check.
- Manual checks are limited to the stated browser, viewport and test conditions.
- Design-study material is not proof of platform execution, deployment or integration.
- The public-release blockers in [RELEASE_CHECKLIST.md](RELEASE_CHECKLIST.md) remain in force for all material considered for future release.
