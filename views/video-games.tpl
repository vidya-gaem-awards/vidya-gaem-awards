<header class="jumbotron subhead">
<h1>2012 in vidya</h1>
<p class="lead">Need a reminder of what games were released in 2012? Take a trip down memory lane with this list of vidya; everything from AAA titles to indie shovelware.</p>
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
</style>

<table class="table table-striped table-bordered table-condensed tablesorter" id="games">
<thead>
<tr>
	<th>Game Title</th>
	<th width='40px' style='border-left-width: 3px;'>PC</th>
	<th width='40px' style='border-left-width: 3px;'>PS3</th>
	<th width='40px'>360</th>
	<th width='40px'>Wii</th>
	<th width='40px' style='border-left-width: 3px;'>PSV</th>
	<th width='40px'>3DS</th>
	<th width='40px' style='border-left-width: 3px;'>PS2</th>
	<th width='40px' style='border-left-width: 3px;'>iOS</th>
</tr>
</thead>
<tbody>
<loop:games>
<tr>
	<td style='text-align: left;'><tag:games[].Game /></td>
	<td style='border-left-width: 3px;'><tag:games[].PC /></td>
	<td style='border-left-width: 3px;'><tag:games[].PS3 /></td>
	<td><tag:games[].360 /></td>
	<td><tag:games[].Wii /></td>
	<td style='border-left-width: 3px;'><tag:games[].PSV /></td>
	<td><tag:games[].3DS /></td>
	<td style='border-left-width: 3px;'><tag:games[].PS2 /></td>
	<td style='border-left-width: 3px;'><tag:games[].iOS /></td>
</tr>
</loop:games>
</tbody>
</table>