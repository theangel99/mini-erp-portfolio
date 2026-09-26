@props(['milestones'])

<div class="relative w-full py-12 px-4">
    {{-- Timeline horizontal line - must be visible! --}}
    <div class="absolute left-4 right-4 bg-gray-400 dark:bg-gray-500" style="top: 58px; height: 2px; z-index: 1;"></div>

    {{-- Milestones container --}}
    <div class="relative flex justify-between items-start gap-4" style="z-index: 2;">
        @foreach($milestones as $index => $milestone)
            <x-timeline.milestone-node
                :milestone="$milestone"
                :index="$index"
                :total="count($milestones)"
            />
        @endforeach
    </div>
</div>
