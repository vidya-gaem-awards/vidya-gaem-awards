<div class="hero-unit" style="padding-top: 30px; padding-bottom: 30px;">
<h1>Award Categories</h1>
<p><a href="/forum-archive/viewforum.php%3Ff=3.html">Provide feedback on categories in general in the forum</a></p>
</div>
<div class="row">
	<div class="span3">
		<p style="text-align: center;">What do you think of each category?</p>
	</div>
</div>
<loop:categories>
<div class="row">
	<div class="span3">
		<div class="thumbs" id="<tag:categories[].id />">
			<a href="#" class="thumbs-up btn <tag:categories[].up />">&#x2713;</a> <a href="#" class="thumbs-down btn <tag:categories[].down />">&#x2717;</a>
		</div>
		<if:true>
		<div style="position: relative; background-color: rgba(0,0,0,0.6); color: white; top: -50px; text-align: center;">
			Category voting has finished.
		</div>
		<else:true>
		<!if:loggedIn>
		<div style="position: relative; background-color: rgba(0,0,0,0.6); color: white; top: -50px; text-align: center;">
			You must be signed in to provide feedback.
		</div>
		</!if:loggedIn>
		</if:true>
	</div>
	<div class="span13">
	<div class="page-header">
		<h1><tag:categories[].name /> <small><tag:categories[].subtitle /></small></h1>
		<p>
			<a href="/forum-archive/viewtopic.php%3Ff=2&t=<tag:categories[].forum />.html">Provide feedback on this category in the forum</a> |
			<a href="nominations.php?category=<tag:categories[].id />">See nominations</a></p>
	</div>
</div>
</div>
</loop:categories>

<if:loggedIn><if:false>
<script>
var firstDialog = true;
$(".thumbs a").click(function(event) {
	event.preventDefault();
	
	var selected = event.currentTarget;
	var opinion;
	var opposite;
	
	if ($(selected).hasClass("disabled")) {
		opinion = 0;
		opposite = selected;
	} else if (selected.classList[0] == "thumbs-up") {
		opinion = 1;
		opposite = selected.nextElementSibling;
	} else if (selected.classList[0] == "thumbs-down") {
		opinion = -1;
		opposite = selected.previousElementSibling;
	}
	
	var ID = selected.parentNode.id;
	
	$.post("category-feedback.php", { ID: ID, opinion: opinion }, function(data) {
		if (data == "done") {
			$(selected).addClass("disabled");
			$(opposite).removeClass("disabled");
			if (firstDialog && opinion == -1) {
				alert("It would be great if you could let us know what's wrong with this category. Just follow the blue link underneath the category name.");
				firstDialog = false;
			}
		}
	});
	
});
</script>
</if:false></if:loggedIn>
