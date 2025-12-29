<?php

namespace App\Services\Interfaces;


interface EmailVerificationServiceInterface
{
    public function initiateEmailVerification(string $userId, string $name, string $email): void;

    public function initiateEmailVerificationResend(string $userId, string $name, string $email): void;

    public function verifyAndConfirmEmail(string $url): void;

}
