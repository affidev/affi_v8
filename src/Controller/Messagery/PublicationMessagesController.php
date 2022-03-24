<?php


namespace App\Controller\Messagery;


use App\Classe\affisession;
use App\Repository\Entity\PublicationConversRepository;
use App\Service\Messages\PublicationMessageor;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



/**
 * @Route("/messagery/publication/")
 */

class PublicationMessagesController extends AbstractController
{
    use affisession;

    /**
     * @Route("/affiche/msg/blog/read/{id?}", name="read_message_blog")
     * @param PublicationConversRepository $publicationConversRepository
     * @param PublicationMessageor $messageor
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     */
    public function readmsgBlog(PublicationConversRepository $publicationConversRepository,PublicationMessageor $messageor, $id): Response
    {
        $convers = $publicationConversRepository->findOneByIdWithAll($id);
        $msgs = $convers->getMsgs();
        $post = $convers->getTabpublication()->getPost();
        $this->initBoardByKey($post->getKeymodule());
        if (!$this->admin) throw new Exception('dispatch ne dispose pas des droits erreur log notification');
        $messageor->majTabNotifs($msgs, $this->dispatch,$this->em);
        $notifs=$this->dispatch->getTbnotifs();

        $vartwig=$this->menuNav->templatingadmin(
            'show-msgblog',
            $this->board->getNamewebsite(),
            $this->board,
            2
        );

        return $this->render('aff_messagery/home.html.twig', [
            'directory'=>"publication",
            'replacejs'=>false,
            'vartwig'=>$vartwig,
            'post'=>$post,
            'website'=>$this->board,
            'board'=>$this->board,
            'dispatch'=>$this->dispatch,
            'msgpublication'=>$convers,
            'notifs'=>$notifs,
        ]);
    }

    /**
     * @Route("new-message-publication", name="new_messages_publication")
     * @param PublicationMessageor $messageor
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function newMsgPublication(PublicationMessageor $messageor,Request $request): JsonResponse
    {
        if($request->isXmlHttpRequest())
        {
            $data = json_decode((string) $request->getContent(), true); //"comment" "slug" "status" "id" "module"
            $result=$messageor->newPublicationConvers($data,$this->dispatch??null);
            if(!$result) return new JsonResponse(['success'=>false]);
            $responseCode = 200;
            http_response_code($responseCode);
            header('Content-Type: application/json');
            return new JsonResponse(['success' => true]);
        }
        return new JsonResponse(['success'=>false]);
    }

    /**
     * @Route("add-convers",  name="add_publications-convers")
     * @param Request $request
     * @param PublicationConversRepository $publicationrepo
     * @param PublicationMessageor $messageor
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function addPublicationConvers(Request $request, PublicationConversRepository $publicationrepo, PublicationMessageor $messageor): JsonResponse
    {

        if ($request->isXmlHttpRequest()) {
            if(!$this->dispatch)  return new JsonResponse(['success' => false,'inf'=> "dispatch pas reconnu"]);
            $data = json_decode((string) $request->getContent(), true);

            $convers = $publicationrepo->findPublicationConversAndMsgById($data['slug']); //slug est historique ici il correspond à l'id du message
            if ($convers) {
                $convers = $messageor->addConvers($data,$convers, $this->dispatch);
                $responseCode = 200;
                http_response_code($responseCode);
                header('Content-Type: application/json');
                return new JsonResponse(['success' => true, 'convers' => $convers->getId()]);
            }
            return new JsonResponse(['success' => false,'inf' => "message inconnu"]);
        }
        return new JsonResponse(['success' => false]);
    }


}