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
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\FileUpload\InputFile;


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
        $price = number_format($price, 2);

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

        $qr_code_text = strtolower($this->coin->name) . ":" . $payment['pay_address'] . "?amount=" . $order->price;

        Log::info($qr_code_text);

        // Generate the QR code image
        $qrCode = QrCode::create($qr_code_text)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->setSize(300)
            ->setMargin(20)   
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        $filename = $order->uuid . '.png';
        $path = config('filesystems.disks.public.root');
        $result->saveToFile($path . '/' . $filename);

        $photo = InputFile::create($path . '/' . $filename, $filename);

        $text =  $this->gift_card->brand->name . " Gift Card Payment\n\n";
        $text .= "🔹 Order ID: " . $this->gift_card->uuid . "\n";
        $text .= "🔹 Amount: $" . $order->price . "\n";
        $text .= "🔹 Payment Method: " . $this->coin->name . "\n";
        $text .= "🔹 Send exactly: " . $payment['pay_amount'] . "\n";
        $text .= "🔹 To the address: " . $payment['pay_address'] . "\n\n"; 
        $text .= "📜 Details:\n"; 
        $text .= "- Gift Card Value: " . $this->gift_card->denomination->value . "\n"; 
        $text .= "- Discount: " . $this->gift_card->denomination->discount . "%\n";
        $text .= "- Final Price: $" . $order->price . "\n\n";
        $text .= "🔸 Please complete the payment within the next 30 minutes.\n";
        $text .= "🔸 You can copy the address and the amount by simply clicking on it\n";

        return $event->telegram->sendPhoto([
            'chat_id' => $event->update->getChat()->id,
            'photo' => $photo,
            'reply_markup' => $this->buildKeyboard() ?? json_encode([], JSON_THROW_ON_ERROR),
            'caption' => $text
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
                    ['text' => '🔙 Back', 'callback_data' => 'getCoins' . '_' . $this->gift_card->id],
                ]
            ]
        ], JSON_THROW_ON_ERROR);
    }
}