<?php

namespace App\Module;

use App\AffiEvents;
use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\Module\TabpublicationMsgs;
use App\Entity\Posts\Post;
use App\Event\MessageEvent;
use App\Event\PostEvent;
use App\Lib\MsgAjax;
use App\Repository\Entity\PostRepository;
use \DateTime;
use App\Entity\Posts\Article;
use App\Entity\Media\Imagejpg;
use App\Entity\Media\Media;
use Doctrine\ORM\EntityManagerInterface;;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class Postator
{
    private PostRepository $postRepository;
    private EventDispatcherInterface $eventdispatcher;
    private EntityManagerInterface $em;
    private DateTime $now;

    /**
     * Postar constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param EntityManagerInterface $entityManager
     * @param PostRepository $postRepository
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, EntityManagerInterface $entityManager, PostRepository $postRepository)
    {
        $this->em=$entityManager;
        $this->now= New DateTime();
        $this->postRepository = $postRepository;
        $this->eventdispatcher=$eventDispatcher;
    }

    /**
     * @param $result
     * @param DispatchSpaceWeb $author
     * @return array|int|null
     */
    public function newAffiche($result, DispatchSpaceWeb $author, $key, $locate, $website): array|int|null
    {
        if($result['post']){
            $affiche=$this->postRepository->find($result['post']);
            if (!$affiche) return MsgAjax::MSG_ERR1;
            $affiche->setModifAt($this->now);
            $affiche->setKeymodule($key);
            $article=$affiche->getHtmlcontent();
            $article->deleteFile();
            $media=$affiche->getMedia();
        }else{
            $affiche = new Post();
            $affiche->setDeleted(false);
            $affiche->setKeymodule($key);
            $affiche->setAuthor($author);
            $affiche->setLocalisation($locate);
            $article= new Article();
            $article->setDatecreat($this->now);
            $article->setDeleted(false);
            $affiche->setHtmlcontent($article);
            $media= New Media();
            $affiche->setMedia($media);
            $tabmsg=new TabpublicationMsgs();
            $affiche->setTbmessages($tabmsg);
            $tabmsg->setPost($affiche);
        }
        $affiche->setTitre($result['titre']);
        $affiche->setSubject(strip_tags($result['description'], null));
        if($result['contenthtml']!="")
        {
            $options=[
                'filesource'=>$result['contenthtml'],
                "tag"=>$result['titre'],
                "name"=>$result['titre']];
            $article->setFile($options);
            $data=$article->initNameFile();
            if($data){
                $article->uploadContent();
            }else{
                $article->deleteFile();
            }
        }

        if($result['imagesource']!="false"){
            $etapefile = $this->AddFiles($result['imagesource'], $media);
            if (!$etapefile) return MsgAjax::MSG_POST2;
        }
        $this->em->persist($affiche);
        $this->em->flush();
     //   $event = new PostEvent($affiche);
       // $this->eventdispatcher->dispatch($event, AffiEvents::NOTIFICATION_NEW_POST);
        return MsgAjax::MSG_POSTOK;
    }

    protected function AddFiles($imagesource, $media): bool
    {
        $options=['file'=>$imagesource,'filetyp'=>'64','name'=>'filereader']; //todo recupere le nom
        $images=$media->getImagejpg();
        if(count($images)>0){
            foreach ($images as $image){
                $media->removeImagejpg($image);  //todo pour l'instant je supprime toutes les images pour eviter des erreurs !!
            }
        }
        $this->createmediasJpg($options, $media);
        return true;
    }

    protected function createmediasJpg($options, $media): bool
    {
        $imagejpg = new Imagejpg();
        $imagejpg->setFile($options);
        $media->addImagejpg($imagejpg);
        return true;
    }

    /**
     * @param $idpost
     * @param $posts
     * @return array
     */
    public function publiedPost($idpost, $posts): array
    {
        foreach ($posts as $el){
            if($el->getId() == $idpost){
                if($el->getPublied()){
                    $el->setPublied(false);
                }else{
                    $el->setPublied(true);
                }
            } else{
                $el->setPublied(false);
            }
            $this->em->persist($el);
        }
        $this->em->flush();
        return MsgAjax::MSG_POSTOK;
        
    }

    /**
     * @param Post $post
     * @return array|null
     */
    public function publiedOnePost(Post $post): ?array
    {
        $post->setPublied(!$post->getPublied());
        $this->em->persist($post);
        $this->em->flush();
        return MsgAjax::MSG_POSTOK;
    }
}
