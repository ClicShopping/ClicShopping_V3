<?php

namespace EmailValidator\Validator;

use EmailValidator\Policy;
use PHPUnit\Framework\TestCase;

class ProviderValidatorTest extends TestCase
{
    public function testGetListLocalOnly(): void
    {
        $domains = ['example.com', 'test.com'];
        $provider = new FreeEmailValidator(new Policy());
        self::assertEquals($domains, $provider->getList(true, $domains));
    }

    public function testGetExternalListJson(): void
    {
        $provider = new FreeEmailValidator(new Policy());
        $reflectionMethod = new \ReflectionMethod($provider, 'getExternalList');
        $reflectionMethod->setAccessible(true);

        $domains = ['example.com', 'test.com'];
        self::assertEquals($domains, $reflectionMethod->invoke($provider, json_encode($domains), 'json'));
    }

    public function testGetExternalListTxt(): void
    {
        $provider = new FreeEmailValidator(new Policy());
        $reflectionMethod = new \ReflectionMethod($provider, 'getExternalList');
        $reflectionMethod->setAccessible(true);

        $domains = ['example.com', 'test.com'];
        self::assertEquals($domains, $reflectionMethod->invoke($provider, implode("\r\n", $domains), 'txt'));
    }
}
