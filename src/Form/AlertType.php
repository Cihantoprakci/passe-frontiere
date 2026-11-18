<?php

namespace App\Form;

use App\Entity\Alert;
use App\Entity\Border;
use App\Repository\BorderRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('border', EntityType::class, [
                'label' => 'Douane',
                'class' => Border::class,
                'choice_label' => 'name',
                'query_builder' => fn (BorderRepository $repository) => $repository->createQueryBuilder('b')
                    ->where('b.active = true')
                    ->orderBy('b.name', 'ASC'),
                'placeholder' => 'Choisir une douane',
            ])
            ->add('thresholdMinutes', IntegerType::class, [
                'label' => 'Prévenir si le retard dépasse (minutes)',
                'attr' => ['min' => 1, 'max' => 120],
            ])
            ->add('windowStart', TimeType::class, [
                'label' => 'Entre',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('windowEnd', TimeType::class, [
                'label' => 'Et',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Alert::class,
        ]);
    }
}
