/**
 * This javascript is used to display public view of calendar.
 */
//For IE
$.ajaxSetup({cache: false});

$(document).ready(function () {
    //Get current date for calculations.            
    var date = new Date();
    var d    = date.getDate();
    var m    = date.getMonth();
    var y    = date.getFullYear();
    
    var activeelem;
    var focusd     = false;
    var viewchangd = false;
    
    var calendar = $('#calendar').fullCalendar({    
        defaultView: "month",            
        loading: function(isLoading, view) {
            if( isLoading )
                $("#loader").show();
            else
                $("#loader").hide();
        },            
        //Do not apply theme
        theme: false,            
        //Header details
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        //Catch the fired event but do not save view
        saveView: function() {
            var viewo = calendar.fullCalendar('getView');
            if (viewchangd) {
                viewchangd = false;
            }
        },
        //Do not allow adding events by selecting cells
        selectable: false,
        selectHelper: false,            
        eventAfterRender: function(evento,elemento,viewo) {
            if (!evento.editable) {
                var childo = elemento.children();
                if (viewo.name == "month") {
                    childo[1].innerHTML += "<div class='fc-unedit-announce'>Uneditable event</div>";
                } else {
                    childo[0].innerHTML += "<div class='fc-unedit-announce'>Uneditable event</div>";
                }
            }
            if (focusd) {
                if (evento.id + "" == $("#ori-name1").val()) {
                    elemento.focus();
                    focusd = false;
                }
            }
        },            
        //Event is resized. So update db. N.A. here
        eventResize: function(event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view) { 
        },
        viewDisplay: function(view) {
            //Add data for screen reader
            viewchangd = true;
            $(".fc-button-firsts").each(
               function() {
                    if ($(this).text().indexOf( 'Previous' ) >= 0) {
                        if (view.name == "month") {
                            $(this).text(calendar_prv_mnth);
                        } else if (view.name == "agendaWeek") {
                            $(this).text(calendar_prv_week);
                        } else {
                            $(this).text(calendar_prv_day);
                        }
                    }
                    if ($(this).text().indexOf( 'Next' ) >= 0) {
                        if (view.name == "month") {
                            $(this).text(calendar_nxt_mnth);
                        } else if (view.name == "agendaWeek") {
                            $(this).text(calendar_nxt_week);
                        } else {
                            $(this).text(calendar_nxt_day);
                        }
                    }
               });                
        },
        //Events are not editable.
        editable: false,
        //Retrieve events from php file.
        events: "mods/_standard/calendar/json-events.php?mid=" + mid + "&pub=1&cid=" + cid            
    });            
});
function refreshevents() {
    //Refresh events as view is changed
    $("#calendar").fullCalendar("refetchEvents");
}