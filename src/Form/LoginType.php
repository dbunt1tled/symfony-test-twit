<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 09.10.18
 * Time: 12:21
 */

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, ['attr' => ['placeholder' => 'Your Email'], 'label' => 'Email'])
            ->add('password', PasswordType::class, ['attr' => ['placeholder' => 'Your Password']])
            ->add('remember_me', CheckboxType::class, ['label' => 'Remember Me','required' => false])
            ->add('Login', SubmitType::class);
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // a unique key to help generate the secret token
            'csrf_token_id'   => 'authenticate',
        ]);
    }
}