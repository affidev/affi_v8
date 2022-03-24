<?php


namespace App\Controller\Repertories;


use App\Classe\customersession;
use App\Entity\Customer\Customers;
use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\DispatchSpace\Spwsite;
use App\Entity\Posts\Post;
use App\Entity\Repertories\Books;
use App\Form\BookType;
use App\Form\DeleteType;
use App\Form\ProfilType;
use App\Lib\Links;
use App\Lib\MsgAjax;
use App\Repository\Entity\ModuleTypeRepository;
use App\Repository\Entity\PostRepository;
use App\Repository\Entity\WebsiteRepository;
use App\Service\Calendar\Calendator;
use App\Service\Registration\Sessioninit;
use App\Service\Repertories\Bookator;
use App\Service\SpaceWeb\SpacewebFactor;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class RepCustomerController extends AbstractController
{
    use customersession;

    /**
     * @Route("/copie-edit-space/", name="copie_update_space")
     * @param Request $request
     * @param SpacewebFactor $spaceWebtor
     * @param Sessioninit $sessioninit
     * @return RedirectResponse|Response
     */
    public function repCustomer(Request $request, SpacewebFactor $spaceWebtor, Sessioninit $sessioninit)
    {
        if(!$this->dispatch) return $this->redirectToRoute('cargo_public');

        if($this->dispatch->getLocality()==null)return $this->redirectToRoute('spaceweblocalize_init');
        $form=$this->createForm(ProfilType::class,$this->dispatch->getCustomer()->getProfil());
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            // $spaceWebtor->majDistach( $dispatch, $form); //todo ???? a voir
            $this->em->flush();
            $sessioninit->initSession($this->dispatch);
            $this->addFlash('success', 'Vos informations ont bien été mises à jour');
            return $this->redirectToRoute('cargo_public');
        }
        $vartwig=$this->menuNav->templateControlCustomer(
            Links::CUSTOMER_LIST,
            'update',
            "update");

        return $this->render('backoffice/customer/home.html.twig', [
            'directory'=>"customer/registration",
            'dispatch'=>$this->dispatch,
            'vartwig'=>$vartwig,
            'form'=>$form->createView(),
            'permissions'=>$this->permission
        ]);
    }

    /**
     * @Route("/{slugcity}/rep/nouveau-rep/", name="new_rep")
     * @param Request $request
     * @param Bookator $bookator
     * @param $slugcity
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function newRep(Request $request, Bookator $bookator, $slugcity)
    {
        if(!$this->dispatch) return $this->redirectToRoute('identify',['city'=>$slugcity]);

        $book=new Books();
        $form=$this->createForm(BookType::class,$book);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            /** @var DispatchSpaceWeb $dispatch */
            $bookator->newBook($dispatch, $book);
            return $this->redirectToRoute('cargo_public',['slugcity'=>$slugcity]);
        }
        $vartwig=$this->menuNav->templateControlCustomer(
            Links::CUSTOMER_LIST,
            'new',
            "new-book");

        return $this->render('public/home.html.twig', [
            'vartwig'=>$vartwig,
            'directory'=>"Repertories",
            'dispatch'=>$this->dispatch,
            'form'=>$form->createView(),
            'permissions'=>$this->permission,
            'city'=>$slugcity??null,
        ]);
    }

    /**
     * Create a book.
     *
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     * @Route("/add-book-ajx", options={"expose"=true}, name="add_book_ajx")
     * @param Request $request
     * @param Bookator $bookator
     * @return JsonResponse
     * @throws \Exception
     */
    public function AddBookAjx(Request $request, Bookator $bookator): JsonResponse
    {

        if($request->isXmlHttpRequest())
        {
            $dispatch=$this->dispatch;
            if(!$dispatch['website']) return new JsonResponse(MsgAjax::MSG_ERRORMD);
            $result['dispatch']=$dispatch;
            $result['titre']=$request->request->get('titre');
            $result['description']=$request->request->get('description');
            $result['book']= $request->request->get('book');
            $result['imagesource']=$request->request->get('file64');
            $issue=$bookator->newPostar($result);
            return new JsonResponse($issue);
        }else{
            return new JsonResponse(MsgAjax::MSG_ERRORRQ);
        }
    }

    /**
     * @Route("/newpost/{id}/", name="copie_new_postation")
     * @param Request $request
     * @param ModuleTypeRepository $moduleTypeRepository
     * @param WebsiteRepository $websiteRepository
     * @param Calendator $calendator
     * @param $id
     * @param null $month
     * @param null $year
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function newPost(Request $request, ModuleTypeRepository $moduleTypeRepository, WebsiteRepository $websiteRepository, Calendator $calendator, $id, $month=null, $year=null)
    {
        /** @var Spwsite $pw */
        $pw=$this->getUserspwsiteOfWebsite($id);
        if(!$pw || !$this->admin) return $this->redirectToRoute('cargo_public');
        $website=$pw->getWebsite();
        $vartwig=$this->dispatch->templatingspaceWeb(
            $website,
            'post/newpost',
            "post",
            $website->getNamewebsite());

        return $this->render('website/home.html.twig', [
            'agent'=>$this->useragent,
            'website'=>$website,
            'post'=>0,
            'pw'=>$pw,
            'pwsite'=>['isadmin'=>false],
            'vartwig'=>$vartwig,
            'directory'=>'member',
            'admin'=>[$this->admin,$this->permission]
        ]);
    }

    /**
     * @Route("/editpost/{id}", name="copie_edit_post")
     * @param Request $request
     * @param PostRepository $postRepository
     * @param $id
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function editPost(Request $request, PostRepository $postRepository, $id)
    {
        /** @var Post $post */
        $post=$postRepository->findPstQ0($id); // sans controle de l'auteur pour acces superadmin
        if(!$post) return $this->redirectToRoute('api-error',['err'=>2]);
        if($post->getHtmlcontent()->getFileblob() && file_exists($post->getHtmlcontent()->getFileblob())){
            $content=file_get_contents($post->getHtmlcontent()->getWebPathblob());
        }else{
            $content="";
        }
        /** @var Spwsite $pw */
        $pw=$this->getUserspwsiteOfWebsite($post->getWebsite()->getId());

        $vartwig=$this->dispatch->templatingspaceWeb(
            null,
            'post/edit',
            "post",
            "post");

        return $this->render('website/home.html.twig', [
            'agent'=>$this->useragent,
            'website'=>$post->getWebsite(),
            'posta'=>$post,
            'post'=>$post->getId(),
            'content'=>$content,
            'directory'=>'member',
            'pw'=>$pw,
            'vartwig'=>$vartwig,
            'admin'=>[$pw->isAdmin(),$this->permission]
        ]);
    }


    /**
     * @Route("/form-delete/{id}", name="copie_form-delete_post")
     * @param Request $request
     * @param PostRepository $postRepository
     * @param $id
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function deletePost(Request $request, PostRepository $postRepository, $id)
    {
        /** @var Post $post */
        $post=$postRepository->findPstQ0($id);
        if($post){
            $pw=$this->getUserspwsiteOfWebsite($post->getWebsite()->getId());
            $website=$post->getWebsite();
            $form = $this->createForm(DeleteType::class, $post);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $post->setDeleted(true);
                $this->em->persist($post);
                $this->em->flush();
                $this->addFlash('info', 'post supprimé.');
                return $this->redirectToRoute('tab_spaceweb', ['id' => $website->getId()]);
            }
            $vartwig = $this->dispatch->templatingspaceWeb(
                null,
                'post/deletepost',
                'delte post',
                $website->getNamewebsite());


            return $this->render('website/home.html.twig', [
                'agent' => $this->useragent,
                'form' => $form->createView(),
                'website' => $website,
                'vartwig' => $vartwig,
                'directory'=>'member',
                'pw'=>$pw,
                'author' => $post->getAuthor()->getId()==$this->iddispatch ?true:false,
                'admin'=>[$pw->isAdmin(),$this->permission]
            ]);
        }
        return $this->redirectToRoute('api-error',['err'=>2]);
    }

}