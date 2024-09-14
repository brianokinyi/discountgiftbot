<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;
use JsonException;

class StartCommand extends Command
{
    protected string $name = 'start';

    protected string $description = 'Start command to get you started';

    /**
     * @inheritDoc
     */
    public function handle(): void
    {
        $text = '✨ Welcome to the Gift Card Bot! ✨ 🎁 Here you can purchase gift cards at amazing discounts! 🛍️ How it works: 1. Choose a gift card from the shop. 2. Complete the payment. 3. Receive your gift card instantly! 🔽 Use the buttons below to navigate: 🔽';

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
                    ['text' => '🛍️ SHOP', 'callback_data' => 'products']
                ],
                [
                    ['text' => '❓ FAQ', 'callback_data' => 'random_number'],
                    ['text' => '🛒 ORDER HISTORY', 'callback_data' => 'random_number'],
                ],
                [
                    ['text' => '📢 CHANNEL', 'callback_data' => 'channel'],
                    ['text' => '💬 SUPPORT', 'callback_data' => 'support'],
                ],
                [
                    ['text' => '🔗 REFERRAL SYSTEM', 'callback_data' => 'referrals']
                ],
                [
                    ['text' => '📜 Vouchers', 'callback_data' => 'vouchers']
                ],
            ]
        ], JSON_THROW_ON_ERROR);
    }
}