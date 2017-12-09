<h1>Top referrers <tag:data[].Days /></h1>

<ul class="breadcrumb">
  <li class="active">
    Change time period:
  </li>
  <li>
    <a href="?days=1">1 day</a> <span class="divider">/</span>
  </li>
  <li>
    <a href="?days=7">7 days</a> <span class="divider">/</span>
  </li>
  <li>
    <a href="?days=14">14 days</a> <span class="divider">/</span>
  </li>
  <li>
    <a href="?days=30">30 days</a> <span class="divider">/</span>
  </li>
  <li>
    <a href="?days=0">All time</a>
  </li>
</ul>

<p>Internal links, blank referrers, Google searches and sites with less than 5 hits are not included in the tally.</p>

<table class="table table-bordered" style="background-color: white;">
<tr>
  <th class="span2">Total hits</th>
  <th class="span2">Latest hit</th>
  <th class="span2">Link</th>
  <th>Referrer</th>
</tr>
<loop:data>
<tr class="<tag:data[].Class />">
  <td><strong><tag:data[].Count /></strong></td>
  <td><abbr title="<tag:data[].LatestAlt />"><tag:data[].Latest /></abbr></td>
  <td><a href="<tag:data[].Link />"><tag:data[].LinkName /></a></td>
  <td><tag:data[].Refer /></td>
</tr>
</loop:data>
</table>

<!-- <h1>Referrer Information</h1>

<table class="table table-bordered">
<tr>
  <th class="span2">Latest Hit</th>
  <th class="span1">Total Hits</th>
  <th>Referrer</th>
</tr>
<loop:data>
<tr>
  <td><tag:data[].Latest /></td>
  <td><tag:data[].Count /></td>
  <td><tag:data[].Refer /></td>
</tr>
</loop:data>
</table> -->
