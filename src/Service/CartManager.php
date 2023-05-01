<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;

class CartManager
{
    private CartRepository $cartRepository;
    private EntityManagerInterface $entityManager;
    private CartItemRepository $cartItemRepository;

    public function __construct(CartRepository $cartRepository, CartItemRepository $cartItemRepository, EntityManagerInterface $entityManager)
    {
        $this->cartRepository = $cartRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->entityManager = $entityManager;
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
            $cartItem->setQuantity(1);
        }

        $this->entityManager->persist($cartItem);
        $this->entityManager->flush();
    }

    public function getCart(User $user)
    {
        $cart = $this->cartRepository->findOneBy(['user' => $user]);

        if(!$cart) {
            $cart = new Cart();
            $cart->setUser($user);

            $this->entityManager->persist($cart);
            $this->entityManager->flush();
        }

        return $cart;
    }

    public function getNbrCartItems(User $User): int
    {
        return array_reduce($this->getCart($User)->getCartItems()->toArray(), static fn (int $carry, CartItem $cartItem) => $carry + $cartItem->getQuantity(), 0);
    }
}
