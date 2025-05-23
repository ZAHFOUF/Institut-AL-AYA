var calendarEl = document.getElementById('calendar');

function addNewEvent(info) {
    // Prevent weekend
    return false;
}

function getInitialView() {
    if (window.innerWidth >= 768 && window.innerWidth < 1200) {
        return 'timeGridWeek';
    } else if (window.innerWidth <= 768) {
        return 'listMonth';
    } else {
        return 'dayGridMonth';
    }
}

var calendar = new FullCalendar.Calendar(calendarEl, {
    timeZone: 'local',
    editable: true,
    droppable: true,
    selectable: true,
    navLinks: true,
    initialView: getInitialView(),
    themeSystem: 'bootstrap',
    locale: 'fr',
    buttonText: {
        today: 'Aujourd\'hui',
        month: 'Mois',
        week: 'Semaine',
        day: 'Jour',
        list: 'Liste' , 
    },
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
    },
    windowResize: function (view) {
        var newView = getInitialView();
        calendar.changeView(newView);
    },
    eventResize: function(info) {
        var indexOfSelectedEvent = defaultEvents.findIndex(function (x) {
            return x.id == info.event.id
        });
        if (defaultEvents[indexOfSelectedEvent]) {
            defaultEvents[indexOfSelectedEvent].title = info.event.title;
            defaultEvents[indexOfSelectedEvent].start = info.event.start;
            defaultEvents[indexOfSelectedEvent].end = (info.event.end) ? info.event.end : null;
            defaultEvents[indexOfSelectedEvent].allDay = info.event.allDay;
            defaultEvents[indexOfSelectedEvent].className = info.event.classNames[0];
            defaultEvents[indexOfSelectedEvent].description = (info.event._def.extendedProps.description) ? info.event._def.extendedProps.description : '';
            defaultEvents[indexOfSelectedEvent].location = (info.event._def.extendedProps.location) ? info.event._def.extendedProps.location : '';
        }
        upcomingEvent(defaultEvents);
    },
    eventClick: function (info) {
       
        return false ;

    },
    eventClassNames: function (info) {
        // Add 'disabled' class if the event's view property is false
        return !info.event.extendedProps.view ? ['disabled'] : [];
      },
    dateClick: function (info) {
        addNewEvent(info);
    },
    events:  [] ,
    eventReceive: function (info) {
        var newid = parseInt(info.event.id);
        var newEvent = {
            id: newid,
            title: info.event.title,
            start: info.event.start,
            allDay: info.event.allDay,
            className: info.event.classNames[0]
        };
        defaultEvents.push(newEvent);
        upcomingEvent(defaultEvents);
    },
    eventContent : function(info) {

         // Custom event title
      let customTitle = document.createElement('div');
      customTitle.innerHTML = `${info.event.title}`;
      customTitle.style.textWrap = "initial" ;
      return { domNodes: [customTitle] };

    },
    eventDrop: function (info) {
        var indexOfSelectedEvent = defaultEvents.findIndex(function (x) {
            return x.id == info.event.id
        });
        if (defaultEvents[indexOfSelectedEvent]) {
            defaultEvents[indexOfSelectedEvent].title = info.event.title;
            defaultEvents[indexOfSelectedEvent].start = info.event.start;
            defaultEvents[indexOfSelectedEvent].end = (info.event.end) ? info.event.end : null;
            defaultEvents[indexOfSelectedEvent].allDay = info.event.allDay;
            defaultEvents[indexOfSelectedEvent].className = info.event.classNames[0];
            defaultEvents[indexOfSelectedEvent].description = (info.event._def.extendedProps.description) ? info.event._def.extendedProps.description : '';
            defaultEvents[indexOfSelectedEvent].location = (info.event._def.extendedProps.location) ? info.event._def.extendedProps.location : '';
        }
        upcomingEvent(defaultEvents);
    }
});

$.ajax({
    url: '{{path('student_planing')}}',
    method: 'POST',
    dataType: 'json',
    success: function (data) {
        data.forEach(event => {
            calendar.addEvent(event);
        });
        $("#loading2").hide()
        calendar.render();
        const prevButton = document.querySelector('.fc-prev-button');
        const nextButton = document.querySelector(".fc-next-button")
        const todayButton = document.querySelector(".fc-today-button")
        prevButton.title = "Précédent Mois"; 
        nextButton.title = "Prochain mois"
        todayButton.title = "Mois en cours"

    },
    error: function (jqXHR, textStatus, errorThrown) {
        console.log('AJAX request failed:', jqXHR, textStatus, errorThrown);
    }
});