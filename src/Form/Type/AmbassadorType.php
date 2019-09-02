<?php

namespace App\Form\Type;

use App\Entity\Volunteer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AmbassadorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('city', TextType::class, ['required' => false])
            ->add('email', EmailType::class)
            ->add('address', TextType::class, ['required' => false])
            ->add('phone', NumberType::class, ['required' => false])
            ->add('message', TextareaType::class, ['required' => false])
            ->add('comments', TextareaType::class, ['required' => false])
            ->add('send', SubmitType::class, ['label'=>'Become an Ambassador Today!'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Volunteer::class,
            "allow_extra_fields" => false,
            "csrf_protection" => false,
        ]);
    }
}
