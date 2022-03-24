<?php


namespace App\Event;

use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\LogMessages\MsgsP;
use App\Entity\Marketplace\Offres;
use App\Entity\Posts\Post;
use App\Entity\Users\Contacts;
use App\Entity\Websites\Website;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class MessagePublicationEvent extends Event
{
    protected MsgsP $message;
    private Response $response;
    private Contacts|null $contact;
    private DispatchSpaceWeb|null $dispatch;
    private Post|Offres $publication;
    private Website $board;
    private DispatchSpaceWeb|Contacts|null $author;

    public function __construct($publication, DispatchSpaceWeb|Contacts $sender, $message, $board, $author=null)
    {
        if ($sender instanceof DispatchSpaceWeb){
            $this->dispatch=$sender;
        }else{
            $this->contact=$sender;
        }
        $this->message=$message;
        $this->publication=$publication;
        $this->board=$board;
        $this->author=$author;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response|null
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * @return DispatchSpaceWeb|null
     */
    public function getDispatch(): ?DispatchSpaceWeb
    {
        return $this->dispatch;
    }

    /**
     * @param $dispatch
     */
    public function setDispatch($dispatch): void
    {
        $this->dispatch = $dispatch;
    }

    /**
     * @return Contacts|null
     */
    public function getContact(): ?Contacts
    {
        return $this->contact;
    }

    /**
     * @param $contact
     */
    public function setContact($contact): void
    {
        $this->contact = $contact;
    }

    /**
     * @return DispatchSpaceWeb|Contacts|null
     */
    public function getAuthor(): DispatchSpaceWeb|Contacts|null
    {
        return $this->author;
    }

    /**
     * @param $author
     */
    public function setAuthor($author): void
    {
        $this->author = $author;
    }

    /**
     * @return MsgsP
     */
    public function getMessage(): MsgsP
    {
        return $this->message;
    }

    /**
     * @param $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }

    /**
     * @return Offres|Post
     */
    public function getPublication(): Offres|Post
    {
        return $this->publication;
    }

    /**
     * @param $publication
     */
    public function setPublication($publication): void
    {
        $this->publication = $publication;
    }

    /**
     * @return Website
     */
    public function getBoard(): Website
    {
        return $this->board;
    }

    /**
     * @param $board
     */
    public function setBoard($board): void
    {
        $this->board = $board;
    }

}
