@props(['milestones'])

<div class="relative w-full px-8 py-8" style="min-height: 300px;">
    {{-- Timeline line --}}
    <div class="absolute top-8 left-8 right-8 h-0.5 bg-gray-200 dark:bg-gray-700" style="z-index: 0;"></div>

    {{-- Milestones --}}
    <div class="relative flex justify-between gap-2" style="align-items: flex-start;">
        @foreach($milestones as $index => $milestone)
            <x-timeline.milestone-node
                :milestone="$milestone"
                :index="$index"
                :total="count($milestones)"
            />
        @endforeach
    </div>
</div>
