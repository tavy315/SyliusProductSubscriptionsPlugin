<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\Entity;

use Sylius\Component\Core\Model\ProductInterface;

interface ProductAwareInterface
{
    public function getProduct(): ?ProductInterface;

    public function setProduct(?ProductInterface $product): void;
}
