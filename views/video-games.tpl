<header class="jumbotron subhead" style="text-align: center;">
<h1>Vidya in 2013</h1>
<p class="lead">Need a reminder of what games were released in 2013?</p>
<p>Click on the row headers to sort by platform.</p>
</header>

<div class="row">
	<div class="span12">
		
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#games").tablesorter(); 
    } 
); 
</script>

<style type="text/css">
#games td {
	text-align: center;
}
.c-pc {
	background-color: black;
	color: white;
}
.c-ps3, .c-ps4, .c-psv {
	background-color: rgb(0, 64, 152);
	color: white;
}
.c-360, .c-xb1 {
	background-color: rgb(17, 125, 16);
	color: white;
}
.c-wii, .c-3ds, .c-wiiu {
	background-color: rgb(127, 127, 127);
	color: white;
}
.yes {
	display: block;
}
#games {
	border-color: black;
	border-radius: 0;
}
thead th {
	background-color: white;
	border-bottom: 2px black solid;
}
#games td, thead th {
	border-radius: 0 !important;
}
.divider {
	border-left: 1px solid black !important;
}
.notable {
	font-weight: bold;
}
</style>

<table class="table table-striped table-bordered table-condensed tablesorter" id="games">
<thead>
<tr>
	<th class='divider'>Game Title</th>
	<th width='40px' class='divider'>PC</th>
	<th width='40px' class='divider'>PS3</th>
	<th width='40px'>PS4</th>
	<th width='40px'>PSV</th>
	<th width='40px' class='divider'>360</th>
	<th width='40px'>XB1</th>
	<th width='50px' class='divider'>Wii</th>
	<th width='50px'>Wii U</th>
	<th width='50px'>3DS</th>
	<th width='60px' class='divider'>Mobile</th>
	<th width='120px' class='divider'>Others</th>
</tr>
</thead>
<tbody>
<loop:games>
<tr>
	<td class='divider <tag:games[].Notable />' style='text-align: left;'><tag:games[].Game /></td>
	<td width='40px' class='divider'><tag:games[].PC /></th>
	<td width='40px' class='divider'><tag:games[].PS3 /></th>
	<td width='40px'><tag:games[].PS4 /></th>
	<td width='40px'><tag:games[].PSV /></th>
	<td width='40px' class='divider'><tag:games[].360 /></th>
	<td width='40px'><tag:games[].XB1 /></th>
	<td width='50px' class='divider'><tag:games[].Wii /></th>
	<td width='50px'><tag:games[].WiiU /></th>
	<td width='50px'><tag:games[].3DS /></th>
	<td width='60px' class='divider'><tag:games[].Mobile /></th>
	<td width='120px' class='divider'><tag:games[].Others /></th>
</tr>
</loop:games>
</tbody>
</table>

<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.5.2/underscore-min.js"></script>
<script type="text/javascript" src="/public/jquery/jquery.floatThead.min.js"></script>
<script type="text/javascript">
$('#games').floatThead({
	scrollingTop: 40
});
</script>