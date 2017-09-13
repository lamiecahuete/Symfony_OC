<?php

namespace OC\PlatformBundle\Purger;

use Doctrine\ORM\EntityManagerInterface;

class PurgerAdvert
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        // Entity Manager
        $this->em = $em;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEm(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     * @param EntityManagerInterface $em
     */
    public function setEm(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function purge($days)
    {
        // On récupère les repositories
        $advertRepository = $this->em->getRepository('OCPlatformBundle:Advert');
        $advertSkillRepository = $this->em->getRepository('OCPlatformBundle:AdvertSkill');

        // On récupère la liste des annonces à purger
        $listAdverts = $advertRepository->getAdvertsToPurge(new \DateTime('-' .$days.' day'));

        // On supprime les annonces
        foreach ($listAdverts as $advert) {
            // On oublie pas de supprimer d'abord chaque AdvertSkill lié
            $advertSkills = $advertSkillRepository->findByAdvert($advert);
            foreach ($advertSkills as $advertSkill) {
                $this->em->remove($advertSkill);
            }

            // au final on supprime l'annonce elle-même
            $this->em->remove($advert);
        }

        // et on flush
        $this->em->flush();
    }
}