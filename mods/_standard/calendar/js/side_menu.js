/**
 * This javascript is used to display calendar in the side menu.
 */
$(document).ready(function() {
    //get current date
    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();
    $('#mini-calendar').fullCalendar({
        theme: false,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        /* Events are not editable */
        editable: false,     
        eventMouseover: function(event, jsEvent, view) {
            if (view.name !== 'agendaDay') {
                $(jsEvent.target).attr('title', event.title);
            }
        },
        events: path+"mods/_standard/calendar/json-events.php?mini=1"
    }); 
});