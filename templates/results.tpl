<if:denied>
Sorry, you aren't allowed to view this page right now. All information about the /v/GAs will be revealed to everybody after they end.<br /><br />
If you think you should have access, give Clamburger your Steam ID: <tag:communityID />
<else:denied>
<h1>Results and other information</h1>
<div class="row">
<div class="span16">
<p>This is the detailed information we used to plan everything behind the scenes. Since the award ceremony has been shown, we have no reason to hide it any more.</p>
<ul>
<!-- <li><a href="?suggestionBox">Suggestion Box</a></li> -->
<li><a href="?categoryVotes">Category Feedback</a></li>
<li><a href="?nominations">Nominations</a></li>
<li><a href="?votes">Votes</a></li>
<li><a href="?votesTable">Votes (tabular data)</a></li>
</ul>
</div>
</div>
<if:categoryVotes>
<div class="page-header">
	<h1>Category Feedback</h1>
</div>

<loop:categoryRows>
<div class="row" style="margin-bottom: 10px;">
<loop:categoryRows[].cols>
<div class="span-one-third" id="<tag:categoryRows[].cols[].ID />">
	
	<script type="text/javascript">

		var chart;
		
		$(document).ready(function() {
			chart = new Highcharts.Chart({
				credits: {
					enabled: false,
				},
				chart: {
					renderTo: '<tag:categoryRows[].cols[].ID />',
					plotBorderWidth: 0,
					reflow: false,
				},
				title: {
					text: "<tag:categoryRows[].cols[].Name />"
				},
				tooltip: false,
				plotOptions: {
					pie: {
						dataLabels: {
							enabled: true,
							color: '#000000',
							connectorColor: '#000000',
							formatter: function() {
								return '<b>'+ this.point.name +'</b>: '+ this.y + '<br />(' + Highcharts.numberFormat(this.percentage, 2) + '%)';
							}
						}
					},
				},
				series: [{
					type: 'pie',
					name: null,
					data: 
						<tag:categoryRows[].cols[].Data />
					
				}]
			});
			
			console.log(chart);
		});

		</script>
	
</div>
</loop:categoryRows[].cols>
</div>
</loop:categoryRows>
</if:categoryVotes>

<if:suggestionBox>
<div class="page-header">
	<h1>Suggestion Box</h1>
</div>

<div class="row">
	<div class="span16">
		<ul>
		<loop:suggestions>
		<li title="<tag:suggestions[].Hash />"><tag:suggestions[].Text /></li>
		</loop:suggestions>
		</ul>
	</div>
</div>
</if:suggestionBox>

<if:nominations>
<loop:categories>
<div class="page-header">
	<h1><tag:categories[].Name /> <small><tag:categories[].Subtitle /></small></h1>
</div>
<div class="row">
	<div class="span16">
		<ul>
		<loop:categories[].Nominations>
			<li title="<tag:categories[].Nominations[].Hash />"><tag:categories[].Nominations[].Text /></li>
		</loop:categories[].Nominations>
		</ul>
	</div>
</div>
</loop:categories>
</if:nominations>

<if:votes>
<if:results>
<div class="page-header">
	<h1>Votes
	<small style='color: maroon;'>currently showing <b><tag:site /></b> votes</small></h1>
</div>

<loop:categoryRows>
<div class="row" style="margin-bottom: 10px;">
<loop:categoryRows[].cols>
<div class="span8" id="<tag:categoryRows[].cols[].ID />">
	
	<script type="text/javascript">

		var chart;
		
		$(document).ready(function() {
			chart = new Highcharts.Chart({
				credits: {
					text: "Total votes: <tag:categoryRows[].cols[].Total />",
					href: "/voting.php?category=<tag:categoryRows[].cols[].ID />"
				},
				chart: {
					renderTo: '<tag:categoryRows[].cols[].ID />',
					plotBorderWidth: 0,
					reflow: false,
				},
				title: {
					text: "<tag:categoryRows[].cols[].Name />"
				},
				subtitle: {
					text: "<tag:categoryRows[].cols[].Subtitle />"
				},
				tooltip: {
					formatter: function() {
						return '<b>'+ this.point.name +'</b>: '+ this.y + '<br />(' + Highcharts.numberFormat(this.percentage, 2) + '%)';
					}
				},
				plotOptions: {
					pie: {
						dataLabels: {
							enabled: true,
							color: '#000000',
							connectorColor: '#000000',
							formatter: function() {
								return '<b>'+ this.point.name +'</b>: '+ this.y + '<br />(' + Highcharts.numberFormat(this.percentage, 2) + '%)';
							}
						}
					},
				},
				series: [{
					type: 'pie',
					name: null,
					data: [	<tag:categoryRows[].cols[].Data /> ]
				}]
			});
			
			console.log(chart);
		});

		</script>
	
</div>
</loop:categoryRows[].cols>
</div>
</loop:categoryRows>
<else:results>
You aren't allowed to see this at the moment.
</if:results>
</if:votes>

<if:votesTable>
<if:results>

<p>We wanted to make sure that only /v/ was allowed to vote: this is essentially an impossible task, but there are some things we were able to do to remove Reddit (and every other site on the Internet).</p>
<ul>
<li><strong>All:</strong> votes from everybody.</li>
<li><strong>Filtered:</strong> 4chan + NULL. This is the number we used to determine the winners.</li>
<li><strong>4chan:</strong> votes confirmed to come from 4chan</li>
<li><strong>Reddit:</strong> votes confirmed to come from Reddit</li>
<li><strong>NULL:</strong> votes that we didn't have any information on (we have reason to believe it was mostly 4channers)</li>
<li><strong>Other:</strong> votes confirmed to come from some other website</li>
</ul>

<div class="row">
	<div class="span16">
	
	<loop:categories>
	<table class="bordered-table condensed-table span11">
		<tr>
			<th><tag:categories[].Name /></th>
			<th><a href="results.php?votes&site=all">All</a></th>
			<th><a href="results.php?votes&site=filtered">Filtered</a></th>
			<th><a href="results.php?votes&site=4chan">4chan</a></th>
			<th><a href="results.php?votes&site=reddit">Reddit</a></th>
			<th><a href="results.php?votes&site=null">NULL</a></th>
			<th><a href="results.php?votes&site=other">Other</a></th>
		</tr>
		<loop:categories[].Votes>
			<tr>
			<td><tag:categories[].Votes[].ID /></td>
			<td class="span2"><tag:categories[].Votes[].All /></td>
			<td class="span2"><tag:categories[].Votes[].Filtered /></td>
			<td class="span2"><tag:categories[].Votes[].Chan /></td>
			<td class="span2"><tag:categories[].Votes[].Reddit /></td>
			<td class="span2"><tag:categories[].Votes[].Null /></td>
			<td class="span2"><tag:categories[].Votes[].Other /></td>
			</tr>
		</loop:categories[].Votes>
	</table>
	</loop:categories>
	
	</div>
</div>

<else:results>
You aren't allowed to see this at the moment.
</if:results>
</if:votesTable>

</if:denied>