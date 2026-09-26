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

    $currentPhase = $getState();
    $currentIndex = array_search($currentPhase, $phases);

    $phaseColors = [
        'editing' => 'bg-blue-500',
        'chief_editor_review' => 'bg-amber-500',
        'multimedia' => 'bg-purple-500',
        'print' => 'bg-pink-500',
        'director_approval' => 'bg-orange-500',
        'sales' => 'bg-green-500',
        'completed' => 'bg-green-700',
    ];
@endphp

<div class="flex items-center gap-3 py-2">
    <div class="flex-1">
        <div class="flex items-center gap-1">
            @foreach($phases as $index => $phase)
                <div class="flex-1 h-2 rounded-full overflow-hidden
                    @if($index <= $currentIndex)
                        {{ $phaseColors[$phase->value] ?? 'bg-gray-300' }}
                    @else
                        bg-gray-200 dark:bg-gray-700
                    @endif
                    @if($index === $currentIndex)
                        ring-2 ring-offset-1 ring-{{ explode('-', $phaseColors[$phase->value] ?? 'gray-300')[1] }}-500
                    @endif
                " title="{{ $phase->getLabel() }}"></div>
            @endforeach
        </div>
    </div>
    <div class="text-xs font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
        {{ $currentPhase->getLabel() }}
    </div>
</div>
