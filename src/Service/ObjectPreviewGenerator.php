<?php

namespace App\Service;

use Pimcore\Model\DataObject\Concrete;

class ObjectPreviewGenerator implements \Pimcore\Model\DataObject\ClassDefinition\PreviewGeneratorInterface
{
	/**
	 * @inheritDoc
	 */
	public function generatePreviewUrl(Concrete $object, array $params): string
	{
		$additionalParams = [];
		foreach ($this->getPreviewConfig($object) as $paramStore) {
			$paramName = $paramStore['name'];
			if ($paramValue = $params[$paramName]) {
				$additionalParams[$paramName] = $paramValue;
			}
		}

		return $object->getUrl($additionalParams['_locale']);
	}

	/**
	 * @inheritDoc
	 */
	public function getPreviewConfig(Concrete $object): array
	{
		return [
			[
				'name' => '_locale',
				'label' => 'Locale',
				'values' => [
					'Czech' => 'cs',
					'English' => 'en'
				],
				'defaultValue' => 'cs'
			]
		];
	}
}
