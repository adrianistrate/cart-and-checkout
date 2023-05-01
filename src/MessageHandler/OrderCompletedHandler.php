<?php

namespace App\MessageHandler;

use App\Message\OrderCompleted;
use App\Repository\CartRepository;
use App\Service\CartManager;
use App\Service\MailerManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class OrderCompletedHandler implements MessageHandlerInterface
{
    private CartRepository $cartRepository;
    private MailerManager $mailerManager;
    private CartManager $cartManager;

    public function __construct(CartRepository $cartRepository, MailerManager $mailerManager, CartManager $cartManager)
    {
        $this->cartRepository = $cartRepository;
        $this->mailerManager = $mailerManager;
        $this->cartManager = $cartManager;
    }
    public function __invoke(OrderCompleted $orderCompleted)
    {
        $cart = $this->cartRepository->find($orderCompleted->getCartId());

        $owners = [];
        foreach($cart->getCartItems() as $cartItem) {
            $product = $cartItem->getProduct();
            $owner = $product->getOwner();

            if($owner) {
                $products = $owners[$owner->getId()]['products'] ?? [];
                $products[] = $product;

                $owners[$owner->getId()] = [
                    'owner' => $owner,
                    'products' => $products
                ];
            }
        }

        if(count($owners)) {
            foreach($owners as $owner) {
                $this->mailerManager->notifyOwnersOnOrderCompleted($owner['owner'], $owner['products']);
            }
        }

        $this->cartManager->complete($cart);
    }
}
