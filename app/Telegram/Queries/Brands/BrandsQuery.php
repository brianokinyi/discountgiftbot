<?php

namespace App\Telegram\Queries\Brands;

use App\Models\Brand;
use Telegram\Bot\Events\UpdateEvent;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;
use App\Telegram\Queries\AbstractQuery;

class BrandsQuery extends AbstractQuery
{
    protected static string $regex = '/^brands$/';

    /**
     * @param UpdateEvent $event
     * @return Message
     * @throws TelegramSDKException
     * @throws \JsonException
     */
    public function handle(UpdateEvent $event): Message
    {
        return $event->telegram->sendMessage([
            'chat_id' => $event->update->getChat()->id,
            'text' => '🛍️ Shop 🛍️

Please select the gift card you want to purchase:

Use the navigation buttons below to browse through different pages of available gift cards.',
            'reply_markup' => $this->buildKeyboard() ?? json_encode([], JSON_THROW_ON_ERROR),
            'disable_notification' => true,
            'reply_parameters' => [
                'chat_id' => $event->update->getChat()->id,
                'allow_sending_without_reply' => true
            ]
        ]);
    }

    /**
     * @throws \JsonException
     */
    private function buildKeyboard(): false|string
    {
        $brands = Brand::orderBy('position', 'ASC')->get();

        $inline_keyboard = collect([]);

        foreach ($brands as $brand) {
            $inline_keyboard->push([['text' => $brand->name, 'callback_data' => 'query_' . $brand->slug]]);
        }

        // Add controls
        $inline_keyboard->push([
            ['text' => '🚫', 'callback_data' => 'query'],
            ['text' => 'Page 1/1', 'callback_data' => 'query'],
            ['text' => 'Next ▶️', 'callback_data' => 'query'],
        ]);

        return json_encode([
            'inline_keyboard' => $inline_keyboard,
        ], JSON_THROW_ON_ERROR);
    }
}