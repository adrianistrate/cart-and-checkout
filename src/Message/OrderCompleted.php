<?php

namespace App\Message;

class OrderCompleted
{
    private int $cartId;

    public function __construct(int $cartId)
    {
        $this->cartId = $cartId;
    }

    public function getCartId(): int
    {
        return $this->cartId;
    }
}
