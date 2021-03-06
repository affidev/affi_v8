<?php


namespace App\Event;

use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\LogMessages\Msgs;
use App\Entity\Users\Contacts;
use App\Entity\Websites\Website;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class MessageWebsiteEvent extends Event
{
    protected Msgs $message;
    private Response $response;
    private Contacts|null $contact;
    private DispatchSpaceWeb|null $dispatch;
    private Website $board;
    private bool $sender;


    public function __construct($board, $sender, $message, $dispatch=null, $contact=null)
    {
        $this->dispatch=$dispatch;
        $this->contact=$contact;
        $this->message=$message;
        $this->board=$board;
        $this->sender=$sender;
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
     * @return DispatchSpaceWeb|Contacts|null
     */
    public function getAuthor(): DispatchSpaceWeb|Contacts|null
    {
        if($this->sender){
            return $this->dispatch;
        }else{
            return $this->contact;
        }
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
     * @return Msgs
     */
    public function getMessage(): Msgs
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
