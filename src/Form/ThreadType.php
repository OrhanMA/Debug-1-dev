<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Thread;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ThreadType extends AbstractType
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $categories = $this->categoryRepository->findAll();


        if (!empty($categories)) {
            $builder->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
            ]);
        }
        $builder
            ->add('title', TextType::class, ['constraints' => [
                new Length(min: 10, max: 100)
            ]])
            ->add('description', TextareaType::class, ['constraints' => [
                new Length(min: 50, max: 255)
            ]])
            ->add('content', TextareaType::class, [
                'constraints' => [
                    new Length(min: 50, max: 1000)
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Thread::class,
        ]);
    }
}
