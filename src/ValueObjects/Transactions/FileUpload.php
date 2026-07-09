<?php

namespace Noardcode\LaravelSignhost\ValueObjects\Transactions;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\UploadedFile;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionFileMetaDataFormSetsCollection;
use Noardcode\LaravelSignhost\Casts\Collections\TransactionFileMetaDataSignersCollection;
use Noardcode\LaravelSignhost\Exceptions\SignhostException;
use Noardcode\LaravelSignhost\ValueObjects\Transactions\FileEntries\FileMetaData;

class FileUpload
{
    private UploadedFile $file;

    private FileMetaData $fileMetaData;

    /**
     * @param  string  $path
     * @param  string|null  $name
     */
    public function __construct(
        private readonly string $path,
        private ?string $name = null,
    ) {}

    /**
     * Return the provided name or the original file name
     *
     * @return string
     *
     * @throws FileNotFoundException|SignhostException
     */
    public function getName(): string
    {
        return $this->name ??= $this->getFile()->getClientOriginalName();
    }

    /**
     * Create a FileMetaData value object for use in the signhost api
     *
     * @return FileMetaData
     *
     * @throws FileNotFoundException|SignhostException
     */
    public function toFileMetaData(): FileMetaData
    {
        /** Create a FileMetaData value object for  */
        return $this->fileMetaData ??= new FileMetaData(
            0,
            $this->getName(),
            false,
        );
    }

    /**
     * @param  FileMetaData  $fileMetaData
     */
    public function setFileMetaData(FileMetaData $fileMetaData): void
    {
        $this->fileMetaData = $fileMetaData;
    }

    /**
     * @throws FileNotFoundException
     * @throws SignhostException
     */
    public function setFormSet(FileMetaData\FormSet $formSet): TransactionFileMetaDataFormSetsCollection
    {
        $this->fileMetaData ??= $this->toFileMetaData();
        $this->fileMetaData->setFormSet($formSet);

        return $this->fileMetaData->getFormSets();
    }

    /**
     * Links a signer to one or more FormSets (by name) on this file. Required
     * for a FormSet set via `setFormSet()` to actually be applied by SignHost
     * — without it, the FormSet is accepted but silently ignored.
     *
     * @throws FileNotFoundException
     * @throws SignhostException
     */
    public function addSigner(FileMetaData\Signer $signer): TransactionFileMetaDataSignersCollection
    {
        $this->fileMetaData ??= $this->toFileMetaData();

        return $this->fileMetaData->addSigner($signer);
    }

    /**
     * Validate the file and return an UploadedFile instance
     *
     * @return UploadedFile
     *
     * @throws FileNotFoundException
     * @throws SignhostException
     */
    public function getFile(): UploadedFile
    {
        /** Check if the file exists */
        if (! file_exists($this->path)) {
            throw new FileNotFoundException('File for sign transaction does not exist');
        }

        /** Check if mimetype pdf */
        if (mime_content_type($this->path) !== 'application/pdf') {
            throw new SignhostException('File for sign transaction is not a pdf');
        }

        /** Create an uploaded file instance for ease of use */
        return $this->file ??= new UploadedFile($this->path, $this->name);
    }
}
