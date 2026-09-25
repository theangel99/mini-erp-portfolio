@props(['milestones'])

<div class="relative w-full py-12">
    {{-- Timeline horizontal line --}}
    <div class="absolute left-0 right-0 h-0.5 bg-gray-300 dark:bg-gray-600" style="top: 18px;"></div>

    {{-- Milestones container --}}
    <div class="relative flex justify-between items-start">
        @foreach($milestones as $index => $milestone)
            <x-timeline.milestone-node
                :milestone="$milestone"
                :index="$index"
                :total="count($milestones)"
            />
        @endforeach
    </div>
</div>
