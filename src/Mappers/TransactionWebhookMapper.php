<?php

namespace Noardcode\LaravelSignhost\Mappers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionReceiversCollection;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionSignersCollection;
use Noardcode\LaravelSignhost\Enums\Language;
use Noardcode\LaravelSignhost\Enums\SignerActivityStatus;
use Noardcode\LaravelSignhost\Enums\SignRequestMode;
use Noardcode\LaravelSignhost\Enums\TransactionStatus;
use Noardcode\LaravelSignhost\ValueObjects\Transaction;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Activity;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Receiver;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Signer;

/**
 * Mapper TransactionWebhookMapper
 *
 * Maps a Signhost Transaction webhook payload (JSON) into a strongly typed
 * Transaction value object.
 *
 * @mapper TransactionWebhookMapper
 */
class TransactionWebhookMapper
{
    /**
     * Map the incoming webhook collection to a Transaction Value Object.
     *
     * @param  Collection  $results  Decoded webhook payload as a Laravel collection
     * @return Transaction
     */
    public function fromCollection(Collection $results): Transaction
    {
        return new Transaction(
            language: ! is_null($results->get('Language')) ? Language::from($results->get('Language')) : Language::English,
            seal: (bool) $results->get('Seal'),
            signers: $this->mapSigners($results->get('Signers') ?? []),
            receivers: $this->mapReceivers($results->get('Receivers') ?? []),
            reference: $results->get('Reference'),
            postbackUrl: $results->get('PostbackUrl'),
            signRequestMode: ! is_null($results->get('SignRequestMode')) ? SignRequestMode::from((int) $results->get('SignRequestMode')) : null,
            daysToExpire: (int) $results->get('DaysToExpire'),
            sendEmailNotifications: (bool) $results->get('SendEmailNotifications'),
            status: ! is_null($results->get('Status')) ? TransactionStatus::from((int) $results->get('Status')) : null,
            cancelationReason: $results->get('CancelationReason'),
            context: ! is_null($results->get('Context')) ? json_encode($results->get('Context'), true) : null,
            id: $results->get('Id'),
            createdDateTime: ! is_null($results->get('CreatedDateTime')) ? Carbon::parse($results->get('CreatedDateTime')) : null,
            modifiedDateTime: ! is_null($results->get('ModifiedDateTime')) ? Carbon::parse($results->get('ModifiedDateTime')) : null,
            canceledDateTime: ! is_null($results->get('CanceledDateTime')) ? Carbon::parse($results->get('CanceledDateTime')) : null,
            checksum: $results->get('Checksum'),
        );
    }

    private function mapSigners(array $signers): TransactionSignersCollection
    {
        $signersCollection = new TransactionSignersCollection;

        foreach ($signers as $signer) {
            $activities = collect([]);
            if (isset($signer['Activities'])) {
                foreach ($signer['Activities'] as $activity) {
                    $activities->push(new Activity(
                        id: $activity['Id'],
                        code: SignerActivityStatus::from($activity['Code']),
                        activity: $activity['Activity'],
                        createdDateTime: Carbon::parse($activity['CreatedDateTime']),
                    ));
                }
            }

            $signersCollection->push(new Signer(
                email: $signer['Email'],
                id: $signer['Id'],
                introText: null, // Not present in webhook payload
                authentications: null, // Not present in webhook payload
                verifications: null, // Not present in webhook payload
                sendSignRequest: $signer['SendSignRequest'] ?? true,
                signRequestSubject: null, // Not present in webhook payload
                signRequestMessage: $signer['SignRequestMessage'] ?? null,
                sendSignConfirmation: $signer['SendSignConfirmation'] ?? true,
                allowDelegation: false, // Not present in webhook payload
                language: isset($signer['Language']) ? Language::from($signer['Language']) : Language::English,
                scribbleName: $signer['ScribbleName'] ?? null,
                daysToRemind: $signer['DaysToRemind'] ?? null,
                expires: null, // Not present in webhook payload
                reference: $signer['Reference'] ?? null,
                returnUrl: $signer['ReturnUrl'] ?? null,
                context: isset($signer['Context']) ? json_encode($signer['Context']) : null,
                mobile: $signer['Mobile'] ?? null,
                iban: $signer['Iban'] ?? null,
                bsn: $signer['BSN'] ?? null,
                requireScribbleName: $signer['RequireScribbleName'] ?? null,
                requireScribble: $signer['RequireScribble'] ?? null,
                requireEmailVerification: $signer['RequireEmailVerification'] ?? null,
                requireSmsVerification: $signer['RequireSmsVerification'] ?? null,
                requireIdealVerification: $signer['RequireIdealVerification'] ?? null,
                requireDigidVerification: $signer['RequireDigidVerification'] ?? null,
                requireSurfnetVerification: $signer['RequireSurfnetVerification'] ?? null,
                scribbleNameFixed: $signer['ScribbleNameFixed'] ?? null,
                rejectReason: $signer['RejectReason'] ?? null,
                signUrl: $signer['SignUrl'] ?? null,
                signedDateTime: isset($signer['SignedDateTime']) ? Carbon::parse($signer['SignedDateTime']) : null,
                rejectDateTime: isset($signer['RejectDateTime']) ? Carbon::parse($signer['RejectDateTime']) : null,
                createdDateTime: isset($signer['CreatedDateTime']) ? Carbon::parse($signer['CreatedDateTime']) : null,
                modifiedDateTime: isset($signer['ModifiedDateTime']) ? Carbon::parse($signer['ModifiedDateTime']) : null,
                activities: $activities,
            ));
        }

        return $signersCollection;
    }

    private function mapReceivers(array $receivers): TransactionReceiversCollection
    {
        $receiversCollection = new TransactionReceiversCollection;

        foreach ($receivers as $receiver) {
            $receiversCollection->push(new Receiver(
                name: $receiver['Name'],
                email: $receiver['Email'],
                language: Language::from($receiver['Language']),
                subject: null, // Not present in webhook payload
                message: $receiver['Message'],
                reference: $receiver['Reference'] ?? null,
                context: isset($receiver['Context']) ? json_encode($receiver['Context']) : null,
                id: $receiver['Id'],
                createdDateTime: isset($receiver['CreatedDateTime']) ? Carbon::parse($receiver['CreatedDateTime']) : null,
                modifiedDateTime: isset($receiver['ModifiedDateTime']) ? Carbon::parse($receiver['ModifiedDateTime']) : null,
            ));
        }

        return $receiversCollection;
    }
}
