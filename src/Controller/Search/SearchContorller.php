<?php

namespace App\Controller\Search;

use App\Infrastructure\Search\SearchInterface;
use App\Repository\Entity\WebsiteRepository;
use Knp\Component\Pager\Event\Subscriber\Paginate\Callback\CallbackPagination;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SearchContorller extends AbstractController
{
    private PaginatorInterface $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @Route("/recherche", name="search")
     */
    public function search(
        Request $request,
        SearchInterface $search,
        NormalizerInterface $normalizer,
        WebsiteRepository $websiteRepository
    ): Response {
        $q = $request->query->get('q', '');
        $redirect = $request->query->get('redirect', '1');

        if (!empty($q) && '0' !== $redirect) {
            $website = $websiteRepository->findByName($q);

            if (null !== $website) {
                /** @var array{'path': string, "params": string[]} $path */
                $path = $normalizer->normalize($website, 'path');

                return $this->redirectToRoute(
                    $path['path'],
                    $path['params']
                );
            }
        }

        $page = (int) $request->get('page', 1) ?: 1;
        $results = $search->search($q, [], 10, $page);
        $paginableResults = new CallbackPagination(fn () => $results->getTotal(), fn () => $results->getItems());

        return $this->render('pages/search.html.twig', [
            'q' => $q,
            'total' => $results->getTotal(),
            'results' => $this->paginator->paginate($paginableResults, $page),
        ]);
    }
}