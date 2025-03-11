<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Medicament;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Optional;

class MedicamentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class)
            ->add('price',NumberType::class)
            ->add('quantity')
            ->add('image',FileType::class,[
                'mapped'=>false,//specifier que c'est un champ n'existe pas dans entity et a chaque propre vie
                'constraints'=>[
                    new Image(),
                ],
                'required'=>false
            ])
            ->add('category',EntityType::class,[
                'class'=>Category::class,
                'choice_label'=>'name', //choice_label qui donne le select menu pour choisir en se basant sur 'name' ou d'autre champ de l'entity Catgeory si on sohaite 
                // 'expanded'=>true //il met de radioButton car un medicament peut avoir une seule category
            ])
            ->add('save', SubmitType::class, [
                'attr' => [
                    'style' => 'background-color: #c3d5b9; color: #000; border: 1px solid #ccc; padding: 10px 20px;',
                ],
                'label' => 'Envoyer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Medicament::class,
        ]);
    }
}
