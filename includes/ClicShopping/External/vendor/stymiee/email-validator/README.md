[![Latest Stable Version](https://poser.pugx.org/stymiee/email-validator/v/stable.svg)](https://packagist.org/packages/stymiee/email-validator)
[![Total Downloads](https://poser.pugx.org/stymiee/email-validator/downloads)](https://packagist.org/packages/stymiee/email-validator)
![Build](https://github.com/stymiee/email-validator/workflows/Build/badge.svg)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/stymiee/email-validator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/stymiee/email-validator/?branch=master)
[![Maintainability](https://api.codeclimate.com/v1/badges/3b45bd94f090378ac5c6/maintainability)](https://codeclimate.com/github/stymiee/email-validator/maintainability)
[![License](https://poser.pugx.org/stymiee/email-validator/license)](https://packagist.org/packages/stymiee/email-validator)

# PHP Email Validator (email-validator)

The PHP Email Validator will validate an email address for all or some of the following conditions:

- is in a valid format
- has configured MX records (optional)
- is not a disposable email address (optional)
- is not a free email account (optional)
- is not a banned email domain (optional)

The Email Validator is configurable, so you have full control over how much validation will occur.

## Requirements

- PHP 7.2 or newer

## Installation

Simply add a dependency on `stymiee/email-validator` to your project's `composer.json` file if you use
[Composer](https://getcomposer.org/) to manage the dependencies of your project.

Here is a minimal example of a `composer.json` file that just defines a dependency on PHP Simple Encryption:

    {
        "require": {
            "stymiee/email-validator": "^1"
        }
    }
    
## Functional Description

The Email Validator library builds upon PHP's built in `filter_var($emailAddress, FILTER_VALIDATE_EMAIL);` by adding a 
default MX record check. It also offers additional validation against disposable email addresses, free email address 
providers, and a custom banned domain list.

### Validate MX 

If `checkMxRecords` is set to `true` in the configuration (see below) the domain name will be validated to ensure it 
exists and has MX records configured. If the domain does not exist or no MX records exist the odds are the email address
is not in use.

### Restrict Disposable Email Addresses

Many users who are abusing a system, or not using that system as intended, can use a disposable email service who 
provides a short-lived (approximately 10 minutes) email address to be used for registrations or user confirmations. If
`checkDisposableEmail` is set to `true` in the configuration (see below) the domain name will be validated to ensure 
it is not associated with a disposable email address provider. 

You can add you own domains to this list if you find the public list providers do not have one you
have identified in their lists. Examples are provided in the `examples` directory which demonstrate how to do this.

### Restrict Free Email Address Providers

Many users who are abusing a system, or not using that system as intended, can use a free email service who 
provides a free email address which is immediately available to be used for registrations or user confirmations. If
`checkFreeEmail` is set to `true` in the configuration (see below) the domain name will be validated to ensure 
it is not associated with a free email address provider. 

You can add you own domains to this list if you find the public list providers do not have one you
have identified in their lists. Examples are provided in the `examples` directory which demonstrate how to do this.

### Restrict Banned Domains

If you have users from a domain abusing your system, or you have business rules that require the blocking of certain 
domains (i.e. public email providers like Gmail or Yahoo mail), you can block then by setting `checkBannedListedEmail` 
to `true` in the configuration (see below) and providing an array of banned domains. Examples are provided in the 
`examples` directory which demonstrate how to do this.

### Configuration

To configure the Email Validator you can pass an array with the follow parameters/values:

#### checkMxRecords

A boolean value that enables/disables MX record validation. Enabled by default.

#### checkBannedListedEmail

A boolean value that enables/disables banned domain validation. Disabled by default.

#### checkDisposableEmail

A boolean value that enables/disables disposable email address validation. Disabled by default.

#### checkFreeEmail

A boolean value that enables/disables free email address provider validation. Disabled by default.

#### localDisposableOnly

A boolean value that when set to `true` will not retrieve third party disposable email provider lists. Use this if you 
cache the list of providers locally which is useful when performance matters. Disabled by default.

#### LocalFreeOnly

A boolean value that when set to `true` will not retrieve third party free email provider lists. Use this if you 
cache the list of providers locally which is useful when performance matters. Disabled by default.

#### bannedList

An array of domains that are not allowed to be used for email addresses.

#### disposableList

An array of domains that are suspected disposable email address providers.

#### freeList

An array of domains that are free email address providers.

**Example**

    $config = [
        'checkMxRecords' => true,
        'checkBannedListedEmail' => true,
        'checkDisposableEmail' => true,
        'checkFreeEmail' => true,
        'bannedList' => $bannedDomainList,
        'disposableList' => $customDisposableEmailList,
        'freeList' => $customFreeEmailList,
    ];
    $emailValidator = new EmailValidator($config);

### Example

    <?php
    use EmailValidator\EmailValidator;
    
    require('../vendor/autoload.php');
    
    $customDisposableEmailList = [
        'example.com',
    ];
    $customFreeEmailList = [
        'example2.com',
    ];
    $bannedDomainList = [
        'domain.com',
    ];
    $testEmailAddresses = [
        'test@johnconde.net',
        'test@gmail.com',
        'test@hotmail.com',
        'test@outlook.com',
        'test@yahoo.com',
        'test@domain.com',
        'test@mxfuel.com',
        'test@example.com',
        'test@example2.com',
        'test@nobugmail.com',
        'test@cellurl.com',
        'test@10minutemail.com',
    ];
    
    $config = [
        'checkMxRecords' => true,
        'checkBannedListedEmail' => true,
        'checkDisposableEmail' => true,
        'checkFreeEmail' => true,
        'bannedList' => $bannedDomainList,
        'disposableList' => $customDisposableEmailList,
        'freeList' => $customFreeEmailList,
    ];
    $emailValidator = new EmailValidator($config);
    
    foreach($testEmailAddresses as $emailAddress) {
        $emailIsValid = $emailValidator->validate($emailAddress);
        echo ($emailIsValid) ? 'Email is valid' : $emailValidator->getErrorReason();
        echo PHP_EOL;
    }
    
**Output**

    Email is valid
    Domain is used by free email providers
    Domain is used by free email providers
    Domain is used by free email providers
    Domain is used by free email providers
    Domain is banned
    Domain is used by disposable email providers
    Domain is used by free email providers
    Domain is used by disposable email providers
    Domain does not accept email
    Domain is used by disposable email providers
    Domain is used by disposable email providers
 
## Notes

The email address is checked against a list of known disposable email address providers which are aggregated from
public disposable email address provider lists. This requires making HTTP requests to get the lists when validating 
the address.

## Support

If you require assistance using this library start by viewing the [HELP.md](HELP.md) file included in this package. It 
includes common problems and solutions as well hwo to ask for additional assistance.
