<?php

namespace App\Tool;

use Exception;
use Pimcore\Model\Document;
use Pimcore\Model\Site;
use Pimcore\Model\Staticroute;

class Utils
{
	/**
	 * @param int $id
	 * @param string $language
	 * @return string relative or absolute (if from other Site) URL
	 */
	public static function getDocUrl(int $id, string $language): string
	{
		$document = Document::getById($id);

		if (!$document) {
			return '/';
		}

		// Check translations
		$document = Utils::getDocumentTranslation($document, $language);

		return $document->getFullPath();
	}

	/**
	 * @param Document $document
	 * @param string $language
	 * @return Document $document
	 */
	public static function getDocumentTranslation(Document $document, string $language): Document
	{
		if ($language == $document->getProperty('language')) {
			return $document;
		}
		$service = new \Pimcore\Model\Document\Service();
		$translations = $service->getTranslations($document);
		if ($translations && $translations[$language]) {
			return Document::getById($translations[$language]);
		}

		return $document;
	}

	/**
	 * @param Document $document
	 * @return array $translations
	 */
	public static function getDocumentTranslations(Document $document): array
	{
		$service = new \Pimcore\Model\Document\Service();
		$docTranslationsIds = $service->getTranslations($document);

		$translations = [];
		foreach ($docTranslationsIds as $language => $documentId) {
			$document = Document::getById($documentId);
			if ($document && $document->isPublished()) {
				$url = $document->getFullPath();

				$translations[$language] = $url;
			}
		}

		return $translations;
	}

	/**
	 * @param string|Staticroute $staticRoute
	 * @param array $params
	 * @return string
	 * @throws Exception
	 */
	public static function getStaticRouteUrl(Staticroute|string $staticRoute, array $params = []): string
	{
		if (!$staticRoute instanceof Staticroute) {
			$staticRoute = Staticroute::getByName($staticRoute, (Site::isSiteRequest()) ? Site::getCurrentSite()->getId() : 0);
		}

		if (!$staticRoute) {
			return '/';
		}

		return $staticRoute->assemble($params, true);
	}
}
