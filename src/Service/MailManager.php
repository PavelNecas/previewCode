<?php

namespace App\Service;

use App\Tool\Utils;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use Pimcore\Model\Document;
use Pimcore\Translation\Translator;
use Throwable;

class MailManager
{
	// Constants
	const COMPONENT = 'MailManager';

	protected Document\Service $documentService;

	protected ApplicationLogger $aplicationLogger;

	protected Translator $translator;

	/**
	 * @param Document\Service $documentService
	 * @param ApplicationLogger $logger
	 * @param Translator $translator
	 */
	public function __construct(
		Document\Service $documentService,
		ApplicationLogger $logger,
		Translator $translator
	) {
		$this->documentService = $documentService;
		$this->aplicationLogger = $logger;
		$this->translator = $translator;
	}

	public function sendMail($docId, $language, $to = null, $from = null, $params = null, $attachment = null): bool
	{
		$document = Document\Email::getById($docId);
		if (!$document) {
			return false;
		}

		// check if translated email doc exist
		$document = Utils::getDocumentTranslation($document, $language);

		$mailer = new \Pimcore\Mail();

		if ($to) {
			$document->setTo($to);
		}
		if ($from) {
			$document->setFrom($from);
		}

		$mailer->setDocument($document);

		if ($params) {
			$mailer->setParams($params);
		}

		//adding an asset as attachment
		if ($attachment instanceof Asset) {
			$mailer->attach($attachment->getData(), $attachment->getFilename(), $attachment->getMimeType());
		}

		try {
			$mailer->send();

			return true;
		} catch (Throwable $t) {
			$error = $this->translator->trans('msg_mail_send_unsuccessful');
			$this->aplicationLogger->logException($error, $t, 'error', null, self::COMPONENT);

			return false;
		}
	}
}
