<?php
/**
 * SKY WhatsApp API - PHP Integration Class
 * 
 * Hii ni class ya PHP ya kuintegrate na SKY WhatsApp API.
 * Copy hii file kwenye project yako na uitumie.
 * 
 * @author SKY WhatsApp API
 * @version 1.0
 */

class SkyWhatsAppAPI
{
    private string $apiUrl;
    private string $apiKey;
    private int $instanceId;
    private int $timeout = 30;
    private ?string $lastError = null;
    private ?array $lastResponse = null;

    /**
     * Initialize API client
     * 
     * @param string $apiUrl Base URL ya API (e.g., https://orange.ericksky.online/api/v1)
     * @param string $apiKey API Key yako
     * @param int $instanceId Instance ID yako (unapata kwenye dashboard)
     */
    public function __construct(string $apiUrl, string $apiKey, int $instanceId)
    {
        // Remove trailing slash
        $this->apiUrl = rtrim($apiUrl, '/');
        $this->apiKey = $apiKey;
        $this->instanceId = $instanceId;
    }

    /**
     * Send WhatsApp Message
     * 
     * @param string $to Phone number (with country code, e.g., 255712345678)
     * @param string $body Message content
     * @param array $metadata Optional metadata
     * @return array Response array with 'success', 'data', or 'error'
     */
    public function sendMessage(string $to, string $body, array $metadata = []): array
    {
        // Format phone number (remove + and spaces)
        $to = preg_replace('/[^0-9]/', '', $to);
        
        // If starts with 0, assume Tanzania and add 255
        if (str_starts_with($to, '0')) {
            $to = '255' . substr($to, 1);
        }

        $data = [
            'instance_id' => $this->instanceId,
            'to' => $to,
            'body' => $body,
        ];

        if (!empty($metadata)) {
            $data['metadata'] = $metadata;
        }

        return $this->makeRequest('POST', '/messages/send', $data);
    }

    /**
     * Get Message History
     * 
     * @param int $perPage Number of messages per page
     * @param int|null $instanceId Filter by instance (optional)
     * @return array Response array
     */
    public function getMessages(int $perPage = 50, ?int $instanceId = null): array
    {
        $params = ['per_page' => $perPage];
        
        if ($instanceId) {
            $params['instance_id'] = $instanceId;
        }

        return $this->makeRequest('GET', '/messages', $params);
    }

    /**
     * Get Single Message
     * 
     * @param int $messageId Message ID
     * @return array Response array
     */
    public function getMessage(int $messageId): array
    {
        return $this->makeRequest('GET', "/messages/{$messageId}");
    }

    /**
     * Get All Instances
     * 
     * @return array Response array
     */
    public function getInstances(): array
    {
        return $this->makeRequest('GET', '/instances');
    }

    /**
     * Get Single Instance
     * 
     * @param int $instanceId Instance ID
     * @return array Response array
     */
    public function getInstance(int $instanceId): array
    {
        return $this->makeRequest('GET', "/instances/{$instanceId}");
    }

    /**
     * Get API Usage Statistics
     * 
     * @return array Response array
     */
    public function getUsage(): array
    {
        return $this->makeRequest('GET', '/usage');
    }

    /**
     * Create a Webhook
     * 
     * @param string $url Webhook URL (e.g., https://your-site.com/webhook)
     * @param array $events Events to subscribe to (e.g., ['message.inbound', 'message.status'])
     * @return array Response array
     */
    public function createWebhook(string $url, array $events = ['message.inbound', 'message.status']): array
    {
        return $this->makeRequest('POST', '/webhooks', [
            'url' => $url,
            'events' => $events,
            'instance_id' => $this->instanceId
        ]);
    }

    /**
     * Get All Webhooks
     * 
     * @return array Response array
     */
    public function getWebhooks(): array
    {
        return $this->makeRequest('GET', '/webhooks');
    }

    /**
     * Delete a Webhook
     * 
     * @param int $webhookId Webhook ID
     * @return array Response array
     */
    public function deleteWebhook(int $webhookId): array
    {
        return $this->makeRequest('DELETE', "/webhooks/{$webhookId}");
    }

    /**
     * Get Last Error
     * 
     * @return string|null Last error message
     */
    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * Get Last Raw Response
     * 
     * @return array|null Last response
     */
    public function getLastResponse(): ?array
    {
        return $this->lastResponse;
    }

    /**
     * Set Timeout
     * 
     * @param int $seconds Timeout in seconds
     * @return self
     */
    public function setTimeout(int $seconds): self
    {
        $this->timeout = $seconds;
        return $this;
    }

    /**
     * Make HTTP Request to API
     * 
     * @param string $method HTTP method (GET, POST, PUT, DELETE)
     * @param string $endpoint API endpoint
     * @param array $data Request data
     * @return array Response array
     */
    private function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        $this->lastError = null;
        $this->lastResponse = null;

        $url = $this->apiUrl . $endpoint;

        // For GET requests, add data as query string
        if ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
        }

        // Initialize cURL
        $ch = curl_init();

        // IMPORTANT HEADERS - Hizi ndizo zinazosababisha 404 ikiwa hazipo!
        $headers = [
            'Authorization: Bearer ' . $this->apiKey,  // API Key
            'Content-Type: application/json',           // MUHIMU!
            'Accept: application/json',                 // MUHIMU SANA! Bila hii, utapata 404
            'X-Requested-With: XMLHttpRequest',         // Optional lakini husaidia
        ];

        // cURL Options
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => true,            // SSL verification
            CURLOPT_FOLLOWLOCATION => true,             // Follow redirects
        ]);

        // Set method and data
        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'PUT':
            case 'PATCH':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        // Execute request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // Handle cURL errors
        if ($error) {
            $this->lastError = "cURL Error: {$error}";
            return [
                'success' => false,
                'error' => [
                    'code' => 'CURL_ERROR',
                    'message' => $error,
                ],
                'http_code' => 0,
            ];
        }

        // Parse response
        $decoded = json_decode($response, true);
        $this->lastResponse = $decoded;

        // Handle HTTP errors
        if ($httpCode >= 400) {
            $errorMessage = $this->parseErrorMessage($decoded, $httpCode);
            $this->lastError = $errorMessage;

            return [
                'success' => false,
                'error' => $decoded['error'] ?? [
                    'code' => 'HTTP_ERROR',
                    'message' => $errorMessage,
                ],
                'http_code' => $httpCode,
            ];
        }

        return $decoded ?? ['success' => true, 'http_code' => $httpCode];
    }

    /**
     * Parse Error Message from Response
     */
    private function parseErrorMessage(?array $response, int $httpCode): string
    {
        if (isset($response['error']['message'])) {
            return $response['error']['message'];
        }

        if (isset($response['message'])) {
            return $response['message'];
        }

        return match ($httpCode) {
            400 => 'Bad Request - Data uliyotuma si sahihi',
            401 => 'Unauthorized - API Key si sahihi au imeisha muda',
            403 => 'Forbidden - Huna ruhusa ya kufanya hii action',
            404 => 'Not Found - Endpoint au resource haipatikani. Check URL na Instance ID',
            422 => 'Validation Error - Data validation imefail',
            429 => 'Too Many Requests - Umezidi limit, jaribu tena baadae',
            500 => 'Server Error - Tatizo la server, jaribu tena',
            default => "HTTP Error {$httpCode}",
        };
    }
}
