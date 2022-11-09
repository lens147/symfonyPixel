<?php

    namespace App\Form;

use App\Entity\Games;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

    class GamesType extends AbstractType{

        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('title', null, [
                    'label' => 'tableAdmin.title',
                    'help' => 'tableAdmin.title_help',
                ])
                ->add('editor', null, [
                    'label' => 'tableAdmin.editor',
                    'query_builder' => function (EntityRepository $er){
                        return $er->createQueryBuilder('e')
                        ->orderBy('e.name', 'ASC')
                        ;
                    }
                ])
                ->add('bool')
                ->add('description')
            ;
        }


        public function configureOptions(OptionsResolver $resolver): void
        {
            // indique que ce formulaire va traiter les données d'objet de type Games
            $resolver->setDefaults([
                'data_class' => Games::class,
            ]);
        }
    }

?>