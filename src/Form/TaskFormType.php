<?php

namespace App\Form;

use App\Entity\German;
use App\Entity\Task;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class TaskFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
			$builder
					->add('title')
					->add('description')
					->add('status', ChoiceType::class, [
							'choices' => [
									'À faire' => 'todo',
									'En cours' => 'in_progress',
									'Terminé' => 'done',
							],
							'expanded' => false,
							'multiple' => false,
					])
			;
	}
	

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
