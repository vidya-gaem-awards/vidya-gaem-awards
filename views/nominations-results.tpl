<header class="jumbotron subhead">
<h1>User Nominations</h1>
</header>

<ul class="breadcrumb">
	<li><a href="/categories">Back to main categories and nominations page</a></li>
</ul>

<loop:categories>
	<h2><tag:categories[].Name /> <small><tag:categories[].Subtitle /></small></h2>
<div class="row">
	<div class="span12">
		<ul>
		<loop:categories[].Nominations>
			<li title="<tag:categories[].Nominations[].Hash />"><tag:categories[].Nominations[].Text /></li>
		</loop:categories[].Nominations>
		</ul>
	</div>
</div>
</loop:categories>