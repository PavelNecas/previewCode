<?php

namespace App\Service;

use App\Model\User;
use App\Tool\Utils;
use Pimcore\File;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Throwable;

class UserManager
{
	const ROOT_FOLDER_ID = 2;
	const ROOT_DOCUMENT_ID = 1;
	const COMPONENT = 'UserManager';

	protected Translator $translator;

	protected MailManager $mailManager;

	protected NewsletterManager $newsletterManager;

	protected ?Request $request;

	protected ApplicationLogger $aplicationLogger;

	/**
	 * @param Translator $translator
	 * @param MailManager $mailManager
	 * @param ApplicationLogger $logger
	 * @param NewsletterManager $newsletterManager
	 * @param RequestStack $requestStack
	 */
	public function __construct(
		Translator $translator,
		MailManager $mailManager,
		ApplicationLogger $logger,
		NewsletterManager $newsletterManager,
		RequestStack $requestStack
	) {
		$this->translator = $translator;
		$this->mailManager = $mailManager;
		$this->aplicationLogger = $logger;
		$this->newsletterManager = $newsletterManager;
		$this->request = $requestStack->getCurrentRequest();
	}

	/**
	 * @param array $data
	 *
	 * @return bool
	 */
	public function createUser($data)
	{
		$data['activationCode'] = uniqid();

		try {
			$user = User::create($data);
			$user->setKey(File::getValidFilename($data['email']));
			$user->setParentId(self::ROOT_FOLDER_ID);
			$user->setPublished(false);
			$user->save();

			// TODO add newsletter
//			if (!empty($data['newsletter'])) {
//				$this->newsletterManager->subscribe($data);
//			}

			$this->mailManager->sendMail(10, $data['lang'], $data['email'], null, [
				'siteUrl' => $this->request->getSchemeAndHttpHost() . Utils::getDocUrl(self::ROOT_DOCUMENT_ID, $data['lang']),
				'activationUrl' => $this->request->getSchemeAndHttpHost() . Utils::getStaticRouteUrl('account-activation', [
					'language' => $data['lang'],
					'code' => $data['activationCode']
				])
			]);

			return true;
		} catch (Throwable $t) {
			$error = $this->translator->trans('msg_user_creation_unsuccessful');
			$this->aplicationLogger->logException($error, $t, 'error', null, self::COMPONENT);

			return false;
		}
	}

	/**
	 * @param string $code
	 * @return string|bool
	 */
	public function activateUser(string $code): string|bool
	{
		// @var $user User
		$user = User::getByActivationCode($code, ['limit' => 1, 'unpublished' => true]);
		if (null !== $user) {
			try {
				$user->setPublished(true);
				$user->save();

				return true;
			} catch (Throwable $t) {
				$error = $this->translator->trans('msg_user_activation_error');
				$this->aplicationLogger->logException($error, $t, 'error', null, self::COMPONENT);

				return $error;
			}
		} else {
			return $this->translator->trans('msg_activation_code_invalid');
		}
	}

	/**
	 * @param array $data
	 * @return bool
	 */
	public function isUserEmailUsed(array $data): bool
	{
		if (User::getByEmail($data['email'], ['limit' => 1, 'unpublished' => true])) {
			return true;
		}

		return false;
	}

	/**
	 * @param \Pimcore\Model\DataObject\User $user
	 * @param string $language
	 * @return bool
	 */
	public function passwordReset(\Pimcore\Model\DataObject\User $user, string $language): bool
	{
		$resetToken = strtolower(md5(uniqid()));

		try {
			$user->setPasswordResetToken($resetToken);
			$user->save();

			if ($this->mailManager->sendMail(11, $language, $user->getEmail(), null, [
				'siteUrl' => $this->request->getSchemeAndHttpHost() . Utils::getDocUrl(self::ROOT_DOCUMENT_ID, $language),
				'resetUrl' => $this->request->getSchemeAndHttpHost() . Utils::getStaticRouteUrl('password-reset', [
					'language' => $language,
					'code' => $resetToken
				])
			])) {
				return true;
			}

			return false;
		} catch (Throwable $t) {
			$error = $this->translator->trans('password-reset-failed');
			$this->aplicationLogger->logException($error, $t, 'error', null, self::COMPONENT);

			return false;
		}
	}

	/**
	 * @param \Pimcore\Model\DataObject\User $user
	 * @param array $data
	 * @return bool
	 */
	public function passwordUpdate(\Pimcore\Model\DataObject\User $user, array $data): bool
	{
		try {
			$user->setPassword($data['password']);
			$user->setPasswordResetToken('');
			$user->save();

			return true;
		} catch (Throwable $t) {
			$error = $this->translator->trans('password-update-failed');
			$this->aplicationLogger->logException($error, $t, 'error', null, self::COMPONENT);

			return false;
		}
	}
}
