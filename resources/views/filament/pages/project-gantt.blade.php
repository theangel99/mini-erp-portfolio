<x-filament-panels::page>
    <link href="https://unpkg.com/vis-timeline@7.7.3/styles/vis-timeline-graph2d.min.css" rel="stylesheet" type="text/css" />

    <div class="space-y-4">
        <div id="timeline" style="width:100%; height:600px;"></div>
    </div>

    <script src="https://unpkg.com/vis-timeline@7.7.3/standalone/umd/vis-timeline-graph2d.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof vis === 'undefined') {
                console.error('vis-timeline library not loaded');
                return;
            }

            // Get data from PHP
            const projects = @json($this->getGanttData());
            console.log('Timeline data:', projects);

            // Convert to vis-timeline format
            const items = new vis.DataSet();
            projects.data.forEach(function(project) {
                const startDate = new Date(project.start_date);
                const endDate = new Date(startDate);
                endDate.setDate(endDate.getDate() + project.duration);

                items.add({
                    id: project.id,
                    content: project.text,
                    start: startDate,
                    end: endDate,
                    title: `${project.text}<br>Naročnik: ${project.customer}<br>Faza: ${project.phase}<br>Trajanje: ${project.duration} dni`,
                    className: 'timeline-item-phase-' + project.id,
                    type: 'range'
                });
            });

            // Timeline configuration
            const options = {
                width: '100%',
                height: '600px',
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
                format: {
                    minorLabels: {
                        millisecond:'SSS',
                        second:     's',
                        minute:     'HH:mm',
                        hour:       'HH:mm',
                        weekday:    'ddd D',
                        day:        'D',
                        week:       'w',
                        month:      'MMM',
                        year:       'YYYY'
                    },
                    majorLabels: {
                        millisecond:'HH:mm:ss',
                        second:     'D MMMM HH:mm',
                        minute:     'ddd D MMMM',
                        hour:       'ddd D MMMM',
                        weekday:    'MMMM YYYY',
                        day:        'MMMM YYYY',
                        week:       'MMMM YYYY',
                        month:      'YYYY',
                        year:       ''
                    }
                },
                locale: 'sl',
                locales: {
                    sl: {
                        months: ['Januar', 'Februar', 'Marec', 'April', 'Maj', 'Junij', 'Julij', 'Avgust', 'September', 'Oktober', 'November', 'December'],
                        monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Maj', 'Jun', 'Jul', 'Avg', 'Sep', 'Okt', 'Nov', 'Dec'],
                        days: ['Nedelja', 'Ponedeljek', 'Torek', 'Sreda', 'Četrtek', 'Petek', 'Sobota'],
                        daysShort: ['Ned', 'Pon', 'Tor', 'Sre', 'Čet', 'Pet', 'Sob']
                    }
                }
            };

            // Create Timeline
            const container = document.getElementById('timeline');
            const timeline = new vis.Timeline(container, items, options);

            // Auto-fit to show all items
            timeline.fit();
        });
    </script>

    <style>
        .vis-timeline {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-family: inherit;
        }

        .vis-item {
            border-radius: 6px;
            border: none;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        .vis-item:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transform: translateY(-1px);
        }

        .vis-item.vis-selected {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }

        .vis-time-axis .vis-text {
            color: inherit;
        }

        .vis-labelset .vis-label {
            color: inherit;
        }

        /* Dark mode support */
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

        .dark .vis-labelset .vis-label {
            color: rgb(229 231 235);
            border-color: rgb(55 65 81);
        }

        .dark .vis-foreground .vis-group {
            border-color: rgb(55 65 81);
        }

        /* Current time marker */
        .vis-current-time {
            background-color: #ef4444;
            width: 2px;
        }
    </style>
</x-filament-panels::page>
