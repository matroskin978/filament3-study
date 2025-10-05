<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = Order::query()
            ->selectRaw("DAY(created_at) as day, COUNT(*) as orders")
            ->whereMonth('created_at', '=', date('m'))
            ->groupBy('day')
            ->orderBy('day', 'asc')
            ->pluck('orders', 'day');
        return [
            'datasets' => [
                [
                    'label' => 'Orders by month',
                    'data' => $data->values(),
                ],
            ],
            'labels' => $data->keys(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
