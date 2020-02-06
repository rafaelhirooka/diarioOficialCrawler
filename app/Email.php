<?php


namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Email
{
    private string $host;
    private int $port;
    private string $security = 'tls';
    private string $username;
    private string $password;
    private string $from;
    private string $to;
    private PHPMailer $mailer;
    private string $body;
    private bool $isHtml = true;
    private string $subject;

    public function __construct(PHPMailer $mailer)
    {
        $this->mailer = $mailer;
    }

    private function build() {
        $this->mailer->isSMTP();
        $this->mailer->Host = $this->host;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $this->username;
        $this->mailer->Password = $this->password;
        $this->mailer->CharSet = PHPMailer::CHARSET_UTF8;
        $this->mailer->SMTPSecure = $this->security;
        $this->mailer->Port = $this->port;
        $this->mailer->setFrom($this->from, 'Diário Oficial - Atualização');
        $this->mailer->addAddress($this->to);
        $this->mailer->isHTML($this->isHtml);
        $this->mailer->Subject = $this->subject;
        $this->mailer->Body = $this->body;
        return $this->mailer;
    }

    public function send() {
        try {
            $this->build()->send();
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function buildFromArray(array $template) {
        $this->setHost($template['host'])
            ->setPort($template['port'])
            ->setSecurity(isset($template['security']) ? $template['security'] : $this->security)
            ->setFrom($template['from'])
            ->setTo($template['to'])
            ->setUsername($template['username'])
            ->setPassword($template['password']);
        return $this;
    }

    public function setHost(string $value)
    {
        $this->host = $value;
        return $this;
    }

    public function setPort(string $value)
    {
        $this->port = $value;
        return $this;
    }

    public function setSecurity(string $value)
    {
        $this->security = $value;
        return $this;
    }

    public function setUsername(string $value)
    {
        $this->username = $value;
        return $this;
    }

    public function setPassword(string $value)
    {
        $this->password = $value;
        return $this;
    }

    public function setFrom(string $value)
    {
        $this->from = $value;
        return $this;
    }

    public function setTo(string $value)
    {
        $this->to = $value;
        return $this;
    }

    public function setBody(string $value)
    {
        $this->body = $value;
        return $this;
    }

    public function setSubject(string $value)
    {
        $this->subject = $value;
        return $this;
    }
}