<div class="hero-unit" style="padding-top: 30px; padding-bottom: 30px;">
<h1>Nominations</h1>
</div>

<div class="page-header">
	<h1>Select a category:</h1>
</div>

<div class="row">

	<div class="span8">
		<ul>
		<loop:categoriesOne>
		<li style="
		<if:categoriesOne[].Active>
		font-weight: bold;
		</if:categoriesOne[].Active>
		<if:categoriesOne[].Disabled>
		<!--opacity: 0.3;-->
		</if:categoriesOne[].Disabled>
		">
		<a href="?category=<tag:categoriesOne[].ID />">
		<tag:categoriesOne[].Name /></a> <tag:categoriesOne[].Subtitle />
		</li>
		</loop:categoriesOne>
		</ul>
	</div>

	<div class="span8">
		<ul>
		<loop:categoriesTwo>
		<li style="
		<if:categoriesTwo[].Active>
		font-weight: bold;
		</if:categoriesTwo[].Active>
		<if:categoriesTwo[].Disabled>
		<!--opacity: 0.3;-->
		</if:categoriesTwo[].Disabled>
		">
		<a href="?category=<tag:categoriesTwo[].ID />">
		<tag:categoriesTwo[].Name /></a> <tag:categoriesTwo[].Subtitle /></li>
		</loop:categoriesTwo>
		</ul>
	</div>

</div>

<if:category>
<loop:category>
<div class="page-header">
	<div class="row">
	<div class="span13">
		<h1><tag:category[].Name /> <small><tag:category[].Subtitle /></small></h1>
	</div>
	<div class="span3" style="text-align: right;">
		<br /><a href="/forum-archive/viewtopic.php%3Ff=2&t=<tag:category[].ForumLink />.html">View forum thread</a>
	</div>
	</div>
</div>

<if:category[].Description>
<div class="row" style="font-size: 120%; margin-bottom: 15px;">
	<div class="span16">
		<tag:category[].Description />
	</div>
</div>
</if:category[].Description>

<div class="row">
	<div class="span8">
		<h2>Nominees:</h2>
		<if:category[].Disabled>
		<ul>
		<if:empty>
			<li><em>There are currently no nominations for this category.</em></li>
		<else:empty>
			<loop:nominees>
			<li id="<tag:nominees[].ID />">
				<!-- <span class="thumbs" style="width: 60px;">
					<a href="#" class="thumbs-up <tag:nominees[].Good />" title="Good nomination">&#x2713;</a> -
					<a href="#" class="thumbs-down <tag:nominees[].Bad />" title="Bad nomination">&#x2717;</a>
				</span> -->
				&nbsp;<tag:nominees[].Name />
			</li>
			</loop:nominees>
		</if:empty>
		</ul>
		<else:category[].Disabled>
		<p>This section will be updated when the nominations have been finalised. In the meantime, nominate as many things for this category as you would like using the column on the right: your feedback will go towards deciding the final nominations.</p>
		</if:category[].Disabled>
		<!--
		<if:admin>

		</if:admin>
		-->
	</div>
	
	<div class="span8">
		<h2>Your nominations:</h2>
		<if:loggedIn>
		<ul>
			<loop:selfNominations>
			<li><tag:selfNominations[] /></li>
			</loop:selfNominations>
		</ul>
		
		<if:category[].Enabled>
		<form action="nominations-suggestion.php?category=<tag:category[].ID />" method="POST">
		<p>Add nomination: <input type="text" class="span4" name="selfNomination" /> <input type="submit" class="btn small" value="Submit" /></li></p>
		</form>
		<else:category[].Enabled>
		<p>Nominations are closed for this category.</p>
		</if:category[].Enabled>
		<else:loggedIn>
		<p>You must be signed in to make nominations for awards.</p>
		</if:loggedIn>
	</div>
	
</div>

<script>
$(".thumbs a").click(function(event) {
	event.preventDefault();
	
	<if:loggedIn>
	var category = "<tag:category[].ID />";
	var selected = event.currentTarget;
	var opinion;
	var opposite;
	
	if ($(selected).hasClass("ohyes")) {
		opinion = 0;
		opposite = selected;
	} else if (selected.classList[0] == "thumbs-up") {
		opinion = 1;
		opposite = selected.nextElementSibling;
	} else if (selected.classList[0] == "thumbs-down") {
		opinion = -1;
		opposite = selected.previousElementSibling;
	}
	
	var nominee = selected.parentNode.parentNode.id;
	
	$.post("nomination-feedback.php", { nominee: nominee, category: category, opinion: opinion }, function(data) {
		if (data == "done") {
			$(selected).addClass("ohyes");
			$(opposite).removeClass("ohyes");
		}
	});
	<else:loggedIn>
	alert("You must be logged in to provide feedback on nominations.");
	</if:loggedIn>
	
});
</script>

</loop:category>
</if:category>
