<?php


namespace App\Service\Search;



use App\Entity\Food\ArticlesFormule;
use App\Repository\Entity\ArticlesFormuleRepository;

class Searchcarte
{

    private ArticlesFormuleRepository $articlesFormuleRepository;



    public function __construct( ArticlesFormuleRepository $articlesFormuleRepository)
    {
        $this->articlesFormuleRepository=$articlesFormuleRepository;
    }

    public function searchcarte($paginator, $repo, $request, $search)
    {
        return $paginator->paginate(
            $repo->findCarteAll($search),
            $request->query->getInt('page', 1),
            3
        );
    }

    public function findCarte($key): array
    {
        $tabcarte=['entree'=>[],'plat'=>[],'dessert'=>[],'boisson'=>[]];
        if ($key) {
            $cartes=$this->articlesFormuleRepository->findAllByKey($key);
            if(!empty($cartes)){
                /** @var ArticlesFormule $carte */
                foreach ($cartes as $carte){
                    if($carte->getCategorie()->getId()==1)$tabcarte['entree'][]=$carte;
                    if($carte->getCategorie()->getId()==2)$tabcarte['plat'][]=$carte;
                    if($carte->getCategorie()->getId()==3)$tabcarte['dessert'][]=$carte;
                    if($carte->getCategorie()->getId()==4)$tabcarte['boisson'][]=$carte;
                }
                return $tabcarte??[];
            }
        }
        return $tabcarte;
    }
}
