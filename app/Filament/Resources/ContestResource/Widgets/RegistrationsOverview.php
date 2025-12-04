<?php

namespace App\Filament\Resources\ContestResource\Widgets;

use App\Enums\RegistrationStatuses;
use App\Models\Registration;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class RegistrationsOverview extends BaseWidget
{
    public ?Model $record = null;


    protected function getStats(): array
    {
        return [
            BaseWidget\Stat::make('Total Registration', Registration::where('contest_id', $this->record->id)->count()),
            BaseWidget\Stat::make('Pending Registration', Registration::where('contest_id', $this->record->id)
                ->where('status', RegistrationStatuses::PENDING)->count()),
            BaseWidget\Stat::make('Paid Registration', Registration::where('contest_id', $this->record->id)
                ->where('status', RegistrationStatuses::PAID)->count()),


        ];
    }
}
