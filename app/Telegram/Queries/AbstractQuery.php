<?php

namespace App\Telegram\Queries;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Events\UpdateEvent;

abstract class AbstractQuery
{
    protected static string $regex;

    public static function match(string $data) {
        $actions = collect(config('telegram.bots.default.queries', []));

        $action = $actions
            ->firstWhere(fn ($action) => preg_match(forward_static_call([$action, 'getRegex']), $data));

        return $action;
    }

    // Method to get the static variable
    public static function getRegex(): string
    {
        return static::$regex;
    }

    // To be implemented by child classes
    abstract public function handle(UpdateEvent $event): mixed;
}