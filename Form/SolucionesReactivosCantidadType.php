<?php

namespace App\Form;

use App\Entity\SolucionesReactivosCantidad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SolucionesReactivosCantidadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cantidad_reactivo')
            ->add('soluciones')
            ->add('reactivos')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SolucionesReactivosCantidad::class,
        ]);
    }
}
