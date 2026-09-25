@props(['milestone', 'index', 'total'])

@php
    $isDone = $milestone->status === \App\Enums\MilestoneStatus::Done;
    $isCurrent = in_array($milestone->status, [\App\Enums\MilestoneStatus::InProgress, \App\Enums\MilestoneStatus::Waiting]);
    $isSkipped = $milestone->status === \App\Enums\MilestoneStatus::Skipped;
@endphp

<div class="flex flex-col items-center flex-grow min-w-[120px] max-w-[200px]">
    {{-- Dot --}}
    <div class="relative z-10 flex items-center justify-center rounded-full border-2 flex-shrink-0
        {{ $isCurrent ? 'w-5 h-5 shadow-lg' : 'w-4 h-4' }}
        @if($isDone) bg-green-500 border-green-600
        @elseif($isCurrent && $milestone->status === \App\Enums\MilestoneStatus::Waiting) bg-orange-500 border-orange-600 animate-pulse
        @elseif($isCurrent) bg-blue-500 border-blue-600 animate-pulse
        @else bg-gray-300 dark:bg-gray-600 border-gray-400 dark:border-gray-500
        @endif">
        @if($isDone)
            <svg class="w-2.5 h-2.5 text-white flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" style="width: 10px; height: 10px;">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
        @endif
    </div>

    {{-- Content --}}
    <div class="mt-3 text-center px-2 w-full">
        <div class="text-sm font-semibold break-words
            @if($isDone) text-green-700 dark:text-green-400
            @elseif($isCurrent && $milestone->status === \App\Enums\MilestoneStatus::Waiting) text-orange-700 dark:text-orange-400
            @elseif($isCurrent) text-blue-700 dark:text-blue-400
            @else text-gray-600 dark:text-gray-400
            @endif
            {{ $isSkipped ? 'line-through' : '' }}">
            {{ $milestone->title }}
        </div>

        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            @if($isDone && $milestone->completed_at)
                {{ $milestone->completed_at->format('d. m. Y') }}
                @if($milestone->completed_at->gt($milestone->planned_at))
                    <span class="text-orange-600 dark:text-orange-400">+{{ $milestone->planned_at->diffInDays($milestone->completed_at) }}d</span>
                @endif
            @else
                {{ $milestone->planned_at->format('d. m. Y') }}
            @endif
        </div>

        @if($isCurrent)
            <div class="mt-1">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                    @if($milestone->status === \App\Enums\MilestoneStatus::Waiting)
                        bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400
                    @else
                        bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                    @endif">
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
