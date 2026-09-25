@props(['milestones'])

<div class="relative min-h-[200px] w-full">
    {{-- Timeline line --}}
    <div class="absolute top-8 left-0 right-0 h-0.5 bg-gray-200 dark:bg-gray-700"></div>

    {{-- Milestones --}}
    <div class="relative flex justify-between items-start gap-4 pb-4">
        @foreach($milestones as $index => $milestone)
            <x-timeline.milestone-node
                :milestone="$milestone"
                :index="$index"
                :total="count($milestones)"
            />
        @endforeach
    </div>
</div>
