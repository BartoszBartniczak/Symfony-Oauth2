<?php

namespace App\Tests\Application\Service;

use App\Application\Service\UUIDService;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Application\Service\UUIDService
 */
class UUIDServiceTest extends TestCase
{

    private UUIDService $service;

    protected function setUp(): void
    {
        $this->service = new UUIDService();
    }

    /**
     * @covers ::generate
     */
    public function testGenerate()
    {
        $this->assertMatchesRegularExpression('/\b[0-9a-f]{8}\b-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-\b[0-9a-f]{12}\b/s', $this->service->generate());
    }
}
