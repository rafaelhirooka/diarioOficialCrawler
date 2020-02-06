<?php


require_once __DIR__ . '/vendor/autoload.php';
$bootstrapJson = __DIR__ . '/config/bootstrap.json';
$mailJson = __DIR__ . '/config/email.json';
$bootstrap = json_decode(file_get_contents($bootstrapJson));
$mailConfig = json_decode(file_get_contents($mailJson));

$bootstrap->haystack = str_replace('{urlEncode}', urlencode($bootstrap->needle), $bootstrap->haystack);
$bootstrap->haystack = str_replace('{urlRawEncode}', rawurlencode($bootstrap->needle), $bootstrap->haystack);

$lastModification = isset($bootstrap->lastModification) ? DateTime::createFromFormat('Y-m-d', $bootstrap->lastModification) : new DateTime();

$crawler = new App\Crawler($bootstrap->haystack, new \DOMDocument('1.0'));
$crawler->run();
$crawlerDate = new DateTime();
$crawler->lastModification($crawlerDate, $result);

// If has modification
if ($crawlerDate > $lastModification) {
    // send email with result as att
    try {
        $mail = new \App\Email(new \PHPMailer\PHPMailer\PHPMailer(true));
        $mail->setSubject('Novidades no Diário Oficial')
            ->setBody("
            <p>Temos novidades no Diário Oficial procurando pelos termos \"$bootstrap->needle\".</p>
            <p>Vamos torcer para que seja boa!</p>
            <p>Para acessar é só clicar no link abaixo:</p>
            <a href='$result'>Acesse a página clicando aqui</a>
        ")
            ->buildFromArray((array)$mailConfig)
            ->send();

        // update json with $crawlerDate
        $bootstrap->lastModification = $crawlerDate->format('Y-m-d');
        file_put_contents($bootstrapJson, json_encode($bootstrap));
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
}