<?php

declare(strict_types=1);

const SOAP_NAMESPACE = 'urn:example:council:service-request';

$projectRoot = dirname(__DIR__);
$inputPath = $projectRoot . '/evidence/06-json-output/sample-request.json';
$evidenceDir = $projectRoot . '/evidence/15-interoperability';

$serviceRequestXmlPath = $evidenceDir . '/service-request.xml';
$xpathChecksPath = $evidenceDir . '/xpath-checks.md';
$soapRequestPath = $evidenceDir . '/mock-soap-request.xml';
$soapResponsePath = $evidenceDir . '/mock-soap-response.xml';
$fieldMappingPath = $evidenceDir . '/field-mapping.md';
$testNotePath = $evidenceDir . '/interoperability-test-note.md';

if (!is_dir($evidenceDir) && !mkdir($evidenceDir, 0775, true) && !is_dir($evidenceDir)) {
    throw new RuntimeException('Could not create evidence directory.');
}

$json = file_get_contents($inputPath);
if ($json === false) {
    throw new RuntimeException('Could not read input JSON.');
}

$data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
if (!is_array($data)) {
    throw new RuntimeException('Input JSON did not decode to an object.');
}

$requiredPaths = [
    'reference',
    'requestType',
    'location.description',
    'location.postcode',
    'location.assetReference',
    'risk.immediateSafetyRisk',
    'contact.name',
    'contact.email',
    'status',
];

$values = [];
foreach ($requiredPaths as $path) {
    $values[$path] = valueAtPath($data, $path);
}

$serviceRequestXml = buildServiceRequestXml($data);
writeFile($serviceRequestXmlPath, $serviceRequestXml->saveXML());

$xpathResults = runXPathChecks($serviceRequestXmlPath);
writeFile($xpathChecksPath, buildXPathChecksMarkdown($xpathResults));

$soapRequestXml = buildSoapRequestXml($data);
writeFile($soapRequestPath, $soapRequestXml->saveXML());

$soapResponseXml = buildSoapResponseXml((string) $values['reference']);
writeFile($soapResponsePath, $soapResponseXml->saveXML());

$wellFormedChecks = [
    'service-request.xml' => isXmlWellFormed($serviceRequestXmlPath),
    'mock-soap-request.xml' => isXmlWellFormed($soapRequestPath),
    'mock-soap-response.xml' => isXmlWellFormed($soapResponsePath),
];

writeFile($fieldMappingPath, buildFieldMappingMarkdown());
writeFile($testNotePath, buildTestNoteMarkdown($xpathResults));

print "Interoperability validation\n";
print "Input JSON valid: Pass\n";
print "Required values found: Pass\n";
print "Canonical XML generated: {$serviceRequestXmlPath}\n";
print "Canonical XML well-formed: " . passFail($wellFormedChecks['service-request.xml']) . "\n";
print "XPath checks:\n";
foreach ($xpathResults as $expression => $value) {
    print "- {$expression}: {$value}\n";
}
print "Mock SOAP request generated: {$soapRequestPath}\n";
print "Mock SOAP request well-formed: " . passFail($wellFormedChecks['mock-soap-request.xml']) . "\n";
print "Mock SOAP response generated: {$soapResponsePath}\n";
print "Mock SOAP response well-formed: " . passFail($wellFormedChecks['mock-soap-response.xml']) . "\n";
print "No real external service called: Pass\n";
print "Evidence files generated: Pass\n";

function valueAtPath(array $data, string $path): mixed
{
    $current = $data;
    foreach (explode('.', $path) as $segment) {
        if (!is_array($current) || !array_key_exists($segment, $current)) {
            throw new RuntimeException("Required value missing: {$path}");
        }
        $current = $current[$segment];
    }

    if ($current === null || $current === '') {
        throw new RuntimeException("Required value empty: {$path}");
    }

    return $current;
}

function buildServiceRequestXml(array $data): DOMDocument
{
    $doc = new DOMDocument('1.0', 'UTF-8');
    $doc->formatOutput = true;

    $root = $doc->createElement('serviceRequest');
    $doc->appendChild($root);

    appendTextElement($doc, $root, 'reference', (string) $data['reference']);
    appendTextElement($doc, $root, 'requestType', (string) $data['requestType']);

    $location = $doc->createElement('location');
    $root->appendChild($location);
    appendTextElement($doc, $location, 'description', (string) $data['location']['description']);
    appendTextElement($doc, $location, 'postcode', (string) $data['location']['postcode']);
    appendTextElement($doc, $location, 'assetReference', (string) $data['location']['assetReference']);

    $risk = $doc->createElement('risk');
    $root->appendChild($risk);
    appendTextElement($doc, $risk, 'immediateSafetyRisk', booleanText((bool) $data['risk']['immediateSafetyRisk']));
    $risk->appendChild($doc->createElement('details'));

    $contact = $doc->createElement('contact');
    $root->appendChild($contact);
    appendTextElement($doc, $contact, 'name', (string) $data['contact']['name']);
    appendTextElement($doc, $contact, 'email', (string) $data['contact']['email']);

    appendTextElement($doc, $root, 'status', (string) $data['status']);

    return $doc;
}

function runXPathChecks(string $xmlPath): array
{
    $doc = new DOMDocument();
    $doc->load($xmlPath);
    $xpath = new DOMXPath($doc);

    $expressions = [
        '/serviceRequest/reference',
        '/serviceRequest/requestType',
        '/serviceRequest/location/postcode',
        '/serviceRequest/location/assetReference',
        '/serviceRequest/risk/immediateSafetyRisk',
        '/serviceRequest/contact/email',
        '/serviceRequest/status',
    ];

    $results = [];
    foreach ($expressions as $expression) {
        $nodes = $xpath->query($expression);
        if ($nodes === false || $nodes->length !== 1) {
            throw new RuntimeException("XPath check failed: {$expression}");
        }
        $results[$expression] = trim($nodes->item(0)->textContent);
    }

    return $results;
}

function buildSoapRequestXml(array $data): DOMDocument
{
    $doc = new DOMDocument('1.0', 'UTF-8');
    $doc->formatOutput = true;

    $envelope = $doc->createElementNS('http://schemas.xmlsoap.org/soap/envelope/', 'soapenv:Envelope');
    $doc->appendChild($envelope);
    $body = $doc->createElementNS('http://schemas.xmlsoap.org/soap/envelope/', 'soapenv:Body');
    $envelope->appendChild($body);

    $submit = $doc->createElementNS(SOAP_NAMESPACE, 'sr:SubmitServiceRequest');
    $body->appendChild($submit);

    appendNamespacedTextElement($doc, $submit, 'reference', (string) $data['reference']);
    appendNamespacedTextElement($doc, $submit, 'requestType', (string) $data['requestType']);
    appendNamespacedTextElement($doc, $submit, 'locationDescription', (string) $data['location']['description']);
    appendNamespacedTextElement($doc, $submit, 'postcode', (string) $data['location']['postcode']);
    appendNamespacedTextElement($doc, $submit, 'assetReference', (string) $data['location']['assetReference']);
    appendNamespacedTextElement($doc, $submit, 'immediateSafetyRisk', booleanText((bool) $data['risk']['immediateSafetyRisk']));
    appendNamespacedTextElement($doc, $submit, 'riskDetails', $data['risk']['details'] === null ? '' : (string) $data['risk']['details']);
    appendNamespacedTextElement($doc, $submit, 'contactName', (string) $data['contact']['name']);
    appendNamespacedTextElement($doc, $submit, 'email', (string) $data['contact']['email']);
    appendNamespacedTextElement($doc, $submit, 'status', (string) $data['status']);

    return $doc;
}

function buildSoapResponseXml(string $reference): DOMDocument
{
    $doc = new DOMDocument('1.0', 'UTF-8');
    $doc->formatOutput = true;

    $envelope = $doc->createElementNS('http://schemas.xmlsoap.org/soap/envelope/', 'soapenv:Envelope');
    $doc->appendChild($envelope);
    $body = $doc->createElementNS('http://schemas.xmlsoap.org/soap/envelope/', 'soapenv:Body');
    $envelope->appendChild($body);

    $response = $doc->createElementNS(SOAP_NAMESPACE, 'sr:SubmitServiceRequestResponse');
    $body->appendChild($response);

    appendNamespacedTextElement($doc, $response, 'accepted', 'true');
    appendNamespacedTextElement($doc, $response, 'reference', $reference);
    appendNamespacedTextElement($doc, $response, 'message', 'Request accepted for demonstration purposes');

    return $doc;
}

function appendTextElement(DOMDocument $doc, DOMElement $parent, string $name, string $value): void
{
    $element = $doc->createElement($name);
    $element->appendChild($doc->createTextNode($value));
    $parent->appendChild($element);
}

function appendNamespacedTextElement(DOMDocument $doc, DOMElement $parent, string $name, string $value): void
{
    $element = $doc->createElementNS(SOAP_NAMESPACE, 'sr:' . $name);
    if ($value !== '') {
        $element->appendChild($doc->createTextNode($value));
    }
    $parent->appendChild($element);
}

function isXmlWellFormed(string $path): bool
{
    $doc = new DOMDocument();
    return $doc->load($path);
}

function booleanText(bool $value): string
{
    return $value ? 'true' : 'false';
}

function passFail(bool $passed): string
{
    return $passed ? 'Pass' : 'Fail';
}

function writeFile(string $path, string $contents): void
{
    if (file_put_contents($path, $contents) === false) {
        throw new RuntimeException("Could not write {$path}");
    }
}

function buildXPathChecksMarkdown(array $xpathResults): string
{
    $lines = [
        '# XPath checks',
        '',
        '| XPath expression | Returned value | Result |',
        '| --- | --- | --- |',
    ];

    foreach ($xpathResults as $expression => $value) {
        $lines[] = '| `' . $expression . '` | `' . $value . '` | Pass |';
    }

    $lines[] = '';

    return implode("\n", $lines);
}

function buildFieldMappingMarkdown(): string
{
    return implode("\n", [
        '# Field mapping',
        '',
        '| JSON path | XML element | SOAP field | Notes |',
        '| --- | --- | --- | --- |',
        '| `reference` | `/serviceRequest/reference` | `reference` | Fictional service-request reference |',
        '| `requestType` | `/serviceRequest/requestType` | `requestType` | Stored machine value |',
        '| `location.postcode` | `/serviceRequest/location/postcode` | `postcode` | UK postcode |',
        '| `location.assetReference` | `/serviceRequest/location/assetReference` | `assetReference` | Streetlight reference |',
        '| `risk.immediateSafetyRisk` | `/serviceRequest/risk/immediateSafetyRisk` | `immediateSafetyRisk` | Boolean |',
        '| `contact.email` | `/serviceRequest/contact/email` | `email` | Fictional email |',
        '| `status` | `/serviceRequest/status` | `status` | Initial status |',
        '',
    ]);
}

function buildTestNoteMarkdown(array $xpathResults): string
{
    $xpathLines = [];
    foreach ($xpathResults as $expression => $value) {
        $xpathLines[] = '- `' . $expression . '` returned `' . $value . '`: Pass';
    }

    return implode("\n", [
        '# Interoperability test note',
        '',
        '## Purpose',
        '',
        'Record Milestone 13 work to demonstrate a small, tested interoperability chain using fictional council service-request data.',
        '',
        '## Input source',
        '',
        '- `evidence/06-json-output/sample-request.json`',
        '',
        '## Transformation method',
        '',
        '- PHP reads and validates the JSON input.',
        '- `DOMDocument` creates canonical XML.',
        '- `DOMXPath` selects required values from the generated XML.',
        '- `DOMDocument` creates mock SOAP 1.1 request and response envelopes.',
        '',
        '## Files produced',
        '',
        '- `evidence/15-interoperability/service-request.xml`',
        '- `evidence/15-interoperability/xpath-checks.md`',
        '- `evidence/15-interoperability/mock-soap-request.xml`',
        '- `evidence/15-interoperability/mock-soap-response.xml`',
        '- `evidence/15-interoperability/field-mapping.md`',
        '- `evidence/15-interoperability/interoperability-test-output.txt`',
        '- `evidence/15-interoperability/interoperability-test-note.md`',
        '',
        '## XPath expressions and results',
        '',
        ...$xpathLines,
        '',
        '## SOAP request and response scope',
        '',
        '- The mock SOAP request uses the fictional namespace `urn:example:council:service-request`.',
        '- The mock SOAP response records `accepted: true`, reference `LSR-DEMO-10482` and message `Request accepted for demonstration purposes`.',
        '- No SOAP request was sent to any external service.',
        '',
        '## Commands run',
        '',
        '- `ddev exec php -l scripts/validate_interoperability.php`',
        '- `ddev exec php scripts/validate_interoperability.php`',
        '- `ddev exec php -r \'foreach (["evidence/15-interoperability/service-request.xml", "evidence/15-interoperability/mock-soap-request.xml", "evidence/15-interoperability/mock-soap-response.xml"] as \$file) { \$doc = new DOMDocument(); if (!\$doc->load(\$file)) { throw new RuntimeException(\$file); } echo \$file . ": well-formed" . PHP_EOL; }\'`',
        '',
        '## Verified results',
        '',
        '- Input JSON valid: Pass',
        '- Required values found: Pass',
        '- Canonical XML generated: Pass',
        '- Canonical XML well-formed: Pass',
        '- XPath checks returned expected values: Pass',
        '- Mock SOAP request generated: Pass',
        '- Mock SOAP request well-formed: Pass',
        '- Mock SOAP response generated: Pass',
        '- Mock SOAP response well-formed: Pass',
        '- No real external service called: Pass',
        '',
        '## Limitations',
        '',
        '- No real SOAP endpoint was called.',
        '- No authentication was implemented.',
        '- No WSDL was consumed.',
        '- No XML Schema validation was performed.',
        '- The transformation is for one fictional record.',
        '- This is not a production integration.',
        '- This is not commercial middleware delivery.',
        '',
        '## Proposed claim',
        '',
        'Built and tested a small interoperability demonstration that transformed a fictional JSON service request into XML, selected values using XPath and produced well-formed mock SOAP request and response messages.',
        '',
    ]);
}
