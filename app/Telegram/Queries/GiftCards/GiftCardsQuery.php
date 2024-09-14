<?php

namespace App\Telegram\Queries\GiftCards;

use App\Models\Brand;
use Telegram\Bot\Events\UpdateEvent;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;
use App\Telegram\Queries\AbstractQuery;
use Webpatser\Countries\Countries;

class GiftCardsQuery extends AbstractQuery
{
    protected static string $regex = '/^brands_[a-zA-Z0-9]+_[a-zA-Z]{2}$/';
    
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
        return json_encode([
            'inline_keyboard' => [
                [
                    ['text' => '🇺🇸 USA', 'callback_data' => 'brands' . '_' . $this->brand->slug . '_' . 'us']
                ],
                [
                    ['text' => '🇨🇦 Canada', 'callback_data' => 'brands' . '_' . $this->brand->slug . '_' . 'ca']
                ],
                [
                    ['text' => '🇬🇧 UK', 'callback_data' => 'brands' . '_' . $this->brand->slug . '_' . 'uk']
                ],
                [
                    ['text' => '🇦🇺 Australia', 'callback_data' => 'brands' . '_' . $this->brand->slug . '_' . 'au']
                ],
                [
                    ['text' => '🔙 Back', 'callback_data' => 'brands' . '_' . $this->brand->slug],
                ],
            ]
        ], JSON_THROW_ON_ERROR);
    }
}