<?php

namespace App\EventListener;

use Pimcore\Event\SystemEvents;
use Pimcore\File;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class KeyListener implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
	{
		return [
			SystemEvents::SERVICE_PRE_GET_VALID_KEY => 'onPreGetValidKey',
		];
	}

	/**
	 * @param GenericEvent $event
	 */
	public function onPreGetValidKey(GenericEvent $event)
	{
		$event->setArgument('key', File::getValidFilename($event->getArgument('key')));
	}
}
