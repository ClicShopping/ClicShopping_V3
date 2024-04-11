<?php

declare(strict_types=1);

namespace EmailValidator\Validator;

use EmailValidator\EmailAddress;

class DisposableEmailValidator extends AProviderValidator
{
    /**
     * @var array Array of client-provided disposable email providers.
     */
    protected $disposableEmailListProviders = [];

    /**
     * @var array Array of URLs containing a list of disposable email addresses and the format of that list.
     */
    protected static $providers = [
        [
            'format' => 'txt',
            'url' => 'https://raw.githubusercontent.com/martenson/disposable-email-domains/master/disposable_email_blocklist.conf'
        ],
        [
            'format' => 'json',
            'url' => 'https://raw.githubusercontent.com/ivolo/disposable-email-domains/master/wildcard.json'
        ],
    ];

    /**
     * Checks to see if validating against disposable domains is enabled. If so, gets the list of disposable domains
     * and checks if the domain is one of them.
     *
     * @param EmailAddress $email
     * @return bool
     */
    public function validate(EmailAddress $email): bool
    {
        $valid = true;
        if ($this->policy->checkDisposableEmail()) {
            if ($this->disposableEmailListProviders === []) {
                $this->disposableEmailListProviders = $this->getList(
                    $this->policy->checkDisposableLocalListOnly(),
                    $this->policy->getDisposableList()
                );
            }
            $domain = $email->getDomain();
            $valid = !in_array($domain, $this->disposableEmailListProviders, true);
        }
        return $valid;
    }
}
