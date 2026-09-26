<x-filament-panels::page>
    <link rel="stylesheet" href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css">

    <div class="space-y-4">
        <div id="gantt_here" style="width:100%; height:600px;"></div>
    </div>

    <script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof gantt === 'undefined') {
                console.error('DHTMLX Gantt library not loaded');
                return;
            }

            // Configure Gantt
            gantt.config.date_format = "%Y-%m-%d";
            gantt.config.columns = [
                {name: "text", label: "Projekt", width: 250, tree: true},
                {name: "customer", label: "Naročnik", width: 150},
                {name: "phase", label: "Faza", width: 150},
                {name: "start_date", label: "Začetek", width: 100},
                {name: "duration", label: "Trajanje (dni)", width: 80, align: "center"}
            ];

            gantt.config.scale_unit = "month";
            gantt.config.date_scale = "%F %Y";
            gantt.config.subscales = [
                {unit: "day", step: 1, date: "%j"}
            ];

            // Enable readonly mode for demo
            gantt.config.readonly = true;

            // Auto-fit tasks to view
            gantt.config.fit_tasks = true;
            gantt.config.auto_scheduling = false;
            gantt.config.auto_scheduling_strict = false;

            // Slovenian locale customization
            gantt.locale = {
                date: {
                    month_full: ["Januar", "Februar", "Marec", "April", "Maj", "Junij", "Julij", "Avgust", "September", "Oktober", "November", "December"],
                    month_short: ["Jan", "Feb", "Mar", "Apr", "Maj", "Jun", "Jul", "Avg", "Sep", "Okt", "Nov", "Dec"],
                    day_full: ["Nedelja", "Ponedeljek", "Torek", "Sreda", "Četrtek", "Petek", "Sobota"],
                    day_short: ["Ned", "Pon", "Tor", "Sre", "Čet", "Pet", "Sob"]
                },
                labels: {
                    new_task: "Nova naloga",
                    icon_save: "Shrani",
                    icon_cancel: "Prekliči",
                    icon_details: "Podrobnosti",
                    icon_edit: "Uredi",
                    icon_delete: "Izbriši",
                    confirm_closing: "",
                    confirm_deleting: "Dogodek bo trajno izbrisan. Ali ste prepričani?",
                    section_description: "Opis",
                    section_time: "Časovno obdobje",
                    section_type: "Tip",
                    column_text: "Ime naloge",
                    column_start_date: "Začetek",
                    column_duration: "Trajanje",
                    column_add: "",
                    link: "Povezava",
                    confirm_link_deleting: "bo izbrisan",
                    link_start: "(začetek)",
                    link_end: "(konec)",
                    type_task: "Naloga",
                    type_project: "Projekt",
                    type_milestone: "Mejnik",
                    minutes: "Minute",
                    hours: "Ure",
                    days: "Dnevi",
                    weeks: "Tedni",
                    months: "Meseci",
                    years: "Leta"
                }
            };

            // Initialize Gantt
            gantt.init("gantt_here");

            // Load data from PHP
            const ganttData = @json($this->getGanttData());
            console.log('Gantt data:', ganttData);
            gantt.parse(ganttData);

            // Auto-zoom to show all tasks
            gantt.attachEvent("onGanttReady", function(){
                gantt.autoSchedule();
            });
        });
    </script>

    <style>
        .gantt_task_line.gantt_selected {
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
        }

        .gantt_grid_head_cell,
        .gantt_grid_data .gantt_cell {
            color: inherit;
        }

        /* Dark mode support */
        .dark .gantt_task,
        .dark .gantt_task_line,
        .dark .gantt_line_wrapper {
            background-color: rgb(31 41 55);
        }

        .dark .gantt_grid_scale,
        .dark .gantt_grid_head_cell {
            background-color: rgb(17 24 39);
            color: rgb(229 231 235);
            border-color: rgb(55 65 81);
        }

        .dark .gantt_row,
        .dark .gantt_cell {
            background-color: rgb(31 41 55);
            color: rgb(229 231 235);
            border-color: rgb(55 65 81);
        }

        .dark .gantt_scale_line {
            border-color: rgb(55 65 81);
        }

        .dark .gantt_task_line {
            background-color: rgb(59 130 246);
            border-color: rgb(37 99 235);
        }
    </style>
</x-filament-panels::page>
