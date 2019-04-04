<?php

namespace App\Form;

use App\Entity\EnsayosRealizados;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Metodo;
use App\Entity\Analisis;

class EnsayosRealizadosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre_analisis', TextType::class, array(
                'label'=> 'Nombre del Ensayo',
//                'expanded'  => true,
//                'multiple'  => true,
                'attr'   =>  array(
                    'class'   => 'form-control')))
            ->add('metodo', EntityType::class, array(
                'class' => Metodo::class,
                'label'=> 'Método',
                'expanded'  => true,
//                'multiple'  => true,
                'attr'   =>  array(
                    'class'   => 'form-control')))
            ->add('comentario',TextareaType::class, array(
                'label'  => 'Descripción',
                'attr'   =>  array(
                    'class'   => 'form-control')))
            ->add('ensayo_en_unidad', ChoiceType::class, array(
                'label'  => 'Realizado en la Unidad',
                'choices'=>[
                    1=>1,
                    2=>2,
                    3=>3,
                ],
                'attr'   =>  array(
                    'class'   => 'form-control')))
            ->add('punto', ChoiceType::class, array(
                'label'  => 'Punto de Ensayo',
                'choices'=>[
                    1=>1,
                    2=>2,
                    3=>3,
                    4=>4,
                ],
                'attr'   =>  array(
                    'class'   => 'form-control')))
//            ->add('hecho_por',TextType::class, array(
//                'label'  => 'Realizado por',
//                'attr'   =>  array(
//                    'class'   => 'form-control')))
//            ->add('fecha',DateTimeType::class, array(
//                'label'  => 'Fecha',
//                'attr'   =>  array(
//                    'id'=> 'newEnsayoRealizadosDateTimeInput',
//                    'class'   => '')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EnsayosRealizados::class,
        ]);
    }
}
