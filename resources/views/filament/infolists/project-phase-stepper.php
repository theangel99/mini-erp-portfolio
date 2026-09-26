@php
    use App\Enums\PublishingPhase;

    $phases = [
        PublishingPhase::Editing,
        PublishingPhase::ChiefEditorReview,
        PublishingPhase::Multimedia,
        PublishingPhase::Print,
        PublishingPhase::DirectorApproval,
        PublishingPhase::Sales,
        PublishingPhase::Completed,
    ];

    $currentPhase = $getRecord()->current_phase;
    $currentIndex = array_search($currentPhase, $phases);

    $phaseColors = [
        'editing' => ['bg' => 'bg-blue-500', 'text' => 'text-blue-700', 'icon' => 'heroicon-o-pencil'],
        'chief_editor_review' => ['bg' => 'bg-amber-500', 'text' => 'text-amber-700', 'icon' => 'heroicon-o-clipboard-document-check'],
        'multimedia' => ['bg' => 'bg-purple-500', 'text' => 'text-purple-700', 'icon' => 'heroicon-o-photo'],
        'print' => ['bg' => 'bg-pink-500', 'text' => 'text-pink-700', 'icon' => 'heroicon-o-printer'],
        'director_approval' => ['bg' => 'bg-orange-500', 'text' => 'text-orange-700', 'icon' => 'heroicon-o-shield-check'],
        'sales' => ['bg' => 'bg-green-500', 'text' => 'text-green-700', 'icon' => 'heroicon-o-currency-dollar'],
        'completed' => ['bg' => 'bg-green-700', 'text' => 'text-green-900', 'icon' => 'heroicon-o-check-badge'],
    ];
@endphp

<div class="w-full py-8">
    <div class="relative">
        <!-- Progress line -->
        <div class="absolute top-5 left-0 right-0 h-1 bg-gray-200 dark:bg-gray-700"></div>
        <div class="absolute top-5 left-0 h-1 {{ $phaseColors[$currentPhase->value]['bg'] ?? 'bg-blue-500' }}"
             style="width: {{ ($currentIndex / (count($phases) - 1)) * 100 }}%"></div>

        <!-- Phases -->
        <div class="relative flex justify-between">
            @foreach($phases as $index => $phase)
                @php
                    $isCompleted = $index < $currentIndex;
                    $isCurrent = $index === $currentIndex;
                    $colors = $phaseColors[$phase->value] ?? $phaseColors['editing'];
                @endphp

                <div class="flex flex-col items-center" style="flex: 1;">
                    <!-- Circle -->
                    <div class="relative z-10 flex items-center justify-center w-10 h-10 rounded-full border-4
                        @if($isCompleted)
                            {{ $colors['bg'] }} border-white dark:border-gray-900
                        @elseif($isCurrent)
                            {{ $colors['bg'] }} border-white dark:border-gray-900 ring-4 ring-offset-2 ring-{{ explode('-', $colors['bg'])[1] }}-300
                        @else
                            bg-gray-200 dark:bg-gray-700 border-white dark:border-gray-900
                        @endif
                    ">
                        @if($isCompleted)
                            <x-filament::icon icon="heroicon-o-check" class="w-5 h-5 text-white" />
                        @elseif($isCurrent)
                            <x-filament::icon :icon="$colors['icon']" class="w-5 h-5 text-white" />
                        @else
                            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $index + 1 }}</span>
                        @endif
                    </div>

                    <!-- Label -->
                    <div class="mt-3 text-center max-w-[120px]">
                        <div class="text-sm font-semibold
                            @if($isCurrent)
                                {{ $colors['text'] }} dark:text-gray-200
                            @elseif($isCompleted)
                                text-gray-700 dark:text-gray-300
                            @else
                                text-gray-500 dark:text-gray-500
                            @endif
                        ">
                            {{ $phase->getLabel() }}
                        </div>
                        @if($isCurrent)
                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Trenutno
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
