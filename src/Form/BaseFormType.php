<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

class BaseFormType extends AbstractType
{
	/**
	 * To simplify input names (form['field] becomes 'field')
	 * @return string|null
	 */
	public function getBlockPrefix()
	{
		return '';
	}
}
