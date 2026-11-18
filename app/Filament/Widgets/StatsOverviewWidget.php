<?php

namespace App\Filament\Widgets;

use App\Models\ContactSubmission;
use App\Models\GalleryImage;
use App\Models\PortfolioMessage;
use App\Models\Project;
use App\Models\Skill;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Projects', Project::count())
                ->description('Published & draft projects')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('primary'),

            Stat::make('Skills', Skill::count())
                ->description('Technical competencies')
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color('success'),

            Stat::make('Messages', PortfolioMessage::count())
                ->description('Guestbook feedback')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('info'),

            Stat::make('Gallery Images', GalleryImage::count())
                ->description('Media showcase')
                ->descriptionIcon('heroicon-m-photo')
                ->color('warning'),

            Stat::make('Contact Submissions', ContactSubmission::count())
                ->description('Visitor inquiries')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('danger'),
        ];
    }
}
