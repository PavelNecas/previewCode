<?php

namespace App\EventListener;

use Pimcore\Analytics\Google\Event\TrackingDataEvent;
use Pimcore\Event\Analytics\Google\TagManager\CodeEvent;
use Pimcore\Event\Analytics\GoogleAnalyticsEvents;
use Pimcore\Event\Analytics\GoogleTagManagerEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TrackingCodesListener implements EventSubscriberInterface
{

	/**
	 * @inheritDoc
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			GoogleAnalyticsEvents::CODE_TRACKING_DATA => 'onGa',
			GoogleTagManagerEvents::CODE_HEAD => 'onGtmHead'
		];
	}

	/**
	 * @param TrackingDataEvent $event
	 */
	public function onGa(TrackingDataEvent $event)
	{
		$event->setTemplate('snippet/tracking/ga.html.twig');
	}

	/**
	 * @param CodeEvent $event
	 */
	public function onGtmHead(CodeEvent $event)
	{
		$event->setTemplate('snippet/tracking/gtmHead.html.twig');
	}
}
