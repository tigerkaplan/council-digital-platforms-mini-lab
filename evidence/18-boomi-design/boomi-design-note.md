# Boomi-oriented integration design note

## Purpose

Record Milestone 16 work to translate verified fictional council service-request artefacts into a Boomi-oriented integration design.

## Scope

This is design evidence only. No Boomi tenant, process, connector, Atom runtime or deployment was used.

## Source evidence

- `evidence/06-json-output/sample-request.json`
- `evidence/15-interoperability/service-request.xml`
- `evidence/15-interoperability/field-mapping.md`
- `evidence/15-interoperability/mock-soap-request.xml`
- `evidence/15-interoperability/mock-soap-response.xml`
- `evidence/15-interoperability/xpath-checks.md`

## Proposed process

The proposed process receives fictional JSON service-request data, parses it against a proposed JSON profile, validates required fields, routes invalid records to a controlled error path, maps valid records to canonical XML, builds a SOAP 1.1 request and evaluates the proposed response.

## Proposed components

The design uses proposed Boomi components or equivalents for start, connector operation, JSON profile, decision, map, XML profile, message or document property, Try/Catch or equivalent error route and stop behaviour.

## Mapping approach

The mapping follows the existing JSON, XML and SOAP evidence. It copies required values, trims surrounding postcode whitespace in the proposed design, maps booleans to lowercase XML text and represents absent optional risk details as an empty element.

The current demonstration preserves the JSON status value `new`. A real integration would require an agreed mapping between source status codes and the consuming system's expected values.

## Routing approach

The routing design separates validation failures, safety-risk routing, successful SOAP responses, SOAP faults, transport timeouts and unexpected response structures. These routing scenarios are design-only.

## Error-handling approach

The proposed error strategy records safe diagnostic categories, preserves correlation through `reference` when available, avoids personal data in logs, distinguishes retryable and non-retryable errors, and routes unresolved failures to manual review or a dead-letter concept.

## Test-design approach

The test design separates cases verified outside Boomi using Milestone 13 evidence from cases that remain design-only. Routing, timeout, retry, connector and duplicate-reference cases were not run in Boomi.

## What was verified outside Boomi

- Valid fictional JSON structure exists.
- Canonical XML was generated and checked in Milestone 13.
- XPath checks returned expected values in Milestone 13.
- Mock SOAP request and response files are well-formed.
- The status value `new` is preserved through the existing JSON, XML and mock SOAP evidence.

## What was not implemented

- No Boomi tenant was used.
- No Boomi process was created.
- No Boomi connector was configured.
- No Boomi Atom or runtime was used.
- No process execution occurred in Boomi.
- No deployment occurred.
- No live Drupal endpoint was connected to Boomi.
- No live SOAP endpoint was called.
- Routing and retry scenarios are design-only.
- Authentication and secrets handling were not implemented.

## Cross-platform narrative

The lightweight Drupal case preview established the service-request workflow shape. The local Mendix application demonstrated equivalent status-progression logic. The interoperability milestone verified JSON, XML, XPath and mock SOAP structures. This milestone translates those verified artefacts into a proposed Boomi-oriented integration design without claiming platform execution.

## Evidence

- `evidence/18-boomi-design/boomi-process-design.md`
- `evidence/18-boomi-design/boomi-component-inventory.md`
- `evidence/18-boomi-design/boomi-field-mapping.md`
- `evidence/18-boomi-design/boomi-routing-rules.md`
- `evidence/18-boomi-design/boomi-error-handling.md`
- `evidence/18-boomi-design/boomi-test-scenarios.md`
- `evidence/18-boomi-design/boomi-process-flow.mmd`
- `evidence/18-boomi-design/boomi-design-note.md`

## Limitations

- No Boomi tenant was used.
- No Boomi process was created.
- No Boomi connector was configured.
- No Boomi Atom or runtime was used.
- No process execution occurred in Boomi.
- No deployment occurred.
- No live Drupal endpoint was connected to Boomi.
- No live SOAP endpoint was called.
- Routing and retry scenarios are design-only.
- Authentication and secrets handling were not implemented.
- Not a production integration.
- Not commercial middleware delivery.

## Proposed claim

Produced a Boomi-oriented integration design for a fictional council service-request flow, covering proposed profiles, field mapping, validation decisions, routing, SOAP messaging, error handling and test scenarios, grounded in separately tested JSON, XML, XPath and mock SOAP evidence.
