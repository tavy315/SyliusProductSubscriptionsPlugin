<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\Controller;

use Sylius\Component\Customer\Context\CustomerContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Tavy315\SyliusProductSubscriptionsPlugin\Form\SubscriptionType;
use Twig\Environment;

final class SubscriptionFormAction
{
    private CustomerContextInterface $customerContext;

    private FormFactoryInterface $formFactory;

    private Environment $twig;

    public function __construct(CustomerContextInterface $customerContext, FormFactoryInterface $formFactory, Environment $twig)
    {
        $this->customerContext = $customerContext;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
    }

    public function __invoke(string $productCode): Response
    {
        $form = $this->formFactory->create(SubscriptionType::class);

        $customer = $this->customerContext->getCustomer();
        if ($customer !== null && $customer->getEmail() !== null) {
            $form->remove('email');
        }

        return new Response($this->twig->render('@Tavy315SyliusProductSubscriptionsPlugin/_form.html.twig', [
            'form'        => $form->createView(),
            'productCode' => $productCode,
        ]));
    }
}
