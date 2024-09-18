<?php

namespace App\Telegram\Queries\Countries;

use App\Models\Brand;
use App\Models\Product;
use Telegram\Bot\Events\UpdateEvent;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;
use App\Telegram\Queries\AbstractQuery;
use Illuminate\Support\Facades\Log;
use Webpatser\Countries\Countries;

class CountriesQuery extends AbstractQuery
{
    protected static string $regex = '/^brands_[a-zA-Z0-9]+$/';
    
    /**
     * The selected brand
     */
    protected Brand $brand;

    /**
     * @param UpdateEvent $event
     * @return Message
     * @throws TelegramSDKException
     * @throws \JsonException
     */
    public function handle(UpdateEvent $event): Message
    {
        $arr = explode('_', $event->update->callbackQuery->data);
        $slug = $arr[1];

        $this->brand = Brand::where('slug', $slug)->with(['giftCards.country'])->firstOrFail();

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
        // TODO: Get countries from DB

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
                    ['text' => '🔙 Back', 'callback_data' => 'brands'],
                ],
            ]
        ], JSON_THROW_ON_ERROR);
    }
}