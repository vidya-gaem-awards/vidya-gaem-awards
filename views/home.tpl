<header class="jumbotron masthead">
    <div class="inner">
        <h1>2013 Vidya Gaem Awards</h1>
        <p class="implying">>implying you're opinion is worth shit</p>
    </div>
</header>
<div class="row">
    <div class="span5">
        <h1>News <small><a href="/news">view all</a></small></h1>
        <ul class="news">
            <loop:news>
            <li class="<tag:news[].Class />">
                <if:news[].New><span class="label label-info">New</span></if:news[].New>
                <strong><tag:news[].Date /></strong>: <tag:news[].Text />
            </li>
            </loop:news>
        </ul>
    </div>
    <div class="span6 offset1">
        <h1>Register your interest</h1>
        
        <form method="POST" class="well" action="/volunteer-submission">
                
            <if:formSuccess>
            <div class="alert alert-success">
                <tag:formSuccess />
            </div>
            </if:formSuccess>
            <if:formError>
            <div class="alert alert-error">
                <tag:formError />
            </div>
            <else:formError>
            <!if:APPLICATIONS_OPEN>
            <div class="alert alert-info" style="margin-bottom: 0px;">
        The <tag:YEAR /> /v/GAs are almost over now, so there aren't any roles left to help with. Feel free to come back next year.
      </div>
      </!if:APPLICATIONS_OPEN>
            </if:formError>
        
      <if:APPLICATIONS_OPEN>
            <p>Want to help out with the /v/GAs? The best thing you can is hang out in <a href="steam://friends/joinchat/103582791432684008">Steam chat</a>. If you think you're extra special, you can use this form instead.</p>
            <!--<p>If you want to make a video, see <a href="/videos">this page</a> instead.</p>-->
            <!if:loggedIn>
            <p><strong>You must be logged in to submit this form.</strong></p>
            </!if:loggedIn>
            <hr />
            <label for="name">Name <em>(doesn't have to be your real name)</em></label>
            <input type="text" class="span5" name="name" id="name" maxlength="60" required />
            <label for="email">Email <em>(so we can contact you)</em></label>
            <input type="text" class="span5" name="email" id="email" maxlength="60" required />
            <label for="skills">What would you be interested in doing?</label>
            <input type="text" class="span5" name="skills" id="skills" maxlength="255" required />
            <input type="submit" class="btn btn-primary span3" value="Submit" <!if:loggedIn>disabled</!if:loggedIn> />
            </if:APPLICATIONS_OPEN>
        </form>
    </div>
</div>

<a href="https://plus.google.com/111295979980964577432" rel="publisher">Google+</a>