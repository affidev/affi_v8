<?php

namespace App\Notification;

use App\Repository\Entity\DispatchSpaceWebRepository;
use App\Repository\Entity\PrivateConversRepository;
use App\Repository\Entity\TbmsgsRepository;
use App\Service\Messages\Messageor;
use App\Service\Modules\Mailator;
use App\Service\SpaceWeb\SpacewebFactor;
use App\Util\Canonicalizer;
use Doctrine\ORM\EntityManagerInterface;

class Messageries
{
    private Messageor $messageor;
    private EntityManagerInterface $entityManager;
    private SpacewebFactor $factor;

    public function __construct(EntityManagerInterface $entityManager, Messageor $messageor, SpacewebFactor $factor)
    {
        $this->messageor=$messageor;
        $this->entityManager = $entityManager;
        $this->factor = $factor;
    }

    public function openMessageWebsite($form, $board){
        if($user and !$this->memberwb){
            if($idcontact=$form['follow']->getData()=="oui"){
                $this->factor->addSpwsiteClient($board, $this->dispatch);
            }
        }
        $this->messageor->newMessage([
            'form' => $form,
            'messagewb' => $messageWb,
            'website' => $website,
            'expe'=>$this->userdispatch??null
        ]);
        if($user){
            return $this->redirectToRoute('messagery_spwb', ['id' => $id]);
        }else{
            $this->addFlash('newmsg', ' votre message a bien été adressé à : '.$website->getNamewebsite().'.');
            return $this->redirectToRoute('contact_keep',['slug'=>$website->getSlug(),'type'=>$form['type']->getData(),'id'=>$form['id']->getData()]);
        }
    }

}