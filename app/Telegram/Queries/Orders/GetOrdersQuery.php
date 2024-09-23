<?php

namespace App\Telegram\Queries\Orders;

use Telegram\Bot\Events\UpdateEvent;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;
use App\Telegram\Queries\AbstractQuery;

class GetOrdersQuery extends AbstractQuery
{
    protected static string $regex = '/^getOrders$/'; # brands_amazon_us_100_btc

    /**
     * @param UpdateEvent $event
     * @return Message
     * @throws TelegramSDKException
     * @throws \JsonException
     */
    public function handle(UpdateEvent $event): Message
    {
        $text = "You have no completed orders!";

        return $event->telegram->sendMessage([
            'chat_id' => $event->update->getChat()->id,
            'reply_markup' => $this->buildKeyboard() ?? json_encode([], JSON_THROW_ON_ERROR),
            'text' => $text
        ]);
    }

     /**
     * @throws \JsonException
     */
    private function buildKeyboard(): false|string
    {
        return json_encode([
            'inline_keyboard' => [
                [
                    ['text' => '🔙 Back', 'callback_data' => 'brands'],
                ]
            ]
        ], JSON_THROW_ON_ERROR);
    }
}