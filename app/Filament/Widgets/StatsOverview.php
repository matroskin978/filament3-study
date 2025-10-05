<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{

    protected ?string $heading = 'Analytics';

    protected ?string $description = 'An overview of some analytics.';

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::query()->count())
                ->icon('heroicon-o-users')
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('Products', Product::query()->count())
                ->icon('heroicon-o-table-cells'),
            Stat::make('Orders', Order::query()->count()),
        ];
    }
}
