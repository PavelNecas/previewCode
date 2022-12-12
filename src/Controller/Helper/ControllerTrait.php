<?php

namespace App\Controller\Helper;

use App\Tool\Utils;
use Pimcore\Model\DataObject;
use Pimcore\Translation\Translator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

trait ControllerTrait
{
	/** @var Request */
	private Request $request;

	/** @var Translator */
	protected Translator $translator;

	/** @var string */
	protected string $language;

	/**
	 * @return Request
	 */
	protected function getRequest(): Request
	{
		return $this->request;
	}

	/**
	 * @return string
	 */
	protected function getLanguage(): string
	{
		return $this->language;
	}

	/**
	 * @param string $keyword the translation key
	 *
	 * @return string translated message
	 */
	public function translate(string $keyword): string
	{
		return $this->translator->trans($keyword);
	}

	/**
	 * redirects to given URL.
	 *
	 * @param string $url the URL
	 * @param int $code
	 * @return RedirectResponse|null
	 */
	public function gotoUrl(string $url, int $code = 302): ?RedirectResponse
	{
		return $this->redirect($url, $code);
	}

	/**
	 * @param int $id
	 * @param null $language
	 *
	 * @return string
	 */
	protected function docUrl(int $id, $language = null): string
	{
		if (null === $language) {
			$language = $this->getLanguage();
		}

		return Utils::getDocUrl($id, $language);
	}

	/**
	 * @param DataObject\Concrete $object
	 * @return array
	 */
	public function getObjectTranslations(DataObject\Concrete $object)
	{
		$languages = \Pimcore\Tool::getValidLanguages();
		if (!method_exists($object, 'getUrl')) {
			return [];
		}

		$translations = [];
		foreach ($languages as $language) {
			if (!$url = $object->getUrl($language)) {
				continue;
			}
			$translations[$language] = $url;
		}

		return $translations;
	}
}
