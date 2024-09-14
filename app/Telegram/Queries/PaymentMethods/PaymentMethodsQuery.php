<?php

namespace App\Telegram\Queries\GiftCards;

use App\Models\Brand;
use Telegram\Bot\Events\UpdateEvent;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;
use App\Telegram\Queries\AbstractQuery;
use Webpatser\Countries\Countries;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GiftCardsQuery extends AbstractQuery
{
    protected static string $regex = '/^brands_[a-zA-Z0-9]+_[a-zA-Z]{2}$/'; # e.g brands_amazon_us_50
    
    /**
     * The selected brand
     */
    protected Brand $brand;

    protected Countries $country;

    /**
     * @param UpdateEvent $event
     * @return Message
     * @throws TelegramSDKException
     * @throws \JsonException
     */
    public function handle(UpdateEvent $event): Message
    {
        $arr = explode('_', $event->update->callbackQuery->data);
        $brand_slug = $arr[1];
        $country_slug = $arr[2];

        $this->brand = Brand::where('slug', $brand_slug)->firstOrFail();

        return $event->telegram->sendMessage([
            'chat_id' => $event->update->getChat()->id,
            'reply_markup' => $this->buildKeyboard() ?? json_encode([], JSON_THROW_ON_ERROR),
            'text' => $this->brand->name . " Gift Cards 

Please select the country for your gift card:",
            'disable_notification' => true
        ]);
    }

    /**
     * @throws \JsonException
     */
    private function buildKeyboard(): false|string
    {
        $response = Http::withHeaders([
            'x-api-key' => config('nowpayments.api_key')
        ])->get('https://api.nowpayments.io/v1/full-currencies');

        if ($response->failed()) {
            Log::error("Failed to fetch currencies with error " . $response);
        }

        $inline_keyboard = collect([]);

        foreach ($response->json()->currencies as $currency) {
            $inline_keyboard->push([[
                'text' => $currency->name, 
                'callback_data' => 'brands' . '_' . $this->brand->slug . '_' . $this->country_iso . '_' . $currency->code
            ]]);
        }

        
        return json_encode([
            'inline_keyboard' => $inline_keyboard
        ], JSON_THROW_ON_ERROR);
    }
}