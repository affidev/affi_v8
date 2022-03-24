<?php


namespace App\Infrastructure\Search\Normalizer;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use App\Entity\Websites\Website;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;


class WebsiteNormalizer implements ContextAwareNormalizerInterface
{
    private WebsitePathNormalizer $pathNormalizer;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(WebsitePathNormalizer $pathNormalizer, UrlGeneratorInterface $urlGenerator)
    {
        $this->pathNormalizer = $pathNormalizer;
        $this->urlGenerator = $urlGenerator;
    }


    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Website && 'search' === $format;
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        if (!$object instanceof Website) {
            throw new \InvalidArgumentException('Unexpected type for normalization, expected Course, got '.get_class($object));
        }

        $url = $this->pathNormalizer->normalize($object);

        return [
            'id' => (string) $object->getId(),
            'content' => MarkdownTransformer::toText((string) $object->getTemplate()->getDescription()),
            'url' => $this->urlGenerator->generate($url['path'], $url['params']),
            'title' => $object->getNamewebsite(),
            'category' => array_map(fn ($t) => $t->getName(), CollectionTransformer::toarray($object->getTemplate()->getTagueries())),
            'type' => 'website',
            'city'=> $object->getLocality()->getCity(),
            'adresses'=>array_map(fn ($t) => $t->getNumero().', '.$t->getNomVoie().' '.$t->getCodePostal().' '.$t->getNomCommune(), CollectionTransformer::toarray($object->getTemplate()->getSector()->getAdresse())),
            'created_at' => $object->getCreateAt()->getTimestamp(),
            'gps'=> (string) $object->getLocality()->getId()??"",
            'pict'=>$this->pathNormalizer->normalizePict($object)
        ];
    }
}