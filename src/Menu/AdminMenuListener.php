<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $customersMenu = $event->getMenu()->getChild('customers');

        if (null !== $customersMenu) {
            $customersMenu
                ->addChild('list_subscriptions', ['route' => 'tavy315_sylius_product_subscriptions_admin_subscription_index'])
                ->setLabel('tavy315_sylius_product_subscriptions.ui.subscriptions')
                ->setLabelAttribute('icon', 'bell')
            ;
        }
    }
}
