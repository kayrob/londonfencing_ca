/**
* Adds or removes an event source from fullCalendar when a calendar is added, disabled, or filtered for view
* All feeds go to the same sync file with a calendar ID parameter
* need to access this method from admin also, so has to be in the global scope
*/
var add_remove_event_sources = function(action,calendarID){
	$('#calendar').fullCalendar(action,'/ajax/apps/calendar/calendar.php?isAjax=y&calendar='+calendarID);
}
/**
* close dialog window based on id
* also used in admin
* @param string id
*/
var close_dialog = function(id){
	$(id).dialog('close');
	$('p.message',id).empty();
	if ($('form',id).length > 0){
		document.getElementById(id.replace("#","")).getElementsByTagName('form')[0].reset();
	}
}
/**
* Set the forms elements (in container divs) to modal dialogs based
* also used in admin
* @param string id
*/
var set_dialog = function(id){
	$(id).dialog({
		autoOpen:false,
		modal:true
	});
}
/**
* This method opens a dialog with event details based on event selected
* is also called from admin page
* Objects passed are fullCalendar objects - jsEvent can be used to position dialog boxes
* @param object calEvent
* @param object jsEvent
* @param object view
*/
var show_event_details = function(calEvent,jsEvent,view){
	var startHours = (calEvent.start.getHours() > 12)?calEvent.start.getHours()-12:calEvent.start.getHours();
	var startMins = (calEvent.start.getMinutes() < 10)?"0"+calEvent.start.getMinutes():calEvent.start.getMinutes();
	var startTOD = (calEvent.start.getHours() >= 12)?"pm":"am";
	var endHours = (calEvent.end.getHours() > 12)?calEvent.end.getHours()-12:calEvent.end.getHours();
	var endMins = (calEvent.end.getMinutes() < 10)?"0"+calEvent.end.getMinutes():calEvent.end.getMinutes();
	var endTOD = (calEvent.end.getHours() >= 12)?"pm":"am";
	$('#tr_allDay td:eq(0)','#dlgE;ventDetails').html('&nbsp;');
	$('#tr_allDay','#dlgEventDetails').hide();
	$('#tr_recurring td:eq(1)','#dlgEventDetails').html('&nbsp;');
	$('#tr_recurring','#dlgEventDetails').hide();
	if (calEvent.allDay == true){
		$('#tr_allDay td:eq(0)','#dlgEventDetails').html("<em>This is an all day event</em>");
		$('#tr_allDay','#dlgEventDetails').show();
	}
	$('#tdDateStart','#dlgEventDetails').html((1+calEvent.start.getMonth())+"/"+calEvent.start.getDate()+"/"+calEvent.start.getFullYear()+" "+startHours+':'+startMins+' '+startTOD);
	$('#tdDateEnd','#dlgEventDetails').html((1+calEvent.end.getMonth())+"/"+calEvent.end.getDate()+"/"+calEvent.end.getFullYear()+" "+endHours+':'+endMins+' '+endTOD);
	$('#tdLocation','#dlgEventDetails').html(calEvent.location);
	$('#tdDescription','#dlgEventDetails').html(calEvent.description);
	if (calEvent.recurrence != 'None'){
		$('#tr_recurring td:eq(1)','#dlgEventDetails').html(calEvent.recurrenceDescription);
		$('#tr_recurring','#dlgEventDetails').show();
	}
	if ($('#pAddEvent a','#dlgEventDetails').length == 1){
		var icalHref = $('#pAddEvent a','#dlgEventDetails').attr('href').split('?');
		$('#pAddEvent a','#dlgEventDetails').attr('href',icalHref[0]+'?event='+calEvent.id);
	}
	$('#dlgEventDetails').dialog({title:calEvent.title});
	$('#dlgEventDetails').dialog('open');
}
/**
* Update list view in full calendar with data returned from ajax call when calendars are added/removed from the side menu
*/
var update_fullCal_events_widget = function(){
    var calendars = new Array();
    $('#leftCol :input').each(function(){
        if ($(this).attr('checked') == true){
            calendars.push($(this).val());
        }
    });
    $.post('/ajax/calendar.php?isAjax=y&qview=list',{'view[]':calendars,'timestamp':$('#plistTimeStamp').html()},function(data){
    	data = (!data.match(/false/))?data:"";
        $('#eventsWidget').html(data);
    });
}
/**
* Manage the onlick event for next and previous buttons in list view
* have to rerender the calendar and hide the cal for list view to keep date state consistent among views
* @see toggleCalendarList()
*/
var page_fullCal_events_widget = function(action){
	var calendars = new Array();
    $('#leftCol :input').each(function(){
        if ($(this).attr('checked') == true){
            calendars.push($(this).val());
        }
    });
    $.post('/ajax/calendar.php?isAjax=y&qview=list',{'view[]':calendars,'cmmd':action,'timestamp':$('#plistTimeStamp').html()},function(data){
        $('#eventsWidget').html(data);
        var monthNames = new Array("January","February","March","April","May","June","July","August","September","October","November","December");
        var date = new Date(($('#plistTimeStamp').html()*1000));
        var month = monthNames[parseInt(date.getMonth(),10)];
        var year = date.getFullYear();
        $('.fc-header-title').html(month+' '+year);
        $('#calendar').fullCalendar('gotoDate',date);
       	toggleCalendarList('list');
    });
}
/**
* Creates click events for the calendar list of the public calendar
* @see add_remove_event_sources()
*/
var set_calendar_list_events_public = function(){
	if ($('#leftCol').length == 1){
		$('#leftCol :input').each(function(){
			$(this).click(function(){
				($(this).attr('checked') == true)?add_remove_event_sources('addEventSource',$(this).val()):add_remove_event_sources('removeEventSource',$(this).val());
				//update_fullCal_events_widget();				
			});
		});
	}
}
/**
* Used for the quick widget calendar - retrieve a list of events for a specific selected date
* @param string timestamp
*/
var show_quick_widget_events = function(obj,timestamp){
	var key = Math.round((Math.random() + Math.random()) * 100);
	$.get('/ajax/apps/calendar/calendar.php?ran='+key,{qview:timestamp},function(data){
				$('#dlgQuickView').empty();
		if (data != 'false'){
			$('#dlgQuickView').html(data);			
		}
		else{
			$('#dlgQuickView').html("An error occurred.<br />Events can't be displayed");
		}
		$('#dlgQuickView').dialog('open');
	});
}
/**
* Update class for list and full calendar buttons so buttons use the correct class when list view is clicked
* @see toggleCalendarList()
*/
var removeActiveClass = function(view){
	if (view == "list"){
		$('.fc-header-right div').each(function(index){
			if (index < 3){
				if ($(this).attr('class').match(/active/)){
					var className = $(this).attr('class').replace(" fc-state-active","");
					$(this).removeClass().addClass(className);
					$("a span",this).click(function(){
						var buttonName = className.split(" ");
						toggleCalendarList(buttonName[0].replace("fc-button-",""));
						$(this).attr("class",$(this).attr('class')+" fc-state-active");
					});
				}
			}
		});
	}
	else if ($('#dvListView').attr("class").match(/active/)){
		className = $('#dvListView').attr('class').replace(" fc-state-active","");
		$('#dvListView').removeClass().addClass(className);
	}
}
/**
* Main method to switch from calendar to list view
* CSS of buttons are updated and full calendar is shown or hidden based on view type. Calendar is re-rendered each time it is reselected for proper display
* @param string view
* @see update_fullCal_events_widget()
* @see removeActiveClass()
*/
var toggleCalendarList = function(view){
	var calDiv = ($('#dvListView').length == 1)?$('#calendar div:eq(11)'):$('#calendar div:eq(10)');
 	if ($('#eventsWidget').length == 1){
 		var listActiveClass = $('#dvListView').attr("class");
 		$('#eventsWidget').hide();
 		if (view == "list"){
 			var calDate = Math.round($('#calendar').fullCalendar('getDate').getTime()/1000);
			$('#plistTimeStamp').html(calDate);
			update_fullCal_events_widget();
			var monthNames = new Array("January","February","March","April","May","June","July","August","September","October","November","December");
			$('.fc-header-title').html(monthNames[$('#calendar').fullCalendar('getDate').getMonth()]+' '+$('#calendar').fullCalendar('getDate').getFullYear());
 			$('.fc-header-center table td:gt(1) div').hide();
			$('.fc-header-center table td:lt(10) div').hide();
			$('.fc-header-center table td:eq(5) div').show();
			$('.fc-header-center table td:eq(0) div').show();
			$('.fc-header-center table td:eq(10) div').show();
 			var className = listActiveClass+" fc-state-active";
			calDiv.hide();
			$('#dvListView').removeClass().addClass(className);
			removeActiveClass(view);
			$('#eventsWidget').show();
		}
		else{
			if ($('#dvListView').length == 1 && listActiveClass.match(/active/)){
				removeActiveClass(view);
				$('.fc-header-left').show();
				$('.fc-header-center').show();
				calDiv.show();
				$('#calendar').fullCalendar('render');
			}
		}
	
	}
}
$(document).ready(function()
{
	if ($("#calendar").length == 1){
		set_dialog('#dlgEventDetails');
		var defaults = {
			header: {
				center: 'prevYear prev today next nextYear',
				right: 'month,agendaWeek,agendaDay'
			},
			buttonText: {
				prev: '&nbsp;&#9668;Month&nbsp;',
				next: '&nbsp;&#9658;Month&nbsp;',
				prevYear: '&nbsp;&#9668;Year&nbsp;',
				nextYear: '&nbsp;&#9658;Year&nbsp;',
				today: 'Today',
				month: ' View Month',
				week: 'View Week',
				day: 'View Day'
			},
			viewDisplay: function(view){
				//display the calendar title in a header outside current element on each re-load
				$('#calendarTitle h2').html(view.title);
				//change 'next button' html values based on calendar view
				var viewName = view.name.replace('agenda','');
				viewName = viewName.charAt(0).toUpperCase()+viewName.substr(1,(viewName.length -1));
				$('.fc-header-center td:eq(2) span').html('&nbsp;&#9668;'+viewName+'&nbsp;');
				$('.fc-header-center td:eq(6) span').html('&nbsp;&#9658;'+viewName+'&nbsp;');
				
				//hide year buttons on day and week view
				$('.fc-header-center td:eq(0) div').show();
				$('.fc-header-center td:eq(8) div').show();
				if (view.name !== "month"){
					$('.fc-header-center td:eq(0) div').hide();
					$('.fc-header-center td:eq(8) div').hide();
				}
			},
			dayClick: function(data,allDay,jsEvent,view){
				$('#calendar td').each(function(){
					if ($(this).css('backgroundColor') != 'rgb(255, 255, 204)'){
						$(this).css('backgroundColor','transparent');
					}
				});
				if ($(this).css('backgroundColor') != 'rgb(255, 255, 204)'){
					$(this).css('backgroundColor','#F3F3F3');
				}
			},
			eventClick: function(calEvent, jsEvent, view){
				if (calEvent.altUrl != ""){
					window.location.href="http://"+calEvent.altUrl;
				}
				else{
					show_event_details(calEvent,jsEvent,view);
				}
			}
		};
		//if is admin page, override the options
		var calOptions = (typeof(adminDefaults) == "undefined")?defaults:$.extend(defaults,adminDefaults());
		
		//custom method to allow jump to month/year
		var customButtons = function(){
			var date = new Date();
			var selectMonth = function(){
				var months = new Array('January','February','March','April','May','June','July','August','September','October','November','December');
				var thisMonth = date.getMonth();
				var selElement = '&nbsp;<select name="calMonth" id="calMonth">';
				for (var i = 0; i < 12; i++){
					var selected = (thisMonth == i)?'selected="selected"':'';
					selElement += '<option value="'+i+'" '+selected+'>'+months[i]+'</option>';
				}
				selElement += '</select>&nbsp;';
				return selElement;
			}
			var selectYear = function(){
				var year = date.getFullYear();
				selElement = '&nbsp;<select name="calYear" id="calYear">';
				for (i = -1; i < 4; i++){
					selected = (i == 0)?'selected="selected"':'';
					selElement += '<option value="'+(i + year)+'" '+selected+'>'+(i + year)+'</option>'
				}
				selElement += "</select>&nbsp;";
				return selElement;	
			}
			var monthSelector = function(){
				var goToMonth = '<form name="frm_goToMonth" id="frm_goToMonth" action="" onsubmit="return false" style="margin:0px;padding:0px;display:inline">';
				goToMonth += selectMonth()+selectYear();
				goToMonth += '<input type="button" value=" Go " id="customMonthYear" /></form>';
				return goToMonth;
			}
			
			$('.fc-header-left td:eq(0)').append(monthSelector());
			$('#customMonthYear').click(function(){
				var selDate = new Date($('#calYear').val(),$('#calMonth').val(),1);
				if ($('#dvListView').length == 1 && $('#dvListView').attr("class").match(/active/) == "active"){
					var calDate = selDate.getTime()/1000;
					$('#plistTimeStamp').html(calDate);
					page_fullCal_events_widget();
				}
				else{
					$('#calendar').fullCalendar('gotoDate',selDate);
				}
				//toggleCalendarList("month");
				
			});
		/*	var prevList = '<div class="fc-button-prevList fc-state-default fc-corner-left fc-corner-right"><a><span>&#9668;&nbsp;Month&nbsp;</span></a></div>';
			var nextList = '<div class="fc-button-nextList fc-state-default fc-corner-left fc-corner-right"><a><span>&#9658;&nbsp;Month&nbsp;</span></a></div>';
			$('<td>'+prevList+'</td>').insertBefore('.fc-header-center table td:eq(0)');
			$('.fc-header-center table tr').append('<td>'+nextList+'</td>');
			$('.fc-header-center table td:eq(0) div').hide();
			$('.fc-header-center table td:eq(10) div').hide();
			$('.fc-header-center table td:eq(0) div').click(function(){
				page_fullCal_events_widget('prev');
			});
			$('.fc-header-center table td:eq(10) div').click(function(){
				page_fullCal_events_widget('next');
			});*/
		}
		/**
		* Create a new button to show list view in the full calendar
		* @see toggleCalendarList()
		*/
/*		var createListView = function(){
			if ($('#eventsWidget').length == 1){
				$('<td><div id="dvListView" class="fc-button-list fc-state-default fc-corner-right"><a><span>View List</span></a></div></td>').insertAfter('.fc-header-right table td:eq(2)');
				$('.fc-button-agendaDay').removeClass('fc-corner-right');
				$('.fc-button-agendaDay').addClass('fc-no-right');
				$('#dvListView a:eq(0)').click(function(){
					toggleCalendarList("list");
					//set up next and previous buttons. keep today toggled on for the other views
				});
			}
		}*/ 
		
		//initial calendar method
		$('#calendar').fullCalendar(calOptions);

		//remove the calendar title from the calendar div b/c we put it in it's own div tag above the calendar for formatting purposes
		$('#calendar h2.fc-header-title').remove();
		customButtons();
		
		//ajax call to get calendars and set events feeds
		//only one feed is shown if a specific calendar is selected
		var key = Math.round((Math.random() + Math.random()) * 100);
		$.get('/ajax/apps/calendar/calendar.php?ran='+key,{calendar:'s'},function(data){
			var result = data.replace(/^(\S\s)*|(\S\s)*$/,'');
			if (result != 'false'){
				var qstring = window.location.href.split('view=');
				var calSelected = (qstring[1] && parseInt(qstring[1],10) >=0)?parseInt(qstring[1],10):false;
				var calendars = $.parseJSON(result);
				var j = 0;
				for (var key in calendars){
					if (calendars.hasOwnProperty(key)){
						if ((calSelected == false && j == 0) || (calSelected == calendars[key])){
							j = key;
						}
						if (calSelected == false || calSelected == calendars[key]){
							add_remove_event_sources('addEventSource',calendars[key]);
						}
					}
				}
				$('#calendar').fullCalendar('refetchEvents',j);
			}
		});
		set_calendar_list_events_public();
		
		//check to see if a date request has been submitted via get from a calendar widget
		if (window.location.href.match(/event=[0-9]{1,6}(_)?([0-9]{1,6})?&start=([0-9]{1,})/)){
			var queryString = window.location.href.split("?");
			if (queryString.length == 2){
				var eventSel = queryString[1].split("&");
				var eventSelID = eventSel[0].substring(6);
				var eventTime = new Date((eventSel[1].substring(6))*1000);
				$('#calendar').fullCalendar('gotoDate',eventTime);
				$('#dlgEventDetails').dialog('open');
			}
		}
		//add new button for list view in calendar
		//createListView();
	}
	//for quick view calendar widget
	if ($('#dlgQuickView').length == 1){
		set_dialog('#dlgQuickView');
	}	
});

var change_qv_month = function(advance){
	var key = Math.round((Math.random() + Math.random()) * 100);
	var qvStamp = $("#pQVdateTime").html();
	$.get("/ajax/apps/calendar/calendar.php?ran="+key+"&isAjax=y",{"qvAdv":advance,"qvStamp":qvStamp},function(data){
		if (data != false){
			var info = data.split("~");
			$("#calendarWidgetTable").remove();
			$(info[0]).insertBefore("#pQVdateTime");
			$("#pQVdateTime").html(info[1]);
			$("#h3QVHeader span:eq(1)").html(info[2]);
		}
	});
}
$(document).ready(function(){
	$(':input[name=eventStart]').datepicker({
		dateFormat: 'yy-mm-dd'
	});
	$(':input[name=eventEnd]').datepicker({
		dateFormat: 'yy-mm-dd'
	});
	if ($('#dvQVCalendar').length == 1){
		$("span","#h3QVHeader").each(function(index){
			if (index != 1){
				$(this).click(function(){
					change_qv_month(index-1);
				});
			}
		});
		$('#dlgQuickView').click(function(){
			$(this).hide();
		});
	}
});