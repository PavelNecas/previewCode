<?php

namespace App\Model;

use Nette\Utils\Strings;
use Pimcore\Model\DataObject;

class News extends DataObject\News
{
	/**
	 * {@inheritdoc}
	 */
	public function save()
	{
		$languages = \Pimcore\Tool::getValidLanguages();
		foreach ($languages as $language) {
			$name = $this->getName($language);
			$slug = $name ? Strings::webalize($name) : $this->getId();
			$this->setUrlSlug($slug, $language);
		}

		parent::save();
	}
}
