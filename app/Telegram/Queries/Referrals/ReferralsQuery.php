<?php

namespace App\Telegram\Queries\Referrals;

use App\Models\Brand;
use Telegram\Bot\Events\UpdateEvent;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;
use App\Telegram\Queries\AbstractQuery;
use Illuminate\Support\Facades\Log;
use Webpatser\Countries\Countries;

class ReferralsQuery extends AbstractQuery
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
        $text .= "🚀 Invite your friends to use our bot and earn rewards! \n\n";
        $text .= "📈 Earnings Potential: \n";
        $text .= "  - 💰 You earn 20% of what a user pays when they join using your unique link.\n\n";
        $text .= "🔗 Your Referral Link: https://t.me/" . config('telegram.bots.default.username') . "?start=" .  $event->update->getMessage()->from->id  . "\n\n";
        $text .= "👤 Your Details:\n"; 
        $text .= " - 👥 Total Referrals: 0 \n";
        $text .= " - #️⃣ Amount of Sales: 0 \n";
        $text .= " - 🏦 Balance: $0.00 \n\n";
        $text .= "📤 Minimum withdrawal amount is $10.\n\n";
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