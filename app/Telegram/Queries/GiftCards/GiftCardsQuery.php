<?php

namespace App\Telegram\Queries\GiftCards;

use App\Models\Brand;
use App\Models\Country;
use App\Models\GiftCard;
use Telegram\Bot\Events\UpdateEvent;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;
use App\Telegram\Queries\AbstractQuery;

class GiftCardsQuery extends AbstractQuery
{
    protected static string $regex = '/^query_[a-zA-Z0-9]+_[a-zA-Z]{2}$/';
    
    /**
     * The selected brand
     */
    protected Brand $brand;

    protected Country $country;

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
        $this->country = Country::where('iso_3166_2', $country_slug)->firstOrFail();

        $text = $this->brand->name . " Gift Cards for " . $this->country->flag . " " . $this->country->iso_3166_2 . "\n";
        $text .= "Select the amount you want to purchase:\n\n";
        $text .= "🔸 Each option shows the value and the discount percentage\n";
        $text .= "🔸 Click on the desired amount to proceed with your purchase";

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
        $gift_cards = GiftCard::where('brand_id', $this->brand->id)->where('country_id', $this->country->id)->with(['denomination'])->get();

        $inline_keyboard = collect([]);

        foreach ($gift_cards as $gift_card) {
            if ($gift_card->in_stock) {
                $price = (100 - $gift_card->denomination->discount)/100 * $gift_card->denomination->denomination;
                $inline_keyboard->push([
                    [
                        'text' => '💳 $' . $gift_card->denomination->denomination . ' Gift Card - ' . $gift_card->denomination->discount . '% OFF | Price: 💰 $' . $price, 
                        'callback_data' => 'getCoins' . '_' . $gift_card->id # E.g giftcard_1
                    ]
                ]);
            } else {
                $inline_keyboard->push([
                    [
                        'text' => '❌ $' . $gift_card->value . ' Gift Card - Out of Stock', 
                        'callback_data' => 'no_action_here'
                    ]
                ]);
            }
        }

        // Add controls
        $inline_keyboard->push([
            ['text' => '🔙 Back', 'callback_data' => 'brands'],
        ]);
    
        return json_encode([
            'inline_keyboard' => $inline_keyboard,
        ], JSON_THROW_ON_ERROR);
    }
}
