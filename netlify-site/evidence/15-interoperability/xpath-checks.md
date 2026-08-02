# XPath checks

| XPath expression | Returned value | Result |
| --- | --- | --- |
| `/serviceRequest/reference` | `LSR-DEMO-10482` | Pass |
| `/serviceRequest/requestType` | `faulty_streetlight` | Pass |
| `/serviceRequest/location/postcode` | `BN3 1AA` | Pass |
| `/serviceRequest/location/assetReference` | `LP-418` | Pass |
| `/serviceRequest/risk/immediateSafetyRisk` | `false` | Pass |
| `/serviceRequest/contact/email` | `alex@example.test` | Pass |
| `/serviceRequest/status` | `new` | Pass |
