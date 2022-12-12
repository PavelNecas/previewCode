<?php

namespace App\Service;

use Pimcore\Model\DataObject\Concrete;

class ArticleLinkGenerator implements \Pimcore\Model\DataObject\ClassDefinition\LinkGeneratorInterface
{

	/**
	 * @inheritDoc
	 */
	public function generate(Concrete $object, array $params = []): string
	{
		// TODO: Implement generate() method.
	}
}
