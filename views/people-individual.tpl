<div class="page-header">
    <h1>
        <img src="data:image/png;base64,<tag:Avatar />" style='height: 30px; margin-bottom: 5px;' />
        <tag:Name />
        <small><tag:PrimaryRole /></small>
    </h1>
    <p>Back to <a href="/people">Cast and Crew</a></p>
</div>
    
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

<div class="row">

    <if:editing>
    <div class="span12">
    <form method="POST" action="/people/<tag:SteamID />" class="form-horizontal well" id="categoryForm">
        <input type="hidden" name="action" value="edit-details">
        <div class="row">
                <div class="span6">
                    
                    <div class="control-group">
                        <label class="control-label" for="input01">Steam Commmunity ID</label>
                        <div class="controls">
                            <input type="text" class="input-large" id="input01" disabled="" value="<tag:SteamID />">
                        </div>
                    </div>
                    
                    <div class="control-group">
                        <label class="control-label" for="input02">Last Known Name</label>
                        <div class="controls">
                            <input type="text" class="input-large" id="input02" disabled="" value="<tag:Name />">
                        </div>
                    </div>
                    
                    <div class="control-group">
                        <label class="control-label" for="input03">Primary Role</label>
                        <div class="controls">
                            <input type="text" class="input-large" id="input03" name="PrimaryRole" value="<tag:PrimaryRole />">
                        </div>
                    </div>
                    
                    <div class="control-group">
                        <label class="control-label" for="input05">Email Address</label>
                        <div class="controls">
                            <input type="text" class="input-large" id="input05" name="Email" value="<tag:Email />">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="controls">
                            <button type="submit" class="btn btn-primary">Save changes</button>
                            <a href="/people/<tag:SteamID />" class="btn">Cancel</a>
                        </div>
                    </div>
                    
                </div>
                
                <div class="span5">
                    
                    
                    
                </div>
            
            
        </div>

    </form>
    </div>
    
    <else:editing>

    <div class="span5">
        <h2>User Information:</h2>
        
        <ul>
            
            <if:SteamAccount>
                <li>Steam profile: <a href="http://steamcommunity.com/profiles/<tag:SteamID />"><tag:SteamID /></a></li>
                <li>Groups:
                
                    <if:Groups>
                        <tag:DisplayGroups />
                    <else:Groups>
                        <span class="label">None</span>
                    </if:Groups> 
                </li>
            <else:SteamAccount>
                <li><strong>This user does not have a linked Steam account.</strong></li>
            </if:SteamAccount>
            
            <li>Email address: <span id="email">
                <if:Email>
                    <a href="mailto:<tag:Email />"><tag:Email /></a>
                <else:Email>
                    <span class="label">Unavailable</span>
                </if:Email>
            </span></li>
            
            <if:SteamAccount>
                <li>Last logged in on <tag:LastLogin /></li>
            </if:SteamAccount>

        </ul>
        
        <if:CanEditGroups>
        <div id="groups" style="display: none;">
            <h2>Groups: </h2>
            <p>Click on a group name to remove that group.</p>
            <ul>
            
                <if:Groups>
                <form method="POST" action="/people/<tag:SteamID />" style="margin-bottom: 10px;">
                    <li>
                        <loop:Groups>
                            <button class="btn btn-danger" name="RemoveGroup" value="<tag:Groups[] />">
                            <tag:Groups[] />
                            </button>&nbsp;
                        </loop:Groups>
                    </li>
                </form>
                </if:Groups>

                <form method="POST" action="/people/<tag:SteamID />">
                    <li>
                        <input type="text" class="input-small" style='margin-bottom: 0px;' name="GroupName" />
                        <input type="submit" class="btn btn-success" name="AddGroup" value="Add"/>
                    </li>
                </form>
            </ul>
        </div>
        </if:CanEditGroups>
        
        <if:CanEdit>
        <div class="row">
            <div class="span12">
                <a class="btn btn-primary" href="/people/<tag:SteamID />/edit">Edit user information</a>
                <if:CanEditGroups>
                    <a class="btn btn-primary" href="#" onclick="$('#groups').toggle('slow', null);">Edit groups</a>
                </if:CanEditGroups>
            </div>
        </div>
        </if:CanEdit>
        
    </div>
        
    <div class="span7">
        <if:CanEditNotes>
            <h2>Notes: <small><a href="#" id="notes-edit" onclick="$('#notes-button').show();$('#notes').removeAttr('readonly');var teeext = $('#notes').val();$(this).hide();">edit</a></small></h2>
            <form method="POST" action="/people/<tag:SteamID />">
                <input type="hidden" name="action" value="edit-notes" />
                <textarea rows="10" class="span7" id="notes" readonly="readonly" name="Notes"><tag:Notes /></textarea>
                <div id="notes-button" style='display: none;'>
                    <input type="submit" class="btn btn-success" value="Save notes"  />
                    <a href="/people/<tag:SteamID />" class="btn">Cancel</a>
                </div>
            </form>
        <else:CanEditNotes>
            <h2>Notes:</h2>
            <textarea rows="10" class="span7" readonly="readonly"><tag:Notes /></textarea>
        </if:CanEditNotes>
    </div>
    
    </if:editing>
    
</div>