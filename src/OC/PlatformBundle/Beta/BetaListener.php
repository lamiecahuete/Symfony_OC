<?php

namespace OC\PlatformBundle\Beta;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class BetaListener
{
	// Notre processeur
	protected $betaHTML;

	// La date de fin de la version bêta :
	// - Avant cette date, on affichera un compte à rebours (J-3 par exemple)
	// - Après cette date, on n'affichera plus le « bêta »
	protected $endDate;

	public function __construct(BetaHTMLAdder $betaHTML, $endDate)
	{
		$this->betaHTML	= $betaHTML;
		$this->endDate	= new \DateTime($endDate);
	}

	// L'arguemtn de la méthode est un FilterResponseEvent
	public function processBeta(FilterResponseEvent $event)
	{
		if (!$event->isMasterRequest()) {
			return;
		}

		$remainingDays = $this->endDate->diff(new \DateTime())->days;

		// Si la date est dépassé, on en fait rien
		if ($remainingDays <= 0) {
			return;
		}

		// On utilise notre BetaHTML
		$response = $this->betaHTML->addBeta($event->getResponse(), $remainingDays);

		// On met à jour la réponse avec la nouvelle valeur
		$event->setResponse($response);

		// On stoppe la propagation de l'event en cours (ici, kernel.response)
		// $event->stopPropagation();
	}
}