import './bootstrap';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';

document.addEventListener('livewire:init', () => {
    Livewire.on('calendarDataUpdated', (data) => {
        initFullCalendar(data);
    });
});

function initFullCalendar(data) {
    const el = document.getElementById('fullcalendar');
    if (!el) return;

    const calendar = new Calendar(el, {
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        initialDate: data.initialDate || new Date().toISOString().slice(0, 10),
        height: 'auto',
        firstDay: 0,
        headerToolbar: false,
        events: data.events || [],
        eventDisplay: 'block',
        displayEventTime: false,
        eventClassNames: (arg) => {
            const status = arg.event.extendedProps.status;
            switch (status) {
                case 'pending': return ['fc-event-pending'];
                case 'confirmed': return ['fc-event-confirmed'];
                case 'active': return ['fc-event-active'];
                default: return [];
            }
        },
        eventDidMount: (info) => {
            info.el.setAttribute('title', info.event.title);
        },
        datesSet: (info) => {
            const year = info.view.currentStart.getFullYear();
            const month = String(info.view.currentStart.getMonth() + 1).padStart(2, '0');
            Livewire.dispatch('calendarMonthChanged', { year, month });
        },
    });

    calendar.render();
    window.calendar = calendar;
}
