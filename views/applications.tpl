<h1>Volunteer Applications</h1>

<table class="table table-bordered table-striped form-table tablesorter" style="margin-top: 5px;">
    <thead>
        <tr>
            <th width='30px'>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>What they said</th>
            <th width='100px'>Timestamp</th>
        </tr>
    </thead>
    <tbody>
        <loop:applications>
        <tr>
            <td><tag:applications[].ID /></td>
            <td><a href="http://steamcommunity.com/profiles/<tag:applications[].UserID />"><tag:applications[].Name /></a></td>
            <td><tag:applications[].Email /></a></td>   
            <td><tag:applications[].Interest /></td>
            <td><tag:applications[].Timestamp />
        </tr>
        </loop:applications>
        </form>
    </tbody>
        
</table>