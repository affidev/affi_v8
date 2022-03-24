<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MontantTvaExtension extends AbstractExtension
{
    public function getFilters()
    {
        return array(new TwigFilter('montantTva', array($this,'montantTvaFilter')));
    }

    function montantTvaFilter($prixHT,$tva)
    {
        return round((($prixHT / $tva) - $prixHT),2);
    }

    public function getName()
    {
        return 'montant_tva_extension';
    }
}