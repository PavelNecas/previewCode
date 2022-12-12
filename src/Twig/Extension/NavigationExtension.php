<?php

namespace App\Twig\Extension;

use Pimcore\Model\Document;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NavigationExtension extends AbstractExtension
{
	/**
	 * @return array|TwigFunction[]
	 */
	public function getFunctions()
	{
		return [
			new TwigFunction('build_navigation', [$this, 'buildNavigation'], [
				'needs_context' => true,
			]),
		];
	}

	/**
	 * @param array $context
	 * @param Document|null $rootDoc
	 * @return array
	 */
	public function buildNavigation(array $context, Document $rootDoc = null): array
	{
		$navigation = [
			'pages' => []
		];

		if (!$rootDoc) {
			Document::getById(1);
		}

		if ($rootDoc->getProperty('visibleInMenu') && !$rootDoc->getProperty('navigation_exclude')) {
			$navigation['root'] = $rootDoc;
		}

		foreach ($rootDoc->getChildren() as $doc) {
			if ($doc->getProperty('visibleInMenu') && !$doc->getProperty('navigation_exclude')) {
				$navigation['pages'][$doc->getId()] = $doc;
			}
		}

		return $navigation;
	}
}
