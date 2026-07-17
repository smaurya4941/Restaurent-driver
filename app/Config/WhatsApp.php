<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class WhatsApp extends BaseConfig
{
    public string $provider = 'log';
    public string $apiUrl = '';
    public string $apiToken = '';
    public string $instanceId = '';
    public string $defaultCountryCode = '91';
    public string $httpMethod = 'POST';
    public string $tokenLocation = 'query';
    public string $tokenParam = 'access_token';
    public string $instanceParam = 'instance_id';
    public string $recipientParam = 'number';
    public string $messageParam = 'message';
    public string $messageTypeParam = 'type';
    public string $messageType = 'text';
    public string $imageMessageType = 'image';
    public string $mediaUrlParam = 'media_url';
    public string $bodyFormat = 'form';
    public int $workerBatchSize = 50;
    public int $retryDelayMinutes = 15;
    public bool $simulateDelivery = true;

    /**
     * Restaurant/brand name used in automated driver notifications
     * (e.g. the welcome message sent right after a driver is registered).
     */
    public string $restaurantName = 'Hawa Hawai Aeroplane Restaurant';

    /**
     * When true, a welcome WhatsApp message is queued automatically as soon
     * as a driver is registered successfully.
     */
    public bool $sendWelcomeOnRegistration = true;

    /**
     * Optional override for the welcome message body. Leave empty to use the
     * built-in default template. Supports {{tokens}}: driver_name, restaurant,
     * restaurant_full, branch, mobile_number, whatsapp_number, city,
     * vehicle_number, vehicle_type, has_vehicle.
     */
    public string $welcomeMessageTemplate = '';

    public function __construct()
    {
        parent::__construct();

        $this->provider = (string) env('whatsapp.provider', $this->provider);
        $this->apiUrl = (string) env('whatsapp.apiUrl', $this->apiUrl);
        $this->apiToken = (string) env('whatsapp.apiToken', $this->apiToken);
        $this->instanceId = (string) env('whatsapp.instanceId', $this->instanceId);
        $this->defaultCountryCode = (string) env('whatsapp.defaultCountryCode', $this->defaultCountryCode);
        $this->httpMethod = strtoupper((string) env('whatsapp.httpMethod', $this->httpMethod));
        $this->tokenLocation = (string) env('whatsapp.tokenLocation', $this->tokenLocation);
        $this->tokenParam = (string) env('whatsapp.tokenParam', $this->tokenParam);
        $this->instanceParam = (string) env('whatsapp.instanceParam', $this->instanceParam);
        $this->recipientParam = (string) env('whatsapp.recipientParam', $this->recipientParam);
        $this->messageParam = (string) env('whatsapp.messageParam', $this->messageParam);
        $this->messageTypeParam = (string) env('whatsapp.messageTypeParam', $this->messageTypeParam);
        $this->messageType = (string) env('whatsapp.messageType', $this->messageType);
        $this->imageMessageType = (string) env('whatsapp.imageMessageType', $this->imageMessageType);
        $this->mediaUrlParam = (string) env('whatsapp.mediaUrlParam', $this->mediaUrlParam);
        $this->bodyFormat = (string) env('whatsapp.bodyFormat', $this->bodyFormat);
        $this->workerBatchSize = (int) env('whatsapp.workerBatchSize', $this->workerBatchSize);
        $this->retryDelayMinutes = (int) env('whatsapp.retryDelayMinutes', $this->retryDelayMinutes);
        $this->simulateDelivery = filter_var(env('whatsapp.simulateDelivery', $this->simulateDelivery), FILTER_VALIDATE_BOOL);
        $this->restaurantName = (string) env('whatsapp.restaurantName', $this->restaurantName);
        $this->sendWelcomeOnRegistration = filter_var(env('whatsapp.sendWelcomeOnRegistration', $this->sendWelcomeOnRegistration), FILTER_VALIDATE_BOOL);
        $this->welcomeMessageTemplate = (string) env('whatsapp.welcomeMessageTemplate', $this->welcomeMessageTemplate);
    }
}
