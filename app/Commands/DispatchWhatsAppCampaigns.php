<?php

namespace App\Commands;

use App\Services\WhatsAppQueueService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class DispatchWhatsAppCampaigns extends BaseCommand
{
    protected $group = 'Custom';
    protected $name = 'whatsapp:process';
    protected $description = 'Process queued WhatsApp messages.';

    public function run(array $params)
    {
        $result = (new WhatsAppQueueService())->processQueuedMessages();
        CLI::write(
            'Processed ' . $result['processed'] . ' queued WhatsApp message(s). Sent: ' . $result['sent'] . ', failed: ' . $result['failed'] . '.',
            'green'
        );
    }
}
