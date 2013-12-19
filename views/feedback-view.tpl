<style type="text/css">
    .histogram-msg {
        padding-right: 5px;
        text-align: right !important;
        white-space: nowrap;
        width: 80px;
    }
    .bar {
        float: left;
    }
    .bar10 {
        background-color: #88B131;
    }
    .bar9 {
        background-color: #A4CC02;
    }
    .bar8 {
        background-color: #FFCF02;
    }
    .bar7 {
        background-color: #FF9F02;
    }
    .bar6 {
        background-color: #FF6F31;
    }
    .bar-- {
    background-color: silver;
  }
    .bar-inline {
    width: 50px;
    display: inline-block;
    text-align: center;
    font-weight: bold;
  }
    .one-button {
    float: left;
    width: 23.1%;
    margin-left: 20px;
  }
  .row .one-button:first-child {
    margin-left: 30px;
  }
  .text-center {
    text-align: center;
    margin-top: 30px;
    margin-bottom: 15px;
  }
</style>

<div class="row">
    
    <div class="span6">
    <h2>General Feedback <small><tag:generalTotal /> votes</small></h2>
        <table class="table">
            <loop:general>
            <tr>
                <td class="histogram-msg"><tag:general[].rating />/10</td>
                <td>
                    <span class="bar bar<tag:general[].bar />" style="width:<tag:general[].width />px;">&nbsp;</span>&nbsp;
                    <span><tag:general[].count /></span>
                </td>
            </tr>
            </loop:general>
        </table>
        <div class="average-rating average-rating-panel goog-inline-block">
            <div class="average-rating-title">Average rating: <tag:generalAverage /></div>
        </div>
  </div>
    
    <div class="span6">
        
        <h2>Ceremony Feedback <small><tag:ceremonyTotal /> votes</small></h2>
        <table class="table">
            <loop:ceremony>
            <tr>
                <td class="histogram-msg"><tag:ceremony[].rating />/10</td>
                <td>
                    <span class="bar bar<tag:ceremony[].bar />" style="width:<tag:ceremony[].width />px;">&nbsp;</span>&nbsp;
                    <span><tag:ceremony[].count /></span>
                </td>
            </tr>
            </loop:ceremony>
        </table>
        <div class="average-rating average-rating-panel goog-inline-block">
            <div class="average-rating-title">Average rating: <tag:ceremonyAverage /></div>
        </div>
    </div>
    
</div>

<!-- <div class="page-header text-center">
  <h1>Response categories</h1>
</div> -->

<hr>

<div class="row">
  <div class="one-button">
    <a href="/feedback/view/best" class="btn btn-block btn-large">The best parts</a>
  </div>
  <div class="one-button">
    <a href="/feedback/view/worst" class="btn btn-block btn-large">The worst parts</a>
  </div>
  <div class="one-button">
    <a href="/feedback/view/comments" class="btn btn-block btn-large">General comments</a>
  </div>
  <div class="one-button">
    <a href="/feedback/view/questions" class="btn btn-block btn-large">Questions for the /v/GA team</a>
  </div>
</div>

<hr>

<if:header>
<div class="row">
  <div class="span12">
    <h2><tag:header /></h2>
    <ul>
    <tag:output />
    <!-- <loop:items>
    <li><tag:items[].ID />: <tag:items[].Text /></li>
    </loop:items> -->
    </ul>
  </div>
</div>
</if:header>

<if:unique>
<div class="row">
  <div class="span12">
    <h2>Viewing individual response #<tag:feedbackID /></h2>
    <p>Submitted on <strong><tag:submissionDate /></strong></p>
    <p>General feedback rating: <span class="bar<tag:general /> bar-inline"><tag:general /></span></p>
    <p>Ceremony feedback rating: <span class="bar<tag:ceremony /> bar-inline"><tag:ceremony /></span></p>
    <p><strong>Email address:</strong> <tag:email /></p>
    <p><strong>The best parts:</strong> <tag:BestThing /></p>
    <p><strong>The worst parts:</strong> <tag:WorstThing /></p>
    <p><strong>General comments:</strong> <tag:OtherComments /></p>
    <p><strong>Questions:</strong> <tag:Questions /></p>
  </div>
</div>
</if:unique>