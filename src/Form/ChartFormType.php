<?php


namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ChartFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', UrlType::class, [
                'attr' => [
                    'placeholder'   => 'Ex: https://www.billboard.com/charts/hot-100',
                    'class'         => 'home_input',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Playlister',
                'attr'  => [
                    'class' => 'home_form_button button_fill'
                ],
            ]);
    }

}