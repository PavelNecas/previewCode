<?php

namespace App\Service;

use App\Tool\Utils;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Document;
use Pimcore\Tool\Newsletter;
use Pimcore\Translation\Translator;
use Throwable;

class NewsletterManager
{
	const ROOT_FOLDER_ID = 9;
	const CONFIRMATION_EMAIL_DOCUMENT_ID = 14;
	const COMPONENT = 'NewsletterManager';

	protected Translator $translator;

	protected ApplicationLogger $aplicationLogger;

	/**
	 * @param Translator $translator
	 * @param ApplicationLogger $logger
	 */
	public function __construct(
		Translator $translator,
		ApplicationLogger $logger,
	) {
		$this->translator = $translator;
		$this->aplicationLogger = $logger;
	}

	/**
	 * @param array $data
	 *
	 * @return bool
	 */
	public function subscribe(array $data): bool
	{
		$newsletter = new Newsletter('newsletter');
		if ($newsletter->checkParams($data)) {
			try {
				$data['parentId'] = self::ROOT_FOLDER_ID;
				$subscriber = $newsletter->subscribe($data);
				$document = Document\Email::getById(self::CONFIRMATION_EMAIL_DOCUMENT_ID);
				// check if translated email doc exist
				$document = Utils::getDocumentTranslation($document, $data['lang']);
				$newsletter->sendConfirmationMail($subscriber, $document, ['additional' => 'parameters']);
				$subscriber->save();

				return true;
			} catch (Throwable $t) {
				$error = $this->translator->trans('msg_subscriber_creation_unsuccessful');
				$this->aplicationLogger->logException($error, $t, 'error', null, self::COMPONENT);
			}
		}

		return false;
	}
}
