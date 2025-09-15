<?php

namespace App\Filament\Resources\RiskResource\Widgets;

use App\Models\Risk;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class RiskStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalRisks = Risk::count();

        if ($totalRisks === 0) {
            return [
                Stat::make('No Risks Found', 'Create a risk to see statistics.')->color('gray'),
            ];
        }

        // 1. Calculate stats for Implementation Status
        $statusCounts = Risk::query()
            ->select('implementation_status', DB::raw('count(*) as count'))
            ->groupBy('implementation_status')
            ->pluck('count', 'implementation_status');

        $openCount = $statusCounts->get('Open', 0);
        $wipCount = $statusCounts->get('Work-in-Progress', 0);
        $closedCount = $statusCounts->get('Closed', 0);

        // 2. Calculate stats for Inherent Risk Levels (based on your 1-15 score)
        $lowInherentCount = Risk::whereBetween('inherent_risk', [1, 4])->count();
        $mediumInherentCount = Risk::whereBetween('inherent_risk', [5, 9])->count();
        $highInherentCount = Risk::where('inherent_risk', '>=', 10)->count();

        // 2. Calculate stats for Residual Risk Levels (based on your 1-15 score)
        $lowResidualCount = Risk::whereBetween('residual_risk', [1, 4])->count();
        $mediumResidualCount = Risk::whereBetween('residual_risk', [5, 9])->count();
        $highResidualCount = Risk::where('residual_risk', '>=', 10)->count();

        return [
            // --- Risk Status Legend ---
            Stat::make('Open Risks', sprintf('%.1f%%', ($openCount / $totalRisks) * 100))
                ->description("{$openCount} of {$totalRisks} risks")
                ->color('warning'),
            Stat::make('Work-in-Progress', sprintf('%.1f%%', ($wipCount / $totalRisks) * 100))
                ->description("{$wipCount} of {$totalRisks} risks")
                ->color('info'),
            Stat::make('Closed Risks', sprintf('%.1f%%', ($closedCount / $totalRisks) * 100))
                ->description("{$closedCount} of {$totalRisks} risks")
                ->color('success'),

            // --- Inherent Risk Level Legend ---
            Stat::make('Low Inherent Risk', sprintf('%.1f%%', ($lowInherentCount / $totalRisks) * 100))
                ->description("{$lowInherentCount} of {$totalRisks} risks")
                ->color('success'),
            Stat::make('Medium Inherent Risk', sprintf('%.1f%%', ($mediumInherentCount / $totalRisks) * 100))
                ->description("{$mediumInherentCount} of {$totalRisks} risks")
                ->color('warning'),
            Stat::make('High Inherent Risk', sprintf('%.1f%%', ($highInherentCount / $totalRisks) * 100))
                ->description("{$highInherentCount} of {$totalRisks} risks")
                ->color('danger'),

            // --- Residual Risk Level Legend ---
            Stat::make('Low Residual Risk', sprintf('%.1f%%', ($lowResidualCount / $totalRisks) * 100))
                ->description("{$lowResidualCount} of {$totalRisks} risks")
                ->color('success'),
            Stat::make('Medium Residual Risk', sprintf('%.1f%%', ($mediumResidualCount / $totalRisks) * 100))
                ->description("{$mediumResidualCount} of {$totalRisks} risks")
                ->color('warning'),
            Stat::make('High Residual Risk', sprintf('%.1f%%', ($highResidualCount / $totalRisks) * 100))
                ->description("{$highResidualCount} of {$totalRisks} risks")
                ->color('danger'),
        ];
    }
}