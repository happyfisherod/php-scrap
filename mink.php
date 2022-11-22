<?php

use Behat\Mink\Mink,
    Behat\Mink\Session,
    Behat\Mink\Driver\GoutteDriver,
    Behat\Mink\Driver\Goutte\Client as GoutteClient;

require __DIR__.'/vendor/autoload.php';

$startUrl = 'https://api-platform.com';

// init Mink and register sessions
$mink = new Mink(array(
    'goutte1' => new Session(new GoutteDriver(new GoutteClient())),
    'goutte2' => new Session(new GoutteDriver(new GoutteClient())),
));

// set the default session name
$mink->setDefaultSessionName('goutte2');
$mink->getSession()->visit($startUrl);
$mink->getSession()->getPage()->findLink('Download')->click();
echo $mink->getSession()->getPage()->getContent();

// this all is done to make possible mixing sessions
// $mink->getSession('goutte1')->visit($startUrl);
// $mink->getSession('goutte1')->getPage()->findLink('Get started')->click();
// echo $mink->getSession()->getPage()->getContent();
