<?php

namespace App\Telegram\Commands;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Commands\Command;
use JsonException;

class StartCommand extends Command
{
    protected string $name = 'start';

    protected string $pattern = '{telegram_id}';

    protected string $description = 'Start command to get you started';

    /**
     * @inheritDoc
     */
    public function handle(): void
    {
        // $referrer_id = $this->argument('telegram_id');
        // Create or update this user
        $user = User::updateOrCreate(
            [ 
                'telegram_id' => $this->getUpdate()->getMessage()->from->id 
            ],
            [
                'telegram_id' => $this->getUpdate()->getMessage()->from->id,
                'username' =>  $this->getUpdate()->getMessage()->from->username,
                'first_name' =>  $this->getUpdate()->getMessage()->from->first_name,
            ]
        );

        $text = "✨ Welcome to the Gift Card Bot! ✨ \n";
        $text .= "🎁 Here you can purchase gift cards at amazing discounts! \n\n";
        $text .= "🛍️ How it works:\n";
        $text .= "1. Choose a gift card from the shop.\n";
        $text .= "2. Complete the payment.\n";
        $text .= "3. Receive your gift card instantly! \n\n";
        $text .= "🔽 Use the buttons below to navigate: 🔽\n";
        $text .= "💫 Ready to unlock amazing deals? Let's get started! 🎉";


        $this->replyWithMessage([
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $this->buildKeyboard(),
        ]);
    }

    /**
     * @throws JsonException
     */
    private function buildKeyboard(): false|string
    {
        return json_encode([
            'inline_keyboard' => [
                [
                    ['text' => '🛍️ SHOP', 'callback_data' => 'brands']
                ],
                [
                    ['text' => '❓ FAQ', 'callback_data' => 'faq'],
                    ['text' => '🛒 ORDER HISTORY', 'callback_data' => 'getOrders'],
                ],
                [
                    ['text' => '📢 CHANNEL', 'url' => 'https://t.me/channel'],
                    ['text' => '💬 SUPPORT', 'url' => 'https://t.me/support'],
                ],
                [
                    ['text' => '🔗 REFERRAL SYSTEM', 'callback_data' => 'referrals']
                ],
                [
                    ['text' => '📜 Vouchers', 'url' => 'https://t.me/vouchers']
                ],
            ]
        ], JSON_THROW_ON_ERROR);
    }
}