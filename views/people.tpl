<h1>Cast and Crew of the /v/GAs</h1>

<script type="text/javascript">
$(document).ready(function() 
    { 
        $("#users").tablesorter(); 
    } 
); 
</script>

<div class="row">
    <div class="span16">
        <p>The list of everybody of importance involved in the creation, organization and production of the /v/GAs.</p>
    </div>
</div>

<ul class="breadcrumb">
    <li class="active">Admin tools:
        <span class="divider">/</span>
    </li>
    <li>
        <a href="/people/permissions">View group and permission information</a> 
        <if:CanAddUser><span class="divider">/</span></li>
    <li>
        <a href="/people/add">Add new user</a></if:CanAddUser>
    </li>
</ul>

<if:userNotFound>
<div class="alert alert-error">
<strong>Error:</strong> The specified Steam profile number is either invalid or the user has never logged into the website.
</div>
</if:userNotFound>

<table class="table table-bordered table-striped form-table tablesorter" style="margin-top: 5px;" id="users">
    <thead>
        <tr>
            <th>&nbsp;</th>
            <th>Name</th>
            <th class="span4">Groups</th>
            <th class="span6">Primary Role</th>
        </tr>
    </thead>
    <tbody>
        <loop:users>
        <tr class="<tag:users[].Class />">
            <td style='padding: 0px; width: 32px; height: 32px; margin: 0px;'><a href="http://steamcommunity.com/profiles/<tag:users[].SteamID />"><img src="data:image/png;base64,<tag:users[].Avatar />" /></a></td>
            <td style='white-space: nowrap;"'><a href="/people/<tag:users[].SteamID />"><tag:users[].Name /></a></td>
            <td><tag:users[].DisplayGroups /></a></td>  
            <td><tag:users[].PrimaryRole /></td>
        </tr>
        </loop:users>
        </form>
    </tbody>
        
</table>
