@props(['deadline'])

@php
    $isCompleted = $deadline->isCompleted();
    $isOverdue = $deadline->isOverdue();
    $wasLate = $isCompleted && $deadline->wasCompletedOnTime() === false;

    // Determine card styling
    if ($isCompleted) {
        $borderColor = $wasLate
            ? 'border-l-warning-500 dark:border-l-warning-600'
            : 'border-l-success-500 dark:border-l-success-600';
        $bgColor = 'bg-white dark:bg-gray-800';
        $opacity = 'opacity-75';
    } elseif ($isOverdue) {
        $borderColor = 'border-l-danger-500 dark:border-l-danger-600';
        $bgColor = 'bg-danger-50 dark:bg-danger-950/50';
        $opacity = '';
    } else {
        $borderColor = match($deadline->priority) {
            \App\Enums\DeadlinePriority::High => 'border-l-danger-500 dark:border-l-danger-600',
            \App\Enums\DeadlinePriority::Medium => 'border-l-warning-500 dark:border-l-warning-600',
            \App\Enums\DeadlinePriority::Low => 'border-l-primary-500 dark:border-l-primary-600',
        };
        $bgColor = 'bg-white dark:bg-gray-800';
        $opacity = '';
    }

    // Priority badge colors
    $priorityColors = match($deadline->priority) {
        \App\Enums\DeadlinePriority::High => 'bg-danger-100 text-danger-700 dark:bg-danger-900/30 dark:text-danger-400',
        \App\Enums\DeadlinePriority::Medium => 'bg-warning-100 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400',
        \App\Enums\DeadlinePriority::Low => 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400',
    };
@endphp

<div class="relative flex gap-3 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 border-l-4 {{ $borderColor }} {{ $bgColor }} {{ $opacity }} hover:shadow-md transition-shadow">
    {{-- Checkbox/Status indicator --}}
    <div class="flex-shrink-0 mt-0.5">
        @if($isCompleted)
            <div class="w-5 h-5 rounded-full bg-success-500 flex items-center justify-center">
                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </div>
        @else
            <div class="w-5 h-5 rounded-full border-2 {{ $isOverdue ? 'border-danger-500 bg-danger-100 dark:border-danger-600 dark:bg-danger-900/30' : 'border-gray-300 dark:border-gray-600' }}"></div>
        @endif
    </div>

    {{-- Content --}}
    <div class="flex-1 min-w-0">
        <div class="flex items-start justify-between gap-2">
            <div class="flex-1">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 {{ $isCompleted ? 'line-through' : '' }}">
                    {{ $deadline->title }}
                </h4>

                @if($deadline->description)
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                        {{ $deadline->description }}
                    </p>
                @endif

                {{-- Project/Milestone context --}}
                <div class="flex flex-wrap items-center gap-2 mt-2 text-xs text-gray-500 dark:text-gray-400">
                    @if($deadline->project)
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                            {{ $deadline->project->name }}
                        </span>
                    @endif

                    @if($deadline->milestone)
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            {{ $deadline->milestone->title }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Priority badge --}}
            @if(!$isCompleted)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $priorityColors }}">
                    {{ $deadline->priority->getLabel() }}
                </span>
            @endif
        </div>

        {{-- Date info --}}
        <div class="flex items-center gap-3 mt-2 text-xs">
            @if($isCompleted)
                <span class="text-success-600 dark:text-success-400">
                    Opravljeno: {{ $deadline->completed_at->format('d. m. Y') }}
                </span>
                @if($wasLate)
                    <span class="text-warning-600 dark:text-warning-400 font-medium">
                        +{{ $deadline->daysLate() }}d zamude
                    </span>
                @endif
            @else
                <span class="{{ $isOverdue ? 'text-danger-600 dark:text-danger-400 font-medium' : 'text-gray-600 dark:text-gray-400' }}">
                    Rok: {{ $deadline->due_at->format('d. m. Y, H:i') }}
                </span>
                @if($isOverdue)
                    <span class="text-danger-600 dark:text-danger-400 font-medium">
                        {{ abs($deadline->due_at->diffInDays(now())) }}d zamude
                    </span>
                @endif
            @endif
        </div>
    </div>
</div>
