<?php

namespace App\Controller;

use Pimcore\Log\ApplicationLogger;
use Pimcore\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

class UserAdminController extends BaseController
{
	/**
	 * @param Request $request
	 * @return Response
	 */
	public function indexAction(Request $request): Response
	{
		if (!$this->user) {
			return $this->gotoUrl($this->docUrl(4));
		}

		return $this->render('userAdmin/index.html.twig');
	}
}
