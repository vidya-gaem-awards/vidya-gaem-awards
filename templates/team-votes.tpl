</div>
<table class="bordered-table zebra-striped condensed-table" style="width: 100%; font-size: 10px; margin-top: -50px;">
<tr>
	<th>&nbsp;</th>
	<loop:users>
	<th style="width: 11%;">User <tag:users[] /></th>
	</loop:users>
</tr>
<loop:categories>
<tr>
	<th><tag:categories[].Name /></th>
	<tag:categories[].HTML />
</tr>
</loop:categories>
</table>
<div>