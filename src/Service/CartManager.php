<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use App\Message\OrderCompleted;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CartManager
{
    private CartRepository $cartRepository;
    private EntityManagerInterface $entityManager;
    private CartItemRepository $cartItemRepository;
    private MessageBusInterface $messageBus;

    public function __construct(CartRepository $cartRepository, CartItemRepository $cartItemRepository, EntityManagerInterface $entityManager, MessageBusInterface $messageBus)
    {
        $this->cartRepository = $cartRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
    }

    public function addToCart(User $user, Product $product, ?int $quantity): void
    {
        $cart = $this->getCart($user);

        $cartItem = $this->cartItemRepository->findOneBy(['cart' => $cart, 'product' => $product]);

        if($cartItem) {
            if(0 === $quantity) {
                $cart->removeCartItem($cartItem);
                $this->entityManager->persist($cart);
            } else {
                $cartItem->setQuantity($quantity ?: $cartItem->getQuantity() + 1);
            }
        } else {
            $cartItem = new CartItem();
            $cartItem->setCart($cart);
            $cartItem->setProduct($product);
            $cartItem->setQuantity($quantity ?: 1);
        }

        $this->entityManager->persist($cartItem);
        $this->entityManager->flush();
    }

    public function getCart(User $user): Cart
    {
        $cart = $this->cartRepository->findOneBy(['user' => $user, 'status' => Cart::STATUS_NEW]);

        if(!$cart) {
            $cart = new Cart();
            $cart
                ->setUser($user)
                ->setStatus(Cart::STATUS_NEW);

            $this->entityManager->persist($cart);
            $this->entityManager->flush();
        }

        return $cart;
    }

    public function checkout(Cart $cart): void
    {
        /** @var User $user */
        $user = $cart->getUser();

        $cart->setStatus(Cart::STATUS_TO_BE_PROCESSED);
        $user->setCredit($user->getCredit() - $cart->getGrandTotal());

        $this->entityManager->persist($user);
        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        $orderCompleted = new OrderCompleted($cart->getId());
        $this->messageBus->dispatch($orderCompleted);
    }

    public function complete(Cart $cart): void
    {
        $cart->setStatus(Cart::STATUS_COMPLETED);

        $this->entityManager->persist($cart);
        $this->entityManager->flush();
    }
}
