<if:create>

<div class="row">
	<div class="span16">
	<h1>Create a Poll:</h1>
	This is where you can create a new poll for /v/ to vote on. There is no limit to the number of responses that a poll can have, but try not to be ridiculous. Polls must be related to the /v/GAs.
	</div>
</div>
<br /><br />
<form name="create" action="polls.php?create" method="POST" class="form-stacked">
<div class="row">
	<div class="span16">
		<div class="clearfix">
			<label for="question">Poll question:</label>
			<div class="input">
				<input type="text" name="question" id="question" class="span8" maxlength="50" style='font-size: 140%; line-height: 140%;' value='<tag:new-question />'/>
			</div>
		</div>
		<div class="clearfix">
			<label for="description">Description (optional):</label>
			<div class="input">
				<input type="text" name="description" id="description" class="span8" maxlength="50" style='font-size: 140%; line-height: 140%;' value='<tag:new-description />'/>
			</div>
		</div>
		<div class="clearfix">
			<label for="responses">Responses (one per line):</label>
			<div class="input">
				<textarea id="responses" name="responses" class="span8" rows="5"><tag:new-responses /></textarea>
			</div>
		</div>
		<div class="actions">
			<input type="hidden" name="creator" value="<tag:new-creator />" />
			<input type="submit" value="Create Poll" class="btn primary" />
			<a href="polls.php" class="btn">Cancel</a>
		</div>
	</div>
</div>
</form>

<else:create>

	<if:poll>
	
		<script type="text/javascript">

		var chart;
		
		$(document).ready(function() {
			chart = new Highcharts.Chart({
				credits: {
					enabled: false,
				},
				chart: {
					animation: false,
					renderTo: 'graph',
					plotBorderWidth: 0,
					reflow: false,
					height: 296,
					spacingBottom: 0,
					spacingTop: 4,
					spacingLeft: 4,
					spacingRight: 4
				},
				title: null,
				tooltip: false,
				plotOptions: {
					pie: {
						allowPointSelect: false,
						dataLabels: {
							enabled: true,
							color: '#000000',
							connectorColor: '#000000',
							formatter: function() {
								if (this.y == 0) {
									return '';
								} else {
									return '<b>'+ this.point.name +'</b>: '+ this.y + '<br />(' + Highcharts.numberFormat(this.percentage, 2) + '%)';
								}
							}
						}
					},
					series: {
						states: {
							hover: {
								enabled: true
							}
						}
					}
				},
				series: [{
					type: 'pie',
					name: null,
					data: [
						<tag:graphData />
					]
				}]
			});
			
			console.log(chart);
			
			var len = chart.series[0].data.length - 1;
			for (var i = len; i >= 0; i--) {
				if (chart.series[0].data[i].y == 0) {
					chart.series[0].data[i].remove();
				}
			}
			
		});

		</script>

	
		<h1>Poll: <tag:question /></h1>
		<if:description>
		<h3 style='color: #777777; margin-top: -20px;'><tag:description /></h3>
		</if:description>
		<table class="bordered-table zebra-striped inputs-list">
		<if:voted>
			<tr>
				<td id="graph" style="min-height: 300px; padding: 0px; background-color: white;" width='50%' rowspan="<tag:rowspan />"></td>
				<if:canVote>
					<if:preview>
						<td>You have not yet voted on this poll. <a href='?id=<tag:pollid />'>Back to voting page.</a></td>
					<else:preview>
						<td><a href='?id=<tag:pollid />&reset'>Change my vote.</a>
						(Clicking this link will delete your current vote.)</td>
					</if:preview>
				<else:canVote>
					<if:closed>
					<td>This poll is now closed.</td>
					<else:closed>
					<td>You must be signed in to vote.</td>
					</if:closed>
				</if:canVote>
			</tr>
				
			<loop:options>
			<tr>
				<th><tag:options[].text /> (<tag:options[].count />)</th>
			</tr>
			</loop:options>
			
		<else:voted>
			<form name="<tag:pollid />" action="polls.php?id=<tag:pollid />" method="POST">
			<loop:options>
			<tr>
				<td>
					<label for="<tag:options[].id />">
					<input type="radio" value="<tag:options[].id />" id="<tag:options[].id />" name="response">
					<span><tag:options[].text /></span>
				</td>
			</tr>
			</loop:options>
			<tr>
				<td>
					<input type="submit" value="Vote" style="margin-top: 7px;" class="btn primary" />
					<input type="button" value="View Results" onClick="location.href='?id=<tag:pollid />&preview'" class="btn" />
				</td>
			</tr>
			</form>
		</if:voted>
		</table>
		<if:previous>
		<h3>Other Polls</h3>
		<ul>
			<loop:previous>
				<li><a href="polls.php?id=<tag:previous[].id />"><tag:previous[].question /></a></li>
			</loop:previous>
		</ul>
		</if:previous>
		
	<else:poll>
		<h2>Polls:</h2>
		There are currently no polls available to vote on.
	</if:poll>
	
	<if:createLink>
		<h3>New Poll</h3>
		<ul>
			<li><a href="polls.php?create" style='color: green;'>Create a poll</a></li>
		</ul>
	</if:createLink>
	
</if:create>