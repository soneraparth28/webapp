<?php

use App\Http\Controllers\SMTP\SMTPTrackerController;
use App\Http\Controllers\Webhook\AmazonSesWebhookController;
use App\Http\Controllers\Webhook\MailgunWebhookController;

Route::post('mailgun', [MailgunWebhookController::class, 'update'])
    ->name('webhook.mailgun');

Route::post('{brand_id}/ses', [AmazonSesWebhookController::class, 'update'])
    ->name('webhook.ses');

Route::post('ses', [AmazonSesWebhookController::class, 'update'])
    ->name('webhook.ses_global');


Route::get('smtp/{hook}/{tracker_id}', [SMTPTrackerController::class, 'update'])
    ->name('webhook.smtp');