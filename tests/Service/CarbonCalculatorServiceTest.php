<?php

namespace App\Tests\Service;

use App\Service\CarbonCalculatorService;
use PHPUnit\Framework\TestCase;

class CarbonCalculatorServiceTest extends TestCase
{
    public function testTransportCalculation(): void
    {
        $service = new CarbonCalculatorService([
            'transport' => ['car' => 0.2],
            'energy' => ['electricity' => 0.05],
            'food' => ['meat' => 4.0],
            'digital' => ['streaming_hour' => 0.06],
        ]);

        $result = $service->calculate('transport', ['distance_km' => 10, 'mode' => 'car']);

        $this->assertSame(2.0, $result);
    }
}