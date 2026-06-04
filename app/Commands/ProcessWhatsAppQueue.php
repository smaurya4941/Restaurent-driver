<?php

namespace App\Commands;

use App\Services\WhatsAppQueueService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ProcessWhatsAppQueue extends BaseCommand
{
    protected $group = 'Custom';
    protected $name = 'whatsapp:worker';
    protected $description = 'Process queued WhatsApp messages in background-safe batches.';
    protected $usage = 'whatsapp:worker [--limit=50]';

    public function run(array $params)
    {
        $limit = CLI::getOption('limit');
        $result = (new WhatsAppQueueService())->processQueuedMessages($limit === null ? null : (int) $limit);

        CLI::write(
            sprintf(
                'Processed %d queued messages. Sent: %d. Failed: %d.',
                $result['processed'],
                $result['sent'],
                $result['failed']
            ),
            'green'
        );
    }
}
