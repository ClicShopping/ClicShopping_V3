<?php

declare(strict_types=1);

namespace EmailValidator;

class Policy
{
    /**
     * @var bool
     */
    private $checkBannedListedEmail;

    /**
     * @var bool
     */
    private $checkDisposableEmail;

    /**
     * @var bool
     */
    private $checkFreeEmail;

    /**
     * @var array
     */
    private $bannedList;

    /**
     * @var bool
     */
    private $checkMxRecords;

    /**
     * @var array|mixed
     */
    private $disposableList;

    /**
     * @var array|mixed
     */
    private $freeList;

    /**
     * @var bool
     */
    private $localDisposableOnly;

    /**
     * @var bool
     */
    private $localFreeOnly;

    public function __construct(array $config = [])
    {
        $this->checkMxRecords         = (bool) ($config['checkMxRecords']         ?? true);
        $this->checkBannedListedEmail = (bool) ($config['checkBannedListedEmail'] ?? false);
        $this->checkDisposableEmail   = (bool) ($config['checkDisposableEmail']   ?? false);
        $this->checkFreeEmail         = (bool) ($config['checkFreeEmail']         ?? false);
        $this->localDisposableOnly    = (bool) ($config['LocalDisposableOnly']    ?? false);
        $this->localFreeOnly          = (bool) ($config['LocalFreeOnly']          ?? false);

        $this->bannedList             = $config['bannedList']     ?? [];
        $this->disposableList         = $config['disposableList'] ?? [];
        $this->freeList               = $config['freeList']       ?? [];
    }

    /**
     * Validate MX records?
     *
     * @return bool
     */
    public function validateMxRecord(): bool
    {
        return $this->checkMxRecords;
    }

    /**
     * Check domain if it is on the banned list?
     *
     * @return bool
     */
    public function checkBannedListedEmail(): bool
    {
        return $this->checkBannedListedEmail;
    }

    /**
     * Check if the domain is used by a disposable email site?
     *
     * @return bool
     */
    public function checkDisposableEmail(): bool
    {
        return $this->checkDisposableEmail;
    }

    /**
     * Check if the domain is used by a free email site?
     *
     * @return bool
     */
    public function checkFreeEmail(): bool
    {
        return $this->checkFreeEmail;
    }

    /**
     * Check if only a local copy of disposable email address domains should be used. Saves the overhead of
     * making HTTP requests to get the list the first time that validation is called.
     *
     * @return bool
     */
    public function checkDisposableLocalListOnly(): bool
    {
        return $this->localDisposableOnly;
    }

    /**
     * Check if only a local copy of free email address domains should be used. Saves the overhead of
     * making HTTP requests to get the list the first time that validation is called.
     *
     * @return bool
     */
    public function checkFreeLocalListOnly(): bool
    {
        return $this->localFreeOnly;
    }

    /**
     * Returns the list of banned domains.
     *
     * @return array
     */
    public function getBannedList(): array
    {
        return $this->bannedList;
    }

    /**
     * Returns the list of free email provider domains.
     *
     * @return array
     */
    public function getFreeList(): array
    {
        return $this->freeList;
    }

    /**
     * Returns the list of disposable email domains.
     *
     * @return array
     */
    public function getDisposableList(): array
    {
        return $this->disposableList;
    }
}
