<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="app_cart", methods={"POST"})
     */
    public function index(Request $request, ProductRepository $productRepository, CartManager $cartManager): Response
    {
        $productId = $request->request->get('product_id');
        $quantity = $request->request->getInt('quantity', null);

        if($quantity < 0) {
            $this->addFlash('error', 'Invalid quantity!');
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }

        $product = $productRepository->find($productId);

        if (!$product) {
            throw $this->createNotFoundException('Product not found!');
        }

        $cartManager->addToCart($this->getUser(), $product, $quantity);

        $this->addFlash('success', 'Product added to cart!');

        return $this->redirectToRoute('app_cart');
    }

    /**
     * @Route("/cart", name="app_cart_show", methods={"GET"})
     */
    public function show(CartManager $cartManager): Response
    {
        $cart = $cartManager->getCart($this->getUser());

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    /**
     * @Route("/cart/checkout", name="app_cart_checkout", methods={"POST"})
     */
    public function checkout(): Response
    {
        return $this->render('cart/checkout.html.twig');
    }
}
