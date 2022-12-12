<?php

namespace App\Twig\Extension;

use App\Tool\Utils;
use Pimcore\Model\Asset;
use Pimcore\Model\Document;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class HelpersExtension extends AbstractExtension
{
	/**
	 * @return array|TwigFunction[]
	 */
	public function getFunctions()
	{
		return [
			new TwigFunction('doc_url', [$this, 'getDocUrl'], [
				'needs_context' => true,
			]),
			new TwigFunction('get_doc_translations', [$this, 'getDocumentTranslations']),
			new TwigFunction('utils_get_asset', [$this, 'getAssetById']),
			new TwigFunction('get_doc_by_path', [$this, 'getDocByPath']),
		];
	}

	public function getFilters()
	{
		return [
			new TwigFilter('typo', [$this, 'typo'], ['is_safe' => ['html']]),
		];
	}

	/**
	 * @param string|null $text
	 * @return string
	 */
	public function typo(?string $text): string
	{
		$filtered = preg_replace(['/(\s+|&nbsp;)(a|o|u|i|k|s|v|z)(\s+)/i'], ['$1$2&nbsp;'], $text);
		return preg_replace('/<p[^>]*>([\s]|&nbsp;)*<\/p>/', '', $filtered);
	}

	/**
	 * @param int $id
	 * @param array $context
	 * @return string
	 */
	public function getDocUrl(array $context, int $id): string
	{
		return Utils::getDocUrl($id, $context['language']);
	}

	/**
	 * @param $document
	 * @return array
	 */
	public function getDocumentTranslations($document): array
	{
		return Utils::getDocumentTranslations($document);
	}

	/**
	 * @param int $id
	 * @return Asset|null
	 */
	public function getAssetById(int $id): ?Asset
	{
		return Asset::getById($id);
	}

	/**
	 * @param string $path
	 * @return Document|null
	 */
	public function getDocByPath(string $path): ?Document
	{
		return Document::getByPath($path);
	}
}
