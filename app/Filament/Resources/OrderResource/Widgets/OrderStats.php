<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('New Orders', Order::query()->where('status', 'new')->count())
            ->label(__('New Orders'))->icon('heroicon-o-shopping-cart'),
            Stat::make('Processing Orders', Order::query()->where('status', 'processing')->count())
            ->label(__('Processing Orders'))->icon('heroicon-o-presentation-chart-bar'),
            Stat::make('Shipped Orders', Order::query()->where('status', 'Shipped')->count())
            ->label(__('Shipped Orders'))->icon('heroicon-o-truck'),
            Stat::make('Average Price', Number::currency(Order::query()->avg('grand_total'), 'USD'))
            ->label(__('Average Price'))->icon('heroicon-o-currency-dollar'),


        ];
    }
}
