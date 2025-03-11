<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Medicament;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Webmozart\Assert\Assert;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('telephone', TextType::class)
            //->add('montant_total')
            // ->add('medicaments', EntityType::class, [ // Utilisez "medicaments" (pluriel, minuscule)
            //     'class' => Medicament::class,
            //     'choice_label' => 'name', // Affiche le champ "name" des entités Medicament
            //     'expanded'=>true,   // Cases à cocher
            //     'multiple' => true,      // Permet la sélection multiple
            // ])
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
            'data_class' => Client::class,
        ]);
    }
}
