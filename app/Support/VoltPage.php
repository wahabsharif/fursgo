<?php

namespace App\Support;

use Illuminate\Container\Container;
use Livewire\Volt\LivewireManager;

class VoltPage
{
    /**
     * Render a Volt component the same way Volt::route() does, without Extra diagnosing Volt names.
     */
    public static function render(string $component): mixed
    {
        $container = Container::getInstance();

        return $container->call([
            $container->make(LivewireManager::class)->new($component),
            '__invoke',
        ]);
    }
}
