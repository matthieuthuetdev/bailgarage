<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Service d’envoi d’emails, utilisant PHPMailer.
 */
class MailService
{
    private PHPMailer $mailer;

    /**
     * MailService constructor.
     *
     * Initialise PHPMailer et configure le transport SMTP.
     *
     * @throws Exception Si la configuration PHPMailer échoue
     */
    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }

    /**
     * Configure PHPMailer avec les paramètres SMTP.
     * Les informations sensibles (login, mot de passe) sont lues depuis l’environnement.
     */
    private function configure(): void
    {
        $this->mailer->isSMTP();
        $this->mailer->Host = getenv('MAILER_HOST') ?: 'ssl0.ovh.net';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = getenv('MAILER_USERNAME') ?: 'info@bailgarage.fr';
        $this->mailer->Password = getenv('MAILER_PASSWORD') ?: '';
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL sur port 465
        $this->mailer->Port = (int)(getenv('MAILER_PORT') ?: 465);
        $this->mailer->setFrom(getenv('MAILER_FROM_ADDRESS') ?: 'info@bailgarage.fr', getenv('MAILER_FROM_NAME') ?: 'Bail Garage');

        // Optionnel : configurer les options SSL/TLS selon cas serveur
        $this->mailer->SMTPOptions = [
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => false
            ]
        ];
    }

    /**
     * Envoie un email simple.
     *
     * @param string $to      Destinataire
     * @param string $subject Sujet
     * @param string $body    Contenu HTML
     * @param string|null $altBody Contenu alternatif en texte brut (facultatif)
     *
     * @return bool True si l’envoi réussit, false sinon
     */
    public function send(string $to, string $subject, string $body, ?string $altBody = null): bool
    {
        try {
            $this->mailer->clearAllRecipients();
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->isHTML(true);

            if ($altBody !== null) {
                $this->mailer->AltBody = $altBody;
            } else {
                // Si pas d’AltBody donné, extraire une version texte simple
                $this->mailer->AltBody = strip_tags($body);
            }

            return (bool)$this->mailer->send();
        } catch (Exception $e) {
            // Log de l’erreur pour debug
            error_log("MailService::send error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoie un email basé sur un template.
     *
     * @param string $to            Destinataire
     * @param string $templateName  Nom du template
     * @param array  $emailData     Données à injecter dans le template (clé => valeur)
     *
     * @return bool True si l’envoi réussit, false sinon
     */
    public function sendTemplate(string $to, string $templateName, array $emailData): bool
    {
        // Lecture du template
        $mailTemplate = new EmailTemplate();
        $template = $mailTemplate->read($templateName);

        if (!isset($template['content'], $template['subject'])) {
            error_log("MailService::sendTemplate: template \"$templateName\" invalide");
            return false;
        }

        $content = $template['content'];
        foreach ($emailData as $key => $value) {
            // On pourrait valider / échapper $value si besoin
            $content = str_replace("{{" . $key . "}}", $value, $content);
        }

        // Construire le corps HTML complet
        $messageHtml = "<!DOCTYPE html><html lang='fr-FR'><head><meta charset='UTF-8'><title>{$template['subject']}</title></head><body>{$content}</body></html>";

        // Envoi avec version texte alternative
        $altMessage = strip_tags($content);

        return $this->send($to, $template['subject'], $messageHtml, $altMessage);
    }
}
