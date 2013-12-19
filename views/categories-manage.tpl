<ul class="breadcrumb">
    <li><a href="/categories">Back to the main awards and nominations page</a></li>
</ul>

<header class="jumbotron subhead" style="text-align: center;">
<if:canEdit>
    <h1>Award Manager</h1>
<else:canEdit>
    <h1>Award Information</h1>
</if:canEdit>
</header>

<hr>

<if:editing>
<!-- <div class="page-header">
    <h1>Editing category:</h1>
</div> -->

<if:editFormError>
<div class="alert alert-error">
    <tag:editFormError />
</div>
</if:editFormError>

<form method="POST" action="/categories/manage" class="form-horizontal well" id="categoryForm">
<input type="hidden" name="action" value="edit" />
<div class="row">
        <div class="span6">
            
            <input type="hidden" name="ID" value="<tag:ID />" />
            
            <div class="control-group">
                <label class="control-label" for="input01">ID</label>
                <div class="controls">
                    <input type="text" class="input-large" id="input01" disabled value="<tag:ID />">
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label" for="input02">Name</label>
                <div class="controls">
                    <input type="text" class="input-large" id="input02" required name="Name" value="<tag:Name />">
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label" for="input03">Subtitle</label>
                <div class="controls">
                    <input type="text" class="input-large" id="input03" required name="Subtitle" value="<tag:Subtitle />">
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label" for="input05">Extra Comments</label>
                <div class="controls">
                    <input type="text" class="input-large email" id="input05" name="Comments" value="<tag:Comments />">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="input06"><abbr title="Determines what shows up in the autocomplete box when users are writing in nominations">Autocomplete Group</abbr></label>
                <div class="controls">
                    <select name="AutocompleteCategory" id="input06">
                        <loop:autocompleters>
                        <option value="<tag:autocompleters[].ID />" <tag:autocompleters[].Selected />>
                            <tag:autocompleters[].Name />
                        </option>
                        </loop:autocompleters>
                    </select>
                </div>
            </div>
            
        </div>
        
        <div class="span5">
            
            <div class="control-group">
                <label class="control-label" for="input04"><abbr title="Categories with a lower position will be sorted first">Position number</abbr></label>
                <div class="controls">
                    <input type="text" class="input-small" id="input04" required name="Order" value="<tag:Order />">
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label" for="Enabled">Category enabled?</label>
                <div class="controls">
                    <label class="checkbox">
                        <input type="checkbox" id="Enabled" value="true" name="Enabled" <tag:Enabled />>
                    </label>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label" for="NominationsEnabled">Nominations enabled?</label>
                <div class="controls">
                    <label class="checkbox">
                        <input type="checkbox" id="NominationsEnabled" value="true" name="NominationsEnabled" <tag:NominationsEnabled />>
                    </label>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label" for="Secret"><abbr title="Secret categories will only show up during the voting stage">Secret category?</abbr></label>
                <div class="controls">
                    <label class="checkbox">
                        <input type="checkbox" id="Secret" value="true" name="Secret" <tag:Secret />>
                    </label>
                </div>
            </div>
            
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                    <a href="/categories/manage" class="btn">Cancel</a>
                </div>
            </div>
        </div>
    
    
</div>

</form>

<form method="POST" action="/categories/manage">
    <div class="alert alert-danger">
        <input type="hidden" id="delete" name="delete" value="delete" />
        <button class="btn btn-danger" title="Remove category" type="submit" name="category" value="<tag:ID />">Delete category</button>
    </div>
</form>

</if:editing>

<if:formSuccess>
<div class="alert alert-success">
    <tag:formSuccess />
</div>
</if:formSuccess>

<if:formError>
<div class="alert alert-error">
    <tag:formError />
</div>
</if:formError>

<if:confirmDeletion>
<form method="POST" action="/categories/manage">
<input type="hidden" name="delete" value="delete" />
<input type="hidden" name="category" value="<tag:confirmDeletion />" />
<input type="hidden" name="confirm" value="confirm" />
<div class="alert alert-warning">
    <p>Are you sure you want to delete the category "<tag:confirmDeletion />"?</p>
    <input type="submit" value="Confirm Deletion" class="btn btn-warning" />
</div>
</form>
</if:confirmDeletion>

<style type="text/css">
td input[type=checkbox] {
    margin: 0;
}
td label {
    display: inline;
}
#categories {
    background-color: white;
}
.label {
    display: block;
    text-align: center;
    padding: 13px 0;
}
#categories .aligned {
    text-align: center;
    vertical-align: middle;
}
.monospace {
    font-family: monospace;
}

.sparkbar {
    margin: 4px 0;
    height: 10px;
    overflow: hidden;
}

.sparkbar-yes {
    float: left;
    height: 10px;
    background: #55A54E;
}

.sparkbar-no {
    float: right;
    height: 10px;
    background: #AA4643;
}
</style>

<table class="table table-bordered form-table" id="categories">
    <thead>
        <tr>
            <th style="width: 120px;">Status</th>
            <th>ID</th>
            <th>Name</th>
            <th style="width: 160px;">Feedback</th>
            <th style="width: 60px;">Order</th>
            <if:canEdit><th style="width: 80px;">Controls</th></if:canEdit>
        </tr>
    </thead>
    <tbody>
        <form method="POST" action="/categories/manage">
        <input type="hidden" id="delete" name="delete" value="delete" />
        <loop:cats>
        <tr class="<tag:cats[].Class />">
            <td class="aligned"><tag:cats[].Status /></td>
            <td class="monospace"><tag:cats[].ID /></td>
            <td><tag:cats[].Name /><br><small><tag:cats[].Subtitle /></small></td>
            <td>
                <div class="sparkbar">
                    <div class="sparkbar-yes" style="width: <tag:cats[].Yes />%"></div>
                    <div class="sparkbar-no" style="width: <tag:cats[].No />%"></div>
                </div>
                <tag:cats[].Feedback /> votes
            </td>
            <td class="aligned"><tag:cats[].Order /></td>
            <if:canEdit><td class="aligned">
                <a class="btn" href="/categories/manage/<tag:cats[].ID />" title="Edit category"><i class="icon-pencil"></i> Edit</a>
            </td></if:canEdit>
        </tr>
        </loop:cats>
        </form>
        <if:canEdit>
        <form method="POST" action="/categories/manage">
            <input type="hidden" name="action" value="new" />
            <tr>
                <td>
                    <input type="checkbox" checked name="enabled" id="enabled" /> <label for="enabled">Enabled</label><br>
                    <input type="checkbox" checked name="nominations" id="nominations" /> <label for="nominations">Nominations</label><br>
                    <input type="checkbox" name="secret" id="secret" /> <label for="secret">Secret</label>
                </td>
                <td><input type="text" name="id" id="id" placeholder="ID" style="width: 90%;" maxlength="30" required /></td>
                <td colspan="2">
                    <input type="text" name="name" id="name" placeholder="Name" style="width: 90%;" required />
                    <input type="text" name="subtitle" id="subtitle" placeholder="Subtitle" style="width: 90%;" required />
                </td>
                <td><input type="text" name="order" id="order" placeholder="Order" style="width: 50px;" required /></td>
                <td><input type="submit" class="btn" /></div></td>
            </tr>
        </form>
        </if:canEdit>
    </tbody>
        
</table>

<if:canEdit>
<form method="POST" action="/categories/manage" id="massChangeNominations">
    <div class="alert alert-info">
        <input type="hidden" id="action" name="action" value="massChangeNominations" />
        <button class="btn" type="submit" name="todo" value="open">Open all nominations</button>
        <button class="btn" type="submit" name="todo" value="close">Close all nominations</button>
    </div>
</form>
</if:canEdit>

<!-- <a class="btn" onclick="addCategory();"><i class="icon-plus-sign"></i> Add new category</a> -->

<script>
$("#massChangeNominations").submit(function() {
  event.preventDefault();
  if(confirm("Are you sure you want to fuck shit up?")) {
    $(this).unbind("submit").submit();
  }
});
</script>