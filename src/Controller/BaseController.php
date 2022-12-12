<?php

namespace App\Controller;

use App\Form\NewsletterFormType;
use App\Service\NewsletterManager;
use Exception;
use Nette\Utils\Strings;
use Pimcore\Controller\FrontendController;
use Pimcore\Controller\KernelControllerEventInterface;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Translation\Translator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

class BaseController extends FrontendController implements KernelControllerEventInterface
{
	use Helper\ControllerTrait;

	protected Environment $twig;

	protected ApplicationLogger $applicationLogger;

	protected Security $security;

	protected ?UserInterface $user;

	protected NewsletterManager $newsletterManager;

	protected ?string $defaultLanguage;

	protected string $currentUrl;

	protected string $cacheKey;

	public function __construct(
		Environment $twig,
		Translator $translator,
		ApplicationLogger $applicationLogger,
		Security $security,
		NewsletterManager $newsletterManager
	) {
		$this->twig = $twig;
		$this->translator = $translator;
		$this->applicationLogger = $applicationLogger;
		$this->security = $security;
		$this->newsletterManager = $newsletterManager;
	}

	/**
	 * @param ControllerEvent $event
	 * @return RedirectResponse|void|null
	 * @throws Exception
	 */
	public function onKernelControllerEvent(ControllerEvent $event)
	{
		// set language
		$this->defaultLanguage = \Pimcore\Tool::getDefaultLanguage();
		$this->language = substr($event->getRequest()->getLocale(), 0, 2);
		if (!$this->language) {
			$this->language = $this->defaultLanguage;
		}
		$this->twig->addGlobal('language', $this->language);

		// set request
		$this->request = $event->getRequest();

		// check if user is logged
		$this->user = $this->security->getUser();
		$this->twig->addGlobal('logged', (bool) $this->user);
		$this->twig->addGlobal('userName', $this->user?->getUsername());

		// set current url (no parameters)
		$this->currentUrl = strtok($this->request->getUri(), '?');
		$this->twig->addGlobal('currentUrl', $this->currentUrl);

		// set document
		$this->twig->addGlobal('document', $this->document);

		// cache keys
		$cacheKey = $this->language . '_' . $this->currentUrl;
		$this->cacheKey = Strings::webalize($cacheKey);
		$this->twig->addGlobal('cacheKey', $this->cacheKey);
		$this->twig->addGlobal('breadcrumbsCacheKey', '_breadcrumbs_' . $this->cacheKey);
		$this->twig->addGlobal('metaCacheKey', '_meta_' . $this->cacheKey);
		$this->twig->addGlobal('mainNavCacheKey', '_main_nav_' . $this->cacheKey);

		// FE by admin
		$this->twig->addGlobal('isFrontendRequestByAdmin', \Pimcore\Tool::isFrontendRequestByAdmin());

		// Newsletter form
		$newsletterForm = $this->createForm(NewsletterFormType::class);
		$newsletterForm->handleRequest($this->request);
		if ($newsletterForm->isSubmitted()) {
			if ($this->handleNewsletterForm($newsletterForm, $this->newsletterManager)) {
				return $this->gotoUrl($this->docUrl(2));
			}
		}
		$this->twig->addGlobal('newsletterForm', $newsletterForm->createView());
	}

	// FORM HANDLERS

	/**
	 * @param FormInterface $newsletterForm
	 * @param NewsletterManager $newsletterManager
	 * @return bool
	 * @throws Exception
	 */
	private function handleNewsletterForm(FormInterface $newsletterForm, NewsletterManager $newsletterManager): bool
	{
		if ($newsletterForm->isValid()) {
			$data = $newsletterForm->getData();
			$data['lang'] = $this->language;

			$subscribe = $newsletterManager->subscribe($data);

			if ($subscribe) {
				$this->addFlash('success', $this->translate('msg_newsletter_successful'));

				return true;
			}
		} else {
			$this->addFlash('danger', $this->translate('msg_newsletter_unsuccessful'));
		}

		return false;
	}
}
