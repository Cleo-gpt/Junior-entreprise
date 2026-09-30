<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class NotificationService
{
    private array $config;
    private PDO $connection;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/app.php';
        $this->connection = Database::connection();
    }

    public function send(
        string $type,
        string $to,
        string $subject,
        string $body,
        array $context = []
    ): void {
        $driver = $this->config['mail']['driver'] ?? 'log';

        if ($driver === 'log') {
            $this->logEmail(compact('type', 'to', 'subject', 'body', 'context'));
            return;
        }

        if ($driver === 'smtp') {
            $this->sendViaSmtp($to, $subject, $body);
            // We might also want to log sent emails in DB even if sent via SMTP
            $this->logEmailToDb(compact('type', 'to', 'subject', 'body', 'context'));
            return;
        }

        throw new \RuntimeException("Driver mail inconnu : {$driver}");
    }

    private function logEmail(array $payload): void
    {
        // Log to DB
        $this->logEmailToDb($payload);

        // Also keep file logging if useful, or remove it. 
        // The original code created a text file. Let's keep it for file-based logs as backup.

        $logPath = $this->config['storage']['path'] . '/emails';
        if (!is_dir($logPath)) {
            mkdir($logPath, 0775, true);
        }

        $id = uniqid('mail_', true); // Just for filename
        $filename = $logPath . '/' . $id . '.txt';
        $content = sprintf(
            "[%s] %s\nTo: %s\nSubject: %s\n\n%s\n\n--- CONTEXTE ---\n%s\n",
            date(DATE_ATOM),
            strtoupper($payload['type']),
            $payload['to'],
            $payload['subject'],
            $payload['body'],
            json_encode($payload['context'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        file_put_contents($filename, $content);
    }

    private function logEmailToDb(array $payload): void
    {
        $stmt = $this->connection->prepare(
            'INSERT INTO emails (type, to_email, subject, body, context, created_at)
             VALUES (:type, :to, :subject, :body, :context, NOW())'
        );

        $stmt->execute([
            'type' => $payload['type'],
            'to' => $payload['to'],
            'subject' => $payload['subject'],
            'body' => $payload['body'],
            'context' => json_encode($payload['context']),
        ]);
    }

    private function sendViaSmtp(string $to, string $subject, string $body): void
    {
        $smtpConfig = $this->config['mail']['smtp'] ?? [];

        $headers = sprintf(
            "From: %s\r\nContent-Type: text/plain; charset=UTF-8\r\n",
            $this->config['mail']['from_address'] ?? 'no-reply@example.com'
        );

        if (isset($smtpConfig['transport']) && $smtpConfig['transport'] === 'mail') {
            mail($to, $subject, $body, $headers);
            return;
        }

        throw new \RuntimeException('Configuration SMTP incomplète. Utilisez le driver log en attendant.');
    }

    public function buildReservationEmail(string $template, array $data): array
    {
        return match ($template) {
            'reservation_confirmation' => [
                'subject' => 'Confirmation de votre réservation',
                'body' => sprintf(
                    "Bonjour %s,\n\nVotre demande de réservation pour le matériel %s a bien été enregistrée du %s au %s.\n\nMerci.",
                    $data['user_name'] ?? 'membre',
                    $data['material_name'] ?? 'matériel',
                    $data['start_date'] ?? '-',
                    $data['end_date'] ?? '-'
                ),
            ],
            'reservation_reminder' => [
                'subject' => 'Rappel de restitution de matériel',
                'body' => sprintf(
                    "Bonjour %s,\n\nPensez à restituer le matériel %s avant le %s.\n\nMerci de votre vigilance.",
                    $data['user_name'] ?? 'membre',
                    $data['material_name'] ?? 'matériel',
                    $data['end_date'] ?? '-'
                ),
            ],
            default => [
                'subject' => $data['subject'] ?? 'Notification',
                'body' => $data['body'] ?? '',
            ],
        };
    }
}
