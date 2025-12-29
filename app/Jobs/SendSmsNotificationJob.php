<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Services\Notification\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Notification $notification
    ) {}

    public function handle(SmsService $smsService): void
    {
        $user = $this->notification->user;

        // Check if user has a phone number
        if (!$user->phone) {
            return;
        }

        $smsService->send(
            recipients: $user->phone,
            message: $this->notification->message
        );
    }
}
