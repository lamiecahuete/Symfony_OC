<?php

namespace OC\PlatformBundle\Form;

use OC\PlatformBundle\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	// Arbitrairement on récup ttes les catégories qui commencent par "D"
		$pattern = 'D%';

        $builder
            ->add('date',       DateTimeType::class)
            ->add('title',      TextType::class)
            ->add('author',     TextType::class)
            ->add('content',    TextareaType::class)
            ->add('image',      ImageType::class)
			->add('content',    CkeditorType::class)
            /*
             * Rappel :
             * 1er argument : nom du champ, ici "categories" car c'est le nom de l'attribut
             * 2eme argument : type du champ, ici "CollectionType" qui est une liste de qq chose
             * 3eme argument : tableau d'option du champ
             */
            ->add('categories', EntityType::class, array(
                'class'         => 'OCPlatformBundle:Category',
                'choice_label'  => 'name',
                'multiple'      => true,
                'query_builder' => function(CategoryRepository $repository) use($pattern) {
                    return $repository->getLikeQueryBuilder($pattern);
                }
            ))
            ->add('save',       SubmitType::class);

        // On ajoute une fonction qui va écouter un évènement
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA, // 1er argument : L'évènement qui nous intéresse, ici PRE_SET_DATA
            function (FormEvent $event) { // 2e argument : La fonction à exécuter lorsque l'évènement est déclenché
                // On récupère notre objet Advert sous-jacent
                $advert = $event->getData();

                // Cette condition est important
                if (null === $advert) {
                    return; // On sort de la fonction sans rien faire lorsque $advert vaut null
                }

                // Si l'annonce n'est pas publiée, ou si elle n'existe pas encore en BDD (id est null)
                if (!$advert->getPublished() || null === $advert->getId()) {
                    // Alors on ajoute le champ published
                    $event->getForm()->add('published', CheckboxType::class, array('required' => false));
                } else {
                    // sinon on le supprime
                    $event->getForm()->remove('published');
                }
            }
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OC\PlatformBundle\Entity\Advert'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'oc_platformbundle_advert';
    }


}
