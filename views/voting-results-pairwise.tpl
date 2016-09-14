<style type="text/css">
.rotate th {
  font-size: 10px;
}

.pairwise th {
  text-overflow: ellipsis;
  white-space: nowrap;
  overflow: hidden;
}
</style>

<h1>Pairwise Voting Results</h1>

<p>Although the simple "pick one" voting method of last year worked fine and was nice and simple, we felt that we could do better. As such we decided to use preferential voting (specifically, the <a href="https://en.wikipedia.org/wiki/Schulze_method">Schulze method</a>), which allowed us to more accurately determine the winner as well as 2nd, 3rd, etc.</p>

<p>Below is the data we used to calculate the rankings. Each row contains the number of people who preferred that nominee to the nominee in each column. The winner is the nominee that was preferred more than any other nominee.</p>

<loop:categories>
<h2><tag:categories[].Name /> <small><tag:categories[].Subtitle /></small></h2>

<table class="table table-bordered table-hover table-condensed pairwise" style="table-layout: fixed;">
<tag:categories[].Table />
</table>
</loop:categories>
