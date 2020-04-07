@extends('b2b.master')
@section('b2b-content')
<div class="clearfix"></div>
<div class="resources-plexusspixel-content">
    <div class="resources-plexusspixel-section-left">
        <div class="resources-plexuspixel-title">
            <div class="plexuss-pixel-pic pixel-pic show-on-desktop"></div>
            <div>Setting up a Plexuss pixel</div>
        </div>
        <div class="plexuspixel-objective">Objective:</div>
        <p class="para">Plexuss is a machine learning platform that optimizes conversions. Placing a pixel notifies<br/>
        the machine in real time which students have converted and to target similar cohort of<br/> students on the Plexuss
        network.</p>
        <p class="para">Plexuss logs the IP addresses of users who have been redirected to a partner website.
        <br/>Plexuss needs a way of track the number of users who have been redirected and that have<br/> completed
        a partner form or application. The partner will receive daily reports on all the <br/>clicks and conversions.</p>
        <ol class="padding-1">
            <li class="padding-2"><strong>1.</strong> Receive a pixel from plexuss. This will look like a HTML image tag.</li>
            <li class="padding-2"><strong>2.</strong> A student completes application on your website.</li>
            <li class="padding-2"><strong>3.</strong> The student is redirected "success" or "thank you" page.</li>
            <li class="padding-2"><strong>4.</strong> Within this "success" or "thank you" page, the pixel needs to placed inside the HTML<br/>
                <span class="padding-3">body tag. This way, when the success page has loaded, the pixel is fired. (Note: this </span><br/><span class="padding-3">can't be done on AJAX call, it has to be on page load.)</span></li>
        </ol>
        <div class="more-detail">More details:</div>
        <p class="para">The pixel is not visible, its purpose is to fire the url in the src so that plexuss can match the <br/>
        IP of the user who has been redirected and have now completed the application to <br/>produce an accurate count.</p>
        <div class="padding-5"></div>
        <div class="pixel-btn-box">
            <button class="pixel-button" id="request-pixel">Request Pixel</button>
        </div>
        <div class="padding-5"></div>
        <div class="clearfix"></div>
    </div>

    <div class="resources-plexusspixel-section-right">
        <div class="resources-plexuspixel-title-1">Resources on Pixel Placement</div>
        <div class="plexuss-pixel-box">
            <div class="title-1">Everything You Need To <br/> Know About Facebook Pixel</div>
            <div class="link-div">
                <a class="link" target="_blank" href="https://adespresso.com/blog/facebook-pixel/">https://adespresso.com/blog/facebook-pixel/</a>
            </div>
            <div class="padding-4"></div>
            <div class="title-1">Pixel Tracking</div>
            <div class="link-div">
                <a class="link" target="_blank" href="https://help.tune.com/hasoffers/pixel-tracking/">https://help.tune.com/hasoffers/pixel-tracking/</a>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
</div>
<div class="padding-5"></div>
<div class="clearfix"></div>
<div id="pixel-modal">
    <form class="b2b-form clearfix" data-abide>

        <h2>Plexuss Pixel</h2>
        <div>
            <input id='name' placeholder="Name" pattern="name" type="text" name="joinName" required/>
            <small class="error">Invalid input</small>
        </div>
        <div>
            <input id='title' placeholder="Title" pattern="title" name="joinTitle" type="text" required/>
            <small class="error">Invalid input</small>
        </div>
        <div>
            <input id='institution' placeholder="Institution" pattern="title" name="joinInstitution" type="text" required/>
            <small class="error">Invalid input</small>
        </div>
        <div>
            <input id='phone' placeholder="Phone" pattern="phone" name="joinPhone" type="text" required/>
            <small class="error">Invalid input</small>
        </div>
        <div>
            <input id='email' placeholder="Email" pattern="email" type="text" name="joinEmail" required/>
            <small class="error">Invalid input</small>
        </div>
        <div class="txt-left ma20">

            <input type="checkbox" id="joinBlogNews" name="joinBlogNews" />
            <label for="joinBlogNews">
                <span></span>
                Signup for our Blog and Newsletter
            </label><br>

            <input type="checkbox" id="joinAnalytics" name="joinAnalytics" />
            <label for="joinAnalytics">
                <span></span>
                Receive monthly College Analytics
            </label>

        </div>
        <div class="plexuss-pixel-info-submit">Submit Request</div>
        <div class="b2b-TandC"><a href="/terms-of-service">Terms &amp; Conditions</a></div>
        <small class="mt10 empty-err" style="display: none;">Failed to send. There is invalid Input.</small>
    </form>
</div>
<div id="thank-you-modal">
    <p class="para"><span class="para-2">Thank you for your interest in Plexuss.</span><br/><span class="para-1">We'll be in touch soon.</span></p>
</div>
<div class="clearfix"></div>
@overwrite
