<?php

declare(strict_types=1);

namespace Tavy315\SyliusProductSubscriptionsPlugin\Controller;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tavy315\SyliusProductSubscriptionsPlugin\Form\SubscriptionType;
use Tavy315\SyliusProductSubscriptionsPlugin\Repository\SubscriptionRepositoryInterface;
use Twig\Environment;

final readonly class SubscriptionFormAction
{
    public function __construct(
        private CustomerContextInterface        $customerContext,
        private FormFactoryInterface            $formFactory,
        private ProductRepositoryInterface      $productRepository,
        private SubscriptionRepositoryInterface $repository,
        private Environment                     $twig,
    ) {
    }

    public function __invoke(string $productCode): Response
    {
        $product = $this->productRepository->findOneBy([ 'code' => $productCode ]);
        if (!$product instanceof ProductInterface) {
            throw new NotFoundHttpException();
        }

        $form = $this->formFactory->create(SubscriptionType::class);

        $customer = $this->customerContext->getCustomer();

        if ($customer !== null && $customer->getEmail() !== null) {
            $form->remove('email');

            $existingSubscription = $this->repository->getNewSubscriptionByCustomerAndProduct($customer, $product);

            if ($existingSubscription !== null) {
                return new Response($this->twig->render('@Tavy315SyliusProductSubscriptionsPlugin/_notification.html.twig'));
            }
        }

        return new Response($this->twig->render('@Tavy315SyliusProductSubscriptionsPlugin/_form.html.twig', [
            'form'        => $form->createView(),
            'productCode' => $productCode,
        ]));
    }
}
