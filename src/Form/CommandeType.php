<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\Medicament;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('montantTotal')
            ->add('date', null, [
                'widget' => 'single_text',
            ])
            ->add('medicaments', EntityType::class, [
                'class' => Medicament::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
            ->add('clients', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'name',
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
            'data_class' => Commande::class,
        ]);
    }
}
