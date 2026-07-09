<?php

namespace Noardcode\LaravelSignhost\ValueObjects;

use Carbon\Carbon;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionFileEntriesCollection;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionReceiversCollection;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionSignersCollection;
use Noardcode\LaravelSignhost\Contracts\ToSignhostArrayContract;
use Noardcode\LaravelSignhost\Enums\Language;
use Noardcode\LaravelSignhost\Enums\SignRequestMode;
use Noardcode\LaravelSignhost\Enums\TransactionStatus;

/**
 * Class IdProof
 */
class IdProof implements ToSignhostArrayContract
{
    /**
     * @param  string  $id
     * @param  TransactionFileEntriesCollection  $fileEntries
     * @param  Language|null  $language  The language of the sender notifications and the receipt.
     * @param  bool  $seal  Seal the document before sending to the signers.
     * @param  TransactionSignersCollection  $signers  The signers of the transaction.
     * @param  null|TransactionReceiversCollection  $receivers  The receivers of the transaction.
     * @param  string  $reference  The reference of the transaction.
     * @param  string|null  $postbackUrl  The absolute URL to postback the status updates.
     * @param  SignRequestMode|null  $signRequestMode  Sending at once, or sequentially.
     * @param  int|null  $daysToExpire  The number of days the transaction is valid (max. 90 days).
     * @param  bool  $sendEmailNotifications  Send email notifications to the sender.
     * @param  TransactionStatus|null  $status
     * @param  string|null  $cancelationReason  The cancellation reason given during a DELETE call.
     * @param  string|null  $context  Any valid JSON object that we will return back to you when doing a GET on the transaction or when we send a postback.
     * @param  Carbon  $createdDateTime
     * @param  Carbon  $modifiedDateTime
     * @param  Carbon|null  $canceledDateTime
     */
    public function __construct(
        protected string $id,
        protected TransactionFileEntriesCollection $fileEntries,
        protected ?Language $language,
        protected bool $seal,
        protected TransactionSignersCollection $signers,
        protected ?TransactionReceiversCollection $receivers,
        protected string $reference,
        protected ?string $postbackUrl,
        protected ?SignRequestMode $signRequestMode,
        protected ?int $daysToExpire,
        protected bool $sendEmailNotifications,
        protected ?TransactionStatus $status,
        protected ?string $cancelationReason,
        protected ?string $context,
        protected Carbon $createdDateTime,
        protected Carbon $modifiedDateTime,
        protected ?Carbon $canceledDateTime = null,
    ) {
        //
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return TransactionFileEntriesCollection
     */
    public function getFileEntries(): TransactionFileEntriesCollection
    {
        return $this->fileEntries;
    }

    /**
     * @return Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @return bool
     */
    public function getSeal(): bool
    {
        return $this->seal;
    }

    /**
     * @return TransactionSignersCollection
     */
    public function getSigners(): TransactionSignersCollection
    {
        return $this->signers;
    }

    /**
     * @return TransactionReceiversCollection|null
     */
    public function getReceivers(): ?TransactionReceiversCollection
    {
        return $this->receivers;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return string|null
     */
    public function getPostbackUrl(): ?string
    {
        return $this->postbackUrl;
    }

    /**
     * @return SignRequestMode|null
     */
    public function getSignRequestMode(): ?SignRequestMode
    {
        return $this->signRequestMode;
    }

    /**
     * @return int|null
     */
    public function getDaysToExpire(): ?int
    {
        return $this->daysToExpire;
    }

    /**
     * @return bool
     */
    public function getSendEmailNotifications(): bool
    {
        return $this->sendEmailNotifications;
    }

    /**
     * @return TransactionStatus|null
     */
    public function getStatus(): ?TransactionStatus
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getCancelationReason(): ?string
    {
        return $this->cancelationReason;
    }

    /**
     * @return string|null
     */
    public function getContext(): ?string
    {
        return $this->context;
    }

    /**
     * @return Carbon
     */
    public function getCreatedDateTime(): Carbon
    {
        return $this->createdDateTime;
    }

    /**
     * @return Carbon
     */
    public function getModifiedDateTime(): Carbon
    {
        return $this->modifiedDateTime;
    }

    public function getCanceledDateTime(): ?Carbon
    {
        return $this->canceledDateTime;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'Language' => $this->getLanguage()?->value,
            'Seal' => $this->getSeal(),
            'Signers' => $this->getSigners()?->toArray(),
            'Receivers' => $this->getReceivers()?->toArray(),
            'Reference' => $this->getReference(),
            'PostbackUrl' => $this->getPostbackUrl(),
            'SignRequestMode' => $this->getSignRequestMode()?->value,
            'DaysToExpire' => $this->getDaysToExpire(),
            'SendEmailNotifications' => $this->getSendEmailNotifications(),
            'Status' => $this->getStatus()?->value,
            'CancelationReason' => $this->getCancelationReason(),
            'Context' => $this->getContext(),
        ];
    }
}
