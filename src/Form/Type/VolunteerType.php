<?php

namespace App\Form\Type;

use App\Entity\Volunteer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VolunteerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('check1', CheckboxType::class, [
                'label'    => 'Represent ATP in your community through events and festivals',
                'required' => false])
            ->add('check2', CheckboxType::class, [
                'label'    => 'Volunteer in our Woburn office for an hour or an afternoon',
                'required' => false])
            ->add('check3', CheckboxType::class, [
                'label'    => 'Organize an event for ATP at your church or in your community',
                'required' => false])
            ->add('check4', CheckboxType::class, [
                'label'    => 'Lend a hand at one of our many events throughout the US',
                'required' => false])
            ->add('check5', CheckboxType::class, [
                'label'    => 'Bring ATP’s Building Bridges curriculum to your school or youth group',
                'required' => false])
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('city', TextType::class)
            ->add('email', EmailType::class)
            ->add('address', TextType::class)
            ->add('phone', TextType::class)
            ->add('comments', TextareaType::class, ['required' => false])
            ->add('send', SubmitType::class, ['label'=>'Become Volunteer'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Volunteer::class,
            "allow_extra_fields" => true,
            "csrf_protection" => false,
        ]);
    }
}
