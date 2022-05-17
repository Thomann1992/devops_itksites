<?php

namespace App\Form\Type\Admin;

use App\Types\DatabaseVersionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MariaDbVersionFilterType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => DatabaseVersionType::CHOICES,
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}