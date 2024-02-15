<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\EventListener;

use Elastica\Exception\ExceptionInterface;
use MonsieurBiz\SyliusSearchPlugin\Exception\MissingParamException;
use MonsieurBiz\SyliusSearchPlugin\Model\Document\Index\Indexer;
use MonsieurBiz\SyliusSearchPlugin\Model\Documentable\DocumentableInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class DocumentListener
{
    public function __construct(private Indexer $documentIndexer)
    {
    }

    public function saveDocument(GenericEvent $event): void
    {
        $subject = $event->getSubject();
        if (!$subject instanceof DocumentableInterface) {
            return;
        }

        try {
            $this->documentIndexer->indexOne($subject);
        } catch (ExceptionInterface|MissingParamException) {
        }
    }

    public function deleteDocument(GenericEvent $event): void
    {
        $subject = $event->getSubject();
        if (!$subject instanceof DocumentableInterface) {
            return;
        }

        try {
            $this->documentIndexer->removeOne($subject);
        } catch (ExceptionInterface|MissingParamException) {
        }
    }
}
