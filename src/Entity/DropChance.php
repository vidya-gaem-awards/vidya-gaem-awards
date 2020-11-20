<?php

namespace App\Entity;

interface DropChance {
    public function getDropChance(): ?string;
    public function getAbsoluteDropChance(): ?string;
}
