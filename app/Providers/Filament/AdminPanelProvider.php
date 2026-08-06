<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('Accounting')
            ->spa()
            ->colors([
                'primary' => \Filament\Support\Colors\Color::Indigo, // Warna Biru Elegan
                'gray' => \Filament\Support\Colors\Color::Zinc,
                'info' => \Filament\Support\Colors\Color::Sky,
                'success' => \Filament\Support\Colors\Color::Emerald,
                'warning' => \Filament\Support\Colors\Color::Amber,
                'danger' => \Filament\Support\Colors\Color::Rose,
            ])
            ->favicon(asset('favicon.svg'))
            ->maxContentWidth(\Filament\Support\Enums\MaxWidth::Full)
            ->font('Poppins')
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make()
                     ->label('Piutang Usaha'),
                \Filament\Navigation\NavigationGroup::make()
                     ->label('Hutang Usaha'),
                \Filament\Navigation\NavigationGroup::make()
                     ->label('Buku Besar'),
                \Filament\Navigation\NavigationGroup::make()
                     ->label('Master Data'),
                \Filament\Navigation\NavigationGroup::make()
                     ->label('Laporan Keuangan'),
            ])
            ->pages([
                Pages\Dashboard::class,
            ])
            ->login()
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\AccountingStatsWidget::class,
                \App\Filament\Widgets\IncomeExpenseChart::class,
                \App\Filament\Widgets\AccountTypePieChart::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugin(
                \Saade\FilamentFullCalendar\FilamentFullCalendarPlugin::make()
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                fn (): string => \Illuminate\Support\Facades\Blade::render('@include("filament.widgets.panduan-floating")'),
            );
    }
}
