<?php


namespace App\EventSubscriber;


use App\AffiEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\Translation\TranslatorInterface;


class FlashSubscriber implements EventSubscriberInterface
{

    /**
     * @var string[]
     */
    private static array $successMessages = array(
        Affievents::CHANGE_PASSWORD_COMPLETED => 'change_password.flash.success',
        Affievents::PROFILE_EDIT_COMPLETED => 'profile.flash.updated',
        Affievents::REGISTRATION_COMPLETED => 'registration.flash.user_created',
        Affievents::RESETTING_RESET_COMPLETED => 'resetting.flash.success',
    );


    private TranslatorInterface $translator;
    private RequestStack $requestStack;


    public function __construct(TranslatorInterface $translator, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Affievents::CHANGE_PASSWORD_COMPLETED => 'addSuccessFlash',
            Affievents::PROFILE_EDIT_COMPLETED => 'addSuccessFlash',
            Affievents::REGISTRATION_COMPLETED => 'addSuccessFlash',
            Affievents::RESETTING_RESET_COMPLETED => 'addSuccessFlash',
        );
    }


    /**
     * @param Event $event
     * @param $eventName
     */
    public function addSuccessFlash(Event $event, $eventName)
    {
        if (!isset(self::$successMessages[$eventName])) {
            throw new \InvalidArgumentException('This event does not correspond to a known flash message');
        }

        $this->requestStack->getSession()->getFlashBag()->add('success', $this->trans(self::$successMessages[$eventName]));
    }

    /**
     * @param string $message
     *
     * @return string
     */
    private function trans(string $message): string
    {
        $params = [];
        return $this->translator->trans($message, $params, 'AFFIChange');
    }
}