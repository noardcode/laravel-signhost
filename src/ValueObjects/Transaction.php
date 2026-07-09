<?php

namespace Noardcode\LaravelSignhost\ValueObjects;

use Carbon\Carbon;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionReceiversCollection;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionSignersCollection;
use Noardcode\LaravelSignhost\Contracts\ToSignhostArrayContract;
use Noardcode\LaravelSignhost\Enums\Language;
use Noardcode\LaravelSignhost\Enums\SignRequestMode;
use Noardcode\LaravelSignhost\Enums\TransactionStatus;

/**
 * Class Transaction
 */
class Transaction implements ToSignhostArrayContract
{
    /**
     * @param  Language  $language  The language of the sender notifications and the receipt.
     * @param  bool  $seal  Seal the document before sending to the signers.
     * @param  TransactionSignersCollection  $signers  The signers of the transaction.
     * @param  null|TransactionReceiversCollection  $receivers  The receivers of the transaction.
     * @param  string|null  $reference  The reference of the transaction.
     * @param  string|null  $postbackUrl  The absolute URL to postback the status updates.
     * @param  SignRequestMode|null  $signRequestMode  Sending at once, or sequentially.
     * @param  int|null  $daysToExpire  The number of days the transaction is valid (max. 90 days).
     * @param  bool  $sendEmailNotifications  Send email notifications to the sender.
     * @param  TransactionStatus|null  $status
     * @param  string|null  $cancelationReason  The cancellation reason given during a DELETE call.
     * @param  string|null  $context  Any valid JSON object that we will return back to you when doing a GET on the transaction or when we send a postback.
     * @param  string|null  $id
     * @param  Carbon|null  $createdDateTime
     * @param  Carbon|null  $modifiedDateTime
     * @param  Carbon|null  $canceledDateTime
     * @param  string|null  $checksum
     */
    public function __construct(
        protected Language $language,
        protected bool $seal,
        protected TransactionSignersCollection $signers,
        protected ?TransactionReceiversCollection $receivers,
        protected ?string $reference,
        protected ?string $postbackUrl = null,
        protected ?SignRequestMode $signRequestMode = null,
        protected ?int $daysToExpire = 60,
        protected bool $sendEmailNotifications = true,
        protected ?TransactionStatus $status = null,
        protected ?string $cancelationReason = null,
        protected ?string $context = null,
        protected ?string $id = null,
        protected ?Carbon $createdDateTime = null,
        protected ?Carbon $modifiedDateTime = null,
        protected ?Carbon $canceledDateTime = null,
        protected ?string $checksum = null,
    ) {
        //
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
     * @return string
     */
    public function getPostbackUrl(): string
    {
        return $this->postbackUrl ?? route(config('signhost.webhook.route'));
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

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCreatedDateTime(): ?Carbon
    {
        return $this->createdDateTime;
    }

    public function getModifiedDateTime(): ?Carbon
    {
        return $this->modifiedDateTime;
    }

    public function getCanceledDateTime(): ?Carbon
    {
        return $this->canceledDateTime;
    }

    public function getChecksum(): ?string
    {
        return $this->checksum;
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
