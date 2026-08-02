<?php

declare(strict_types=1);

namespace Drupal\council_service_request_api\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns fictional council service request demonstration data.
 */
final class ServiceRequestApiController {

  /**
   * Returns a fictional service request.
   */
  public function demo(): JsonResponse {
    return new JsonResponse([
      'reference' => 'LSR-DEMO-10482',
      'requestType' => 'faulty_streetlight',
      'location' => [
        'description' => 'Outside number 24, Example Street',
        'postcode' => 'BN3 1AA',
        'assetReference' => 'LP-418',
      ],
      'risk' => [
        'immediateSafetyRisk' => FALSE,
        'details' => NULL,
      ],
      'contact' => [
        'name' => 'Alex Morgan',
        'email' => 'alex@example.test',
      ],
      'status' => 'new',
    ]);
  }

}
