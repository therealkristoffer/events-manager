<?php
/*
 * Default Event Single Template
 * This template could be used to show a single event
 * You can override the default display settings pages by removing 'example-' at the start of this file name and moving it to yourthemefolder/plugins/events-manager/templates/
 * 
 * This file is called within an EM_Event object, so $this corresponds to the EM_Event object we're displaying
 * 
 */ 

//this is one way, another could be to just directly use $this to get event data
ob_start();
?>
<table>
	<tr>
		<th style="width:150px;">When? </th>
		<td>
			Date(s) - #j #M #Y #@_{ \u\n\t\i\l j M Y}<br />
			Time (24h Format) - #_24HSTARTTIME - #_24HENDTIME<br />
			Time (12h Format) - #_12HSTARTTIME - #_12HENDTIME
		</td>
	</tr>
	<tr>
		<th>What? </th>
		<td>
			<h3>#_NAME</h3>
			<p>
				Event ID : #_EVENTID<br />
				Category Name : #_CATEGORY <br />
				Category ID : #_CATEGORYID<br />
			</p>
			#_NOTES
		</td>
	</tr>
	<tr>
		<th>
			<strong>ATTRIBUTE EXAMPLES</strong>
			Price<br />
			Dress Code<br />
		</th>
		<td>
			#_ATT{Price}{Free}<br />
			#_ATT{Dress Code}{Informal}<br />
			#_AVAILABLESPACES / #_SPACES<br />
			
		</td>
	</tr>
	<tr>
		<th>Who? </th>
		<td>
			#_CONTACTAVATAR #_CONTACTNAME (aka #_CONTACTUSERNAME)<br />
			User ID : #_CONTACTID<br />
			Email : #_CONTACTEMAIL<br />
			Phone : #_CONTACTPHONE<br />
			URL : #_CONTACTPROFILEURL - #_CONTACTPROFILELINK<br />			
		</td>
	</tr>
	<tr>
		<th>Bookings</th>
		<td>
			Total Spaces : #_SPACES<br />
			Booked Spaces : #_BOOKEDSPACES<br />
			Available Spaces : #_AVAILABLESPACES<br />
			
			<p>#_ADDBOOKINGFORM</p>
			<p>#_REMOVEBOOKINGFORM</p>
		</td>
	</tr>
	<tr>
		<th>Where? </th>
		<td>
			Location Image : 
			#_LOCATIONIMAGE
			<p>
				#_LOCATIONNAME, #_LOCATIONADDRESS, #_LOCATIONTOWN<br/>
				ID - #_LOCATIONID<br />
				Links - #_LOCATIONPAGEURL or #_LOCATIONURL or #_LOCATIONLINK
			</p>
			#_LOCATIONEXCERPT
			<br />
			#_LOCATIONMAP
			<br />
			<h3>Upcoming Events at #_LOCATION</h3>
			#_LOCATIONNEXTEVENTS			
			<br />
			<h3>Previous Events at #_LOCATION</h3>
			#_LOCATIONPASTEVENTS			
			<br />
			<h3>All Events at #_LOCATION</h3>
			#_LOCATIONALLEVENTS
		</td>
	</tr>
</table>
#_EDITEVENTLINK
<?php 
$format = ob_get_clean();
//now we just grab the format and output! we could throw in some conditions above and let EM handle the formatting
echo $this->output($format);