<?php

namespace App\Form;

use App\Entity\Reactivos;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\DecimalType;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\NumberFormatter\NumberFormatter;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;//new
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;//new
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Categoria;

class ReactivosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, array(
                'label'  => 'Nombre',
                'attr'   =>  array(
                'class'   => 'form-control field-type')))
            ->add('nombre_quimico', TextType::class, array(
                'label'  => 'Nombre Químico',
                'attr'   =>  array(
                'class'   => 'form-control field-type')))
            ->add('formula', TextType::class, array(
                'label'  => 'Fórmula',
                'attr'   =>  array(
                'class'   => 'form-control field-type')))
            ->add('codigo_comercial', TextType::class, array(
                'label'  => 'Código Comercial',
                'attr'   =>  array(
                'class'   => 'form-control field-type')))
            ->add('envase_comercial', TextType::class, array(
                'label'  => 'Envase Comercial',
                'attr'   =>  array(
                'class'   => 'form-control field-type')))
            ->add('unidad', ChoiceType::class, array(
                'label'  => 'Unidad',
                'choices'=>[
                    'Kilogramos'=>'Kilogramos',
                    'Litros'=>'Litros'
                ],
                'attr'   =>  array(
                    'class'   => 'form-control field-type')))
            ->add('cantidad', IntegerType::class, array(
                'label'  => 'Cantidad (Kg/L)',
                'attr'   =>  array(
                    'class'   => 'form-control field-type')))
            ->add('cantidad_minima', IntegerType::class, array(
                'label'  => 'Cantidad Mínima (Kg/L)',
                'attr'   =>  array(
                    'class'   => 'form-control field-type')))
            ->add('categoria', EntityType::class, array(
                'class' => Categoria::class,
                'expanded'  => true,
                'label' => 'Categoría',
                'multiple'  => true,
                'attr'   =>  array(
                    'class'   => 'form-control field-type')))
            ->add('precio', MoneyType::class, array(
                'label' => 'Precio (CUC)',
                'attr'   =>  array(
                    'type' => 'number',
                    'class'   => 'form-control field-type')))
            ->add('proveedor', TextType::class, array(
                'label'  => 'Proveedor',
                'attr'   =>  array(
                    'class'   => 'form-control field-type')))
            ->add('sinonimo', TextType::class, array(
                'label'  => 'Sinónimo',
                'required' => false,
                'attr'   =>  array(
                    'class'   => 'form-control field-type')))
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reactivos::class,
        ]);
    }
}
