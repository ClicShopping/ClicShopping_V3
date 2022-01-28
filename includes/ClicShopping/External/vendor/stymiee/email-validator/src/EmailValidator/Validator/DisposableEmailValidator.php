<?php

declare(strict_types=1);

namespace EmailValidator\Validator;

use EmailValidator\EmailAddress;

class DisposableEmailValidator extends AProviderValidator
{
    /**
     * @var array Array of URLs containing a list of disposable email addresses and the format of that list.
     */
    protected static $providers = [
        [
            'format' => 'txt',
            'url' => 'https://raw.githubusercontent.com/martenson/disposable-email-domains/master/disposable_email_blocklist.conf'
        ],
        [
            'format' => 'txt',
            'url' => 'https://gist.githubusercontent.com/michenriksen/8710649/raw/e09ee253960ec1ff0add4f92b62616ebbe24ab87/disposable-email-provider-domains'
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
            static $disposableEmailListProviders;
            if ($disposableEmailListProviders === null) {
                $disposableEmailListProviders = $this->getList(
                    $this->policy->checkDisposableLocalListOnly(),
                    $this->policy->getDisposableList()
                );
            }
            $domain = $email->getDomain();
            $valid = !in_array($domain, $disposableEmailListProviders, true);
        }
        return $valid;
    }
}
