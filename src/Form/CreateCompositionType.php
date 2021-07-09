<?php

namespace App\Form;

use App\Entity\Composition;
use App\Repository\FandomRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateCompositionType extends AbstractType
{
    protected FandomRepository $fandomRepository;

    public function __construct(FandomRepository $fandomRepository)
    {
        $this->fandomRepository = $fandomRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fandoms = $this->fandomRepository->findAll();

        $builder
            ->add('title', TextType::class, ['label' => 'Title'])
            ->add('description', TextType::class, ['label' => 'Description'])
            ->add('text', TextareaType::class, ['label' => 'Text'])
            ->add('fandom', ChoiceType::class,[
                'choices' => $fandoms,
                'choice_label' => function ($choice) {
                return $choice->getName();
                },
                'label' => 'Fandom'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Composition::class,
        ]);
    }
}
