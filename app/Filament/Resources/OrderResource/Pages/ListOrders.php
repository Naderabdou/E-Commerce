<?php

namespace App\Filament\Resources\OrderResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Support\Enums\IconPosition;
use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrderStats::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make(__('All'))->icon('heroicon-o-bars-4')->iconPosition(IconPosition::Before),
            'new' => Tab::make(__('New'))->icon('heroicon-o-shopping-cart')->iconPosition(IconPosition::Before)
                ->query(fn ($query) => $query->where('status', 'new')),
            'processing' => Tab::make(__('Processing'))->icon('heroicon-o-presentation-chart-bar')->iconPosition(IconPosition::Before)
                ->query(fn ($query) => $query->where('status', 'processing')),
            'shipped' => Tab::make(__('Shipped'))->icon('heroicon-o-truck')->iconPosition(IconPosition::Before)
                ->query(fn ($query) => $query->where('status', 'shipped')),
            'delivered' => Tab::make(__('Delivered'))->icon('heroicon-o-check-circle')->iconPosition(IconPosition::Before)
                ->query(fn ($query) => $query->where('status', 'delivered')),

        ];
    }
 
}
