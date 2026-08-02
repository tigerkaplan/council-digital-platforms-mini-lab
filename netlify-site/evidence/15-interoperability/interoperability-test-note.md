# Interoperability test note

## Purpose

Record Milestone 13 work to demonstrate a small, tested interoperability chain using fictional council service-request data.

## Input source

- `evidence/06-json-output/sample-request.json`

## Transformation method

- PHP reads and validates the JSON input.
- `DOMDocument` creates canonical XML.
- `DOMXPath` selects required values from the generated XML.
- `DOMDocument` creates mock SOAP 1.1 request and response envelopes.

## Files produced

- `evidence/15-interoperability/service-request.xml`
- `evidence/15-interoperability/xpath-checks.md`
- `evidence/15-interoperability/mock-soap-request.xml`
- `evidence/15-interoperability/mock-soap-response.xml`
- `evidence/15-interoperability/field-mapping.md`
- `evidence/15-interoperability/interoperability-test-output.txt`
- `evidence/15-interoperability/interoperability-test-note.md`

## XPath expressions and results

- `/serviceRequest/reference` returned `LSR-DEMO-10482`: Pass
- `/serviceRequest/requestType` returned `faulty_streetlight`: Pass
- `/serviceRequest/location/postcode` returned `BN3 1AA`: Pass
- `/serviceRequest/location/assetReference` returned `LP-418`: Pass
- `/serviceRequest/risk/immediateSafetyRisk` returned `false`: Pass
- `/serviceRequest/contact/email` returned `alex@example.test`: Pass
- `/serviceRequest/status` returned `new`: Pass

## SOAP request and response scope

- The mock SOAP request uses the fictional namespace `urn:example:council:service-request`.
- The mock SOAP response records `accepted: true`, reference `LSR-DEMO-10482` and message `Request accepted for demonstration purposes`.
- No SOAP request was sent to any external service.
- The interoperability demonstration preserves the source JSON status value `new` unchanged through the canonical XML and mock SOAP messages. A real integration would require an explicit mapping between source status codes and the enumeration values expected by the consuming system.

## Commands run

- `ddev exec php -l scripts/validate_interoperability.php`
- `ddev exec php scripts/validate_interoperability.php`
- `ddev exec php -r 'foreach (["evidence/15-interoperability/service-request.xml", "evidence/15-interoperability/mock-soap-request.xml", "evidence/15-interoperability/mock-soap-response.xml"] as \$file) { \$doc = new DOMDocument(); if (!\$doc->load(\$file)) { throw new RuntimeException(\$file); } echo \$file . ": well-formed" . PHP_EOL; }'`

## Verified results

- Input JSON valid: Pass
- Required values found: Pass
- Canonical XML generated: Pass
- Canonical XML well-formed: Pass
- XPath checks returned expected values: Pass
- Mock SOAP request generated: Pass
- Mock SOAP request well-formed: Pass
- Mock SOAP response generated: Pass
- Mock SOAP response well-formed: Pass
- No real external service called: Pass

## Limitations

- No real SOAP endpoint was called.
- No authentication was implemented.
- No WSDL was consumed.
- No XML Schema validation was performed.
- The transformation is for one fictional record.
- This is not a production integration.
- This is not commercial middleware delivery.
- The source JSON status value `new` was preserved unchanged through the canonical XML and mock SOAP messages.

## Proposed claim

Built and tested a small interoperability demonstration that transformed a fictional JSON service request into XML, selected values using XPath and produced well-formed mock SOAP request and response messages.
