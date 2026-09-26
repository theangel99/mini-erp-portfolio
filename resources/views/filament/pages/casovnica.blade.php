<x-filament-panels::page>
    {{-- Projects Tab --}}
    @if($activeTab === 'projects')
        @if($selectedProject)
            {{-- Project Selector --}}
            <div class="mb-6">
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="selectedProjectId">
                        <option value="">Izberi projekt...</option>
                        @foreach($this->getProjectsForSelect() as $id => $name)
                            <option value="{{ $id }}" @selected($id == $selectedProjectId)>
                                {{ $name }}
                            </option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            {{-- Project Header --}}
            <div class="mb-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $selectedProject->name }}
                        </h2>
                        @if($selectedProject->customer)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ $selectedProject->customer->name }}
                            </p>
                        @endif
                    </div>
                    <div class="text-right">
                        <x-filament::badge :color="$selectedProject->status->getColor()">
                            {{ $selectedProject->status->getLabel() }}
                        </x-filament::badge>
                    </div>
                </div>

                {{-- Progress info --}}
                @php
                    $total = $selectedProject->milestones->count();
                    $done = $selectedProject->milestones->where('status', \App\Enums\MilestoneStatus::Done)->count();
                    $percentage = $total > 0 ? round(($done / $total) * 100) : 0;
                @endphp

                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">
                            Napredek: {{ $done }} / {{ $total }} korakov
                        </span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ $percentage }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div
                            class="bg-primary-600 h-2 rounded-full transition-all"
                            style="width: {{ $percentage }}%"
                        ></div>
                    </div>
                </div>

                @if($selectedProject->description)
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-4">
                        {{ $selectedProject->description }}
                    </p>
                @endif
            </div>

            {{-- Timeline Visualization --}}
            <div class="mb-6">
                <x-filament::section>
                    <x-slot name="heading">
                        Časovnica projekta
                    </x-slot>

                    <div class="py-8">
                        <x-timeline.horizontal :milestones="$selectedProject->milestones" />
                    </div>
                </x-filament::section>
            </div>

            {{-- Current milestone "What are we waiting for" card --}}
            @php
                $currentMilestone = $selectedProject->currentMilestone();
            @endphp

            @if($currentMilestone && $currentMilestone->status === \App\Enums\MilestoneStatus::Waiting)
                <x-filament::section
                    :heading="'Kaj čakamo?'"
                    :description="'Projekt je trenutno v stanju čakanja'"
                    icon="heroicon-o-clock"
                    icon-color="warning"
                >
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Čakamo na:
                            </span>
                            <span class="text-sm text-gray-900 dark:text-white ml-2">
                                {{ $currentMilestone->waiting_on?->getLabel() ?? 'N/A' }}
                            </span>
                        </div>

                        @if($currentMilestone->waiting_note)
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Opomba:
                                </span>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ $currentMilestone->waiting_note }}
                                </p>
                            </div>
                        @endif

                        @if($currentMilestone->waiting_since)
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Čakamo že:
                                </span>
                                <span class="text-sm text-warning-600 dark:text-warning-400 ml-2 font-semibold">
                                    {{ $currentMilestone->waitingDays() }} dni
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">
                                    (od {{ $currentMilestone->waiting_since->format('d. m. Y') }})
                                </span>
                            </div>
                        @endif
                    </div>
                </x-filament::section>
            @endif
        @else
            <x-filament::section>
                <div class="text-center py-12">
                    <p class="text-gray-500 dark:text-gray-400">
                        Ni aktivnih projektov
                    </p>
                </div>
            </x-filament::section>
        @endif
    @endif

    {{-- Deadlines Tab --}}
    @if($activeTab === 'deadlines')
        {{-- Stats Overview --}}
        @php
            $stats = $this->getDeadlineStats();
        @endphp

        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <x-filament::section class="text-center">
                <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                    {{ $stats['tomorrow'] }}
                </div>
                <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                    Do jutri
                </div>
            </x-filament::section>

            <x-filament::section class="text-center">
                <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                    {{ $stats['this_week'] }}
                </div>
                <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                    Ta teden
                </div>
            </x-filament::section>

            <x-filament::section class="text-center">
                <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">
                    {{ $stats['next_month'] }}
                </div>
                <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                    Naslednji mesec
                </div>
            </x-filament::section>

            <x-filament::section class="text-center">
                <div class="text-2xl font-bold text-danger-600 dark:text-danger-400">
                    {{ $stats['overdue'] }}
                </div>
                <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                    Zapadli
                </div>
            </x-filament::section>

            <x-filament::section class="text-center">
                <div class="text-2xl font-bold text-success-600 dark:text-success-400">
                    {{ $stats['completed'] }}
                </div>
                <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                    Opravljeno
                </div>
            </x-filament::section>
        </div>

        {{-- Deadlines Timeline using Filament Timeline View --}}
        {{ $this->table }}
    @endif
</x-filament-panels::page>
