<?php

namespace App\Services;

use App\Libraries\WhatsApp\LogWhatsAppProvider;
use App\Libraries\WhatsApp\RestWhatsAppProvider;
use App\Libraries\WhatsApp\WhatsAppProviderInterface;
use Config\Services;
use Config\WhatsApp;
use RuntimeException;

class WhatsAppProviderFactory
{
    public function make(): WhatsAppProviderInterface
    {
        $config = config(WhatsApp::class);

        return match ($config->provider) {
            'log' => new LogWhatsAppProvider(Services::logger()),
            'rest' => new RestWhatsAppProvider($config, Services::curlrequest()),
            default => throw new RuntimeException('Unsupported WhatsApp provider: ' . $config->provider),
        };
    }
}
