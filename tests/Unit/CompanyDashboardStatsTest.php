<?php

use App\Support\CompanyDashboardStats;

it('flags treatment overdue ahead of open reports', function () {
    $signal = CompanyDashboardStats::signalFor('in_progress', 'operational', [
        'open_safety' => 4,
        'open_quality' => 1,
        'overdue_capas' => 2,
    ]);

    expect($signal)->toBe([
        'key' => 'treatment_overdue',
        'label' => 'Treatment overdue',
    ]);
});

it('flags an operational risk when the department has open safety reports', function () {
    $signal = CompanyDashboardStats::signalFor('identified', 'operational', [
        'open_safety' => 1,
        'open_quality' => 0,
        'overdue_capas' => 3,
    ]);

    expect($signal['key'])->toBe('active_exposure');
});

it('falls back to active exposure when treatment is underway but no capa is overdue', function () {
    $signal = CompanyDashboardStats::signalFor('monitoring', 'operational', [
        'open_safety' => 2,
        'open_quality' => 0,
        'overdue_capas' => 0,
    ]);

    expect($signal['key'])->toBe('active_exposure');
});

it('flags a compliance risk when the department has open quality reports', function () {
    $signal = CompanyDashboardStats::signalFor('assessed', 'compliance', [
        'open_safety' => 0,
        'open_quality' => 2,
        'overdue_capas' => 0,
    ]);

    expect($signal['key'])->toBe('quality_exposure');
});

it('does not flag a risk whose category does not match the open reports', function () {
    expect(CompanyDashboardStats::signalFor('identified', 'financial', [
        'open_safety' => 5,
        'open_quality' => 5,
        'overdue_capas' => 0,
    ]))->toBeNull();
});

it('leaves closed and mitigated risks unmarked', function () {
    $hot = [
        'open_safety' => 9,
        'open_quality' => 9,
        'overdue_capas' => 9,
    ];

    expect(CompanyDashboardStats::signalFor('closed', 'operational', $hot))->toBeNull();
    expect(CompanyDashboardStats::signalFor('mitigated', 'compliance', $hot))->toBeNull();
});
