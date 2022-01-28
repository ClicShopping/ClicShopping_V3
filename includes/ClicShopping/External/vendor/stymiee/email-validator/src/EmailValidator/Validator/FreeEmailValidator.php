<?php

declare(strict_types=1);

namespace EmailValidator\Validator;

use EmailValidator\EmailAddress;

class FreeEmailValidator extends AProviderValidator
{
    /**
     * @var array Array of URLs containing a list of free email addresses and the format of that list.
     */
    protected static $providers = [
        [
            'format' => 'txt',
            'url' => 'https://gist.githubusercontent.com/tbrianjones/5992856/raw/93213efb652749e226e69884d6c048e595c1280a/free_email_provider_domains.txt'
        ],
    ];

    /**
     * Checks to see if validating against free email domains is enabled. If so, gets the list of email domains
     * and checks if the domain is one of them.
     *
     * @param EmailAddress $email
     * @return bool
     */
    public function validate(EmailAddress $email): bool
    {
        $valid = true;
        if ($this->policy->checkFreeEmail()) {
            static $freeEmailListProviders;
            if ($freeEmailListProviders === null) {
                $freeEmailListProviders = $this->getList(
                    $this->policy->checkFreeLocalListOnly(),
                    $this->policy->getFreeList()
                );
            }
            $domain = $email->getDomain();
            $valid = !in_array($domain, $freeEmailListProviders, true);
        }
        return $valid;
    }
}
