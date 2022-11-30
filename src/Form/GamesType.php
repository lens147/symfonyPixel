<?php

    namespace App\Form;

use App\Entity\Games;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;

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
                        ->orderBy('e.name', 'ASC');
                    }
                ])

                ->add('releaseDate', DateType::class, [
                    'label' => 'tableAdmin.releaseDate',
                    'years' => range(1972,date("Y")),
                ])

                ->add('bool', ChoiceType::class, [
                    'label' => 'tableAdmin.enabled',
                    'choices' => [
                        'no' => false,
                        'yes' => true,
                    ],
                    'expanded' => true,
                ])
                ->add('description', null, [
                    'label' => 'tableAdmin.description',
                    'attr' => [
                        'row' => 10,
                    ]
                ])

                ->add('mainImage', ImageType::class, [
                    'label' => 'tableAdmin.Image'
                ])

                ->add('deleteMainImage', CheckboxType::class, [
                    'label' => 'tableAdmin.delete_main_image',
                    'required' => false
                ])

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