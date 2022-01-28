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
}
