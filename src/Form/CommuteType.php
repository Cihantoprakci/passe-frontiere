<?php

namespace App\Form;

use App\Entity\Border;
use App\Entity\Commute;
use App\Repository\BorderRepository;
use App\Service\TrainClient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommuteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('stationFrom', ChoiceType::class, [
                'label' => 'Gare de départ',
                'choices' => array_combine(TrainClient::STATIONS, TrainClient::STATIONS),
            ])
            ->add('border', EntityType::class, [
                'label' => 'Douane habituelle en voiture',
                'class' => Border::class,
                'choice_label' => 'name',
                'query_builder' => fn (BorderRepository $repository) => $repository->createQueryBuilder('b')
                    ->where('b.active = true')
                    ->orderBy('b.name', 'ASC'),
                'placeholder' => 'Je ne passe pas en voiture',
                'required' => false,
            ])
            ->add('leaveAt', TimeType::class, [
                'label' => 'Heure de départ',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commute::class,
        ]);
    }
}
