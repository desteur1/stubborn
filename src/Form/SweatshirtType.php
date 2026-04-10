<?php

namespace App\Form;

use App\Entity\Sweatshirt;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class SweatshirtType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, ['label' => 'Nom du sweat-shirt'])
            ->add('price', null, ['label' => 'Prix (€)'])
            ->add('featured', null, ['label' => 'Produit en avant'])
            ->add('stockXS', null, ['label' => 'Stock XS'])
            ->add('stockS', null, ['label' => 'Stock S'])
            ->add('stockM', null, ['label' => 'Stock M'])
            ->add('stockL', null, ['label' => 'Stock L'])
            ->add('stockXL', null, ['label' => 'Stock XL'])
            // Champ pour l'upload de l'image, non mappé à l'entité
            ->add('imageFile', FileType::class, [
                    'mapped' => false,
                    'required' => false,
                    'label' => 'Image du produit'
                ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sweatshirt::class,
            
        ]);
    }
}
