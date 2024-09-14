<?php

namespace App\Telegram\Queries\GiftCards;

use App\Models\Brand;
use App\Models\Order;
use Telegram\Bot\Events\UpdateEvent;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;
use App\Telegram\Queries\AbstractQuery;
use Webpatser\Countries\Countries;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GiftCardsQuery extends AbstractQuery
{
    protected static string $regex = '/^brands_[a-zA-Z0-9]+_[a-zA-Z]{2}$/'; # brands_amazon_us_100_btc
    
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

        $this->brand = Brand::where('slug', $brand_slug)->with(['giftCards.country'])->firstOrFail();

        $order = Order::create([
            'gift_card_id' => ''
        ]);

        $response = Http::withHeaders([
            'x-api-key' => config('nowpayments.api_key')
        ])->post('https://api.nowpayments.io/v1/v1/payment', [
            "price_amount" => 3999.5,
            "price_currency" => "usd",
            "pay_currency" => "btc",
            "ipn_callback_url" => route('nowpayments.callback'),
            "order_id" => "RGDBP-21314",
            "order_description" => "Apple Macbook Pro 2019 x 1"
        ]);

        if ($response->failed()) {
            Log::error("Failed to fetch currencies with error " . $response);
        }

        $text = "*$100 Amazon Gift Card!*\n\n" .
                "💎 *Original Price:* \$100\n" .
                "💎 *Discount:* 55%\n" .
                "💎 *Final Price:* \$45.00\n\n" .
                "💰 *Select your payment method:*";

        return $event->telegram->sendMessage([
            'chat_id' => $event->update->getChat()->id,
            'reply_markup' => $this->buildKeyboard() ?? json_encode([], JSON_THROW_ON_ERROR),
            'text' => $text
        ]);
    }
}