<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TvaExtension extends AbstractExtension
{

    public function getFilters()
    {
        return array(
            new TwigFilter('tva', array($this, 'tvaFilter')),


        );
    }

    /**
     * @param $prixHT
     * @param $tva
     * @return float
     */
    public function tvaFilter($prixHT,$tva)
    {

       return round($prixHT/$tva,2);
    }

    public function getName()
    {
        return 'tva_extension';
    }



}