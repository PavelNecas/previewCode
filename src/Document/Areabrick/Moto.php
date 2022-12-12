<?php

namespace App\Document\Areabrick;

use Pimcore\Extension\Document\Areabrick\AbstractTemplateAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class Moto extends AbstractTemplateAreabrick
{
	/**
	 * @inheritdoc
	 */
	public function getName(): string
	{
		return 'Moto';
	}

	/**
	 * @inheritdoc
	 */
	public function getDescription(): string
	{
		return 'Text input for moto text with different style';
	}

	/**
	 * @inheritdoc
	 */
	public function getIcon(): ?string
	{
		return '/bundles/pimcoreadmin/img/flat-color-icons/diamond.svg';
	}

	/**
	 * @inheritdoc
	 */
	public function getTemplateLocation(): string
	{
		return static::TEMPLATE_LOCATION_GLOBAL;
	}

	/**
	 * @inheritdoc
	 */
	public function needsReload(): bool
	{
		// optional
		// here you can decide whether adding this bricks should trigger a reload
		// in the editing interface, this could be necessary in some cases. default=false
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function getHtmlTagOpen(Info $info): string
	{
		return '';
	}

	/**
	 * @inheritdoc
	 */
	public function getHtmlTagClose(Info $info): string
	{
		return '';
	}
}
