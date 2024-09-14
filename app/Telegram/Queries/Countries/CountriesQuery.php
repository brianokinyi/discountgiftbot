<?php

namespace App\Telegram\Queries\Countries;

use App\Models\Product;
use Telegram\Bot\Events\UpdateEvent;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;
use App\Telegram\Queries\AbstractQuery;

class CountriesQuery extends AbstractQuery
{
    protected static string $regex = '/countries/';

    /**
     * @param UpdateEvent $event
     * @return Message
     * @throws TelegramSDKException
     * @throws \JsonException
     */
    public function handle(UpdateEvent $event): Message
    {
        // $product = Product::where('slug')->firstOrFail();

        return $event->telegram->sendMessage([
            'chat_id' => $event->update->getChat()->id,
            'reply_markup' => $this->buildKeyboard() ?? json_encode([], JSON_THROW_ON_ERROR),
            'text' => '🛒 Amazon Gift Cards 

Please select the country for your gift card:'
        ]);
    }

    /**
     * @throws \JsonException
     */
    private function buildKeyboard(): false|string
    {
        // $countries = Country::where();

        return json_encode([
            'inline_keyboard' => [
                [
                    ['text' => '🇺🇸 USA', 'callback_data' => 'countries_amazon_us']
                ],
                [
                    ['text' => '🇨🇦 Canada', 'callback_data' => 'countries_amazon_ca']
                ],
                [
                    ['text' => '🇬🇧 UK', 'callback_data' => 'countries_amazon_uk']
                ],
                [
                    ['text' => '🔙 Back', 'callback_data' => 'products_chik-afil-a'],
                ],
            ]
        ], JSON_THROW_ON_ERROR);
    }
}