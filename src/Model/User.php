<?php

namespace App\Model;

use Pimcore\Model\DataObject\ClassDefinition\Data\Password;
use Pimcore\Model\DataObject\User as BaseUser;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Our custom user class implementing Symfony's UserInterface.
 */
class User extends BaseUser implements UserInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function getSalt(): ?string
	{
		// user has no salt as we use password_hash
		// which handles the salt by itself
		return null;
	}

	/**
	 * Use email as username.
	 *
	 * @return string
	 */
	public function getUsername(): string
	{
		return $this->getEmail();
	}

	/**
	 * @return array
	 */
	public function getRoles(): array
	{
		return ['ROLE_USER'];
	}

	/**
	 * Trigger the hash calculation to remove the plain text password from the instance. This
	 * is necessary to make sure no plain text passwords are serialized.
	 *
	 * {@inheritdoc}
	 */
	public function eraseCredentials()
	{
		/** @var Password $field */
		$field = $this->getClass()->getFieldDefinition('password');
		$field->getDataForResource($this->getPassword(), $this);
	}
}
