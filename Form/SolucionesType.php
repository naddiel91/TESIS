<?php

namespace App\Form;

use App\Entity\Analisis;
use App\Entity\Soluciones;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class SolucionesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, array(
                'label'  => 'Nombre',
                'attr'   =>  array(
                    'class'   => 'form-control')))
            ->add('descripcion', TextareaType::class, array(
                'label'  => 'Descripción',
                'attr'   =>  array(
                    'class'   => 'form-control',
                    'style' => 'height: 165px;'
                )))
            ->add('analisis', EntityType::class, array(
                'class' => Analisis::class,
                'label' => 'Análisis',
                'expanded'  => true,
                'attr'   =>  array(
                    'class'   => 'form-control sol_analisis')))
//            ->add('fecha_creada', DateType::class, array(
//                'label' => 'Fecha Creada',
//                'attr'   =>  array(
//                    'class'   => 'form-control')))
//            ->add('fecha_vencimiento', DateType::class, array(
//                'label' => 'Fecha Vencimiento',
//                'attr'   =>  array(
//                    'class'   => 'form-control')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Soluciones::class,
        ]);
    }
}
