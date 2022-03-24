<?php


namespace App\EventSubscriber;


use App\AffiEvents;
use App\Event\ResaEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfirmResaSubscriber implements EventSubscriberInterface
{



    public function flashConfirm(ResaEvent $event)
    {
        $resa=$event->getResa();

    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            AffiEvents::CONFIRM_RESA =>[
                ['flashConfirm', 10],
            ],
        ];
    }
}