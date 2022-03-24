<?php

namespace App\Controller\Api;


use App\LinkLocal\Linkers\Repository\WebsiteLinkerRepository;
use App\Controller\AbstractController;
use App\Infrastructure\Search\SearchInterface;
use App\Infrastructure\Search\SearchResultItemInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;



class SearchController extends AbstractController
{
    private SearchInterface $search;
    private websiteLinkerRepository $wblinkerRepository;
    private SerializerInterface $serializer;
    private WebsiteLinkerRepository $websiteLinkerRepository;

    public function __construct(SearchInterface $search, WebsiteLinkerRepository $wblinkerRepository, SerializerInterface $serializer)
    {
        $this->search = $search;
        $this->websiteLinkerRepository = $wblinkerRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Route("ajx/method/find/search", name="search_api")
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim($request->get('q', ''));
        if (empty($q)) {
            return $this->json([]);
        }
        $city=trim($request->get('city', ''));

        /*
        $technologies = $this->websiteLinkerRepository->searchByName($q);
        $technologiesMatches = array_map(fn (Website $technology) => [
            'title' => $technology->getName(),
            'url' => $this->serializer->serialize($technology, 'path'),
            'category' => 'Technologie',
        ], $technologies);

        */

        // On trouve les contenus qui correspondent à la recheche
        $results = $this->search->search($q, [$city], 5);
   //     dump($results);
        $contentMatches = array_map(fn (SearchResultItemInterface $item) => [
            'title' => $item->getTitle(),
            'id'=>$item->getId(),
            'url' => $item->getUrl(),
            'category' => $item->getType(),
            'pict'=>$item->getPict(),
        ], $results->getItems());

        return $this->json([
           // 'items' => array_merge($technologiesMatches, $contentMatches),
            'items' =>  $contentMatches,
            'hits' => $results->getTotal(),
        ]);
    }

    /**
     * @Route("ajx/method/find/searchnocity", name="search_api_nocity")
     */
    public function searchnocity(Request $request): JsonResponse
    {
        $q = trim($request->get('q', ''));
        if (empty($q)) {
            return $this->json([]);
        }

        /*
        $technologies = $this->websiteLinkerRepository->searchByName($q);
        $technologiesMatches = array_map(fn (Website $technology) => [
            'title' => $technology->getName(),
            'url' => $this->serializer->serialize($technology, 'path'),
            'category' => 'Technologie',
        ], $technologies);

        */

        // On trouve les contenus qui correspondent à la recheche
        $results = $this->search->search($q, [], 5);
        //     dump($results);
        $contentMatches = array_map(fn (SearchResultItemInterface $item) => [
            'title' => $item->getTitle(),
            'id'=>$item->getId(),
            'url' => $item->getUrl(),
            'category' => $item->getType(),
            'pict'=>$item->getPict(),
        ], $results->getItems());

        return $this->json([
            // 'items' => array_merge($technologiesMatches, $contentMatches),
            'items' =>  $contentMatches,
            'hits' => $results->getTotal(),
        ]);
    }

    /**
     * @Route("ajx/method/find/totalsearch", name="search_api_total")
     */
    public function searchTotal(Request $request): JsonResponse
    {
        $q = trim($request->get('q', ''));
        if (empty($q)) {
            return $this->json([]);
        }

        /*
        $technologies = $this->websiteLinkerRepository->searchByName($q);
        $technologiesMatches = array_map(fn (Website $technology) => [
            'title' => $technology->getName(),
            'url' => $this->serializer->serialize($technology, 'path'),
            'category' => 'Technologie',
        ], $technologies);

        */

        // On trouve les contenus qui correspondent à la recheche
        $results = $this->search->search($q, [], 5);
        //     dump($results);
        $contentMatches = array_map(fn (SearchResultItemInterface $item) => [
            'title' => $item->getTitle(),
            'id'=>$item->getId(),
            'url' => $item->getUrl(),
            'category' => $item->getType(),
            'pict'=>$item->getPict(),
        ], $results->getItems());

        return $this->json([
            // 'items' => array_merge($technologiesMatches, $contentMatches),
            'items' =>  $contentMatches,
            'hits' => $results->getTotal(),
        ]);
    }
}
