<?php

namespace OC\PlatformBundle\Twig;

use OC\PlatformBundle\Antispam\OCAntispam;

class AntispamExtension extends \Twig_Extension
{
	/**
	 * @var OCAntispam
	 */
	private $ocAntispam;

	public function __construct(OCAntispam $ocAntispam)
	{
		$this->ocAntispam = $ocAntispam;
	}

	public function checkIfArgumentIsSpam($text)
	{
		return $this->ocAntispam->isSpam($text);
	}

	// Twig va exécuter cette méthode pour savoir quelle fonction ajoute notre service
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('checkIfSpam', array($this, 'checkIfArgumentIsSpam')),
		);
	}

	// La méthode getName() identifie votre extension Twig, elle est obligatoire
	public function getName()
	{
		return 'OCAntispam';
	}
}