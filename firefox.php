<?php

use Symfony\Component\Panther\Client;

require __DIR__.'/vendor/autoload.php';

$client = Client::createFirefoxClient();

try {
    $client->request('GET', 'https://api-platform.com');
    $client->clickLink('Get started');

    $crawler = $client->waitFor('#installing-the-framework');

    echo $crawler->filter('#installing-the-framework')->text();
    $client->takeScreenshot('screen.png');
} catch (Exception $e) {
    print "First error: " . $e->getMessage() . "\n";

    try {
        $client->request('GET', 'https://api-platform.com');
        $client->clickLink('Get started');

        $crawler = $client->waitFor('#installing-the-framework');

        echo $crawler->filter('#installing-the-framework')->text();
        $client->takeScreenshot('screen.png');
    } catch (Exception $ex) {
        print "Second error: " . $ex->getMessage() . "\n";
    }
}

print "\nDone.\n";
