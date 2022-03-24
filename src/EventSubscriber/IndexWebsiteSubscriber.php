<?php


namespace App\EventSubscriber;

use App\Event\WebsiteCreatedEvent;
use App\Infrastructure\Search\Typesense\TypesenseIndexer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class IndexWebsiteSubscriber implements EventSubscriberInterface
{

    private TypesenseIndexer $indexer;
    private NormalizerInterface $normalizer;

    public function __construct(
        TypesenseIndexer $indexer,
        NormalizerInterface $normalizer,
    )
    {
        $this->indexer = $indexer;
        $this->normalizer = $normalizer;
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
                WebsiteCreatedEvent::CREATE => 'indexNewWebsite',
                WebsiteCreatedEvent::MAJ => 'majNewWebsite',
            ];
    }

    /**
     * @throws ExceptionInterface
     */
    public function indexNewWebsite(WebsiteCreatedEvent $event){
        $data=$this->normalizer->normalize($event->getWebsite(), 'search');
        $this->indexer->indexOne((array) $data);
    }

    /**
     * @throws ExceptionInterface
     */
    public function majNewWebsite(WebsiteCreatedEvent $event){
        $data=$this->normalizer->normalize($event->getWebsite(), 'search');
        $this->indexer->indexOne((array) $data);
    }

}