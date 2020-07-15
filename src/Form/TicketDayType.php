<?php

namespace App\Form;

use App\Entity\TicketDay;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketDayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('max', NumberType::class, [
                'label' => 'Tickets max'
            ])
            ->add('remaining', NumberType::class, [
                'label' => 'Tickets restants'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TicketDay::class,
        ]);
    }
}
