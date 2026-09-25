@props(['title', 'deadlines', 'color' => 'gray', 'icon' => null])

@php
    // Color variants based on group type
    $colorClasses = [
        'danger' => [
            'border' => 'border-danger-200 dark:border-danger-800',
            'bg' => 'bg-danger-50 dark:bg-danger-950',
            'text' => 'text-danger-700 dark:text-danger-400',
            'icon' => 'text-danger-500 dark:text-danger-400',
        ],
        'warning' => [
            'border' => 'border-warning-200 dark:border-warning-800',
            'bg' => 'bg-warning-50 dark:bg-warning-950',
            'text' => 'text-warning-700 dark:text-warning-400',
            'icon' => 'text-warning-500 dark:text-warning-400',
        ],
        'primary' => [
            'border' => 'border-primary-200 dark:border-primary-800',
            'bg' => 'bg-primary-50 dark:bg-primary-950',
            'text' => 'text-primary-700 dark:text-primary-400',
            'icon' => 'text-primary-500 dark:text-primary-400',
        ],
        'success' => [
            'border' => 'border-success-200 dark:border-success-800',
            'bg' => 'bg-success-50 dark:bg-success-950',
            'text' => 'text-success-700 dark:text-success-400',
            'icon' => 'text-success-500 dark:text-success-400',
        ],
        'gray' => [
            'border' => 'border-gray-200 dark:border-gray-700',
            'bg' => 'bg-gray-50 dark:bg-gray-900',
            'text' => 'text-gray-700 dark:text-gray-300',
            'icon' => 'text-gray-500 dark:text-gray-400',
        ],
    ];

    $colors = $colorClasses[$color] ?? $colorClasses['gray'];
@endphp

<div class="mb-6">
    {{-- Group header --}}
    <div class="flex items-center gap-2 mb-3 px-2">
        @if($icon)
            <div class="{{ $colors['icon'] }}">
                @svg($icon, 'w-5 h-5')
            </div>
        @endif
        <h3 class="text-sm font-semibold {{ $colors['text'] }}">
            {{ $title }}
        </h3>
        <span class="text-xs {{ $colors['text'] }} opacity-70">
            ({{ count($deadlines) }})
        </span>
    </div>

    {{-- Deadline items --}}
    <div class="space-y-2">
        @forelse($deadlines as $deadline)
            <x-timeline.deadline-item :deadline="$deadline" />
        @empty
            <div class="px-4 py-3 rounded-lg {{ $colors['border'] }} border {{ $colors['bg'] }}">
                <p class="text-sm {{ $colors['text'] }} opacity-70">
                    Ni rokov v tej skupini
                </p>
            </div>
        @endforelse
    </div>
</div>
