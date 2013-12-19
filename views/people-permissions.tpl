<h1>Group and Permission Information</h1>

<ul class="breadcrumb">
    <li class="active">
        <li><a href="/people">Back to people page</a>
    </li>
</ul>

<div class="row">
    <div class="span12">
        <p>The code this year allows for user groups and access permissions that are significantly easier to use then the ones used previously. There are five main levels of access, from level 1 (the lowest) to level 5 (the highest). Every user gets the permissions from their level and every level below. There are also a few specific groups that only provide access to one or two things which can be assigned if needed, but these are rarely used.</p>
        <p>You are a <strong>level <tag:level /></strong> user.</p>
    </div>
</div>

<h2>This is what you can do:</h2>

<div class="row">
    <div class="span12">
        <ul>
            <loop:permissions>
            <li><tag:permissions[] /></li>
            </loop:permissions>
        </ul>
    </div>
</div>