<?php

require __DIR__.'/vendor/autoload.php';

use HeadlessChromium\BrowserFactory;

//$browserFactory = new BrowserFactory('chromium-browser');
$browserFactory = new BrowserFactory('google-chrome');

// starts headless chrome
$browser = $browserFactory->createBrowser();

try {
    // creates a new page and navigate to an URL
    $page = $browser->createPage();
    $page->navigate('https://api-platform.com')->waitForNavigation();

    // get page title
    $pageTitle = $page->evaluate('document.title')->getReturnValue();

    // screenshot - Say "Cheese"! 😄
    $page->screenshot()->saveToFile('./foobar.png');

    // pdf
    $page->pdf(['printBackground' => false])->saveToFile('./foobar.pdf');
} finally {
    // bye
    $browser->close();
}
