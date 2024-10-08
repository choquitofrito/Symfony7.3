<?php
namespace App\Form; 

// src/Form/AnimalType.php
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

class AnimalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // champs base de l'entité
            ->add('nom')
            // champs pour l'upload
            ->add('files', FileType::class, [
                'label' => 'Upload Files',
                'multiple' => true, // Allow multiple files, user adds one at a time
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => 'image/*', // Adjust for other file types if needed
                    'class' => 'file-upload-input',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
