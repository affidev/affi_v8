<?php


namespace App\Event;

use App\Entity\DispatchSpace\DispatchSpaceWeb;
use App\Entity\LogMessages\MsgWebsite;
use App\Entity\Users\Contacts;
use App\Entity\Websites\Website;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class MessageEvent extends Event
{

    protected Website $website;
    protected MsgWebsite $message;
    private Response $response;
    private Contacts|null $contact;
    private DispatchSpaceWeb|null $dispatch;

    public function __construct($website, $dispatch, $contact, $message)
    {
        $this->website=$website;
        $this->contact=$contact;
        $this->dispatch = $dispatch;
        $this->message = $message;
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
     * @return Website
     */
    public function getWebsite(): Website
    {
        return $this->website;
    }

    /**
     * @param $website
     */
    public function setWebsite($website): void
    {
        $this->website = $website;
    }

    /**
     * @return DispatchSpaceWeb
     */
    public function getDispatch()
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
     * @return Contacts
     */
    public function getContact(): Contacts
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
     * @return MsgW
     */
    public function getMessage(): MsgW
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


}
