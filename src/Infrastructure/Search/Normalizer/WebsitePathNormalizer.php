<?php


namespace App\Infrastructure\Search\Normalizer;

use App\Encoder\PathEncoder;
use App\Entity\Websites\Website;
use App\Normalizer\Normalizer;

class WebsitePathNormalizer extends Normalizer
{
    public function normalize($object, string $format = null, array $context = []): array
    {
        if ($object instanceof Website) {
            return [
                'path' => 'show_website',
                'params' => ['city'=>$object->getLocality()->getSlugcity(), 'nameboard' => $object->getSlug()],
            ];
        }
        throw new \RuntimeException("Can't normalize path");
    }

    public function normalizePict($object, string $format = null, array $context = []): string
    {
        if ($object instanceof Website) {
            if($object->getTemplate()->getLogo()!= null){
                return  '/spaceweb/template/'.$object->getTemplate()->getLogo()->getNamefile();
            }else{
                return  '/img/pinsaffi.png';
            }
        }
        throw new \RuntimeException("Can't normalize path");
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return ($data instanceof Website)
            && PathEncoder::FORMAT === $format;
    }
}