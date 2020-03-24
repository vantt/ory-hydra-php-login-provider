<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class LoginForm extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
          ->add('username', TextType::class, [
                            'help' => 'Enter user name',
                          ]
          )
          ->add('password', PasswordType::class, [
                            'help' => 'Enterpassword',
                          ]
          )
          ->add('challenge', HiddenType::class)
          ->add('login', SubmitType::class, ['label' => 'Login In']);
    }
}