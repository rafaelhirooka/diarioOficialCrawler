<?php
require_once __DIR__ . '/vendor/autoload.php';
$bootstrap = json_decode(file_get_contents(__DIR__ . '/config/bootstrap.json'));

$bootstrap->haystack = str_replace('{urlEncode}', urlencode($bootstrap->needle), $bootstrap->haystack);
$bootstrap->haystack = str_replace('{urlRawEncode}', rawurlencode($bootstrap->needle), $bootstrap->haystack);

$lastModification = isset($bootstrap->lastModification) ? DateTime::createFromFormat('Y-m-d', $bootstrap->lastModification) : new DateTime();

$crawler = new App\Crawler($bootstrap->haystack, new \DOMDocument('1.0'));
$crawler->run();
$crawlerDate = new DateTime();
$crawler->lastModification($crawlerDate, $result);
// update json with $crawlerDate
if ($lastModification > $crawlerDate) {
    // send email with result as att
}