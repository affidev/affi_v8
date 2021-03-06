<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;


class AgeExtension extends AbstractExtension
{

	public function getFilters(){

		return array(
			new TwigFilter('age', array($this, 'agecalculate')),
		);

	}

	public function agecalculate(\DateTime $birthdate){
		$now=new \DateTime();
		$interval=$now->diff($birthdate);
		return $interval->y;
	}
}