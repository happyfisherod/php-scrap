<?php

use Behat\Mink\Mink,
    Behat\Mink\Session,
    Behat\Mink\Driver\GoutteDriver,
    Behat\Mink\Driver\Goutte\Client as GoutteClient,
	Behat\Mink\Driver\Selenium2Driver;

require __DIR__.'/vendor/autoload.php';

$startUrl = 'https://api-platform.com';

// init Mink and register sessions
$mink = new Mink(array(
    'goutte1' => new Session(new GoutteDriver(new GoutteClient())),
    'goutte2' => new Session(new GoutteDriver(new GoutteClient())),
	'selenium2' => new Session(new Selenium2Driver('firefox')),
));

// set the default session name
$mink->setDefaultSessionName('goutte2');
$mink->getSession()->visit($startUrl);
$mink->getSession()->getPage()->findLink('Download')->click();
//echo $mink->getSession()->getPage()->getContent();

// this all is done to make possible mixing sessions
// $mink->getSession('goutte1')->visit($startUrl);
// $mink->getSession('goutte1')->getPage()->findLink('Get started')->click();
// echo $mink->getSession()->getPage()->getContent();

saveContent($mink, 'minkdownload.html');

saveScreenshot($mink, 'minkscreenshot.png', $startUrl);

function saveContent($mink, $filename) {
	try{
		$contents = $mink->getSession()->getPage()->getContent();
		file_put_contents($filename, $contents);
	}
	catch (\Exception $e) {
		echo $e->getMessage();
	}
}

function saveScreenshot($mink, $imagename, $url) {
	try{
		$mink->getSession('selenium2')->visit($url);
		$imagestring = $mink->getSession('selenium2')->getDriver()->getScreenshot();
		file_put_contents($imagename, $imagestring);
	} catch (\Exception $e) {
		echo $e->getMessage();
	}
}
