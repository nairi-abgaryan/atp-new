<?php

namespace App\Form\Type;

use App\Entity\Donation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class DonationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', HiddenType::class, ['required' => true])
            ->add('amount', HiddenType::class, [
                "constraints" => [
                    new Assert\NotBlank(),
                ]
            ])
            ->add('firstName', TextType::class, ['required' => true])
            ->add('lastName', TextType::class, ['required' => true])
            ->add('country', HiddenType::class, ['required' => true])
            ->add('city', TextType::class, ['required' => true])
            ->add('state', HiddenType::class, ['required' => true])
            ->add('code', TextType::class, ['required' => true])
            ->add('email', EmailType::class, ['required' => true])
            ->add('address', TextType::class, ['required' => true])
            ->add('phone', TextType::class, ['required' => true])
            ->add('employer', TextType::class, ['required' => false])
            ->add('comments', TextareaType::class, ['required' => false])
            ->add('certificate', ChoiceType::class, array(
                'choices'  => [
                    'No' => 'No',
                    'Yes' => 'Yes',
                ]))
            ->add('send', SubmitType::class, ['label'=>'Next'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Donation::class,
            "allow_extra_fields" => false,
            "csrf_protection" => false,
        ]);
    }
}
