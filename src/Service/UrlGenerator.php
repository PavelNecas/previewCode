<?php

namespace App\Service;

use App\Tool\Utils;
use Exception;
use Nette\Utils\Strings;
use Pimcore\Logger;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Data\CalculatedValue;

class UrlGenerator implements CalculatorClassInterface
{
	/**
	 * @param DataObject\Concrete $object
	 * @param CalculatedValue $context
	 *
	 * @return string
	 * @throws Exception
	 */
	public function compute(DataObject\Concrete $object, CalculatedValue $context): string
	{
		switch ($context->getFieldname()) {
			case 'url':
				return self::getUrl($object, $context->getPosition());
			default:
				Logger::error('CALCULATING VALUE - unsupported field name: ' . $context->getFieldname());

				break;
		}

		return '';
	}

	/**
	 * @param DataObject\Concrete $object
	 * @param CalculatedValue     $context
	 *
	 * @return string
	 */
	public function getCalculatedValueForEditMode(DataObject\Concrete $object, CalculatedValue $context): string
	{
		return self::compute($object, $context);
	}

	/**
	 * @param DataObject\Concrete $object
	 * @param string|null $language
	 * @param array $params
	 * @return string
	 * @throws Exception
	 */
	public static function getUrl(DataObject\Concrete $object, string $language = null, array $params = []): string
	{
		$url = '';
		switch ($object->getClassName()) {
			case 'Article': {
				if ($slug = $object->getName($language) ? Strings::webalize($object->getName($language)) : '') {
					$params = [
						'slug' => $slug
					];
					$staticRouteName = 'article-' . $language;
					$url = Utils::getStaticRouteUrl($staticRouteName, $params);
					break;
				}
			}
			case 'News': {
				if ($slug = $object->getName($language) ? Strings::webalize($object->getName($language)) : '') {
					$params = [
						'slug' => $slug
					];
					$staticRouteName = 'news-' . $language;
					$url = Utils::getStaticRouteUrl($staticRouteName, $params);
					break;
				}
			}
		}

		return $url ?: '';
	}
}
