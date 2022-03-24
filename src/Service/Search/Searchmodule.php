<?php


namespace App\Service\Search;


use App\Entity\Food\ArticlesFormule;
use App\Entity\Module\ModuleList;
use App\Repository\Entity\ArticlesFormuleRepository;
use App\Repository\Entity\FormulesRepository;
use App\Repository\Entity\OffresRepository;
use App\Repository\Entity\PostEventRepository;
use App\Repository\Entity\PostRepository;
use App\Repository\Entity\WebsiteRepository;
use Doctrine\ORM\NonUniqueResultException;

class Searchmodule
{

    private OffresRepository $offreRepository;
    private PostRepository $postRepository;
    private PostEventRepository $postEventRepository;
    private WebsiteRepository $websiteRepository;
    private ArticlesFormuleRepository $articlesFormuleRepository;
    private FormulesRepository $formuleRepository;


    public function __construct(WebsiteRepository $websiteRepository,PostEventRepository $postEventRepository,
                                OffresRepository $offreRepository, PostRepository $postRepository,
                                ArticlesFormuleRepository $articlesFormuleRepository, FormulesRepository $formulesRepository)
    {
        $this->offreRepository = $offreRepository;
        $this->postRepository = $postRepository;
        $this->postEventRepository=$postEventRepository;
        $this->websiteRepository=$websiteRepository;
        $this->articlesFormuleRepository=$articlesFormuleRepository;
        $this->formuleRepository=$formulesRepository;
    }


    public function findModule($wb, $classmodule): bool| ModuleList
    {
        $listModules=$wb->getListmodules();
        if (!empty($listModules)) {
            foreach ($listModules as $modList) {
                if ($modList->getClassmodule() === $classmodule) {
                    return $modList;
                }
            }
        }
        return false;
    }


    /**
     * @param $id
     * @return bool|array
     * @throws NonUniqueResultException
     */
    public function searchOnePostAndListAndMsg($id): bool|array
    {
        $posts=[];
        $post=$this->postRepository->findOnePostAndMsg($id);

        if($post){
            $board=$this->websiteRepository->findWbByKey($post->getKeymodule());
            if($post->getHtmlcontent()->getFileblob()){
                $content=file_get_contents($post->getHtmlcontent()->getphpPathblob());
            }else{
                $content="";
            }
            $posts=$this->postRepository->findPostsByKeyWithOutId($post->getKeymodule(),$id);
            return ['board'=>$board,'posts'=>$posts, 'post'=>$post,'content'=>$content, 'key'=>$post->getKeymodule(), "msgp"=>$post->getTbmessages()];
        }else{
            return false;
        }
    }

    /**
     * @param $id
     * @return bool|array
     * @throws NonUniqueResultException
     */
    public function searchOnePostAndMsgP($id): bool|array
    {

        $post=$this->postRepository->findOnePostAndMsg($id);

        if($post){
            $board=$this->websiteRepository->findWbByKey($post->getKeymodule());
            if($post->getHtmlcontent()->getFileblob()){
                $content=file_get_contents($post->getHtmlcontent()->getphpPathblob());
            }else{
                $content="";
            }

            return ['board'=>$board, 'post'=>$post,'content'=>$content, 'key'=>$post->getKeymodule(), "msgp"=>$post->getTbmessages()];
        }else{
            return false;
        }
    }

    /**
     * @param $id
     * @return array|bool
     * @throws NonUniqueResultException
     */
    public function searchOneOffreandList($id): bool|array
    {
        $offre=$this->offreRepository->findOneOffre($id);
        if($offre){
            $board=$this->websiteRepository->findWbByKey($offre->getKeymodule());
            if( $offre->getProduct()->getHtmlcontent()->getFileblob()){
                $content=file_get_contents($offre->getProduct()->getHtmlcontent()->getWebPathblob());
            }else{
                $content="";
            }
            $offres=$this->offreRepository->findOffresByKeyWithOutId($offre->getKeymodule(),$id);
            return ['board'=>$board,'offres'=>$offres, 'offre'=>$offre,'content'=>$content, 'key'=>$offre->getKeymodule()];
        }else{
            return false;
        }
    }

    /**
     * @param $id
     * @return array|bool
     * @throws NonUniqueResultException
     */
    public function searchOneEventandList($id): bool|array
    {
        $event=$this->postEventRepository->findEventById($id);
        if($event){
            $board=$this->websiteRepository->findWbByKey($event->getKeymodule());
            $events=$this->postEventRepository->findEventsByKeyWithOutId($event->getKeymodule(), $id);
            return ['events'=>$events, 'event'=>$event, 'board'=>$board];
        }else{
            return false;
        }
    }


    /**
     * @param $id
     * @return array|bool
     * @throws NonUniqueResultException
     */
    public function searchOneMenuandList($id): bool|array
    {
        $menu=$this->formuleRepository->findFormuleById($id);
        if($menu){
            $board=$this->websiteRepository->findWbByKey($menu->getKeymodule());
            $menus=$this->formuleRepository->findFormulessByKeyWithOutId($menu->getKeymodule(), $id);
            return ['menus'=>$menus, 'menu'=>$menu, 'board'=>$board];
        }else{
            return false;
        }
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