<?php

namespace Noardcode\LaravelSignhost\ValueObjects\Transactions;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionAuthenticationsCollection;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionVerificationsCollection;
use Noardcode\LaravelSignhost\Contracts\ToSignhostArrayContract;
use Noardcode\LaravelSignhost\Enums\Language;
use Noardcode\LaravelSignhost\Exceptions\SignhostException;

/**
 * Class Signer
 */
class Signer implements ToSignhostArrayContract
{
    /**
     * @param  string  $email  The e-mail address of the signer.
     * @param  string|null  $id  The id of the signer must be unique within a transaction.
     * @param  string|null  $introText  An intro text to show to the user during the sign process.
     * @param  string|null  $scribbleName  The name of the signer, this will be pre-filled in the scribble form.
     * @param  array|TransactionAuthenticationsCollection|null  $authentications  List of authentications that the signer has to authenticate with.
     * @param  array|TransactionVerificationsCollection|null  $verifications  List of verifications that the signer has to verify with.
     * @param  Language  $language  The language of the receiving user.
     * @param  bool  $sendSignRequest  Send a sign invitation to the signer his e-mail address.
     * @param  string|null  $signRequestSubject  The subject of the sign request email in plain text.
     * @param  string|null  $signRequestMessage  The message of the sign request in plain text.
     * @param  bool  $sendSignConfirmation  Send the sign confirmation to the signer his e-mail address.
     * @param  bool  $allowDelegation
     * @param  int|null  $daysToRemind  Number of days before reminding the signers. -1 to disable reminders.
     * @param  Carbon|null  $expires  When set, the signer is no longer allowed to sign the transaction after this date.
     * @param  string|null  $reference  The reference of the signer.
     * @param  string|null  $returnUrl  The url to redirect the user to after signing, rejecting or cancelling.
     * @param  string|null  $context  Any valid JSON object that we will return back to you when doing a GET on the transaction or when we send a postback.
     */
    public function __construct(
        protected string $email,
        protected ?string $id = null,
        protected ?string $introText = null,
        protected array|TransactionAuthenticationsCollection|null $authentications = null,
        protected array|TransactionVerificationsCollection|null $verifications = null,
        protected bool $sendSignRequest = true,
        protected ?string $signRequestSubject = null,
        protected ?string $signRequestMessage = null,
        protected bool $sendSignConfirmation = true,
        protected bool $allowDelegation = false,
        protected Language $language = Language::English,
        protected ?string $scribbleName = null,
        protected ?int $daysToRemind = 7,
        protected ?Carbon $expires = null,
        protected ?string $reference = null,
        protected ?string $returnUrl = null, // Defaults to https://signhost.com
        protected ?string $context = null,
        protected ?string $mobile = null,
        protected ?string $iban = null,
        protected ?string $bsn = null,
        protected ?bool $requireScribbleName = null,
        protected ?bool $requireScribble = null,
        protected ?bool $requireEmailVerification = null,
        protected ?bool $requireSmsVerification = null,
        protected ?bool $requireIdealVerification = null,
        protected ?bool $requireDigidVerification = null,
        protected ?bool $requireSurfnetVerification = null,
        protected ?bool $scribbleNameFixed = null,
        protected ?string $rejectReason = null,
        protected ?string $signUrl = null,
        protected ?Carbon $signedDateTime = null,
        protected ?Carbon $rejectDateTime = null,
        protected ?Carbon $createdDateTime = null,
        protected ?Carbon $modifiedDateTime = null,
        protected ?Collection $activities = null,
    ) {
        //
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        if (is_null($this->id)) {
            $this->id = Str::uuid()->toString();
        }

        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getScribbleName(): ?string
    {
        return $this->scribbleName;
    }

    /**
     * @return string|null
     */
    public function getIntroText(): ?string
    {
        return $this->introText ?? '';
    }

    /**
     * @return array|TransactionAuthenticationsCollection|null
     *
     * @throws SignhostException
     */
    public function getAuthentications(): array|TransactionAuthenticationsCollection|null
    {
        if ($this->authentications instanceof TransactionAuthenticationsCollection) {
            return $this->authentications;
        }

        if (is_array($this->authentications)) {
            return new TransactionAuthenticationsCollection($this->authentications);
        }

        return null;
    }

    /**
     * @return array|TransactionVerificationsCollection|null
     *
     * @throws SignhostException
     */
    public function getVerifications(): array|TransactionVerificationsCollection|null
    {
        if ($this->verifications instanceof TransactionVerificationsCollection) {
            return $this->verifications;
        }

        if (is_array($this->verifications)) {
            return new TransactionVerificationsCollection($this->verifications);
        }

        return null;
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
    public function getSendSignRequest(): bool
    {
        return $this->sendSignRequest;
    }

    /**
     * @return string|null
     */
    public function getSignRequestSubject(): ?string
    {
        return $this->signRequestSubject;
    }

    /**
     * @return string|null
     */
    public function getSignRequestMessage(): ?string
    {
        return $this->signRequestMessage;
    }

    /**
     * @return bool
     */
    public function getSendSignConfirmation(): bool
    {
        return $this->sendSignConfirmation;
    }

    /**
     * @return bool
     */
    public function getAllowDelegation(): bool
    {
        return $this->allowDelegation;
    }

    /**
     * @return int|null
     */
    public function getDaysToRemind(): ?int
    {
        return $this->daysToRemind;
    }

    /**
     * @return Carbon|null
     */
    public function getExpires(): ?Carbon
    {
        return $this->expires;
    }

    /**
     * @return string|null
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * @return string|null
     */
    public function getReturnUrl(): ?string
    {
        return $this->returnUrl;
    }

    /**
     * @return string|null
     */
    public function getContext(): ?string
    {
        return $this->context;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function getBsn(): ?string
    {
        return $this->bsn;
    }

    public function getRequireScribbleName(): ?bool
    {
        return $this->requireScribbleName;
    }

    public function getRequireScribble(): ?bool
    {
        return $this->requireScribble;
    }

    public function getRequireEmailVerification(): ?bool
    {
        return $this->requireEmailVerification;
    }

    public function getRequireSmsVerification(): ?bool
    {
        return $this->requireSmsVerification;
    }

    public function getRequireIdealVerification(): ?bool
    {
        return $this->requireIdealVerification;
    }

    public function getRequireDigidVerification(): ?bool
    {
        return $this->requireDigidVerification;
    }

    public function getRequireSurfnetVerification(): ?bool
    {
        return $this->requireSurfnetVerification;
    }

    public function getScribbleNameFixed(): ?bool
    {
        return $this->scribbleNameFixed;
    }

    public function getRejectReason(): ?string
    {
        return $this->rejectReason;
    }

    public function getSignUrl(): ?string
    {
        return $this->signUrl;
    }

    public function getSignedDateTime(): ?Carbon
    {
        return $this->signedDateTime;
    }

    public function getRejectDateTime(): ?Carbon
    {
        return $this->rejectDateTime;
    }

    public function getCreatedDateTime(): ?Carbon
    {
        return $this->createdDateTime;
    }

    public function getModifiedDateTime(): ?Carbon
    {
        return $this->modifiedDateTime;
    }

    public function getActivities(): ?Collection
    {
        return $this->activities;
    }

    /**
     * @return array
     *
     * @throws SignhostException
     */
    public function toArray(): array
    {
        return [
            'Id' => $this->getId(),
            'Email' => $this->getEmail(),
            'IntroText' => $this->getIntroText(),
            'Authentications' => $this->getAuthentications()?->toArray(),
            'Verifications' => $this->getVerifications()?->toArray(),
            'SendSignRequest' => $this->getSendSignRequest(),
            'SignRequestSubject' => $this->getSignRequestSubject(),
            'SignRequestMessage' => $this->getSignRequestMessage(),
            'SendSignConfirmation' => $this->getSendSignConfirmation(),
            'AllowDelegation' => $this->getAllowDelegation(),
            'Language' => $this->getLanguage()->value,
            'ScribbleName' => $this->getScribbleName(),
            'DaysToRemind' => $this->getDaysToRemind(),
            'Expires' => $this->getExpires(),
            'Reference' => $this->getReference(),
            'ReturnUrl' => $this->getReturnUrl(),
            'Context' => $this->getContext(),
        ];
    }
}
