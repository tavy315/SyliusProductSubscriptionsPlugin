<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\Entity;

interface StatusAwareInterface
{
    public const STATUS_NEW = 1;
    public const STATUS_SENT = 2;
    public const STATUS_DELETED = 3;

    public function getStatus(): int;

    public function setStatus(int $status);
}
