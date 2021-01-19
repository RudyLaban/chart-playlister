<?php


namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;

/**
 * Formulaire d'ajout d'image à une Chart
 *
 * Class ChartAddImageType
 * @package App\Form
 */
class ChartAddImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '4M',
                        'allowLandscape' => false,
                        'allowLandscapeMessage' => 'L\'image doit avoir une forme carrée. C\'est plus sympa pour une pochette de playlist.',
                        'allowPortrait' => false,
                        'allowPortraitMessage' => 'L\'image doit avoir une forme carrée. C\'est plus sympa pour une pochette de playlist.',
                    ])
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
            ]);
    }


}