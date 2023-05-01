<?php

namespace App\Twig;

use App\Service\CartManager;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CartExtension extends AbstractExtension
{
    private CartManager $cartManager;
    private Security $security;

    public function __construct(CartManager $cartManager, Security $security)
    {
        $this->cartManager = $cartManager;
        $this->security = $security;
    }
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_nbr_cart_items', [$this, 'getNbrCartItems']),
            new TwigFunction('get_cart_grand_total', [$this, 'getCartGrandTotal']),
        ];
    }

    public function getNbrCartItems(): int
    {
        return $this->cartManager->getNbrCartItems($this->security->getUser());
    }

    public function getCartGrandTotal(): float
    {
        return $this->cartManager->getCart($this->security->getUser())->getGrandTotal();
    }
}
