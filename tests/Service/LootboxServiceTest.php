<?php

namespace App\Tests\Service;

use App\Entity\LootboxItem;
use App\Repository\LootboxItemRepository;
use App\Repository\LootboxTierRepository;
use App\Service\LootboxService;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

class LootboxServiceTest extends TestCase {

    private Stub $tierStub;
    private Stub $itemStub;
    private LootboxService $service;

    public function setUp(): void
    {
        $this->tierStub = $this->createStub(LootboxTierRepository::class);
        $this->tierStub->method('getTotalRelativeDropChance')->willReturn(0.0);

        $this->itemStub = $this->createStub(LootboxItemRepository::class);

        $this->service = new LootboxService($this->itemStub, $this->tierStub);
    }

    private function stubWillReturn(float $relative = 0.0, float $absolute = 0.0)
    {
        $this->itemStub->method('getTotalRelativeDropChance')->willReturn($relative);
        $this->itemStub->method('getTotalAbsoluteDropChance')->willReturn($absolute);
    }

    private function createItem(?float $relative = null, ?float $absolute = null): LootboxItem
    {
        return (new LootboxItem)->setDropChance($relative)->setAbsoluteDropChance($absolute);
    }

    public function testZero()
    {
        // Special case should return zero when all values are zero

        $this->stubWillReturn(0.0, 0.0);
        $this->assertSame(0.0, $this->service->getAbsoluteDropChanceFromRelativeChance(0, false));
    }

    public function testNoExistingObjects()
    {
        // 100 / (0 + 100) = 1.0

        $this->stubWillReturn(0.0, 0.0);
        $this->assertSame(1.0, $this->service->getAbsoluteDropChanceFromRelativeChance(100, true));
    }

    public function testEditingOnlyExistingObjects()
    {
        // 100 / (200 - 50 + 100) = 0.4

        $item = $this->createItem(50.0);
        $this->stubWillReturn(200.0, 0.0);
        $this->assertSame(0.4, $this->service->getAbsoluteDropChanceFromRelativeChance(100, false, $item));
    }

    public function testNewWithExistingObjects()
    {
        // 10 / (40 + 10) = 0.2

        $this->stubWillReturn(40.0, 0.0);
        $this->assertSame(0.2, $this->service->getAbsoluteDropChanceFromRelativeChance(10, true));
    }

    public function testViewChangeForExistingObjects()
    {
        // 10 / 50 = 0.2

        $this->stubWillReturn(50.0, 0.0);
        $this->assertSame(0.2, $this->service->getAbsoluteDropChanceFromRelativeChance(10, false));
    }

    public function testNewWithExistingAbsolute()
    {
        // 10 / (40 + 10) = 0.2
        // 0.2 * (1 - 0.25) = 0.15

        $this->stubWillReturn(40.0, 0.25);
        $this->assertSame(0.15, $this->service->getAbsoluteDropChanceFromRelativeChance(10, true));
    }

    public function testAbsoluteAtOne()
    {
        // If absolute is 1.0, all relative drop chances will have a 0.0 absolute chance

        $this->stubWillReturn(40.0, 1.0);
        $this->assertSame(0.0, $this->service->getAbsoluteDropChanceFromRelativeChance(10, true));
    }

    public function testAbsoluteMoreThanOne()
    {
        // The absolute value should not be allowed to be more than 1.0 in normal cases.
        // But if it somehow happens, relative drop chance should still return 0.0

        $this->stubWillReturn(40.0, 2.0);
        $this->assertSame(0.0, $this->service->getAbsoluteDropChanceFromRelativeChance(10, true));
    }

    public function testEditWhereOriginalObjectHasAbsolute()
    {
        // 10 / (40 + 10) = 0.2
        // 0.2 * (1 - 0.4 + 0.2) = 0.16

        $item = $this->createItem(null, 0.2);
        $this->stubWillReturn(40.0, 0.4);
        $this->assertSame(0.16, $this->service->getAbsoluteDropChanceFromRelativeChance(10, false, $item));
    }
}
