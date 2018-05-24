<?php

namespace App\Form;

use App\Entity\Item;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Regex;

class ItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
            ->add(
                'name', null, array(
                'required' => true, 
                'constraints' => array(
                    new NotBlank()
                )
            ))
            ->add('amount', NumberType::class, array(
                'required' => true,
                'constraints' => array(
                    new NotBlank(), 
                    new GreaterThanOrEqual(0), 
                    new Regex(
                        array(
                            'pattern' => '/^(([0-9]{1})|([1-9]{1}[0-9]+))$/'
                        )
                    )
            )))
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'         => Item::class,
                'csrf_protection'    => false,
                'allow_extra_fields' => true,
            ]
        );
    }
}