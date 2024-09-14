<?php

namespace App\Telegram\Queries\Products;

use App\Models\Product;
use Telegram\Bot\Events\UpdateEvent;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;
use App\Telegram\Queries\AbstractQuery;

class ProductsQuery extends AbstractQuery
{
    protected static string $regex = '/products/';

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
            'reply_markup' => $this->buildKeyboard() ?? json_encode([], JSON_THROW_ON_ERROR),
            'text' => '🛍️ Shop 🛍️

Please select the gift card you want to purchase:

Use the navigation buttons below to browse through different pages of available gift cards.'
        ]);
    }

    /**
     * @throws \JsonException
     */
    private function buildKeyboard(): false|string
    {
        // $products = Product::all();

        return json_encode([
            'inline_keyboard' => [
                [
                    ['text' => '🛒 Amazon', 'callback_data' => 'products_amazon']
                ],
                [
                    ['text' => '💳 Visa', 'callback_data' => 'products_visa']
                ],
                [
                    ['text' => '🍏 Apple', 'callback_data' => 'products_apple']
                ],
                [
                    ['text' => '☕ Starbucks', 'callback_data' => 'products_starbucks']
                ],
                [
                    ['text' => '🍗 Chick-fil-A', 'callback_data' => 'products_chik-afil-a']
                ],
                [
                    ['text' => '🚫', 'callback_data' => '/start'],
                    ['text' => 'Page 1/2', 'callback_data' => 'products_chik-afil-a'],
                    ['text' => 'Next ▶️', 'callback_data' => '/start'],
                ],
            ]
        ], JSON_THROW_ON_ERROR);
    }
}