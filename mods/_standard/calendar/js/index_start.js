/**
 * This javascript is used to display main view of calendar.
 */
//For IE
$.ajaxSetup({cache: false});

function changeview(name, year, month, datem) {
    //Save view in session object
    $.ajax({url:"mods/_standard/calendar/change_view.php?viewn=" +
            name + "&year=" + year + "&month=" + month + "&date=" + datem});
}

$(document).ready(function () {
    // Get current date for calculations.
    var date       = new Date();
    var d          = date.getDate();
    var m          = date.getMonth();
    var y          = date.getFullYear();               
    var focusd     = false;
    var viewchangd = false;        
    var gmtHours   = -date.getTimezoneOffset() / 60;
    var activeelem;
    //Get client's timezone and save it for future reference
    $("#export").attr("href", "mods/_standard/calendar/export.php?hrs="+gmtHours);
    
    var calendar = $('#calendar').fullCalendar({        
        defaultView: view_name,
        loading: function (isLoading, view) {
            //If data is loading then show loader otherwise hide it
            if (isLoading) {
                $("#loader").show();
            } else {
                $("#loader").hide();
            }
        },            
        //Do not apply theme
        theme: false,            
        //Header details
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        //Save current state of the calendar after view is changed
        saveView: function() {
            var viewo = calendar.fullCalendar('getView');
            if (viewchangd) {
                changeview(viewo.name, viewo.start.getFullYear(), 
                           viewo.start.getMonth(), viewo.start.getDate() );
                viewchangd = false;
            }
        },
        //Allow adding events by selecting cells.
        selectable: true,
        selectHelper: true,            
        eventAfterRender: function(evento, elemento, viewo) {
            //If events are editable then add tooltip otherwise add hidden data for screen reader
            if (!evento.editable) {
                var childo = elemento.children();
                if( viewo.name == "month" )
                    childo[1].innerHTML += "<div class='fc-unedit-announce'>" + calendar_uneditable + "</div>";
                else
                    childo[0].innerHTML += "<div class='fc-unedit-announce'>" + calendar_uneditable + "</div>";
            } else {
                var childo = elemento.children();
                if( viewo.name == "month" )
                    fluid.tooltip( childo[1], {
                        content: function() {
                            return calendar_tooltip_event;
                        }
                    });
                else
                    fluid.tooltip( childo[0], {
                        content: function() {
                            return calendar_tooltip_event;
                        }
                    });
            }
            //Adjust focus on currently selected event
            if (focusd) {
                if (evento.id + "" == $("#ori-name1").val()) {
                    elemento.focus();
                    focusd = false;
                }
            }
        },
        //Event is resized. So update db.
        eventResize: function(event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view) { 
            //get new start date, end date and send it to the db
            var newsdate = $.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm") + ":00"; 
            var newedate = $.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm") + ":00"; 
            $.get("mods/_standard/calendar/update_personal_event.php",{id:event.id, start:newsdate, end:newedate, title:'',allday:'', cmd:"drag"});
        },            
        viewDisplay: function(view) {
            viewchangd = true;
            //Add button description for screen reader
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
               }
            );
            //Add tooltip to cells.
            fluid.tooltip(".fc-view-" + view.name, {
                content: function () {
                    return calendar_tooltip_cell;
                }
            });
        },
        //Event is clicked. So open dialog for editing event.
        eventClick: function(calevent,jsEvent,view){
            //Save currently active element so that focus can be restored later
            if (document.activeElement.tagName == "A") {
                activeelem = document.activeElement;
            }    
            if (!calevent.editable) {
                return;            
            }
            $("#fc-emode1").val("edit");
            $("#dialog1").dialog('open');                    
            
            //Display event name in the event title input box
            $("#name1").val(calevent.title);
            
            var date = calevent.start;
            //Display start date
            $("#date-start1").val($.fullCalendar.formatDate(date, "yyyy-MM-dd"));
            //Store event id for manipulation
            $("#ori-name1").val( calevent.id );
            
            //If allDay is true then no need to display time otherwise display time
            if (calevent.allDay == true) {
                //Disable time elements from the form
                $("#container-fc-tm").html("<input type='text' name='time' id='time-start1' disabled='disabled' class='text ui-widget-content ui-corner-all'>");
                
                document.getElementById("date-end1").disabled = false;
                document.getElementById("date-start1").disabled = false;
                
                $("#time-start1").addClass("fc-form-hide");
                $("#time-end1").addClass("fc-form-hide");                     
                $("#lbl-end-time1").addClass("fc-form-hide");
                $("#lbl-start-time1").addClass("fc-form-hide");
                 
                //Add and set datepickers
                $("#date-start1").val($.fullCalendar.formatDate(date, "yyyy-MM-dd"));
                $("#date-start1").focus(
                    function (ev){
                        scwShow(this,ev);                            
                    }
                );
                $("#date-start1").click(
                    function (ev){
                        scwShow(this,ev);
                    }
                );
                                    
                if (calevent.end != null) {
                    $("#date-end1").val($.fullCalendar.formatDate(calevent.end, "yyyy-MM-dd"));
                } else {
                    $("#date-end1").val($.fullCalendar.formatDate(date, "yyyy-MM-dd"));
                }
                
                $("#date-end1").focus(
                    function(ev) {
                        scwShow(this, ev);                            
                    }
                );
                $("#date-end1").click(
                    function(ev){
                        scwShow(this, ev);
                    }
                );
            } else {
                //Enable time elements
                $("#container-fc-tm").html("<select name='time' id='time-start1' class='text ui-widget-content ui-corner-all'></select>");
                
                $("#time-start1").removeClass("fc-form-hide");
                $("#time-end1").removeClass("fc-form-hide");
                $("#lbl-end-time1").removeClass("fc-form-hide");
                $("#lbl-start-time1").removeClass("fc-form-hide");
                
                $("#date-end1").val($.fullCalendar.formatDate(date, "yyyy-MM-dd"));
                $("#time-start1").val(date.getHours() + ":" + date.getMinutes());
                
                //Add and set datepickers
                $("#date-start1").val($.fullCalendar.formatDate(date, "yyyy-MM-dd"));
                $("#date-start1").focus(
                    function(ev){
                        scwShow(this,ev);                            
                    }
                );
                $("#date-start1").click(
                    function(ev){
                        scwShow(this,ev);
                    }
                );
                if (calevent.end != null) {
                    $("#date-end1").val($.fullCalendar.formatDate(calevent.end, "yyyy-MM-dd"));
                } else {
                    $("#date-end1").val($.fullCalendar.formatDate(date, "yyyy-MM-dd"));
                }
                $("#date-end1").focus(
                    function(ev) {
                        scwShow(this, ev);                            
                    }
                );
                $("#date-end1").click(
                    function(ev) {
                        scwShow(this, ev);
                    }
                );
                
                //Adjust start time and end time dropdown boxes so that the current values are displayed first
                select = $('#time-end1');
                $("#time-end1 > option").each(function() {
                    $(this).remove();
                });
                var startpt = date.getHours();
                var endpt   = calevent.end;
                if (endpt == null) {
                    endpt = date;
                }
                var bol     = true;
                for (tempi=0; tempi<=24; tempi++) {
                    if (tempi == 24) {
                        select.append("<option value='" + tempi + ":0' >" + tempi + "</option>");
                    } else {
                        if (bol) {
                            select.append("<option value='" + tempi + ":0' >" + tempi + "</option>");
                        }
                        select.append("<option value='" + tempi + ":30' >" + tempi + ":30" + "</option>");
                        bol = true;                                
                    }
                }
                if (endpt.getMinutes() < 30) {
                    select.val(endpt.getHours() + ":0");
                }
                else {
                    select.val(endpt.getHours() + ":30");
                }
                
                select = $('#time-start1');
                bol = true;
                for (tempi=0; tempi<=24; tempi++) {
                    if (tempi == 24) {
                        select.append("<option value='" + tempi + ":0' >" + tempi + "</option>");
                    } else {
                        if (bol) {
                            select.append("<option value='" + tempi + ":0' >" + tempi + "</option>");
                        }
                        select.append("<option value='"+tempi+":30' >"+tempi+":30"+"</option>");                                
                        bol = true;                                
                    }
                }
                if (date.getMinutes() < 30) {
                    select.val(date.getHours() + ":0");
                }
                else {
                    select.val(date.getHours() + ":30");
                }
            }
            //Save allDay value in hidden field
            $("#viewname1").val("" + calevent.allDay);
        },
        //Cell is clicked. So open dialog for creating new event.
        select: function (date, end, allDay, jsEvent, view) {                
            activeelem = document.activeElement;
            
            $("#fc-emode").val("create");                    
            $("#dialog").dialog('open');
            //Display event title in the input box
            $("#name").val(calendar_form_title_def);
            //Display start date
            $("#date-start").val($.fullCalendar.formatDate(date, "yyyy-MM-dd"));
            //If allday is true then disable time elements else enable them
            if (allDay == true) {
                //Month view or all-day events from other 2 views have allDay value true
                //hide time elements
                document.getElementById("date-end").disabled = false;                    
                
                $("#time-start").addClass("fc-form-hide");
                $("#time-end").addClass("fc-form-hide");   
                $("#lbl-end-time").addClass("fc-form-hide");
                $("#lbl-start-time").addClass("fc-form-hide");
                
                //Add and set date pickers
                $("#date-end").val($.fullCalendar.formatDate(date, "yyyy-MM-dd"));
                $("#date-end").focus(
                    function(ev) {
                        scwShow(this, ev);                            
                    }
                );
                $("#date-end").click(
                    function(ev) {
                        scwShow(this, ev);
                    }
                );                                    
            } else {
                //Enable time elements and prepare them with initial values
                document.getElementById("date-end").disabled = true;
                
                $("#time-start").removeClass("fc-form-hide");
                $("#time-end").removeClass("fc-form-hide");
                $("#lbl-end-time").removeClass("fc-form-hide");
                $("#lbl-start-time").removeClass("fc-form-hide");
                $("#date-end").val($.fullCalendar.formatDate(date, "yyyy-MM-dd"));
                $("#time-start").val(date.getHours() + ":" + date.getMinutes());
                
                select = $('#time-end');
                $("#time-end > option").each( function() {
                    $(this).remove();
                });
                
                var startpt = date.getHours();
                var bol     = false;
                if (date.getMinutes() == 0) {
                    bol = false;
                } else {
                    startpt++;                            
                    bol = true;
                }
                //Adjust time dropdown
                for (tempi=startpt; tempi<=24; tempi++) {
                    if (tempi == 24) {
                        select.append("<option value='" + tempi + ":0' >" + tempi + "</option>");
                    } else {
                        if (bol) {
                            select.append("<option value='" + tempi + ":0' >" + tempi + "</option>");
                        }
                        select.append("<option value='"+tempi+":30' >"+tempi+":30"+"</option>");
                        bol = true;                            
                    }                            
                }                    
                
                if (date.getMinutes() == 0) {
                    $("#time-end").val(date.getHours() + ":30");
                } else {
                    $("#time-end").val((parseInt(date.getHours()) + 1) + ":0");
                }
            }
            //Save view name in hidden field
            $("#viewname").val(view.name);                    
        },
        //Events are editable.
        editable: false,
        //Retrieve events from php file.
        eventSources: [
            'mods/_standard/calendar/json-events.php?all=1',
            'mods/_standard/calendar/json-events.php?mid=' + mid + '&pub=1',
            'mods/_standard/calendar/json-events-gcal.php'
        ]
    });
    var btns = {};
    btns[calendar_creat_e] = function () {
        //Get start date
        var startsplt = $("#date-start").val().split("-");
        var ends;
        //Get end date and time
        if ($('#viewname').val() == "month" 
            || document.getElementById("date-end").disabled == false) {
            ends = $("#date-end").val();
        } else {
            ends =  $("#date-start").val();                        
            var timestr = $("#time-start").val().split(":");
            var timestp = $("#time-end").val().split(":");                        
        }
        
        var endsplt = ends.split("-");
        var newid;
        //String processing of the date values
        if (startsplt[1].charAt(0) == '0') {
            startsplt[1] = startsplt[1].charAt(1);
        }
        if (endsplt[1].charAt(0) == '0') {
            endsplt[1] = endsplt[1].charAt(1);
        }
        if (startsplt[2].charAt(0) == '0') {
            startsplt[2] = startsplt[2].charAt(1);
        }
        if (endsplt[2].charAt(0) == '0') {
            endsplt[2] = endsplt[2].charAt(1);
        }
        //First send new events to db, db will return id and then display events in the calendar
        if ($('#viewname').val() == "month" 
            || document.getElementById("date-end").disabled == false) {
            
            var mysqlstartd = $.fullCalendar.formatDate(new Date(parseInt(startsplt[0]),
                                parseInt(startsplt[1])-1,
                                parseInt(startsplt[2])),
                                "yyyy-MM-dd HH:mm") + ":00";
            var mysqlendd   = $.fullCalendar.formatDate(new Date(parseInt(endsplt[0]),
                                parseInt(endsplt[1])-1,
                                parseInt(endsplt[2])),
                                "yyyy-MM-dd HH:mm") + ":00";
            
            $.get(
                "mods/_standard/calendar/update_personal_event.php",
                {id:'',start:mysqlstartd, end:mysqlendd, title:$("#name").val(), cmd:"create",allday:"true"}, 
                function(data) {
                    calendar.fullCalendar("refetchEvents");
                }
            );
            
            $(this).dialog("close");
            activeelem.focus();
        } else {
            var mysqlstartd = $.fullCalendar.formatDate(new Date(parseInt(startsplt[0]),
                                parseInt(startsplt[1])-1,
                                parseInt(startsplt[2]),
                                timestr[0],
                                timestr[1]), "yyyy-MM-dd HH:mm") + ":00";
            var mysqlendd = $.fullCalendar.formatDate( new Date(parseInt(endsplt[0]),
                              parseInt(endsplt[1])-1,
                              parseInt(endsplt[2]),
                              timestp[0],
                              timestp[1]), "yyyy-MM-dd HH:mm") + ":00";
            $.get(
                "mods/_standard/calendar/update_personal_event.php",
                {id:'',start:mysqlstartd, end:mysqlendd, title:$("#name").val(), cmd:"create",allday:"false"}, function(data) {
                    calendar.fullCalendar('refetchEvents');
                }
            );
            $(this).dialog('close');
            activeelem.focus();
        }
    };
    btns[calendar_cancel_e] = function () {
        $(this).dialog('close');
            activeelem.focus();
    };
    //Create event jQuery dialog
    $("#dialog").dialog({
        autoOpen: false,
        height:   300,
        width:    500,
        modal:    true,
        buttons:  btns,
        close: function() {
            scwHide();
            if (activeelem != null) {
                activeelem.focus();
            }
        }
    });
    
    //Buttons for editing
    var edit_btns = {};
    edit_btns[calendar_del_e] = function () {
        if ($("#ori-name1").val().indexOf("http") >= 0) {
            $.get("mods/_standard/calendar/google_calendar_update.php", 
                  {id:$("#ori-name1").val(), cmd:"delete"});
        } else {
            //Delete event from db
            $.get("mods/_standard/calendar/update_personal_event.php",
                {id:$("#ori-name1").val(), start:"", end:"", title:"", allday:"", cmd:"delete"}
            );
        }
        calendar.fullCalendar("removeEvents",
          function(ev) {
            //Remove event data from hidden elements
            $(".fc-month-vhidden").each(
                function(index) {
                    if ($(this).parent().prev().prev().text().indexOf( '"' +ev.id +'"' ) >= 0) {
                        $(this).parent().prev().prev().html("");
                        $(this).parent().prev().html("");
                    }
                }
            );
            $(".fc-cell-date").each(
                function(index) {
                    if ($(this).prev().text().indexOf( '"' +ev.id +'"' ) >= 0) {
                        $(this).prev().html("");
                        $(this).next().html("");
                    }
                }
            );
            $(".fc-allday-bhidden").each(
                function(index) {
                    if ($(this).prev().prev().text().indexOf( '"'+ev.id+'"' ) >= 0) {
                        $(this).prev().prev().html("");
                        $(this).prev().html("");
                    }
                }
            );
            //Matching event found for deleting
            if (ev.id == $("#ori-name1").val()) {
                return true;
            }
          }                    
        );
        calendar.fullCalendar('refetchEvents');
        $(this).dialog('close');
    };
    edit_btns[calendar_edit_e] = function () {
        //Get new values of time and date
        var startsplt = $("#date-start1").val().split("-");
        var ends;
        if ($('#viewname1').val() == "true") {
            ends = $("#date-end1").val();
        } else {
            ends =  $("#date-start1").val();
                
            var timestr = $('#time-start1').val().split(':');
            var timestp = $('#time-end1').val().split(':');                        
        }
        
        var endsplt = ends.split("-");
        
        if (startsplt[1].charAt(0) == '0') {
            startsplt[1] = startsplt[1].charAt(1);
        }
        if (endsplt[1].charAt(0) == '0') {
            endsplt[1] = endsplt[1].charAt(1);
        }
        if (startsplt[2].charAt(0) == '0') {
            startsplt[2] = startsplt[2].charAt(1);
        }
        if (endsplt[2].charAt(0) == '0') {
            endsplt[2] = endsplt[2].charAt(1);
        }
        //If allDay is true then only use dates otherwise use both dates and time values
        if ($('#viewname1').val() == "true") {
            var sdat = new Date(parseInt(startsplt[0]),
                                parseInt(startsplt[1])-1,
                                parseInt(startsplt[2]));
            var edat = new Date(parseInt(endsplt[0]),
                                parseInt(endsplt[1])-1,
                                parseInt(endsplt[2]));
            if (edat < sdat) {
                alert("Enter valid dates");
                $(this).dialog('close');
                activeelem.focus();
                return;
            }
        } else {
            var sdat = new Date(parseInt(startsplt[0]),
                                parseInt(startsplt[1])-1,
                                parseInt(startsplt[2]),
                                timestr[0],
                                timestr[1]);
            var edat = new Date(parseInt(endsplt[0]),
                                parseInt(endsplt[1])-1,
                                parseInt(endsplt[2]),
                                timestp[0],
                                timestp[1]);
            if (edat < sdat) {
                alert("Enter valid dates");
                $(this).dialog('close');
                activeelem.focus();
                return;
            }
        }
        //Remove old event data
        calendar.fullCalendar("removeEvents",
            function(ev) {
                $(".fc-month-vhidden").each(
                    function(index){
                        if ($(this).parent().prev().prev().text().indexOf( '"'+ev.id+'"' ) >= 0) {
                            $(this).parent().prev().prev().html("");
                            $(this).parent().prev().html("");
                        }
                    }
                );
                $(".fc-cell-date").each(
                    function(index){
                        if ($(this).prev().text().indexOf( '"'+ev.id+'"' ) >= 0) {
                            $(this).prev().html("");
                            $(this).next().html("");
                        }
                    }
                );
                $(".fc-allday-bhidden").each(
                    function(index){
                        if ($(this).prev().prev().text().indexOf( '"'+ev.id+'"' ) >= 0) {
                            $(this).prev().prev().html("");
                            $(this).prev().html("");
                        }
                    }
                );
                if (ev.id == $("#ori-name1").val()) {
                    return true;
                }                        
            }
        );
        //Add edited event as a new event and also update db values
        if ($('#viewname1').val() == "true") {
            if ($("#ori-name1").val().indexOf('http') >= 0) {
                var mysqlendd = $.fullCalendar.formatDate(new Date(parseInt(endsplt[0]),
                                                                   parseInt(endsplt[1])-1,
                                                                   parseInt(endsplt[2])),
                                                                   "u");
                var mysqlstartd = $.fullCalendar.formatDate(new Date(parseInt(startsplt[0]),
                                                                     parseInt(startsplt[1])-1,
                                                                     parseInt(startsplt[2])),
                                                                     "u");
                $.get(
                    "mods/_standard/calendar/google_calendar_update.php",
                    {
                    id:$("#ori-name1").val(), start:mysqlstartd, end:mysqlendd, 
                    title:$("#name1").val(), cmd:"update"
                    },
                    function(data) {
                    calendar.fullCalendar('refetchEvents'); 
                    focusd = true;
                    }
                );
            }
            else
            {
                var mysqlendd = $.fullCalendar.formatDate(new Date(parseInt(endsplt[0]),
                                                                            parseInt(endsplt[1])-1,
                                                                            parseInt(endsplt[2])),
                                                                            "yyyy-MM-dd HH:mm") + ":00";
                var mysqlstartd = $.fullCalendar.formatDate(new Date(parseInt(startsplt[0]),
                                                                              parseInt(startsplt[1])-1,
                                                                              parseInt(startsplt[2])),
                                                                              "yyyy-MM-dd HH:mm") + ":00";
                $.get(
                    "mods/_standard/calendar/update_personal_event.php",
                    {
                    id:$("#ori-name1").val(), start:mysqlstartd, end:mysqlendd, 
                    title:$("#name1").val(), cmd:"update",allday:"true"
                    },
                    function(data) {
                    calendar.fullCalendar('refetchEvents'); 
                    focusd = true;
                    }
                );
            }
            $(this).dialog('close');
        } else {
            if ($("#ori-name1").val().indexOf('http') >= 0) {
                var mysqlendd = $.fullCalendar.formatDate(new Date(parseInt(endsplt[0]),
                                                                    parseInt(endsplt[1])-1,
                                                                    parseInt(endsplt[2]),
                                                                    timestp[0],
                                                                    timestp[1]), 
                                                                    "u");
                var mysqlstartd = $.fullCalendar.formatDate(new Date(parseInt(startsplt[0]),
                                                                    parseInt(startsplt[1])-1,
                                                                    parseInt(startsplt[2]),
                                                                    timestr[0],
                                                                    timestr[1]),
                                                                    "u");
                $.get(
                    "mods/_standard/calendar/google_calendar_update.php",
                    {
                    id:$("#ori-name1").val(), start:mysqlstartd, end:mysqlendd,
                    title:$("#name1").val(), cmd:"update"
                    },
                    function(data) {
                            calendar.fullCalendar('refetchEvents');
                            focusd = true;
                    }
                );
            } else {
                var mysqlendd = $.fullCalendar.formatDate(new Date(parseInt(endsplt[0]),
                                                                   parseInt(endsplt[1])-1,
                                                                   parseInt(endsplt[2]),
                                                                   timestp[0],
                                                                   timestp[1]),
                                                                   "yyyy-MM-dd HH:mm") + ":00";
                var mysqlstartd = $.fullCalendar.formatDate(new Date(parseInt(startsplt[0]),
                                                                     parseInt(startsplt[1])-1,
                                                                     parseInt(startsplt[2]),
                                                                     timestr[0],
                                                                     timestr[1]),
                                                                     "yyyy-MM-dd HH:mm") + ":00";
                $.get(
                    "mods/_standard/calendar/update_personal_event.php",
                    {
                    id:$("#ori-name1").val(),start:mysqlstartd, end:mysqlendd,
                    title:$("#name1").val(), cmd:"update",allday:"false"},
                    function(data) {
                        calendar.fullCalendar('refetchEvents');
                        focusd = true;
                    }
                );
            }
            $(this).dialog('close');
        }
    };
    edit_btns[calendar_cancel_e] = function() {
        $(this).dialog('close');
        activeelem.focus();
    };
    
    /* Edit event dialog */
    $("#dialog1").dialog({
        autoOpen: false,
        height:   350,
        width:    700,
        modal:    true,
        buttons:  edit_btns,
        close: function() {
            scwHide();
            if (activeelem != null) {
                activeelem.focus();
            }
        }
    });
    if (session_view_on == 1) { 
        calendar.fullCalendar('gotoDate', fc_year, fc_month, fc_date);
    }
});
function refreshevents() {
    $("#calendar").fullCalendar("refetchEvents");
}