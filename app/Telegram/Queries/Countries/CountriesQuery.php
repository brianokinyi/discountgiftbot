<?php

namespace App\Telegram\Queries\Countries;

use App\Models\Brand;
use App\Models\Country;
use Telegram\Bot\Events\UpdateEvent;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;
use App\Telegram\Queries\AbstractQuery;

class CountriesQuery extends AbstractQuery
{
    protected static string $regex = '/^query_[a-zA-Z0-9]+$/';
    
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
        $countries = Country::orderBy('position', 'ASC')->get();

        $inline_keyboard = collect([]);

        foreach ($countries as $country) {
            $inline_keyboard->push([['text' => $country->flag . ' ' . $country->iso_3166_2, 'callback_data' => 'query_' . $this->brand->slug . '_' . $country->iso_3166_2]]);
        }

        // Add controls
        $inline_keyboard->push([
            ['text' => '🔙 Back', 'callback_data' => 'brands']
        ]);

        return json_encode([
            'inline_keyboard' => $inline_keyboard,
        ], JSON_THROW_ON_ERROR);
    }
}