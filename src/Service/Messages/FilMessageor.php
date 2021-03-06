<?php


namespace App\Service\Messages;


use App\AffiEvents;
use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\DispatchSpace\Spwsite;
use App\Entity\LogMessages\Loginner;
use App\Entity\LogMessages\Msgs;
use App\Entity\LogMessages\MsgW;
use App\Entity\LogMessages\MsgWebsite;
use App\Entity\LogMessages\Tbmsgs;
use App\Entity\Users\Contacts;
use App\Entity\Users\ProfilUser;
use App\Entity\Websites\Website;
use App\Event\MessageEvent;
use App\Lib\Tools;
use App\Repository\Entity\ContactationRepository;
use App\Repository\Entity\DispatchSpaceWebRepository;
use App\Repository\Entity\MsgsRepository;
use App\Repository\Entity\MsgWebisteRepository;
use App\Repository\Entity\TbmsgsRepository;
use App\Service\Modules\Mailator;
use App\Util\Canonicalizer;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FilMessageor
{

    private MsgWebisteRepository $msgWebisteRepository;
    private EntityManagerInterface $entityManager;
    private TbmsgsRepository $tabreadrepo;
    private Canonicalizer $emailCanonicalizer;
    private Mailator $mailator;
    private MsgsRepository $mesrepo;
    private DispatchSpaceWebRepository $repodispatch;
    private ContactationRepository $repoContact;
    private bool $member=false;
    private bool $contact=false;
    private object $expe;
    private $session;
    private EventDispatcherInterface $eventdispatcher;


    public function __construct(EventDispatcherInterface $eventDispatcher,SessionInterface $session, DispatchSpaceWebRepository $dispatchSpaceWebRepository, ContactationRepository $contactationRepository,MsgWebisteRepository $msgWebisteRepository, EntityManagerInterface $entityManager, TbmsgsRepository $tbmsgsRepository, Canonicalizer $emailCanonicalizer, Mailator $mailator)
    {
        $this->msgWebisteRepository=$msgWebisteRepository;
        $this->entityManager = $entityManager;
        $this->tabreadrepo=$tbmsgsRepository;
        $this->emailCanonicalizer = $emailCanonicalizer;
        $this->mailator=$mailator;
        $this->repodispatch=$dispatchSpaceWebRepository;
        $this->repoContact=$contactationRepository;
        $this->session=$session;
        $this->eventdispatcher=$eventDispatcher;
    }

    /**??
     * @param $website Website
     * @param $pw Spwsite
     * @return mixed
     */
    public function messnoreadToList(Website $website, Spwsite $pw): mixed
    {
        $reads= $this->tabreadrepo->findMessnoreadToList($website->getId());
        /** @var Tbmsgs $read */
        foreach ($reads as $key => $read){
            if ($read->getDispatch()->getId() !== $pw->getDisptachwebsite()->getId() || $read->getIdmessage()->getCreateAt() < $pw->getCreateAt()) {
                unset($reads[$key]);
            }
        }
        return $reads;
    }

    /**
     * @param $website Website
     * @return array
     */
    public function tabSpw(Website $website): array
    {  //tab de tous les dispatch d'un website
        $tabreaders=[];
        $i=0;
        /** @var Spwsite $reader */
        foreach ($website->getSpwsites() as $reader) {
            $tabreaders[$i]['reader'] = $reader->getDisptachwebsite();
            $tabreaders[$i]['admin'] = $reader->getIsadmin();
            $tabreaders[$i]['role'] = $reader->getRole();
            ++$i;
        }
        return $tabreaders;
    }

    /**
     * @param $tab
     * @param $dispatch
     * @return array
     */
    public function tabSpwWhithOutDispatch($tab,$dispatch): array
    {  // tab de tous  les admin d'un website sauf l'utilisateur en cours
        $tabdest=[];
        /** @var Spwsite $reader */
        foreach ($tab as $reader) {
            if($reader['reader']!==$dispatch){
                if($reader['role']!=='client') $tabdest[] = $reader['reader']->getCustomer()->getEmailcontact();
            }
        }
        return $tabdest;
    }

    /**
     * tab de tous les admin et d'un client d'un website sauf l'utilisateur en cours - pour reponse conversation exp :client Ne sert ?? rien ???
     *
     * @param $website Website
     * @param $dispatch DispatchSpaceWeb
     * @return bool
     */
    public function isTheClient(Website $website, DispatchSpaceWeb $dispatch): bool
    {
        /** @var Spwsite $reader */
        foreach ($website->getSpwsites() as $reader) {
                if($reader->getRole()=='client'){
                    if($reader->getDisptachwebsite() === $dispatch) return true;
            }
        }
        return false;
    }

    /**
     * @param $website
     * @param $dispatch
     * @return array
     * @throws Exception
     */
    public function addTabAll($website, $dispatch, $msg): array
    {  // ajout uniquement les membres et admin  retroun le array des dsinatire des notifs hors auteur
        $tabdest=[];
        foreach ($this->tabSpw($website) as $dpt) {
            if($dpt['admin'] || $dpt['role']=="member") {
                $tab = new Tbmsgs();
                $tab->setDispatch($dpt['reader']);
                if ($dpt['reader'] === $dispatch) {
                    $tab->setIsRead(true);
                    $tab->setReadAt(new dateTime());
                }else{
                    $tabdest[]=$dpt['reader']->getCustomer()->getEmailcontact();
                }
                $msg->addTabreader($tab);
            }
        }
        return  $tabdest;
    }

    /**
     * @param $website
     * @param $msg
     */
    public function addTabadmin($website,  $msg){
        /** @var DispatchSpaceWeb $dpt */
        foreach ($this->tabSpw($website) as $dpt) {
            if($dpt['admin']){
                $tab = new Tbmsgs();
                $tab->setDispatch($dpt['reader']);
                $msg->addTabreader($tab);
            }
        }
    }

    /**
     * @param $website
     * @param $msg
     * @param $dispatch
     * @return array
     * @throws Exception
     */
    public function addTabadminAndClient($website, $msg, $dispatch): array
    {
        $tabdest=[];
        foreach ($this->tabSpw($website) as $dpt) {
            if($dpt['admin']) {
                $tab = new Tbmsgs();
                $tab->setDispatch($dpt['reader']);
                if ($dpt['reader'] === $dispatch) {
                    $tab->setIsRead(true);
                    $tab->setReadAt(new dateTime());
                }else{
                    $tabdest[]=$dpt['reader']->getCustomer()->getEmailcontact();
                }
                $msg->addTabreader($tab);
            }else{
                if ($dpt['role']==='client' && $dpt['reader'] === $dispatch) {
                    $tab = new Tbmsgs();
                    $tab->setDispatch($dpt['reader']);
                    $tab->setIsRead(true);
                    $tab->setReadAt(new dateTime());
                    $msg->addTabreader($tab);
                }
            }
        }
        return  $tabdest;
    }

    /**
     * @param $website Website
     * @param $msg
     * @param $client
     * @param $dispatch
     * @throws Exception
     */
    public function addTabaReponseAdminAtClient($website, $msg, $client, $dispatch){   //reponse  d'un admin vers un client
        /** @var DispatchSpaceWeb $dpt */
        foreach ($this->tabSpw($website) as $dpt) {
            if($dpt['admin']){
                $tab = new Tbmsgs();
                $tab->setDispatch($dpt['reader']);
                if ($dpt['reader'] == $dispatch) {
                    $tab->setDispatch($dpt['reader']);
                    $tab->setIsRead(true);
                    $tab->setReadAt(new dateTime());
                }
                $msg->addTabreader($tab);
            }else{
                if($dpt['reader']==$client){
                    $tab = new Tbmsgs();
                    $tab->setDispatch($dpt['reader']);
                    $msg->addTabreader($tab);
                }
            }
        }
    }

    /**
     * @param $website Website
     * @param $msg
     * @param $client DispatchSpaceWeb
     * @throws Exception
     */
    public function addTabaReponseClientAtAdmin($website, $msg, $client){  //reponse d'un cleint vers website
        /** @var DispatchSpaceWeb $dpt */
        foreach ($this->tabSpw($website) as $dpt) {

            if($dpt['admin']){
                $tab = new Tbmsgs();
                $tab->setDispatch($dpt['reader']);
                $msg->addTabreader($tab);
            }else{
                if($dpt['reader']==$client){
                    $tab = new Tbmsgs();
                    $tab->setDispatch($dpt['reader']);
                    $tab->setIsRead(true);
                    $tab->setReadAt(new dateTime());
                    $msg->addTabreader($tab);
                }
            }
        }
    }


    /**
     * @param $data
     * @return MsgWebsite
     * @throws Exception
     */
    public function newMessageFil($data): MsgWebsite
    {
        $request=$data['request'];
        $website = $data['website'];
      //  dump($request); // comment, slug, email, status, id, module (user, contact, nomail)
        $content = Tools::cleanspecialcaractere($request['comment']);
        $message = new  MsgWebsite();

        // 1- identification de l'expediteur
        if($data['dispatch']){
            dump($data['dispatch']);
            $this->member=true;
            $this->contact=false;
            $this->expe=$data['dispatch'];
            $message->setSpacewebexpe($this->expe);
            $message->setIsspaceweb(true);
            $message->setIsclient($this->isTheClient($website, $this->expe));
            $expediteur = $this->expe;
        }else{
            $email = $request['email'];
            $this->member=false;
            $this->contact=true;
            if($request['status']==="contact"){
                $contact=$this->repoContact->findBymail($email);  //contact
                $identity = $contact->getUseridentity();
                $this->expe=$contact;
                $this->expe->setDatemajAt(new \DateTime());
            } else {
                $this->expe = new Contacts();
                $identity = new profilUser();
                // todo recuperation des info source du contact et validation des data formulaire
                $this->expe->setValidatetop(true);
              //  if (!$this->expe->getValidatetop()) return false; // todo  validation anti robot
                $identity->setFirstname("");
                $identity->setLastname("");
                $identity->setEmailfirst($email);
                $this->expe->setEmailCanonical($this->emailCanonicalizer->canonicalize($email)); //todo voir si necessaire de garder
                $this->expe->setUseridentity($identity);
                $this->entityManager->persist($this->expe);
                $this->entityManager->flush();
            }
            $expediteur = $identity;
            $message->setContactexp($this->expe);
            $message->setIsspaceweb(false);
        }

        // 2 - hydratation de l'objet messagesubject

        $message->setSubject((substr($content, 0, 25)) . '...');
        $message->setWebsitedest($website);
        $website->addMsg($message);
        $this->entityManager->persist($website);
        $this->entityManager->flush();

        $msg= new Msgs();
        if(!$this->member && $this->session->has('tabinfoip')){
            $tabip=$this->session->get('tabinfoip');
            $log=new Loginner();
            $log->setAgent($this->session->get('agent'));
            $log->setIp($tabip['ip']??null);
            $log->setReferer($tabip['ref']??null);
            $log->setUri($tabip['uri']??null);
            $log->setMsg($msg);
            $msg->setMsglog($log);
        }
        $msg->setBodyTxt($content);
        $msg->setContentHtml(""); // todo voir html et txt
        $tab=[];
        if($this->member){
            $msg->setAuthor($this->expe); // todo verfi que ce test dans le readconvers ne gene pas ???
            if(!$message->getIsclient()){
                $tab=$this->addTabAll($website, $this->expe, $msg);
            }else {
                $tab=$this->addTabadminAndClient($website, $msg, $this->expe); //ajout des tbsmg (admin et le client) - retroun le tab des destinatires (notif)
            }
        }else{
            $this->addTabadmin($website, $msg);
        }
        $message->addMsg($msg);
        $this->entityManager->persist($message);
        $this->entityManager->flush();

        if($this->member && !$message->getIsclient()) {  //auteur membre pas client
            $this->mailator->notifMessageMemberwebsite($website, $tab, $message); //true =>resa, false =>contact
        }else{
            $this->mailator->newMail($website, $expediteur, $this->member, $message);   //envoie message uniquement au email du website (template)
        }

        if($this->contact){
            $event = new MessageEvent($website, null, $this->expe, $message);
            $this->eventdispatcher->dispatch($event, AffiEvents::NOTIFICATION_CONTACT_NEW_MESSAGE);
        }else{
            $event = new MessageEvent($website, $this->expe, null, $message);
            $this->eventdispatcher->dispatch($event, AffiEvents::NOTIFICATION_NEW_MESSAGE);
        }
        return $message;
    }


    /**
     * @param $id
     * @param $pw Spwsite
     * @return array
     */
    public function messwebsite($id, Spwsite $pw): array
    {
        // recuperation des message et tri chrono (requete sur tous les messages du website et unset de ceux qui ne concernent par le dispatch)
       $sort= $this->msgWebisteRepository->findMsgsWebsite($id);
        $msgs=array_reverse((array)$sort);

        $stop=false;
        foreach ($msgs as $key => $msg) { // on marque les message d??j?? lu ou dont est l'auteur
            $isread=true;
            foreach ($msg->getMsgs() as $m) {  //pour chaque messagges
                if ($m->getAuthor() !== $pw->getDisptachwebsite()) {  // si pas authir du m
                    foreach ($m->getTabreaders() as $read) {
                        if ($read->getDispatch() === $pw->getDisptachwebsite() && !$read->getIsRead()) {
                            $isread = false;
                        }
                        if(!$isread) break;
                    }
                }
                if(!$isread) break;
            }
            $msg->setSender($isread);

            if ($pw->getRole()==='client') {  // dans le cas o?? le pw est un cleint ou un membre //todo pourquoi aussi les membres ?
                if (!$msg->getSpacewebexpe() || $msg->getSpacewebexpe()->getId() !== $pw->getDisptachwebsite()->getId()) { // si message d'un contact ou autre que l'utilisateur
                    unset($msgs[$key]);
                }
            }

            if ($pw->getRole()==='member') {  // dans le cas o?? le pw est un cleint ou un membre //todo pourquoi aussi les membres ?
                if (!$msg->getSpacewebexpe() || $msg->getCreateAt()<$pw->getCreateAt()) { // si message d'un contact ou pw creer apr??s le message
                    unset($msgs[$key]);
                }
            }

            if ($pw->getRole()==='admin') {  // dans le cas o?? le pw est un cleint ou un membre //todo pourquoi aussi les membres ?
                if ($msg->getCreateAt() < $pw->getCreateAt()) { // si message d'un contact ou pw creer apr??s le message
                    unset($msgs[$key]);
                }
            }
        }
        return $msgs;
    }


    /**
     * @param $pagination PaginatorInterface
     * @param $pw Spwsite
     * @return array
     */
    public function sortmesswebsite($pagination, Spwsite $pw): array
    {
        //$sort=get_object_vars($paginator);
       // $msgs=array_reverse((array)$pagination->getItems()); // ???

        $msgs=$pagination->getItems();
        $stop=false;
        foreach ($msgs as $key => $msg) { // on marque les message d??j?? lu ou dont est l'auteur
            $isread=true;
            foreach ($msg->getMsgs() as $m) {  //pour chaque messagges
                if ($m->getAuthor() !== $pw->getDisptachwebsite()) {  // si pas authir du m
                    foreach ($m->getTabreaders() as $read) {
                        if ($read->getDispatch() === $pw->getDisptachwebsite() && !$read->getIsRead()) {
                            $isread = false;
                        }
                        if(!$isread) break;
                    }
                }
                if(!$isread) break;
            }
            $msg->setSender($isread);

            if ($pw->getRole()==='client') {  // dans le cas o?? le pw est un cleint ou un membre //todo pourquoi aussi les membres ?
                if (!$msg->getSpacewebexpe() || $msg->getSpacewebexpe()->getId() !== $pw->getDisptachwebsite()->getId()) { // si message d'un contact ou autre que l'utilisateur
                    unset($msgs[$key]);
                }
            }

            if ($pw->getRole()==='member') {  // dans le cas o?? le pw est un cleint ou un membre //todo pourquoi aussi les membres ?
                if (!$msg->getSpacewebexpe() || $msg->getCreateAt()<$pw->getCreateAt()) { // si message d'un contact ou pw creer apr??s le message
                    unset($msgs[$key]);
                }
            }

            if ($pw->getRole()==='admin') {  // dans le cas o?? le pw est un cleint ou un membre //todo pourquoi aussi les membres ?
                if ($msg->getCreateAt() < $pw->getCreateAt()) { // si message d'un contact ou pw creer apr??s le message
                    unset($msgs[$key]);
                }
            }
        }
        return $msgs;
    }


    /**
     * @param $tab
     * @param $pw Spwsite*
     * @throws Exception
     */
    public function readersconvers($tab, $pw)
    {
        /** @var Msgs $m */
        foreach ($tab as $m) {
            $diptch = false;
            foreach ($m->getTabreaders() as $read) {
                /** @var Tbmsgs $read */
                if ($read->getDispatch() === $pw->getDisptachwebsite()) {
                    $diptch = true;
                    if (!$read->getIsRead()) { //si read du dipsatch n'est pas lu
                        $read->setIsRead(true);
                        $read->setReadAt(new DateTime());
                        $this->entityManager->persist($read);
                        $this->entityManager->flush();
                    }
                }
                if($diptch) break;
            }
            if(!$diptch){    // rajout fonction si un nouveau membre prend connaissance d'ancien message
                $ntb=new Tbmsgs();
                $ntb->setDispatch($pw->getDisptachwebsite());
                $ntb->setIsRead(true);
                $ntb->setReadAt(new DateTime());
                $m->addTabreader($ntb);
                $this->entityManager->persist($m);
                $this->entityManager->flush();
            }
        }
        return ;
    }

    /**
     * @param $pw Spwsite
     * @return bool
     */
    public function sortMessgsPw($msg, $pw){
        if (!$pw->isAdmin() && $pw->getRole()==='contact') {  // dans le cas o?? le pw est un contact il faut qu'il soit le createur du message (spacewebexpe)
            if (!$msg->getSpacewebexpe() || $msg->getSpacewebexpe()->getId() !== $pw->getDisptachwebsite()->getId()) {
                return false;
            }else{
                return true;
            }
        }
        return true;
    }


    /**
     * @param $webitemsg
     * @param $dispatch DispatchSpaceWeb
     * @param $content
     * @param $website Website
     * @return Msgs
     * @throws Exception
     */
    public function addConvers($webitemsg, $dispatch, $content, $website){
        $msg = new Msgs();
        $msg->setBodyTxt($content);
        $msg->setMsgwebsite($webitemsg);
        $msg->setAuthor($dispatch);
        if(!$webitemsg->getIsspaceweb()){  //reponse ?? un contact
            $this->addTabadmin($website, $msg);
            $this->mailator->reponseMailContact($webitemsg, $msg);
        }else{
            if($webitemsg->getIsclient()){
                $client=$webitemsg->getSpacewebexpe();
                if($dispatch===$client){
                    $this->addTabaReponseClientAtAdmin($website, $msg, $dispatch);
                }else{
                    $this->addTabaReponseAdminAtClient($website, $msg,  $client, $dispatch);
                    $this->mailator->reponseMailToClient($website, $webitemsg, $client);
                }
            }else{
                $this->addTabAll($website, $dispatch, $msg);
            }
        }
        $webitemsg->addMsg($msg);
        $this->entityManager->persist($webitemsg);
        $this->entityManager->flush();
        return $msg;
    }

    /**
     * @param $website Website
     * @param $pw Spwsite
     * @return mixed
     */
    public function messnoread($website, $pw){
        $reads= $this->tabreadrepo->findnoread($website->getId());
        /** @var Tbmsgs $read */
        foreach ($reads as $key => $read){
            if ($read->getDispatch()->getId() !== $pw->getDisptachwebsite()->getId()) {
                unset($reads[$key]);
            }
        }
        return $reads;
    }

    /**
     * @param $d
     */
    protected function initExpediteur($d)
    {

        if(!$d['expe']){
            $type=$d['form']['type']->getData();
            switch ($type) {
                case 'member':
                    $this->expe=$this->repodispatch->find($d['form']['id']->getData()); //dispatch
                    $this->member=true;
                    $this->contact=false;
                    break;
                case 'contact':
                    $this->expe=$this->repoContact->find($d['form']['id']->getData());  //contact
                    $this->member=false;
                    $this->contact=true;
                    break;
                default:
                    $this->contact=false;
                    $this->member=false;
                    break;
            }
        }else{
            $this->expe=$d['expe'];
            $this->member=true;
            $this->contact=false;
        }
    }

}