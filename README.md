# Council Digital Platforms Mini Lab

## Project overview

This personal learning project models a fictional council public-highway reporting journey. It uses fictional names, requests and `.test` email addresses only. It is a local prototype, not a production council system, and has no live case-management integration.

The project brings together a Drupal service form, accessibility-focused manual checks, a small internal workflow demonstration, structured-data exercises and bounded operational evidence. It demonstrates how form wording, validation, conditional logic, data shape and evidence quality need to agree in a digital service.

## What the prototype demonstrates

- A single-page Drupal Webform for reporting a fictional public-highway problem.
- Conditional immediate-safety-risk details where the current configuration proves the behaviour.
- Scoped PHP postcode validation.
- A static, Webform-specific Twig confirmation.
- A read-only Drupal GET endpoint returning one fixed fictional service-request record.
- A local internal preview with a browser-memory-only status demonstration.
- Screenshot-supported Mendix learning evidence, fictional SQLite exercises, XML/XPath/mock SOAP exercises and a Boomi-oriented design study.

## Service journey

1. A fictional resident reports a public-highway problem.
2. The form validates postcode-shaped input and conditionally requests risk details.
3. A fixed fictional JSON record demonstrates a structured response.
4. A local staff preview presents the record and a non-persistent status sequence.
5. Separate SQL, XML, XPath, mock SOAP and design-study evidence explores related technical concepts.

The journey is a learning model. It does not represent a live council workflow, real resident data, automatic assignment or an integrated production system.

## Personas

| Persona | Review focus | Boundary |
| --- | --- | --- |
| Alex | Narrow/mobile viewport | A fictional persona used for a focused layout review, not formal user research. |
| Priya | Keyboard, focus, errors, confirmation and zoom/reflow | Manual accessibility-focused checks only; no end-to-end screen-reader test. |
| Tom | Plain language | A fictional copy-review persona, not usability research with participants. |
| Sam | Internal staff-preview workflow | A fictional view of fixed data and browser-memory-only statuses. |

## Verified features

### Public Webform and postcode validation

The Webform is single-page; it is not a wizard or multi-step form. The verified postcode behaviour is:

- `BN3 1AA` is accepted.
- `ABC123` is rejected.
- `BN3 1 AA` is invalid.
- An empty postcode shows `Enter a postcode, for example BN3 1AA.`

The custom rule trims surrounding whitespace, converts letters to uppercase and collapses repeated whitespace before validating. It does not use an external postcode service, look up an address or provide comprehensive UK-address validation.

### Structured data, confirmation and workflow

Created a read-only Drupal GET endpoint returning one fixed fictional service-request record. It does not accept submissions, write data or provide a production API.

The internal preview uses that fixed Street Lighting context and demonstrates local status changes only. It has no role-based access, automatic assignment, persistence after refresh/reset or production workflow capability.

The Twig confirmation is scoped to the named Webform. It contains static confirmation content and a Back to form link, but does not display submitted values or a dynamic reference.

## Technology and evidence boundaries

- **Drupal and DDEV:** Drupal 10 project configuration with DDEV local development services.
- **Mendix:** Created a screenshot-supported local Mendix learning prototype using fictional manually entered data; the source package is not included in this repository.
- **SQL and SQLite:** Built and executed a fictional SQLite data model demonstrating joins, aggregation, transactions and constraints. It is standalone: it is not the Drupal database and is not connected to Mendix or Webform submissions.
- **XML and XPath:** Generated well-formed XML and executed XPath checks against the fictional service-request record.
- **Mock SOAP:** Generated well-formed mock SOAP 1.1 request and response messages as an interoperability exercise. No external endpoint, WSDL, XSD validation or authentication was used.
- **XSLT:** XSLT was not implemented in the verified repository state.
- **Boomi:** Produced a Boomi-oriented process-design study; no Boomi tenant process was executed.
- **Operational records:** Historical local runtime and runbook checks only. They do not evidence production operations, monitoring, high availability, disaster recovery or live support.

## Local setup

### Requirements

- Docker Desktop or another supported Docker engine.
- DDEV.
- PHP and Composer through the DDEV project runtime.
- Python 3 only if serving the static case study locally.

### Existing local project

From the repository root, start and inspect the existing project:

```bash
ddev start
ddev describe
ddev drush status
```

Use the primary URL reported by `ddev describe`, then append these routes:

- `/form/report-public-highway-problem`
- `/api/service-request/demo`
- `/internal-case-preview/`

If Composer dependencies are absent in a fresh checkout, the project’s tracked `composer.json` supports:

```bash
ddev composer install
```

The clean public release candidate intentionally excludes `composer.lock` because it carries upstream package-maintainer contact metadata. It also excludes three unrelated mail/default configuration files: two contain generic example addresses that do not follow this project's `.test` convention, and one contains password-named account-email template values. This means the candidate is a public source-and-evidence review export, not a complete configuration-import package, and it does not provide the exact development dependency lock. Use the retained controlled working copy when those exact local-development materials are required.

This repository does not include a public Drupal database dump or a fully scripted fresh-install path. The exported configuration in `config/sync/` is not a database. Configuration import changes active Drupal configuration and requires a compatible installed site, so it is not a routine setup or verification step.

### Static case study

`project.html` is a static case study, separate from the Drupal runtime. From the repository root:

```bash
python3 -m http.server 8000
```

Open `http://localhost:8000/project.html` in a browser, then stop the server with `Ctrl+C`.

To stop the DDEV project without deleting project data:

```bash
ddev stop
```

## Local verification

These checks verify the current local environment; they are different from inspecting historical evidence records.

```bash
ddev drush status
ddev drush config:status
ddev exec curl -fsS http://localhost/api/service-request/demo | python3 -m json.tool
ddev exec php -l scripts/run_sql_evidence.php
ddev exec php -l scripts/validate_interoperability.php
```

In a controlled browser check, confirm that the Webform route and `/internal-case-preview/` load. Use fictional data only. When intentionally testing the form, verify the postcode examples above and do not inspect unrelated submissions or use an administrative results view as public evidence.

The SQL and interoperability scripts write generated evidence files when run directly. For routine documentation checks, use the syntax commands above rather than rerunning those generators against the tracked evidence paths.

## Evidence and case study

- Read the [static case study](project.html).
- Use the [evidence register](EVIDENCE_REGISTER.md) to find public-safe supporting material and its limitations.
- Use the [release checklist](RELEASE_CHECKLIST.md) before considering any public release activity.

## Accessibility checks and limitations

The project records manual accessibility-focused checks for keyboard use, visible focus, error states, confirmation behaviour, zoom/reflow and narrow viewports. These checks are scoped to their recorded fictional test states.

Dedicated end-to-end screen-reader testing was not completed. The project does not claim full WCAG compliance, accessibility certification or `aria-describedby` support where current rendered markup does not provide it.

## Technical limitations

- The JSON endpoint is one fixed fictional GET response, not a live submissions feed.
- The staff preview is not persistent, authenticated, automatically assigned or integrated with a case-management system.
- Mendix source and platform artefacts are not included.
- The private SQLite binary has been removed from the release-facing tree and is ignored. The fictional SQL claim remains reproducible from the tracked schema, seed, queries and runner using an isolated temporary copy.
- XML, XPath and mock SOAP exercises have no live service, WSDL, XSD validation or authentication.
- XSLT is not implemented.
- The Boomi work is a design study only, with no tenant execution or deployment.

## Public-release status

This repository is not yet cleared for public release. Phase 9 confirmed that its current history requires a clean-history export rather than direct publication. Phase 9A replaces the two unapproved Studio Pro views in the case study with deterministic public-safe crops. The three Mendix runtime screenshots remain selected only as fictional local-prototype evidence; no source package, Drupal import, deployment or production claim is made.

Before any release decision, it still needs:

- final independent review of the Phase 9 and Phase 9A release decision and selected-evidence context;
- approval of the clean-history public release candidate after that review;
- explicit approval for public GitHub, static deployment and portfolio handoff.

## Repository structure

```text
.ddev/                 Local DDEV configuration
config/sync/           Exported Drupal configuration
web/modules/custom/    Custom validation, API and confirmation modules
web/internal-case-preview/
                       Local static staff-preview demonstration
scripts/               SQL, interoperability and local operational helpers
evidence/              Classified fictional evidence and audit records
project.html           Static case study
```
