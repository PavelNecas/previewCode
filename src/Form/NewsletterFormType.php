<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class NewsletterFormType extends \Symfony\Component\Form\AbstractType
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
			->add('submit', SubmitType::class, [
				'label' => 'label_newsletter_submit',
			]);
	}
}
