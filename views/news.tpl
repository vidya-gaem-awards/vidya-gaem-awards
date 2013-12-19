<header class="jumbotron subhead">
    <h1>/v/GA News</h1>
</header>

<div class="row">
<div class="span12">
<p class="lead">All times are in 4chan time (<tag:timezone />). It is currently <tag:currentTime />.</p>

<div class="row">
<div class="span6">
<loop:news>

<h3><tag:news[].Date /></h3>
<p><tag:news[].Text /></p>

</loop:news>
</div>
</div>