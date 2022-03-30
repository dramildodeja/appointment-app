<html>
<head>
<style>
html *
{
   font-family: Arial !important;
}
table.calendar {
	border-left: 1px solid #999;
}
tr.calendar-row {
}
td.calendar-day {
	min-height: 80px;
	font-size: 11px;
	position: relative;
	vertical-align: top;
}
* html div.calendar-day {
	height: 80px;
}
td.calendar-day:hover {
	background: #eceff5;
}
td.calendar-day-np {
	background: #eee;
	min-height: 80px;
}
* html div.calendar-day-np {
	height: 80px;
}
td.calendar-day-head {
	background: #ccc;
	font-weight: bold;
	text-align: center;
	width: 120px;
	padding: 5px;
	border-bottom: 1px solid #999;
	border-top: 1px solid #999;
	border-right: 1px solid #999;
}
div.day-number {
	background: #999;
	padding: 5px;
	color: #fff;
	font-weight: bold;
	float: right;
	margin: -5px -5px 0 0;
	width: 20px;
	text-align: center;
}
td.calendar-day, td.calendar-day-np {
	width: 120px;
	padding: 5px;
	border-bottom: 1px solid #999;
	border-right: 1px solid #999;
}
#step2 {
    display:none;
}
#step3 {
    display:none;
}
#backbtn { display: none }
</style>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Appointment Booking App</title>
<link href="../css/jquery-ui.css" rel="stylesheet">
<script src="../js/jquery-1.10.2.js"></script>
<script src="../js/jquery-ui.js"></script>
<!--<script src="../lang/datepicker-fi.js"></script>-->

<?php
if (isset($_GET['viewall'])) {
    function draw_calendar($month,$year){
        	include 'config.php';
        	$conn = mysqli_connect($servername, $username, $password,  $dbname);
        	if (!$conn) {
            	die("Connection failed: " . mysqli_connect_error());
        	}
        	$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';
        	$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';
        	$running_day = date('w',mktime(0,0,0,$month,1,$year));
        	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
        	$days_in_this_week = 1;
        	$day_counter = 0;
        	$dates_array = array();
        	$calendar.= '<tr class="calendar-row">';
        	for($x = 0; $x < $running_day; $x++):
        		$calendar.= '<td class="calendar-day-np"> </td>';
        		$days_in_this_week++;
        	endfor;
        	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
        		    $calendar.= '<td class="calendar-day">';
        			$calendar.= '<div class="day-number">'.$list_day.'</div>';
        			$calendar.= str_repeat('<p> </p>',2);
        			$current_epoch = mktime(0,0,0,$month,$list_day,$year);
        			$sql = "SELECT * FROM $tablename WHERE $current_epoch BETWEEN start_day AND end_day";
        			$result = mysqli_query($conn, $sql);
            		if (mysqli_num_rows($result) > 0) {
            			while($row = mysqli_fetch_assoc($result)) {
        					if($row["canceled"] == 1) $calendar .= "<font color=\"grey\"><s>";
            				$calendar .= "<b>" . $row["item"] . "</b><br>ID: " . $row["id"] . "<br>" . $row["name"] . "<br>" . $row["phone"] . "<br>" . $row["email"] . "<br>";
            				if($current_epoch == $row["start_day"] AND $current_epoch != $row["end_day"]) {
            					$calendar .= "Booking starts: " . sprintf("%02d:%02d", $row["start_time"]/60/60, ($row["start_time"]%(60*60)/60)) . "<br><hr><br>";
            				}
            				if($current_epoch == $row["start_day"] AND $current_epoch == $row["end_day"]) {
            					$calendar .= "Booking starts: " . sprintf("%02d:%02d", $row["start_time"]/60/60, ($row["start_time"]%(60*60)/60)) . "<br>";
            				}
            				if($current_epoch == $row["end_day"]) {
            					$calendar .= "Booking ends: " . sprintf("%02d:%02d", $row["end_time"]/60/60, ($row["end_time"]%(60*60)/60)) . "<br><hr><br>";
            				}
            				if($current_epoch != $row["start_day"] AND $current_epoch != $row["end_day"]) {
        	    				$calendar .= "Booking: 24h<br><hr><br>";
        	    			}
        					if($row["canceled"] == 1) {
        					    $calendar .= "</s></font>";
        					}
        					$calendar .= "<a href='cancel.php?id=".$row["id"]."'>Cancel</a>&nbsp;|&nbsp;<a href='delete.php?id=".$row["id"]."'>Delete</a>";
            			}
        			} else {
            			$calendar .= "No bookings";
        			}
        		$calendar.= '</td>';
        		if($running_day == 6):
        			$calendar.= '</tr>';
        			if(($day_counter+1) != $days_in_month):
        				$calendar.= '<tr class="calendar-row">';
        			endif;
        			$running_day = -1;
        			$days_in_this_week = 0;
        		endif;
        		$days_in_this_week++; $running_day++; $day_counter++;
        	endfor;
        	if($days_in_this_week < 8):
        		for($x = 1; $x <= (8 - $days_in_this_week); $x++):
        			$calendar.= '<td class="calendar-day-np"> </td>';
        		endfor;
        	endif;
        	$calendar.= '</tr>';
        	$calendar.= '</table>';
        	mysqli_close($conn);
        	return $calendar;
        }
        include 'config.php';
        echo '<h3>View Appointments</h3>';
        $d = new DateTime(date("Y-m-d"));
        echo '<h3>' . $months[$d->format('n')-1] . ' ' . $d->format('Y') . '</h3>';
        echo draw_calendar($d->format('m'),$d->format('Y'));
        $d->modify( 'first day of next month' );
        echo '<h3>' . $months[$d->format('n')-1] . ' ' . $d->format('Y') . '</h3>';
        echo draw_calendar($d->format('m'),$d->format('Y'));
        $d->modify( 'first day of next month' );
        echo '<h3>' . $months[$d->format('n')-1] . ' ' . $d->format('Y') . '</h3>';
        echo draw_calendar($d->format('m'),$d->format('Y'));
} else {
?>
<script>
    $(function() {
	<!--$.datepicker.setDefaults($.datepicker.regional['fi']);-->
    $( "#from" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#to" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#to" ).datepicker({
      defaultDate: "+1w",
	  regional: "fi",
      changeMonth: true,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#from" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
    $('#stepbtn').click( function() {
      if ($('#step2').is(':visible')) {
          $('#step3').show();
          $('#backbtn').show();
      } else if ($('#step1').is(':visible')) {
          $('#step2').show();
          $('#backbtn').show();
      }
   });
   $('#backbtn').click( function() {
      if ($('#step3').is(':visible')) {
          $('#step3').hide();
          $('#stepbtn').show();
      } else if ($('#step2').is(':visible')) {
          $('#step2').hide();
          $('#stepbtn').show();
          $('#backbtn').hide();
      }
   });
  });
</script>
</head>
<body>
<table cellpadding="5" width="800">
   <tr>
      <td valign="top">
         <form action="book.php" method="post" autocomplete="off">
            <h3>Book Appointment</h3>
            <hr>
            <div id="step1">
               <h4>Select Service</h4>
               <p>
                  <input checked="checked" name="item" type="radio" value="Service 1" />Service 1&nbsp;
                  <input name="item" type="radio" value="Service 2" />Service 2&nbsp;
                  <input name="item" type="radio" value="Service 3" />Service 3&nbsp;
                  <input name="item" type="radio" value="Service 4" />Service 4&nbsp;
               </p>
            </div>
            <div id="step2">
               <h4>Select Date & Time</h4>
               <table>
                  <tr>
                     <td><input id="from" placeholder="Click to open calendar" name="start_day" required="true" type="text" /></td>
                     <input id="to" name="end_day" hidden="true" type="text" />
                     <td>&nbsp;</td>
                     <td>
                        <select name="start_hour">
                           <option value="00" selected="selected">Midnight</option>
                           <option value="01">01 AM</option>
                           <option value="02">02 AM</option>
                           <option value="03">03 AM</option>
                           <option value="04">04 AM</option>
                           <option value="05">05 AM</option>
                           <option value="06">06 AM</option>
                           <option value="07">07 AM</option>
                           <option value="08">08 AM</option>
                           <option value="09">09 AM</option>
                           <option value="10">10 AM</option>
                           <option value="11">11 AM</option>
                           <option value="12">12 PM</option>
                           <option value="13">1 PM</option>
                           <option value="14">2 PM</option>
                           <option value="15">3 PM</option>
                           <option value="16">4 PM</option>
                           <option value="17">5 PM</option>
                           <option value="18">6 PM</option>
                           <option value="19">7 PM</option>
                           <option value="20">8 PM</option>
                           <option value="21">9 PM</option>
                           <option value="22">10 PM</option>
                           <option value="23">11 PM</option>
                        </select>
                        :
                        <select name="start_minute">
                           <option selected="selected">00</option>
                           <option>30</option>
                        </select>
                     </td>
                     <td>&nbsp;</td>
                     <td>
                        <select hidden="true" name="end_hour">
                          <option value="00">Midnight</option>
                          <option value="01">01 AM</option>
                          <option value="02">02 AM</option>
                          <option value="03">03 AM</option>
                          <option value="04">04 AM</option>
                          <option value="05">05 AM</option>
                          <option value="06">06 AM</option>
                          <option value="07">07 AM</option>
                          <option value="08">08 AM</option>
                          <option value="09">09 AM</option>
                          <option value="10">10 AM</option>
                          <option value="11">11 AM</option>
                          <option value="12">12 PM</option>
                          <option value="13">1 PM</option>
                          <option value="14">2 PM</option>
                          <option value="15">3 PM</option>
                          <option value="16">4 PM</option>
                          <option value="17">5 PM</option>
                          <option value="18">6 PM</option>
                          <option value="19">7 PM</option>
                          <option value="20">8 PM</option>
                          <option value="21">9 PM</option>
                          <option value="22">10 PM</option>
                          <option value="23" selected="selected">11 PM</option>
                        </select>
                        <select hidden="true" name="end_minute">
                           <option>00</option>
                           <option selected="selected">30</option>
                        </select>
                     </td>
                  </tr>
               </table>
            </div>
            <div id="step3">
               <h4>Add Your Details</h4>
               <table>
                  <tr>
                     <td>Your Name:</td>
                     <td><input maxlength="50" name="name" required="true" type="text" /></td>
                     <td>&nbsp;</td>
                     <td>&nbsp;</td>
                  </tr>
                  <tr>
                       <td>Email:</td>
                       <td><input maxlength="20" name="email" required="true" type="email" /></td>
                       <td>&nbsp;</td>
                       <td>&nbsp;</td>
                  </tr>
                  <tr>
                     <td>Phone:</td>
                     <td><input maxlength="10" name="phone" required="true" type="phone" /></td>
                     <td>&nbsp;</td>
                     <td>&nbsp;</td>
                  </tr>
                  <tr>
                       <td>&nbsp;</td>
                       <td><input style="background:lightgreen" name="book" type="submit" value="Confirm Appointment" /></td>
                       <td>&nbsp;</td>
                       <td>&nbsp;</td>
                  </tr>
               </table>
            </div>
         </form>
         <hr>
         <button id="stepbtn">Next</button>
         <button id="backbtn">Back</button>
      </td>
   </tr>
</table>
<?php
}
?>
</body>
</html>