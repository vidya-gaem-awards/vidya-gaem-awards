<header class="jumbotron subhead">
<h1>2013 in vidya</h1>
<p class="lead">Need a reminder of what games were released in 2013? Take a trip down memory lane with this list of vidya; everything from AAA titles to indie shovelware.</p>
</header>

<div class="row">
	<div class="span12">
		<p>Click on the row headers to sort by platform.</p>
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
</style>

<table class="table table-striped table-bordered table-condensed tablesorter" id="games">
<thead>
<tr>
	<th>Game Title</th>
	<th width='40px' style='border-left-width: 3px;'>PC</th>
	<th width='40px' style='border-left-width: 3px;'>PS3</th>
	<th width='40px'>PS4</th>
	<th width='40px'>PSV</th>
	<th width='40px' style='border-left-width: 3px;'>360</th>
	<th width='40px'>XB1</th>
	<th width='50px' style='border-left-width: 3px;'>Wii</th>
	<th width='50px'>Wii U</th>
	<th width='50px'>3DS</th>
	<th width='60px' style='border-left-width: 3px;'>Mobile</th>
	<th width='60px' style='border-left-width: 3px;'>Others</th>
</tr>
</thead>
<tbody>
<loop:games>
<tr>
	<td style='text-align: left;'><tag:games[].Game /></td>
	<td width='40px' style='border-left-width: 3px;'><tag:games[].PC /></th>
	<td width='40px' style='border-left-width: 3px;'><tag:games[].PS3 /></th>
	<td width='40px'><tag:games[].PS4 /></th>
	<td width='40px'><tag:games[].PSV /></th>
	<td width='40px' style='border-left-width: 3px;'><tag:games[].360 /></th>
	<td width='40px'><tag:games[].XB1 /></th>
	<td width='50px' style='border-left-width: 3px;'><tag:games[].Wii /></th>
	<td width='50px'><tag:games[].WiiU /></th>
	<td width='50px'><tag:games[].3DS /></th>
	<td width='60px' style='border-left-width: 3px;'><tag:games[].Mobile /></th>
	<td width='60px' style='border-left-width: 3px;'></th>
</tr>
</loop:games>
</tbody>
</table>