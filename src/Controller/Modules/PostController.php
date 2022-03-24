<?php


namespace App\Controller\Modules;

use App\Classe\affisession;
use App\Entity\Posts\Post;
use App\Form\DeleteType;
use App\Lib\MsgAjax;
use App\Module\Postator;
use App\Repository\Entity\PostRepository;
use App\Repository\Entity\WebsiteRepository;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/member/wb/post")
 * @IsGranted("ROLE_CUSTOMER")
 */

class PostController extends AbstractController
{
    use affisession;

    /**
     * Create a news.
     *
     * @Route("/add-news-ajx", options={"expose"=true}, name="add_news_ajx")
     * @param Request $request
     * @param Postator $postator
     * @param WebsiteRepository $websiteRepository
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    public function AddNewsAjx(Request $request, Postator $postator, WebsiteRepository $websiteRepository): JsonResponse
    {

        if($request->isXmlHttpRequest())
        {
            if(!$website= $websiteRepository->findWbQ3($request->request->get('slug'),$this->iddispatch)) return new JsonResponse(MsgAjax::MSG_NOWB);
            $key=$website->getCodesite();
            $locate=$website->getLocality();
            $result['titre']=$request->request->get('titre');
            $result['description']=$request->request->get('description');
            $result['contenthtml']=$request->request->get('contenthtml');
            $result['post']= $request->request->get('post')!=="0"?$request->request->get('post'):false;
            $result['imagesource']=$request->request->get('file64');
            $issue=$postator->newAffiche($result, $this->dispatch, $key, $locate, $website);
            return new JsonResponse($issue);
        }else{
            return new JsonResponse(MsgAjax::MSG_ERRORRQ);
        }
    }

    /**
     * @Route("/newpost/", name="new_generic_postation")
     * @Route("/newpost/{id?}/", name="new_postation")
     * @param $id
     * @return Response
     */
    public function newPost($id=null): Response
    {
        if($id){
            if(!$this->getUserspwsiteOfWebsite($id) || !$this->admin )$this->redirectToRoute('cargo_public');
        }else{
            if(!$this->dispatch) throw new Exception('dispatch inconnu');
            $this->activeBoard();
        }

        $vartwig=$this->menuNav->templatingadmin(
            'newpost',
            $this->board->getNamewebsite(),
            $this->board,
            2
        );

        return $this->render('aff_websiteadmin/home.html.twig', [
            'directory'=>'post',
            'replacejs'=>false,
            'dispatch'=>$this->dispatch,
            'board'=>$this->board,
            'website'=>$this->board,
            'post'=>0,
            'vartwig'=>$vartwig,
        ]);
    }

    /**
     * @Route("/editpost/{id}", name="edit_post")
     * @param Request $request
     * @param PostRepository $postRepository
     * @param $id
     * @return RedirectResponse|Response
     */
    public function editPost(Request $request, PostRepository $postRepository, $id): RedirectResponse|Response
    {
        /** @var Post $post */
        if(!$post=$postRepository->findPstQ0($id))return $this->redirectToRoute('api-error',['err'=>2]); // sans controle de l'auteur pour acces superadmin
        $this->initBoardByKey($post->getKeymodule());

        if($post->getHtmlcontent()->getFileblob() && file_exists($post->getHtmlcontent()->getWebPathblob())){
            $content=file_get_contents($post->getHtmlcontent()->getWebPathblob());
        }else{
            $content="";
        }

        $vartwig=$this->menuNav->templatingadmin(
            'edit',
            "edition de l'affiche",
            $this->board,2);

        return $this->render('aff_websiteadmin/home.html.twig', [
            'directory'=>'post',
            'replacejs'=>false,
            'website'=>$this->board,
            'board'=>$this->board,
            'posta'=>$post,
            'post'=>$post->getId(),
            'content'=>$content,
            'vartwig'=>$vartwig,
            'admin'=>[$this->admin,$this->permission],
            'back'=> $this->generateUrl('module_blog',['city'=>$this->board->getLocality()->getCity(),'nameboard' => $this->board->getSlug()]),
        ]);
    }


    /**
     * @Route("/form-delete/{id}", name="form-delete_post")
     * @param Request $request
     * @param PostRepository $postRepository
     * @param $id
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function deletePost(Request $request, PostRepository $postRepository, $id): RedirectResponse|Response
    {
        /** @var Post $post */
        if(!$post=$postRepository->findPstQ0($id))return $this->redirectToRoute('api-error',['err'=>2]);
        $this->initBoardByKey($post->getKeymodule());
        $form = $this->createForm(DeleteType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setDeleted(true);
            $this->em->persist($post);
            $this->em->flush();
            $this->addFlash('info', 'post supprimé.');
            return $this->redirectToRoute('module_blog', ['city' => $this->board->getLocality()->getSlugcity(), 'nameboard'=>$this->board->getSlug()]);
        }
        $vartwig = $this->menuNav->templatingadmin(
            'deletepost',
            'delete post',
            $this->board,2);

        return $this->render('aff_websiteadmin/home.html.twig', [
            'directory'=>'post',
            'replacejs'=>false,
            'form' => $form->createView(),
            'website' => $this->board,
            'board' => $this->board,
            'dispatch'=>$this->dispatch,
            'vartwig' => $vartwig,
            'author' => $post->getAuthor()->getId()==$this->iddispatch,
            'admin'=>[$this->admin,$this->permission]
        ]);
    }

    /**
     * @Route("/publied-post/{id}/{board}", name="publied_post")
     * @param PostRepository $postRepository
     * @param Postator $postator
     * @param $id
     * @param $board
     * @return RedirectResponse|Response
     */
    public function publiedPost(PostRepository $postRepository,Postator $postator, $id,$board): RedirectResponse|Response
    {
        /** @var Post $post */
        $post=$postRepository->find($id);
        $this->initBoardByKey($post->getKeymodule());
        $ret=$postator->publiedOnePost($post);// todo récuper le retour pour gerer les erreurs
        return $this->redirectToRoute('show_blog', ['id' => $this->board->getId()]);
    }

}