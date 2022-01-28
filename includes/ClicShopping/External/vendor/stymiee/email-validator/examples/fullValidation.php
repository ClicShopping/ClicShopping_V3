<?php

namespace EmailValidator;

require('../vendor/autoload.php');

$customDisposableEmailList = [
    'example.com',
];

$bannedDomainList = [
    'domain.com',
];

$customFreeEmailList = [
    'example2.com',
];

$testEmailAddresses = [
    'test@domain.com',
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

foreach ($testEmailAddresses as $emailAddress) {
    $emailIsValid = $emailValidator->validate($emailAddress);
    echo  ($emailIsValid) ? 'Email is valid' : $emailValidator->getErrorReason();
    echo PHP_EOL;
}
