<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Aktivni projekti
        </x-slot>

        <link href="https://unpkg.com/vis-timeline@7.7.3/styles/vis-timeline-graph2d.min.css" rel="stylesheet" type="text/css" />
        <script src="https://unpkg.com/vis-timeline@7.7.3/standalone/umd/vis-timeline-graph2d.min.js"></script>

        <div wire:ignore>
            <div id="dashboard-timeline-{{ $this->getId() }}" style="width:100%; height:300px;"></div>
        </div>

        @script
        <script>
            const containerId = 'dashboard-timeline-{{ $this->getId() }}';
            console.log('Initializing timeline for container:', containerId);

            function initDashboardTimeline() {
                if (typeof vis === 'undefined') {
                    console.log('Waiting for vis library...');
                    setTimeout(initDashboardTimeline, 100);
                    return;
                }

                const container = document.getElementById(containerId);
                if (!container) {
                    console.error('Container not found:', containerId);
                    return;
                }

                const projects = @json($this->getProjects());
                console.log('Projects loaded:', projects);

                const items = new vis.DataSet();
                projects.data.forEach(function(project) {
                    const startDate = new Date(project.start_date);
                    const endDate = new Date(startDate);
                    endDate.setDate(endDate.getDate() + project.duration);
                    const progressPercent = Math.round(project.progress * 100);

                    items.add({
                        id: project.id,
                        content: project.text + ' ' + progressPercent + '%',
                        start: startDate,
                        end: endDate,
                        title: project.text + '\nNaročnik: ' + project.customer + '\nFaza: ' + project.phase + '\nProgress: ' + progressPercent + '%',
                        className: 'phase-' + project.phase_value + ' progress-' + progressPercent,
                        type: 'range'
                    });
                });

                const options = {
                    width: '100%',
                    height: '300px',
                    stack: true,
                    showMajorLabels: true,
                    showMinorLabels: true,
                    zoomable: true,
                    moveable: true,
                    locale: 'sl',
                    locales: {
                        sl: {
                            months: ['Januar', 'Februar', 'Marec', 'April', 'Maj', 'Junij', 'Julij', 'Avgust', 'September', 'Oktober', 'November', 'December'],
                            monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Maj', 'Jun', 'Jul', 'Avg', 'Sep', 'Okt', 'Nov', 'Dec']
                        }
                    },
                    onSelect: function(properties) {
                        if (properties.items.length > 0) {
                            window.location.href = '/admin/projects/' + properties.items[0];
                        }
                    }
                };

                console.log('Creating timeline...');
                const timeline = new vis.Timeline(container, items, options);
                timeline.fit();
                console.log('Timeline created!');
            }

            initDashboardTimeline();
        </script>
        @endscript

        <style>
            .vis-timeline {
                border: 1px solid rgb(229 231 235);
                border-radius: 0.5rem;
            }

            .vis-item {
                border-radius: 6px;
                border-width: 2px;
                border-style: solid;
                color: white;
                font-weight: 500;
                cursor: pointer;
            }

            .vis-item.phase-editing { background: #3b82f6; border-color: #2563eb; }
            .vis-item.phase-chief_editor_review { background: #f59e0b; border-color: #d97706; }
            .vis-item.phase-multimedia { background: #8b5cf6; border-color: #7c3aed; }
            .vis-item.phase-print { background: #ec4899; border-color: #db2777; }
            .vis-item.phase-director_approval { background: #f97316; border-color: #ea580c; }
            .vis-item.phase-sales { background: #10b981; border-color: #059669; }

            .vis-item::after {
                content: '';
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                background: linear-gradient(90deg, transparent 0%, transparent var(--progress, 100%), rgba(255, 255, 255, 0.5) var(--progress, 100%), rgba(255, 255, 255, 0.5) 100%);
                pointer-events: none;
            }

            .vis-item.progress-0::after { --progress: 0%; }
            .vis-item.progress-10::after { --progress: 10%; }
            .vis-item.progress-20::after { --progress: 20%; }
            .vis-item.progress-30::after { --progress: 30%; }
            .vis-item.progress-40::after { --progress: 40%; }
            .vis-item.progress-50::after { --progress: 50%; }
            .vis-item.progress-60::after { --progress: 60%; }
            .vis-item.progress-70::after { --progress: 70%; }
            .vis-item.progress-80::after { --progress: 80%; }
            .vis-item.progress-90::after { --progress: 90%; }
            .vis-item.progress-100::after { --progress: 100%; }
        </style>
    </x-filament::section>
</x-filament-widgets::widget>
