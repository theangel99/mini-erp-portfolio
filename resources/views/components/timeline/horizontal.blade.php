@props(['milestones'])

<div class="relative min-h-[200px] w-full px-8">
    {{-- Timeline line --}}
    <div class="absolute top-8 left-8 right-8 h-0.5 bg-gray-200 dark:bg-gray-700"></div>

    {{-- Milestones --}}
    <div class="relative flex justify-between items-start gap-2 pb-4">
        @foreach($milestones as $index => $milestone)
            <x-timeline.milestone-node
                :milestone="$milestone"
                :index="$index"
                :total="count($milestones)"
            />
        @endforeach
    </div>
</div>
