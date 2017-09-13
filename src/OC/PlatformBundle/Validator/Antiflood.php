<?php

namespace OC\PlatformBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Antiflood extends Constraint
{
	public $message = "Vous avez déjà posté un message il y a moins de 15 secondes, merci d'attendre un peu.";

	public function validatedBy()
	{
		// Ici on fait appel à l'alias du service
		return 'oc_platform_antiflood';
	}
}