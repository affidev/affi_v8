<?php


namespace App\Controller\Ajax;


use App\Repository\Entity\OffresRepository;
use App\Repository\Entity\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


class AjaxController extends AbstractController
{
    /**
     * @Route("ajx/apifile/content-post/{id}", options={"expose"=true}, name="ajx_content_post", methods={"GET"})
     * @param Request $request
     * @param PostRepository $postRepository
     * @param SerializerInterface $serializer
     * @param $id
     * @return JsonResponse
     */
    public function loadContentPost(Request $request, PostRepository $postRepository, SerializerInterface $serializer, $id): JsonResponse
    {

                if ($post=$postRepository->find($id)) {
                    if ($post->getHtmlcontent()->getFileblob()) {
                        if(file_exists($post->getHtmlcontent()->getphpPathblob())){
                        $content = file_get_contents($post->getHtmlcontent()->getphpPathblob());
                        $contentjson = $serializer->serialize($content, 'json');
                        $responseCode = 200;
                        http_response_code($responseCode);
                        header('Content-Type: application/json');
                        return new JsonResponse(['success' => true, "notice" => $contentjson]);
                        }
                        return new JsonResponse(['error' => "no file exits"]);
                    }
                    return new JsonResponse(['success' => "no get fileblog"]);
                }
            return new JsonResponse(['success' => "no find notice"]);


    }

    /**
     * @Route("ajx/apifile/content-offre/{id}", options={"expose"=true}, name="ajx_content_offre", methods={"GET"})
     * @param Request $request
     * @param OffresRepository $offresRepository
     * @param SerializerInterface $serializer
     * @param $id
     * @return JsonResponse
     */
    public function loadContentOffre(Request $request, OffresRepository $offresRepository, SerializerInterface $serializer, $id): JsonResponse
    {

        if ($offre = $offresRepository->find($id)) {
            if ($offre->getProduct()->getHtmlcontent()->getFileblob()) {
                if(file_exists($offre->getProduct()->getHtmlcontent()->getphpPathblob())){
                    $content = file_get_contents($offre->getProduct()->getHtmlcontent()->getphpPathblob());
                    //$contentjson = $serializer->serialize($content, 'json');
                    $responseCode = 200;
                    http_response_code($responseCode);
                    header('Content-Type: Text/Html');
                    return new JsonResponse(['success' => true, "notice" => $content]);
                }
                return new JsonResponse(['error' => "no file exits"]);
            }
            return new JsonResponse(['error' => "no get fileblog"]);
        }
        return new JsonResponse(['error' => "no find notice"]);
    }
}