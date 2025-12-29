<?php

namespace App\Services;

use App\Exceptions\EmailAlreadyVerifiedException;
use App\Exceptions\ExpiredVerificationException;
use App\Exceptions\InvalidVerificationException;
use App\Mail\VerificationMail;
use App\Services\Interfaces\EmailVerificationServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Support\Facades\Mail;

class EmailVerificationService implements EmailVerificationServiceInterface
{
    private const VERIFICATION_EXPIRES = 15;
    private const VERIFICATION_ENDPOINT = '/api/email/verify-email';

    function __construct(
        protected UserServiceInterface $userService
    ) { }

    public function initiateEmailVerification(string $userId, string $name, string $email): void
    {
        $url = $this->generateVerificationUrl($userId, $email);
        Mail::to($email)->send(new VerificationMail($userId, $url));
    }

    public function initiateEmailVerificationResend(string $userId, string $name, string $email): void
    {
        $user = $this->userService->findById($userId);

        if($user['email_verified_at']){
            throw new EmailAlreadyVerifiedException();
        }

        $url = $this->generateVerificationUrl($userId, $email);
        Mail::to($email)->send(new VerificationMail($userId, $url));
    }

    public function verifyAndConfirmEmail(string $url): void
    {
        $url = $this->createCanonicalUrl($url);

        $userId = $this->getQueryParam($url, 'id');

        if (!$this->isValidSignature($url)) {
            throw new InvalidVerificationException('Invalid verification link');
        }

        if ($this->isExpired($url)) {
            throw new ExpiredVerificationException('Verification link expired');
        }

        $this->userService->update($userId, [
            'email_verified_at' => now()
        ]);
    }

    private function generateVerificationUrl(string $userId, string $email): string
    {
        $parameters = ['id' => $userId, 'email' => $email];
        $baseUrl = url('/') . self::VERIFICATION_ENDPOINT;
        $expiration = now()->addMinutes(self::VERIFICATION_EXPIRES)->getTimestamp();
        $parameters = $parameters + ['expires' => $expiration];

        ksort($parameters);

        $key = config('app.key');
        $signature = hash_hmac('sha256', $baseUrl . '?' . http_build_query($parameters), $key);

        return $baseUrl . '?' . http_build_query($parameters + ['signature' => $signature]);
    }

    private function isValidSignature(string $url): bool
    {
        $parts = explode('&signature=', $url);
        $expectedSignature = hash_hmac('sha256', $parts[0], config('app.key'));

        return hash_equals($expectedSignature, $parts[1]);
    }

    private function isExpired(string $url): bool
    {
        $expires = $this->getQueryParam($url, 'expires');
        $now = now()->getTimestamp();

        return $now >= $expires;
    }

    private function getQueryParam(string $url, string $paramName): string|null
    {
        $queryString = parse_url($url, PHP_URL_QUERY);
        parse_str($queryString, $parameters);

        return $parameters[$paramName] ?? null;
    }

    public function createCanonicalUrl(string $url): string
    {
        $requiredParams = ['id', 'email', 'expires', 'signature'];
        foreach ($requiredParams as $param) {
            if ($this->getQueryParam($url, $param) === null) {
                throw new InvalidVerificationException("Missing required parameter: {$param}");
            }
        }

        $parameters = [
            'id' => $this->getQueryParam($url, 'id'),
            'email' => $this->getQueryParam($url, 'email'),
            'expires' => $this->getQueryParam($url, 'expires'),
        ];
        ksort($parameters);

        $baseUrl = url('/') . self::VERIFICATION_ENDPOINT;
        return $baseUrl . '?' . http_build_query($parameters + ['signature' => $this->getQueryParam($url, 'signature')]);
    }
}
