<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

class FirebaseNotificationService
{
    protected static bool $fake = false;
    protected static array $sentMessages = [];

    /**
     * Enable fake mode for testing.
     */
    public static function fake(): void
    {
        static::$fake = true;
        static::$sentMessages = [];
    }

    /**
     * Reset fake mode.
     */
    public static function resetFake(): void
    {
        static::$fake = false;
        static::$sentMessages = [];
    }

    /**
     * Get recorded sent messages in fake mode.
     */
    public static function getSentMessages(): array
    {
        return static::$sentMessages;
    }

    /**
     * Check if Firebase credentials are present and readable.
     */
    public function isConfigured(): bool
    {
        $path = $this->getCredentialsPath();
        return file_exists($path) && is_readable($path);
    }

    /**
     * Get the absolute path to the Firebase service account credentials JSON.
     */
    public function getCredentialsPath(): string
    {
        $configuredPath = env('FIREBASE_CREDENTIALS', 'storage/app/firebase/firebase-service-account.json');
        return base_path($configuredPath);
    }

    /**
     * Send an FCM push notification to a device token.
     *
     * @param string $token Device FCM token
     * @param string $title Notification title
     * @param string $body Notification body
     * @param array $data Additional key-value payload data
     * @return array Result status array
     */
    public function sendToToken(string $token, string $title, string $body, array $data = []): array
    {
        $token = trim($token);
        if (empty($token)) {
            return [
                'success' => false,
                'reason' => 'empty_token',
            ];
        }

        // Stringify all data values for FCM compatibility
        $stringData = array_map(fn($v) => is_scalar($v) ? (string) $v : json_encode($v), $data);

        if (static::$fake) {
            $record = [
                'token' => $token,
                'title' => $title,
                'body' => $body,
                'data' => $stringData,
                'time' => now(),
            ];
            static::$sentMessages[] = $record;

            Log::info("FCM [FAKE] Notification sent to {$token}: {$title}");

            return [
                'success' => true,
                'fake' => true,
                'message_id' => 'fake_msg_' . uniqid(),
            ];
        }

        $credentialsPath = $this->getCredentialsPath();

        if (!file_exists($credentialsPath)) {
            Log::warning("FCM credentials file not found at: {$credentialsPath}. Notification skipped for token: {$token}");
            return [
                'success' => false,
                'reason' => 'credentials_not_found',
            ];
        }

        try {
            $factory = (new Factory())->withServiceAccount($credentialsPath);
            $messaging = $factory->createMessaging();

            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(FcmNotification::create($title, $body))
                ->withData($stringData);

            $result = $messaging->send($message);

            Log::info("FCM Notification sent successfully to {$token}. Message ID: {$result}");

            return [
                'success' => true,
                'message_id' => $result,
            ];
        } catch (\Throwable $e) {
            Log::error("FCM Notification failed to send to {$token}: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
