<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\Controller;

use Sylius\Component\Customer\Context\CustomerContextInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Tavy315\SyliusProductSubscriptionsPlugin\Form\SubscriptionType;

final class SubscriptionFormAction extends AbstractController
{
    private CustomerContextInterface $customerContext;

    public function __construct(CustomerContextInterface $customerContext)
    {
        $this->customerContext = $customerContext;
    }

    public function __invoke(string $productCode): Response
    {
        $form = $this->createForm(SubscriptionType::class);

        $customer = $this->customerContext->getCustomer();
        if ($customer !== null && $customer->getEmail() !== null) {
            $form->remove('email');
        }

        return $this->render('@Tavy315SyliusProductSubscriptionsPlugin/_form.html.twig', [
            'form'        => $form->createView(),
            'productCode' => $productCode,
        ]);
    }
}
