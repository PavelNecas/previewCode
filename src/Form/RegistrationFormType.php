<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class RegistrationFormType extends BaseFormType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('email', EmailType::class, [
				'label' => 'label_email',
				'required' => true,
				'constraints' => [
					new NotBlank(['message' => 'msg_field_can_t_be_empty']),
					new Email(['message' => 'msg_email_not_valid']),
				],
			])
			->add('password', RepeatedType::class, [
				'type' => PasswordType::class,
				'invalid_message' => 'msg_passwords_do_not_match',
				'required' => true,
				'first_options' => ['label' => 'label_password'],
				'second_options' => ['label' => 'label_password_again'],
				'constraints' => [
					new NotBlank(['message' => 'msg_field_can_t_be_empty']),
				],
			])
			->add('submit', SubmitType::class, [
				'label' => 'label_registration',
			]);
	}
}
