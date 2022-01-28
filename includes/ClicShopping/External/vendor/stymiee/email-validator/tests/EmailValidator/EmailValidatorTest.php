<?php

namespace EmailValidator;

use EmailValidator\Validator\BannedListValidator;
use EmailValidator\Validator\BasicValidator;
use EmailValidator\Validator\DisposableEmailValidator;
use EmailValidator\Validator\FreeEmailValidator;
use EmailValidator\Validator\MxValidator;
use PHPUnit\Framework\TestCase;

class EmailValidatorTest extends TestCase
{
    public function validateDataProvider(): array
    {
        return [
            [EmailValidator::FAIL_BASIC, false, true, true, true, true],
            [EmailValidator::FAIL_MX_RECORD, true, false, true, true, true],
            [EmailValidator::FAIL_BANNED_DOMAIN, true, true, false, true, true],
            [EmailValidator::FAIL_DISPOSABLE_DOMAIN, true, true, true, false, true],
            [EmailValidator::FAIL_FREE_PROVIDER, true, true, true, true, false],
            [EmailValidator::NO_ERROR, true, true, true, true, true],
        ];
    }

    /**
     * @dataProvider validateDataProvider
     * @param int $errCode
     * @param bool $basic
     * @param bool $mx
     * @param bool $banned
     * @param bool $disposable
     * @param bool $free
     */
    public function testValidate(int $errCode, bool $basic, bool $mx, bool $banned, bool $disposable, bool $free): void
    {
        $emailValidator = new EmailValidator();

        $basicValidator = $this->getMockBuilder(BasicValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $basicValidator->method('validate')
            ->willReturn($basic);
        $bValidator = new \ReflectionProperty($emailValidator, 'basicValidator');
        $bValidator->setAccessible(true);
        $bValidator->setValue($emailValidator, $basicValidator);

        $mxValidator = $this->getMockBuilder(MxValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mxValidator->method('validate')
            ->willReturn($mx);
        $mValidator = new \ReflectionProperty($emailValidator, 'mxValidator');
        $mValidator->setAccessible(true);
        $mValidator->setValue($emailValidator, $mxValidator);

        $bannedValidator = $this->getMockBuilder(MxValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $bannedValidator->method('validate')
            ->willReturn($banned);
        $bnValidator = new \ReflectionProperty($emailValidator, 'bannedListValidator');
        $bnValidator->setAccessible(true);
        $bnValidator->setValue($emailValidator, $bannedValidator);

        $disposableValidator = $this->getMockBuilder(MxValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $disposableValidator->method('validate')
            ->willReturn($disposable);
        $dValidator = new \ReflectionProperty($emailValidator, 'disposableEmailValidator');
        $dValidator->setAccessible(true);
        $dValidator->setValue($emailValidator, $disposableValidator);

        $freeValidator = $this->getMockBuilder(MxValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $freeValidator->method('validate')
            ->willReturn($free);
        $fValidator = new \ReflectionProperty($emailValidator, 'freeEmailValidator');
        $fValidator->setAccessible(true);
        $fValidator->setValue($emailValidator, $freeValidator);

        $reason = new \ReflectionProperty($emailValidator, 'reason');
        $reason->setAccessible(true);
        $emailValidator->validate('user@example.com');

        self::assertEquals($errCode, $reason->getValue($emailValidator));
    }

    public function errorReasonDataProvider(): array
    {
        return [
            [EmailValidator::FAIL_BASIC, 'Invalid format'],
            [EmailValidator::FAIL_MX_RECORD, 'Domain does not accept email'],
            [EmailValidator::FAIL_BANNED_DOMAIN, 'Domain is banned'],
            [EmailValidator::FAIL_DISPOSABLE_DOMAIN, 'Domain is used by disposable email providers'],
            [EmailValidator::FAIL_FREE_PROVIDER, 'Domain is used by free email providers'],
            [EmailValidator::NO_ERROR, ''],
        ];
    }

    /**
     * @dataProvider errorReasonDataProvider
     * @param int $errorCode
     * @param string $errorMsg
     */
    public function testGetErrorReason(int $errorCode, string $errorMsg): void
    {
        $emailValidator = new EmailValidator();

        $reason = new \ReflectionProperty(EmailValidator::class, 'reason');
        $reason->setAccessible(true);
        $reason->setValue($emailValidator, $errorCode);

        self::assertEquals($errorMsg, $emailValidator->getErrorReason());
    }
}
