<?php

namespace EmailValidator;

use PHPUnit\Framework\TestCase;

class EmailAddressTest extends TestCase
{
    public function domainDataProvider(): array
    {
        return [
            ['user@example.com', 'example.com'],
            ['example.com', null],
        ];
    }

    /**
     * @dataProvider domainDataProvider
     * @param string $emailAddress
     * @param string|null $domain
     */
    public function testGetDomain(string $emailAddress, ?string $domain): void
    {
        $email = new EmailAddress($emailAddress);
        self::assertEquals($email->getDomain(), $domain);
    }

    public function emailDataProvider(): array
    {
        return [
            ['test@johnconde.net'],
            ['test@gmail.com'],
            ['test@hotmail.com'],
            ['test@outlook.com'],
            ['test@yahoo.com'],
            ['test@domain.com'],
            ['test@example.com'],
            ['test@example2.com'],
            ['test@nobugmail.com'],
            ['test@mxfuel.com'],
            ['test@cellurl.com'],
            ['test@10minutemail.com'],
        ];
    }

    /**
     * @dataProvider emailDataProvider
     * @param string $emailAddress
     */
    public function testGetEmailAddress(string $emailAddress): void
    {
        $email = new EmailAddress($emailAddress);
        self::assertEquals($email->getEmailAddress(), $emailAddress);
    }
}
