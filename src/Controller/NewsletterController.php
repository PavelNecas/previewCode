<?php

namespace App\Controller;

use Pimcore\Tool\Newsletter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NewsletterController extends BaseController
{
	/**
	 * @param Request $request
	 * @return Response
	 */
	public function newsletterAction(Request $request): Response
	{
		return $this->render('newsletter/newsletterMail.html.twig');
	}

	// VIEW-LESS ACTIONS (ALWAYS REDIRECT)

	/**
	 * @param Request $request
	 * @return Response
	 * @throws \Exception
	 */
	public function confirmAction(Request $request): Response
	{
		$newsletter = new Newsletter('newsletter');

		if ($newsletter->confirm($request->get('token'))) {
			$this->addFlash('success', $this->translate('msg_newsletter_activation_success'));
		} else {
			$this->addFlash('error', $this->translate('msg_newsletter_activation_error'));
		}

		return $this->gotoUrl($this->docUrl(2));
	}

	/**
	 * @param Request $request
	 * @return Response
	 * @throws \Exception
	 */
	public function unsubscribeAction(Request $request)
	{
		$newsletter = new Newsletter('newsletter');
		$success = false;

		if ($request->get('email')) {
			if ($newsletter->unsubscribeByEmail($request->get('email'))) {
				$success = true;
			}
		}

		if ($request->get('token')) {
			if ($newsletter->unsubscribeByEmail($request->get('token'))) {
				$success = true;
			}
		}

		if ($success) {
			$this->addFlash('success', $this->translate('msg_newsletter_unsubscribe_success'));
		} else {
			$this->addFlash('error', $this->translate('msg_newsletter_unsubscribe_error'));
		}

		return $this->gotoUrl($this->docUrl(2));
	}
}
