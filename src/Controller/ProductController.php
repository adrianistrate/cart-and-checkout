<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/", name="app_products")
     */
    public function index(Request $request, PaginatorInterface $paginator, ProductRepository $productRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $keyword = $request->query->get('keyword');

        $qb = $productRepository->getQueryBuilderWithSearch($keyword);

        $queryParams = ['keyword' => $keyword];

        $pagination = $paginator->paginate($qb, $page, 5);

        return $this->render('product/index.html.twig', [
            'pagination' => $pagination,
            'keyword' => $keyword,
            'queryParams' => $queryParams,
        ]);
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return Response
     *
     * @Route("/product/{id}", name="app_product_view", requirements={"id"="\d+"})
     */
    public function view(Product $product): Response
    {
        return $this->render('product/product.html.twig', [
            'product' => $product,
        ]);
    }
}
