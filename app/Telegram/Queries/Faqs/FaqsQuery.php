<?php

namespace App\Telegram\Queries\Faqs;

use App\Models\Brand;
use Telegram\Bot\Events\UpdateEvent;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;
use App\Telegram\Queries\AbstractQuery;

class FaqsQuery extends AbstractQuery
{
    protected static string $regex = '/^referrals$/';
    
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
        $text = "💸 Referral System 💸 \n\n";
        $text .= "🎉 Start inviting your friends now and boost your earnings! 🎉\n";


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
                    ['text' => '🔄 Refresh', 'callback_data' => 'referrals']
                ],
                [
                    ['text' => '💵 PAYOUT', 'callback_data' => 'referrals_payout']
                ],
                [
                    ['text' => '🔙 Back', 'callback_data' => '/start'],
                ],
            ]
        ], JSON_THROW_ON_ERROR);
    }
}