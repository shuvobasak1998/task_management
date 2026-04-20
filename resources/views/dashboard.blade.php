@extends('layouts.app')

@section('title', 'Dashboard | TaskFlow Studio')
@section('current_section', 'dashboard')
@section('supports_create_task', 'true')

@section('content')
    <div class="space-y-8">
        <section class="grid gap-6 xl:grid-cols-[1.25fr_0.95fr]">
            <div class="space-y-6">
                <x-ui.page-hero
                    kicker="Dashboard"
                    title="Analytical clarity for the whole workspace."
                    description="Review momentum, spot delivery risk, and understand how the team is moving before you dive into task execution."
                    :tags="['Monthly trends', 'Status mix', 'Delivery signals']"
                >
                </x-ui.page-hero>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($dashboardStats as $stat)
                        <x-ui.metric-card :label="$stat['label']" :value="$stat['value']" :variant="$stat['variant']" />
                    @endforeach
                </div>
            </div>

            <x-analytics.monthly-completion-chart :points="$monthlyCompletionChart" :peak="$monthlyCompletionPeak">
                <x-slot:side>
                    <x-analytics.status-distribution :chart="$distributionChart" :meta="$distributionChartMeta" />
                </x-slot:side>
            </x-analytics.monthly-completion-chart>
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            <x-analytics.task-summary-list
                kicker="Attention"
                title="Overdue tasks"
                :tasks="$overdueTasks"
                variant="alert"
                empty-message="No overdue tasks right now."
            />

            <x-analytics.task-summary-list
                kicker="Upcoming"
                title="Due soon"
                :tasks="$dueSoonTasks"
                variant="accent"
                empty-message="Nothing is approaching its deadline yet."
            />
        </section>
    </div>

    <x-tasks.create-task-modal
        title="Create a task from the dashboard"
        description="Capture ownership, timing, and priority without leaving your analytics view."
        :priorities="$priorities"
        :statuses="$statuses"
        :users="$users"
    />
@endsection
