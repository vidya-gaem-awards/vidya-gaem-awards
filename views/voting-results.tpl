<style type="text/css">
.fail {
  color: red;
  text-decoration: line-through;
}
.thumbnail {
  background-color: white;
}
.winner {
  background-color: white;
  height: 70px;
  line-height: 70px;
  text-align: center;
  font-weight: bold;
  font-size: 200%;
  border: 1px solid black;
  margin-bottom: 20px;
}
.allofvnullwithcode {
  border: 2px solid black;
  padding-bottom: 2px;
  background-color: #FFFFE0;
}
.infobox {
  border: 3px solid lightblue;
}
.thumbnails li {
  text-overflow: ellipsis;
  white-space: nowrap;
  overflow-x: hidden;
  list-style-position: inside;
}
.thumbnails ol {
  margin-left: 5px;
}
</style>

<h1>Detailed Voting Results</h1>
<p>Here are the detailed results for the 2013 /v/GAs. In order to determine the winner for each category, we had to split the votes into several filter groups, depending on where the person who voted came from. We include some of the groups here for your amusement.</p>
<p>If all you want is the official results for each category, you can get those on the <a href="/winners">winners page</a>. If you want to see the results in even <em>more</em> detail, you can view the <a href="/voting/results/pairwise">pairwise voting results</a>.</p>
<ul class="breadcrumb">
  <if:all>
  <li class="active">Currently viewing the complete rankings.</li>
  <li><a href="/voting/results">Show the top 5 positions only</a></li>
  <else:all>
  <li class="active">Currently viewing the top 5 nominees for each category only.</li>
    <li><a href="/voting/results/all">Show complete rankings</a></li>
    </if:all>
</ul>

<div class="infobox" style="padding: 10px;">
<h2>Which votes actually counted?</h2>
<p>The final results are taken from the boxes labelled "<strong>All of /v/ + NULL with code</strong>". They have been highlighted below.</p>
<p>What this means: if you clicked on a link directly from a thread on /v/, your vote counted. If you copied the link and pasted it into your address bar, your vote also counted.</p>
<p><strong>In short: if you came directly from /v/, your vote counted. Otherwise it didn't.</strong> If you came from an external site, it <em>definitely</em> didn't.</p>
<p style='color: maroon;'>Out of 258,213 votes, only 95,707 counted. The other 162,506 were ignored.</p>
</div>
  
<loop:categories>
<h2><tag:categories[].Name /> <small><tag:categories[].Subtitle /></small></h2>

<ul class="thumbnails">
  <loop:categories[].Filters>
  <li class="span3">
    <div class="thumbnail <tag:categories[].Filters[].FilterNameSafe />" style="background-color: <tag:categories[].Filters[].Colour />;">
      <strong><tag:categories[].Filters[].FilterName /></strong>
      <span style="color: grey;"><tag:categories[].Filters[].VoteCount />   votes</span>
      <ol>
        <loop:categories[].Filters[].Rankings>
        <li><tag:categories[].Filters[].Rankings[] /></li>
        </loop:categories[].Filters[].Rankings>
      </ol>
    </div>
  </li>
  </loop:categories[].Filters>
</ul>

</loop:categories>
