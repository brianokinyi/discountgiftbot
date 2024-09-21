<?php

namespace App\Telegram\Queries\Coins;

use App\Models\Coin;
use App\Models\GiftCard;
use Telegram\Bot\Events\UpdateEvent;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;
use App\Telegram\Queries\AbstractQuery;

class CoinsQuery extends AbstractQuery
{
    protected static string $regex = '/^getCoins_[0-9]+$/'; # e.g getCoins_amazon_us_1 
    
    /**
     * The selected gift card
     */
    protected GiftCard $gift_card;

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

        $this->gift_card = GiftCard::where('id', $gift_card_id)->firstOrFail();

        $price = (100 - $this->gift_card->denomination->discount)/100 * $this->gift_card->denomination->denomination;

        $text = $this->gift_card->brand->name . " Gift Card for " . $this->gift_card->country->flag . " " . $this->gift_card->country->iso_3166_2 . "\n\n";
        $text .= "🔹 Original Price: " . $this->gift_card->denomination->value . "\n";
        $text .= "🔹 Discount: " . $this->gift_card->denomination->discount . "\n";
        $text .= "🔹 Final Price: " . $price . "\n\n";
        $text .= "💰 Select your payment method:";

        return $event->telegram->sendMessage([
            'chat_id' => $event->update->getChat()->id,
            'reply_markup' => $this->buildKeyboard() ?? json_encode([], JSON_THROW_ON_ERROR),
            'text' => $text,
            'disable_notification' => true
        ]);
    }

    /**
     * @throws \JsonException
     */
    private function buildKeyboard(): false|string
    {
        $coins = Coin::orderBy('priority', 'ASC')->get();

        $inline_keyboard = collect([]);

        foreach (array_chunk($coins->toArray(), 2) as $chunk) {
            $inline_keyboard[] = array_map(function($coin) {
                return  [
                    'text' => $coin['logo'] . ' ' . $coin['name'], 
                    'callback_data' => 'createOrder' . '_' . $this->gift_card->id . '_' . $coin['code']
                ];
            }, $chunk);
        }

        return json_encode([
            'inline_keyboard' => $inline_keyboard
        ], JSON_THROW_ON_ERROR);
    }
}