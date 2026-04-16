<?php

namespace App\Services;

use DeepL\DeepLClient;
use DeepL\DeepLClientOptions;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class DeepLService
{
    protected ?DeepLClient $client = null;

    public function __construct()
    {
        $authKey = config('deepl.auth_key');

        if (!empty($authKey)) {
            $httpClient = new Client([
                'verify' => 'C:/xampp/php/extras/ssl/cacert.pem',
            ]);

            $this->client = new DeepLClient($authKey, [
                DeepLClientOptions::HTTP_CLIENT => $httpClient,
            ]);
        }
    }

    public function isEnabled(): bool
    {
        return (bool) config('deepl.enabled') && !empty(config('deepl.auth_key'));
    }

    public function isSupportedLocale(string $locale): bool
    {
        return in_array($locale, config('deepl.supported_locales', []), true);
    }

    public function getTargetLanguage(string $locale): ?string
    {
        return config("deepl.target_languages.$locale");
    }

    public function translateText(string $text, string $targetLocale, ?string $sourceLocale = null): ?string
    {
        if (!$this->isEnabled() || !$this->client || !$this->isSupportedLocale($targetLocale)) {
            return null;
        }

        $targetLang = $this->getTargetLanguage($targetLocale);

        if (!$targetLang) {
            return null;
        }

        try {
            $result = $this->client->translateText(
                $text,
                $sourceLocale ? strtoupper($sourceLocale) : null,
                $targetLang
            );

            return $result->text;
        } catch (Exception $e) {
            Log::error('DeepL translation failed', [
                'target_locale' => $targetLocale,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}