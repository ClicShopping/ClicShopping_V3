<?php

declare(strict_types=1);

namespace EmailValidator;

class EmailAddress
{
    /**
     * @var string
     */
    private $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    /**
     * Returns the domain name portion of the email address.
     *
     * @return string|null
     */
    public function getDomain(): ?string
    {
        return explode('@', $this->email)[1] ?? null;
    }

    /**
     * Returns the email address.
     *
     * @return string
     */
    public function getEmailAddress(): string
    {
        return $this->email;
    }

    /**
     * Returns the username of the email address.
     *
     * @since 1.1.0
     * @return string|null
     */
    private function getUsername(): ?string
    {
        return explode('@', $this->email)[0] ?? '';
    }

    /**
     * Determines if a gmail account is using the "plus trick".
     *
     * @since 1.1.0
     * @return bool
     */
    public function isGmailWithPlusChar(): bool
    {
        $result = false;
        if (in_array($this->getDomain(), ['gmail.com', 'googlemail.com'])) {
            $result = strpos($this->getUsername(), '+') !== false;
        }

        return $result;
    }

    /**
     * Returns a gmail address without the "plus trick" portion of the email address.
     *
     * @since 1.1.0
     * @return string
     */
    public function getGmailAddressWithoutPlus(): string
    {
        return preg_replace('/^(.+?)(\+.+?)(@.+)/', '$1$3', $this->getEmailAddress());
    }

    /**
     * Returns a gmail address without the "plus trick" portion of the email address and all dots removed.
     *
     * @since 1.1.4
     * @return string
     */
    public function getSanitizedGmailAddress(): string
    {
        $email = new EmailAddress($this->getGmailAddressWithoutPlus());
        return sprintf(
            '%s@%s',
            str_replace('.', '', $email->getUsername()),
            $email->getDomain()
        );
    }
}
