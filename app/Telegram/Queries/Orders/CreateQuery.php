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
use Ramsey\Uuid\Uuid;

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
            'uuid' => Uuid::uuid7(),
            'gift_card_id' => ''
        ]);

        $response = Http::withHeaders([
            'x-api-key' => config('nowpayments.api_key')
        ])->post('https://api.nowpayments.io/v1/v1/payment', [
            "price_amount" => $order->price,
            "price_currency" => "usd",
            "pay_currency" => "btc",
            "ipn_callback_url" => route('nowpayments.callback'),
            "order_id" => $order->uuid,
            "order_description" => "Apple Macbook Pro 2019 x 1"
        ]);

        if ($response->failed()) {
            Log::error("Failed to fetch currencies with error " . $response);
        }

        $text = "🛒 Amazon Gift Card Payment\n\n" .
                "🔹 Order ID:* \$100\n" .
                "🔹 Amount: $45.00 \n" .
                "🔹 Payment Method: BITCOIN \n\n" .
                "🔹 Send exactly: 0.00075192" .
                "🔹 To the address: " . 
                "📜 Details:" . 
                "- Gift Card Value: $100" . 
                "- Discount: 55%" .
                "- Final Price: $45.00" .
                "🔸 Please complete the payment within the next 30 minutes." .
                "🔸 You can copy the address and the amount by simply clicking on it";

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