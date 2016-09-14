<if:denied>
You aren't allowed to see this. You may need to sign in first.
<else:denied>
<style type="text/css">
	.histogram-msg {
		padding-right: 5px;
		text-align: right;
		white-space: nowrap;
		width: 80px;
	}
	.bar {
		float: left;
	}
	.bar5 {
		background-color: #88B131;
	}
	.bar4 {
		background-color: #A4CC02;
	}
	.bar3 {
		background-color: #FFCF02;
	}
	.bar2 {
		background-color: #FF9F02;
	}
	.bar1 {
		background-color: #FF6F31;
	}
</style>

<ul>
<li><a href="#comments">Jump to comments</a>
<li><a href="#questions">Jump to questions</a>
</ul>

<div class="row">

	<div class="span8">
		<h1>General Feedback <small><tag:generalTotal /> votes</small></h1>
		<table>
			<loop:general>
			<tr>
				<td class="histogram-msg"><tag:general[].rating />/10</td>
				<td>
					<span class="bar bar<tag:general[].bar />" style="width:<tag:general[].width />px;">&nbsp;</span>&nbsp;
					<span><tag:general[].count /></span>
				</td>
			</tr>
			</loop:general>
		</table>
		<div class="average-rating average-rating-panel goog-inline-block">
			<div class="average-rating-title">Average rating: <tag:generalAverage /></div>
		</div>
	</div>
	
	<div class="span8">
		<h1>Ceremony Feedback <small><tag:ceremonyTotal /> votes</small></h1>
		<table>
			<loop:ceremony>
			<tr>
				<td class="histogram-msg"><tag:ceremony[].rating />/10</td>
				<td>
					<span class="bar bar<tag:ceremony[].bar />" style="width:<tag:ceremony[].width />px;">&nbsp;</span>&nbsp;
					<span><tag:ceremony[].count /></span>
				</td>
			</tr>
			</loop:ceremony>
		</table>
		<div class="average-rating average-rating-panel goog-inline-block">
			<div class="average-rating-title">Average rating: <tag:ceremonyAverage /></div>
		</div>
	</div>
	
</div>

<div class="row" style='margin-top: 20px;'>

	<div class="span8">
		<h1>Best Thing <small><tag:bestCount /> comments</small></h1>		
		<ul>
			<loop:best>
			<li><tag:best[] /></li>
			</loop:best>
		</ul>
	</div>
		
	<div class="span8">
		<h1>Worst Thing <small><tag:worstCount /> comments</small></h1>
		<ul>
			<loop:worst>
			<li><tag:worst[] /></li>
			</loop:worst>
		</ul>
	</div>

</div>

<div class="row" style='margin-top:20px;' id="comments">

	<div class="span16">
		<h1>Other Comments <small><tag:otherCount /> comments</small></h1>
		<ul>
			<loop:other>
			<li><tag:other[] /></li>
			</loop:other>
		</ul>
	</div>
	
</div>

<div class="row" style='margin-top:20px;' id="questions">

	<div class="span16">
		<h1>Questions <small><tag:questionCount /> questions</small></h1>
		<ul>
			<loop:questions>
			<li><tag:questions[] /></li>
			</loop:questions>
		</ul>
	</div>
</div>

</if:denied>