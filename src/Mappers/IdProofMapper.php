<?php

namespace Noardcode\LaravelSignhost\Mappers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionFileEntriesCollection;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionFileEntryLinksCollection;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionReceiversCollection;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionSignersCollection;
use Noardcode\LaravelSignhost\Enums\FileEntryLinkRel;
use Noardcode\LaravelSignhost\Enums\Language;
use Noardcode\LaravelSignhost\Enums\SignRequestMode;
use Noardcode\LaravelSignhost\Enums\TransactionStatus;
use Noardcode\LaravelSignhost\Exceptions\SignhostException;
use Noardcode\LaravelSignhost\ValueObjects\IdProof;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileEntries\Link;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileEntry;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\Receiver;

/**
 * Mapper IdProofMapper
 *
 * Maps a Signhost IdProof webhook payload (JSON) into a strongly typed IdProof
 * value object, including file entries and receivers.
 *
 * @mapper IdProofMapper
 */
class IdProofMapper
{
    /**
     * @param  Collection  $collection
     * @return IdProof
     *
     * @throws SignhostException
     */
    public function fromCollection(Collection $collection): IdProof
    {
        return new IdProof(
            id: $collection->get('Id'),
            fileEntries: $this->mapFiles($collection->get('Files') ?? []),
            language: ! is_null($collection->get('Language')) ? Language::from($collection->get('Language')) : null,
            seal: (bool) $collection->get('Seal'),
            signers: new TransactionSignersCollection([]),
            receivers: $this->mapReceivers($collection->get('Receivers') ?? []),
            reference: $collection->get('Reference'),
            postbackUrl: $collection->get('PostbackUrl'),
            signRequestMode: SignRequestMode::from((int) $collection->get('SignRequestMode')),
            daysToExpire: (int) $collection->get('DaysToExpire'),
            sendEmailNotifications: (bool) $collection->get('SendEmailNotifications'),
            status: TransactionStatus::from((int) $collection->get('Status')),
            cancelationReason: $collection->get('CancelationReason'),
            context: ! is_null($collection->get('Context')) ? json_encode($collection->get('Context'), true) : null,
            createdDateTime: Carbon::createFromTimeString($collection->get('CreatedDateTime')),
            modifiedDateTime: Carbon::createFromTimeString($collection->get('ModifiedDateTime')),
            canceledDateTime: ! is_null($collection->get('CanceledDateTime')) ? Carbon::createFromTimeString($collection->get('CanceledDateTime')) : null,
        );
    }

    /**
     * @param  array  $files
     * @return TransactionFileEntriesCollection
     */
    private function mapFiles(array $files): TransactionFileEntriesCollection
    {
        $filesCollection = new TransactionFileEntriesCollection;
        foreach ($files as $displayName => $file) {
            $linksCollection = new TransactionFileEntryLinksCollection;
            foreach ($file['Links'] as $link) {
                $linksCollection->push(new Link(
                    FileEntryLinkRel::from($link['Rel']),
                    $link['Type'],
                    $link['Link']
                ));
            }

            $filesCollection->push(new FileEntry($displayName, $linksCollection));
        }

        return $filesCollection;
    }

    /**
     * @param  array  $receivers
     * @return TransactionReceiversCollection
     *
     * @throws SignhostException
     */
    private function mapReceivers(array $receivers): TransactionReceiversCollection
    {
        $receiversCollection = new TransactionReceiversCollection;
        foreach ($receivers as $receiver) {
            $receiversCollection->push(new Receiver(
                name: (string) $receiver['Name'],
                email: (string) $receiver['Email'],
                language: Language::from($receiver['Language']),
                subject: null,
                message: (string) $receiver['Message'],
                reference: (string) $receiver['Reference'],
                context: ! is_null($receiver['Context']) ? json_encode($receiver['Context'], true) : null,
                id: (string) $receiver['Id'],
                createdDateTime: Carbon::createFromTimeString($receiver['CreatedDateTime']),
                modifiedDateTime: Carbon::createFromTimeString($receiver['ModifiedDateTime']),
            ));
        }

        return $receiversCollection;
    }
}
