<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Aktivni projekti
        </x-slot>

        <link href="https://unpkg.com/vis-timeline@7.7.3/styles/vis-timeline-graph2d.min.css" rel="stylesheet" type="text/css" />

        <div class="space-y-4">
            <div id="dashboard-timeline" style="width:100%; height:300px;"></div>
        </div>

        <script src="https://unpkg.com/vis-timeline@7.7.3/standalone/umd/vis-timeline-graph2d.min.js"></script>

        <script>
            (function() {
                console.log('Timeline widget script running');

                // Wait for vis library to load
                function initTimeline() {
                    if (typeof vis === 'undefined') {
                        console.log('Waiting for vis library...');
                        setTimeout(initTimeline, 100);
                        return;
                    }

                    console.log('vis library loaded, initializing timeline');
                    const projects = @json($this->getProjects());
                    console.log('Projects data:', projects);

                const items = new vis.DataSet();
                projects.data.forEach(function(project) {
                    const startDate = new Date(project.start_date);
                    const endDate = new Date(startDate);
                    endDate.setDate(endDate.getDate() + project.duration);

                    const progressPercent = Math.round(project.progress * 100);

                    items.add({
                        id: project.id,
                        content: `${project.text} <span class="text-xs opacity-90">${progressPercent}%</span>`,
                        start: startDate,
                        end: endDate,
                        title: `${project.text}<br>Naročnik: ${project.customer}<br>Faza: ${project.phase}<br>Progress: ${progressPercent}%`,
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
                    orientation: 'top',
                    tooltip: {
                        followMouse: true,
                        overflowMethod: 'cap'
                    },
                    locale: 'sl',
                    locales: {
                        sl: {
                            months: ['Januar', 'Februar', 'Marec', 'April', 'Maj', 'Junij', 'Julij', 'Avgust', 'September', 'Oktober', 'November', 'December'],
                            monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Maj', 'Jun', 'Jul', 'Avg', 'Sep', 'Okt', 'Nov', 'Dec'],
                            days: ['Nedelja', 'Ponedeljek', 'Torek', 'Sreda', 'Četrtek', 'Petek', 'Sobota'],
                            daysShort: ['Ned', 'Pon', 'Tor', 'Sre', 'Čet', 'Pet', 'Sob']
                        }
                    },
                    onSelect: function(properties) {
                        if (properties.items.length > 0) {
                            const projectId = properties.items[0];
                            window.location.href = '/admin/projects/' + projectId;
                        }
                    }
                };

                    const container = document.getElementById('dashboard-timeline');
                    if (!container) {
                        console.error('Timeline container not found');
                        return;
                    }

                    console.log('Creating timeline...');
                    const timeline = new vis.Timeline(container, items, options);
                    timeline.fit();
                    console.log('Timeline created successfully');
                }

                initTimeline();
            })();
        </script>

        <style>
            .vis-timeline {
                border: 1px solid rgb(229 231 235);
                border-radius: 0.5rem;
                font-family: inherit;
            }

            .vis-item {
                border-radius: 6px;
                border-width: 2px;
                border-style: solid;
                color: white;
                font-weight: 500;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                transition: all 0.2s ease;
                cursor: pointer;
            }

            .vis-item:hover {
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                transform: translateY(-1px);
            }

            /* Phase colors */
            .vis-item.phase-editing {
                background: #3b82f6;
                border-color: #2563eb;
            }

            .vis-item.phase-chief_editor_review {
                background: #f59e0b;
                border-color: #d97706;
            }

            .vis-item.phase-multimedia {
                background: #8b5cf6;
                border-color: #7c3aed;
            }

            .vis-item.phase-print {
                background: #ec4899;
                border-color: #db2777;
            }

            .vis-item.phase-director_approval {
                background: #f97316;
                border-color: #ea580c;
            }

            .vis-item.phase-sales {
                background: #10b981;
                border-color: #059669;
            }

            .vis-item.phase-completed {
                background: #059669;
                border-color: #047857;
            }

            /* Progress overlay */
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

            /* Dark mode */
            .dark .vis-timeline {
                background-color: rgb(31 41 55);
                border-color: rgb(55 65 81);
            }

            .dark .vis-panel {
                background-color: rgb(31 41 55);
            }

            .dark .vis-time-axis {
                border-color: rgb(55 65 81);
            }

            .dark .vis-time-axis .vis-grid {
                border-color: rgb(55 65 81);
            }

            .dark .vis-time-axis .vis-text {
                color: rgb(229 231 235);
            }
        </style>
    </x-filament::section>
</x-filament-widgets::widget>
