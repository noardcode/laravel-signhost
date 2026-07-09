<?php

namespace Noardcode\LaravelSignhost\ValueObjects\Transactions\IdProof;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionAuthenticationsCollection;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionVerificationsCollection;
use Noardcode\LaravelSignhost\Contracts\ToSignhostArrayContract;
use Noardcode\LaravelSignhost\Enums\Language;

/**
 * Class Signer
 */
class Signer extends \Noardcode\LaravelSignhost\ValueObjects\Transactions\Signer implements ToSignhostArrayContract
{
    /**
     * @param  string  $email  The e-mail address of the signer.
     * @param  string|null  $id  The id of the signer must be unique within a transaction.
     * @param  string|null  $introText  An intro text to show to the user during the sign process.
     * @param  array|TransactionAuthenticationsCollection|null  $authentications  List of authentications that the signer has to authenticate with.
     * @param  array|TransactionVerificationsCollection|null  $verifications  List of verifications that the signer has to verify with.
     * @param  bool  $sendSignRequest  Send a sign invitation to the signer his e-mail address.
     * @param  string|null  $signUrl
     * @param  string|null  $showUrl
     * @param  string|null  $receiptUrl
     * @param  string|null  $signRequestSubject  The subject of the sign request email in plain text.
     * @param  string|null  $signRequestMessage  The message of the sign request in plain text.
     * @param  bool  $sendSignConfirmation  Send the sign confirmation to the signer his e-mail address.
     * @param  bool  $allowDelegation
     * @param  string|null  $delegateReason
     * @param  string|null  $delegateSignerEmail
     * @param  string|null  $delegateSignerName
     * @param  Carbon|null  $signerDelegationDateTime
     * @param  string|null  $delegateSignUrl
     * @param  Language  $language  The language of the receiving user.
     * @param  string|null  $scribbleName  The name of the signer, this will be pre-filled in the scribble form.
     * @param  int|null  $daysToRemind  Number of days before reminding the signers. -1 to disable reminders.
     * @param  Carbon|null  $expires  When set, the signer is no longer allowed to sign the transaction after this date.
     * @param  string|null  $reference  The reference of the signer.
     * @param  string|null  $rejectReason
     * @param  string|null  $returnUrl  The url to redirect the user to after signing, rejecting or cancelling.
     * @param  string|null  $context  Any valid JSON object that we will return back to you when doing a GET on the transaction or when we send a postback.
     * @param  Collection|null  $activities
     */
    public function __construct(
        protected string $email,
        protected ?string $id = null,
        protected ?string $introText = null,
        protected array|TransactionAuthenticationsCollection|null $authentications = null,
        protected array|TransactionVerificationsCollection|null $verifications = null,
        protected bool $sendSignRequest = true,
        protected ?string $signUrl = null,
        protected ?string $showUrl = null,
        protected ?string $receiptUrl = null,
        protected ?string $signRequestSubject = null,
        protected ?string $signRequestMessage = null,
        protected bool $sendSignConfirmation = true,
        protected bool $allowDelegation = false,
        protected ?string $delegateReason = null,
        protected ?string $delegateSignerEmail = null,
        protected ?string $delegateSignerName = null,
        protected ?Carbon $signerDelegationDateTime = null,
        protected ?string $delegateSignUrl = null,
        protected Language $language = Language::English,
        protected ?string $scribbleName = null,
        protected ?int $daysToRemind = 7,
        protected ?Carbon $expires = null,
        protected ?string $reference = null,
        protected ?string $rejectReason = null,
        protected ?string $returnUrl = null, // Defaults to https://signhost.com
        protected ?string $context = null,
        protected ?Collection $activities = null,
    ) {
        parent::__construct(
            $email,
            $id,
            $introText,
            $authentications,
            $verifications,
            $sendSignRequest,
            $signRequestSubject,
            $signRequestMessage,
            $sendSignConfirmation,
            $allowDelegation,
            $language,
            $scribbleName,
            $daysToRemind,
            $expires,
            $reference,
            $returnUrl,
            $context
        );
    }

    /**
     * @return string|null
     */
    public function getSignUrl(): ?string
    {
        return $this->signUrl;
    }

    /**
     * @return string|null
     */
    public function getShowUrl(): ?string
    {
        return $this->showUrl;
    }

    /**
     * @return string|null
     */
    public function getReceiptUrl(): ?string
    {
        return $this->receiptUrl;
    }

    /**
     * @return string|null
     */
    public function getDelegateReason(): ?string
    {
        return $this->delegateReason;
    }

    /**
     * @return string|null
     */
    public function getDelegateSignerEmail(): ?string
    {
        return $this->delegateSignerEmail;
    }

    /**
     * @return string|null
     */
    public function getDelegateSignerName(): ?string
    {
        return $this->delegateSignerName;
    }

    /**
     * @return Carbon|null
     */
    public function getSignerDelegationDateTime(): ?Carbon
    {
        return $this->signerDelegationDateTime;
    }

    /**
     * @return string|null
     */
    public function getDelegateSignUrl(): ?string
    {
        return $this->delegateSignUrl;
    }

    /**
     * @return string|null
     */
    public function getRejectReason(): ?string
    {
        return $this->rejectReason;
    }

    /**
     * @return Collection|null
     */
    public function getActivities(): ?Collection
    {
        return $this->activities;
    }
}
