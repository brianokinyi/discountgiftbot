<?php

namespace App\Telegram\Queries\Orders;

use App\Models\Coin;
use App\Models\GiftCard;
use App\Models\Order;
use Telegram\Bot\Events\UpdateEvent;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;
use App\Telegram\Queries\AbstractQuery;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class CreateOrderQuery extends AbstractQuery
{
    protected static string $regex = '/^createOrder_[0-9]+_[a-zA-Z]+$/'; # createOrder_1_BTC
    
    /**
     * The selected gift card
     */
    protected GiftCard $gift_card;
    

    protected Coin $coin;

    /**
     * @param UpdateEvent $event
     * @return Message
     * @throws TelegramSDKException
     * @throws \JsonException
     */
    public function handle(UpdateEvent $event): Message
    {
        $arr = explode('_', $event->update->callbackQuery->data);
        $gift_card_id = $arr[1];
        $coin_code = $arr[2];

        $this->gift_card = GiftCard::where('id', $gift_card_id)->firstOrFail();
        $this->coin = Coin::where('code', $coin_code)->firstOrFail();

        $price = (100 - $this->gift_card->denomination->discount)/100 * $this->gift_card->denomination->denomination;

        $uuid =  Uuid::uuid7();

        $order = new Order();
        $order->user_id = 1;
        $order->uuid = $uuid;
        $order->gift_card_id = $this->gift_card->id;
        $order->price = $price;
        $order->save();

        $response = Http::withHeaders([
            'x-api-key' => config('nowpayments.api_key'),
            'Content-Type' => 'application/json'
        ])->post('https://api.nowpayments.io/v1/payment', [
            "price_amount" => $order->price,
            "price_currency" => "usd",
            "pay_currency" => $coin_code,
            "ipn_callback_url" => route('nowpayments.callback'),
            "order_id" => $order->uuid,
            "order_description" => "Purchase order " . $uuid
        ]);

        if ($response->failed()) {
            Log::error("Failed to fetch currencies with error " . $response);
            return false;
        }

        $payment = $response->json();

        $order->payment_id = $payment['payment_id'];
        $order->payment_status = $payment['payment_status'];
        $order->save();

        $text =  $this->gift_card->brand->name . " Gift Card Payment\n\n";
        $text .= "🔹 Order ID: " . $this->gift_card->uuid . "\n";
        $text .= "🔹 Amount: $" . $order->price . "\n";
        $text .= "🔹 Payment Method: " . $this->coin->name . "\n";
        $text .= "🔹 Send exactly: " . $payment['pay_amount'] . "\n";
        $text .= "🔹 To the address: " . $payment['pay_address'] . "\n\n"; 
        $text .= "📜 Details:\n"; 
        $text .= "- Gift Card Value: " . $this->gift_card->denomination->value . "\n"; 
        $text .= "- Discount: " . $this->gift_card->denomination->discount . "\n";
        $text .= "- Final Price: $" . $price . "\n\n";
        $text .= "🔸 Please complete the payment within the next 30 minutes.\n";
        $text .= "🔸 You can copy the address and the amount by simply clicking on it\n";

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