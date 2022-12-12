<?php

namespace App\Controller;

use App\Form\LoginFormType;
use App\Form\PasswordChangeFormType;
use App\Form\PasswordResetFormType;
use App\Form\RegistrationFormType;
use App\Model\User;
use App\Service\UserManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends BaseController
{
	/**
	 * @param AuthenticationUtils $authenticationUtils
	 * @return Response
	 */
	public function loginAction(
		AuthenticationUtils $authenticationUtils,
	): Response {

		// set login error msg
		$lastAuthError = $authenticationUtils->getLastAuthenticationError();
		if (null !== $lastAuthError) {
			$this->addFlash('error', $this->translate('msg_authentication_error'));
		}

		// pre-set login form data
		$defaultLoginFormData = [
			'email' => $authenticationUtils->getLastUsername(),
			'target' => $this->docUrl(7),
		];

		// login form submission is handled by symfony's security system
		$loginForm = $this->createForm(LoginFormType::class, $defaultLoginFormData, ['action' => '/login-check']);

		return $this->render('user/login.html.twig', [
			'loginForm' => $loginForm->createView()
		]);
	}

	/**
	 * @param Request $request
	 * @param UserManager $userManager
	 * @return Response
	 */
	public function passwordResetAction(Request $request, UserManager $userManager): Response
	{
		$passwordResetForm = $this->createForm(PasswordResetFormType::class);

		$passwordResetForm->handleRequest($request);
		if ($passwordResetForm->isSubmitted()) {
			if ($this->handlePasswordResetForm($passwordResetForm, $userManager)) {
				$this->addFlash('success', $this->translate('msg_reset_password_link_send_successful'));

				return $this->gotoUrl($this->docUrl(2));
			}

			$this->addFlash('error', $this->translate('msg_reset_password_link_send_unsuccessful'));
			$passwordResetForm['email']->addError(new FormError('msg_reset_password_link_send_unsuccessful'));
		}

		return $this->render('user/passwordReset.html.twig', [
			'passwordResetForm' => $passwordResetForm->createView()
		]);
	}

	/**
	 * @param string $resetToken
	 * @param Request $request
	 * @param UserManager $userManager
	 * @return Response
	 */
	public function passwordChangeAction(string $resetToken, Request $request, UserManager $userManager): Response
	{
		$passwordChangeForm = $this->createForm(PasswordChangeFormType::class);

		$passwordChangeForm->handleRequest($request);
		if ($passwordChangeForm->isSubmitted()) {
			if ($this->handlePasswordChangeForm($resetToken, $passwordChangeForm, $userManager)) {
				$this->addFlash('success', $this->translate('msg_password_change_successful'));

				return $this->gotoUrl($this->docUrl(4));
			}

			$this->addFlash('error', $this->translate('msg_password_change_unsuccessful'));
			$passwordChangeForm['password']->addError(new FormError('msg_password_change_unsuccessful'));
		}

		return $this->render('user/passwordChange.html.twig', [
			'passwordChangeForm' => $passwordChangeForm->createView()
		]);
	}

	/**
	 * @param Request $request
	 * @param UserManager $userManager
	 * @return Response
	 */
	public function registrationAction(Request $request, UserManager $userManager): Response
	{
		$registrationForm = $this->createForm(RegistrationFormType::class);
		$registrationForm->handleRequest($request);
		if ($registrationForm->isSubmitted()) {
			if ($this->handleRegistrationForm($registrationForm, $userManager)) {
				return $this->gotoUrl($this->docUrl(4));
			}
		}

		return $this->render('user/registration.html.twig', [
			'registrationForm' => $registrationForm->createView()
		]);
	}

	// FORM HANDLERS

	/**
	 * @param FormInterface $form
	 * @param UserManager   $userManager
	 *
	 * @return bool
	 */
	private function handleRegistrationForm(FormInterface $form, UserManager $userManager): bool
	{
		if ($form->isValid()) {
			$data = $form->getData();
			$data['lang'] = $this->language;

			if ($userManager->isUserEmailUsed($data)) {
				$form['email']->addError(new FormError('msg_registration_email_used'));
				$this->addFlash('danger', $this->translate('msg_registration_email_used'));

				return false;
			}

			$createUser = $userManager->createUser($data);

			if ($createUser) {
				$this->addFlash('success', $this->translate('msg_registration_successful'));

				return true;
			}
		} else {
			$this->addFlash('danger', $this->translate('msg_registration_unsuccessful'));
		}

		return false;
	}

	/**
	 * @param FormInterface $form
	 * @param UserManager   $userManager
	 *
	 * @return bool
	 */
	private function handlePasswordResetForm(FormInterface $form, UserManager $userManager): bool
	{
		if ($form->isValid()) {
			$data = $form->getData();

			$user = User::getByEmail($data['email'], ['limit' => 1, 'unpublished' => true]);
			if (!$user) {
				$form['email']->addError(new FormError('msg_registration_email_not_found'));
				$this->addFlash('danger', $this->translate('msg_registration_email_not_found'));

				return false;
			}

			if ($userManager->passwordReset($user, $this->language)) {
				return true;
			}

			return false;
		}

		return false;
	}

	/**
	 * @param string $resetToken
	 * @param FormInterface $form
	 * @param UserManager $userManager
	 *
	 * @return bool
	 */
	private function handlePasswordChangeForm(string $resetToken, FormInterface $form, UserManager $userManager): bool
	{
		if ($form->isValid()) {
			$data = $form->getData();

			$user = User::getByPasswordResetToken($resetToken, ['limit' => 1, 'unpublished' => true]);
			if (!$user) {
				$form['email']->addError(new FormError('msg_registration_email_not_found'));
				$this->addFlash('danger', $this->translate('msg_registration_email_not_found'));

				return false;
			}

			if ($userManager->passwordUpdate($user, $data)) {
				return true;
			}

			return false;
		}

		return false;
	}

	// STATIC-ROUTE ROUTED ACTIONS

	// VIEW-LESS ACTIONS (ALWAYS REDIRECT)

	/**
	 * @Route("/login-check")
	 *
	 * @return Response
	 */
	public function loginCheckAction(): Response
	{
		// used for symfony's security system login check
	}

	/**
	 * @Route("/logout")
	 *
	 * @return Response
	 */
	public function logoutAction(): Response
	{
		// used for symfony's security system logout
	}

	/**
	 * @param string $code
	 * @param UserManager $userManager
	 * @return response
	 */
	public function accountActivationAction(string $code, UserManager $userManager): Response
	{
		$result = $userManager->activateUser($code);

		$result === true
			? $this->addFlash('success', $this->translate('msg_account_activated'))
			: $this->addFlash('error', $result);

		return $this->gotoUrl($this->docUrl(4));
	}
}
