<?php

namespace OC\PlatformBundle\Beta;

use Symfony\Component\HttpFoundation\Response;

class BetaHTMLAdder
{
	// Méthode pour ajouter le "bêta" à une réponse
	public function addBeta(Response $response, $remainingDays)
	{
		$content = $response->getContent();

		// Code à rajouter
		// (Je met ici du CSS en ligne, ms il faudrait utiliser un fichier CSS!)
		$html = '<div style="position:absolute; bottom:35px; background:orange; width:100%; text-align:center; padding:0.5em;">Beta 
J-'.(int) $remainingDays.' !</div>';

		// Insertion du code ds la page, au début du <body>
		$content = str_replace(
			'<body>',
			'<body>'.$html,
			$content
		);

		// Modification du contenu ds la réponse
		$response->setContent($content);

		return $response;
	}
}