<header class="jumbotron subhead">
<h1>Category Vote Feedback</h1>
</header>

<ul class="breadcrumb">
	<li><a href="/categories">Back to main categories and nominations page</a></li>
</ul>

<loop:categoryRows>
<div class="row" style="margin-bottom: 10px;">
<loop:categoryRows[].cols>
<div class="span4" id="<tag:categoryRows[].cols[].ID />">
	
	<script type="text/javascript">

		var chart;
		
		$(document).ready(function() {
			chart = new Highcharts.Chart({
				credits: {
					enabled: false,
				},
				chart: {
				  <tag:categoryRows[].cols[].Disabled />
					renderTo: '<tag:categoryRows[].cols[].ID />',
					plotBorderWidth: 0,
					reflow: false,
				},
				colors: ['#55A54E', '#AA4643'],
				title: {
					text: "<tag:categoryRows[].cols[].Name />",
					style: {
						color: '<tag:categoryRows[].cols[].TitleColour />'
					}
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
							distance: -40,
							shadow: true,
							connectorColor: '#000000',
							formatter: function() {
								return '<b>'+ this.point.name +'</b>: '+ this.y + '<br />(' + Highcharts.numberFormat(this.percentage, 2) + '%)';
							},
							overflow: 'justify'
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

<script src='http://code.highcharts.com/3.0.7/highcharts.js'></script>