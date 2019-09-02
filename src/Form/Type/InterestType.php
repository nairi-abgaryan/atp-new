<?php

namespace App\Form\Type;

use App\Entity\Interest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterestType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('check1', CheckboxType::class, [
                'label'    => 'Teacher Kit',
                'required' => false])
            ->add('check2', CheckboxType::class, [
                'label'    => 'ATP Visit to My School',
                'required' => false])
            ->add('check3', CheckboxType::class, [
                'label'    => 'Bringing My School to Armenia',
                'required' => false])
            ->add('check4', CheckboxType::class, [
                'label'    => 'Volunteering with Building Bridges',
                'required' => false])
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('school', TextType::class)
            ->add('title', TextType::class)
            ->add('grade', TextType::class)
            ->add('email', EmailType::class)
            ->add('address', TextType::class, ['required' => false])
            ->add('phone', TextType::class, ['required' => false])
            ->add('comments', TextareaType::class, ['required' => false])
            ->add('send', SubmitType::class, ['label'=>'Submit'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Interest::class,
            "allow_extra_fields" => false,
            "csrf_protection" => false,
        ]);
    }
}
