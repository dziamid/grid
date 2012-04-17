<?php

namespace Belectrika\GridBundle\Form\Price;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ItemType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('price')
            ->add('amount')
        ;
    }

    public function getName()
    {
        return 'belectrika_gridbundle_price_itemtype';
    }
}
