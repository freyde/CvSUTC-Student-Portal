<?php

namespace App\Services;

<<<<<<< HEAD
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class SendGridMailService
{
    protected $apiKey;
    protected $fromEmail;
    protected $fromName;

    public function __construct()
    {
        $this->apiKey = config('services.sendgrid.api_key', env('SENDGRID_API_KEY'));
        $this->fromEmail = config('mail.from.address', env('MAIL_FROM_ADDRESS', 'hello@example.com'));
        $this->fromName = config('mail.from.name', env('MAIL_FROM_NAME', 'Student Portal'));
    }

    /**
     * Send an email using SendGrid API
     *
     * @param string $to
     * @param string $subject
     * @param string $htmlContent
     * @param string|null $textContent
     * @return bool
     * @throws \Exception
     */
    public function send(string $to, string $subject, string $htmlContent, ?string $textContent = null): bool
    {
        if (!$this->apiKey) {
            throw new \Exception('SendGrid API key is not configured. Please set SENDGRID_API_KEY in your .env file.');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.sendgrid.com/v3/mail/send', [
            'personalizations' => [
                [
                    'to' => [
                        [
                            'email' => $to,
                        ],
                    ],
                ],
            ],
            'from' => [
                'email' => $this->fromEmail,
                'name' => $this->fromName,
            ],
            'subject' => $subject,
            'content' => [
                [
                    'type' => 'text/html',
                    'value' => $htmlContent,
                ],
            ],
        ]);

        if ($response->successful()) {
            Log::info('SendGrid email sent successfully', [
                'to' => $to,
                'subject' => $subject,
            ]);
            return true;
        }

        $errorMessage = $response->json()['errors'][0]['message'] ?? $response->body();
        Log::error('SendGrid email failed', [
            'to' => $to,
            'subject' => $subject,
            'status' => $response->status(),
            'error' => $errorMessage,
        ]);

        throw new \Exception('Failed to send email via SendGrid: ' . $errorMessage);
    }

    /**
     * Send email using a Blade view
     *
     * @param string $to
     * @param string $subject
     * @param string $view
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function sendView(string $to, string $subject, string $view, array $data = []): bool
    {
        $htmlContent = View::make($view, $data)->render();
        return $this->send($to, $subject, $htmlContent);
=======
use SendGrid;
use SendGrid\Mail\Mail;

class SendGridMailService
{
    public function send($to, $subject, $content)
    {
        $email = new Mail();
        $email->setFrom(
            config('mail.from.address'),
            config('mail.from.name')
        );
        $email->setSubject($subject);
        $email->addTo($to);
        $email->addContent('text/plain', $content);

        $sendgrid = new SendGrid(env('SENDGRID_API_KEY'));

        return $sendgrid->send($email);
>>>>>>> 062e7d695d72fa4af7e7681d5ca27f484c0649ce
    }
}

