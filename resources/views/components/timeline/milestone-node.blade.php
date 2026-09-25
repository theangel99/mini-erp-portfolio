@props(['milestone', 'index', 'total'])

@php
    $isDone = $milestone->status === \App\Enums\MilestoneStatus::Done;
    $isCurrent = in_array($milestone->status, [\App\Enums\MilestoneStatus::InProgress, \App\Enums\MilestoneStatus::Waiting]);
    $isUpcoming = $milestone->status === \App\Enums\MilestoneStatus::Upcoming;
    $isSkipped = $milestone->status === \App\Enums\MilestoneStatus::Skipped;

    // Colors based on status
    if ($isDone) {
        $dotColor = 'bg-success-500 border-success-600';
        $textColor = 'text-success-700 dark:text-success-400';
    } elseif ($isCurrent) {
        $dotColor = $milestone->status === \App\Enums\MilestoneStatus::Waiting
            ? 'bg-warning-500 border-warning-600 animate-pulse'
            : 'bg-primary-500 border-primary-600 animate-pulse';
        $textColor = $milestone->status === \App\Enums\MilestoneStatus::Waiting
            ? 'text-warning-700 dark:text-warning-400'
            : 'text-primary-700 dark:text-primary-400';
    } else {
        $dotColor = 'bg-gray-300 dark:bg-gray-600 border-gray-400 dark:border-gray-500';
        $textColor = 'text-gray-600 dark:text-gray-400';
    }

    $dotSize = $isCurrent ? 'w-5 h-5' : 'w-4 h-4';
@endphp

<div class="flex flex-col items-center flex-1 min-w-0" style="background: rgba(255,0,0,0.1);">
    {{-- Dot --}}
    <div class="relative z-10 flex items-center justify-center {{ $dotSize }} rounded-full {{ $dotColor }} border-2 {{ $isCurrent ? 'shadow-lg' : '' }}" style="background: rgba(0,255,0,0.3);">
        @if($isDone)
            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
        @endif
    </div>

    {{-- Content --}}
    <div class="mt-3 text-center px-2">
        <div class="text-sm font-semibold {{ $textColor }} {{ $isSkipped ? 'line-through' : '' }}">
            {{ $milestone->title }}
        </div>

        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            @if($isDone && $milestone->completed_at)
                {{ $milestone->completed_at->format('d. m. Y') }}
                @if($milestone->completed_at->gt($milestone->planned_at))
                    <span class="text-warning-600">+{{ $milestone->planned_at->diffInDays($milestone->completed_at) }}d</span>
                @endif
            @else
                {{ $milestone->planned_at->format('d. m. Y') }}
            @endif
        </div>

        @if($isCurrent)
            <div class="mt-1">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $milestone->status === \App\Enums\MilestoneStatus::Waiting ? 'bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400' : 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400' }}">
                    {{ $milestone->status->getLabel() }}
                </span>
            </div>
        @endif

        @if($isSkipped)
            <div class="mt-1">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                    Preskočeno
                </span>
            </div>
        @endif
    </div>
</div>
