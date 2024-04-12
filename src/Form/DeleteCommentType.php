<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Thread;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class DeleteCommentType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('confirmation', TextType::class, [
        'label' => "Enter 'Yes, delete my comment' to delete process further",
        'constraints' => [
          new NotBlank([
            'message' => 'Please enter the sentence to delete the comment'
          ])
        ]
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([]);
  }
}
