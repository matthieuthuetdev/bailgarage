<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }

    private function configure(): void
    {
        $this->mailer->isSMTP();
        $this->mailer->Host = 'ssl0.ovh.net';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'info@bailgarage.fr';
        $this->mailer->Password = '$uW+3!d2wQ5B';
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Utilisation de SMTPS pour le port 465
        $this->mailer->Port = 465; // Port sécurisé comme recommandé dans vos informations
        $this->mailer->setFrom('info@bailgarage.fr', 'Bail Garage');
    }

    public function send(string $to, string $subject, string $body): bool
    {
        try {
            $this->mailer->clearAllRecipients();
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->isHTML(true);
            return $this->mailer->send();
        } catch (Exception) {
            return false;
        }
    }
    public function sendTemplate($_to, $templateName, $emailData)
    {
        $mailTemplate = new EmailTemplate();
        $template = $mailTemplate->read($templateName);
        $content = $template["content"];
        foreach ($emailData as $element => $data) {
            $content = str_replace("{{" . $element . "}}", $data,$content);
        }
        $message = "<!DOCTYPE html><html lang='fr-fr'><head><meta charset='UTF-8'><title>Document</title></head><body>" . $content . "</body></html>";
        $this->send($_to, $template["subject"], $message);
    }
}
