<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

class RecaptchaService
{
    public function enabled(): bool
    {
        return (bool) config('services.recaptcha.enabled')
            && filled(config('services.recaptcha.site_key'))
            && filled(config('services.recaptcha.secret_key'));
    }

    public function verify(?string $token, ?string $ipAddress = null): bool
    {
        if (! $this->enabled()) {
            return true;
        }

        if (blank($token)) {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(8)
                ->retry(2, 250)
                ->post(config('services.recaptcha.verify_url'), [
                    'secret' => config('services.recaptcha.secret_key'),
                    'response' => $token,
                    'remoteip' => $ipAddress,
                ]);

            return $response->successful() && $response->boolean('success');
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }
}
