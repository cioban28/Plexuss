<div class='row main-tab'>
    <div class='large-12 column'>
        <h3 style="text-align:center;">Choose the service you are interested in</h3>
    </div>
</div>
<div class="row main-tab">
    <div class='large-12 column'>
        <div class="btn-box" style="padding-top:70px;">
            <button id="college-app" class="btn btn-primary btn-app">
                Get students to complete <strong>College Application</strong>
            </button>
        </div>
        <div class="btn-box">
            <button id="complete-form" class="btn btn-primary btn-app">
                Get students to complete <strong>Forms</strong>
            </button>
        </div>
        <div class="btn-box">
            <button id="site-click" class="btn btn-primary btn-app">
                Drive students to site via <strong>Clicks</strong>
            </button>
        </div>
        <div class="btn-box">
            <button id="lead" class="btn btn-primary btn-app">
                Post <strong>Leads</strong>
            </button>
        </div>
    </div>
</div>

<input type="hidden" name="rep_company" id="rep_company" value=""/>
<input type="hidden" name="email" id="signup_email" value=""/>
<input type="hidden" name="id" id="signup_id" value=""/>
<input type="hidden" name="service" id="service" value=""/>

<div class="row college-app">
    <div class='row first-tab hidden-div'>
        <div class='large-12 column'>
            <span class="bck"> < back</span>
            <h3 style="text-align:center;">Complete Applications</h3>
        </div>
    </div>
    <div class="row first-tab hidden-div">
        <div class='large-12 column'>
            <div class="dwnl-cont">
                <form>
                    <input type="hidden" value="{{csrf_token()}}" name="_token">
                    <div class="row">
                        <div class="large-2 column"><strong>Step 1:</strong></div>
                        <div class="large-5 column url">Provide a link to your college application:</div>
                        <div class="large-5 column">
                            <input type="text" name="url" id="url" placeholder="Url here" required/>
                            <span class="hide-error url-error"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="large-2 column"><strong>Step 2:</strong></div>
                        <div class="large-5 small-5 column pixel">Place your pixel on your submit page</div>
                        <div class="large-5 small-5 column">
                            <div class="comp"></div>
                            <p class="pixel-1">Auto-generate pixel</p>
                            <div class="copy-btn">
                                <span class="clipboard hide-error">Text Copied</span>
                                <input type="button" class="clip btn copy" value="Copy">
                            </div>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='admin-agreement-checkbox large-12 column step-4'>
                            <input id='admin-agreement-check-step-4' class='agreement-checkbox' type='checkbox' value="inquiry_check"/>
                            <label for='admin-agreement-check-step-4'><b>Check, if you want Plexuss to call every application or inquiry</b></label>
                        </div>
                    </div>
                    <input type="hidden" name="rep_company" id="company-value-1"/>
                    <input type="hidden" name="email" id="email-1"/>
                    <input type="hidden" name="id" id="signup_id_1" value=""/>
                    <input type="hidden" name="service" id="service-1" value=""/>
                    <div class='large-12 column submit-container-1 step-4'>
                        <button class='admin-signup-button step-4'>Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class='row second-tab hidden-div'>
        <div class='large-12 column'>
            <span class="bck"> < back</span>
            <h3 style="text-align:center;">Complete Applications</h3>
        </div>
    </div>
    <div class="row second-tab hidden-div">
        <div class='large-12 column'>
            <div class="dwnl-cont">
                <form>
                    <input type="hidden" value="{{csrf_token()}}" name="_token">
                    <div class="row">
                        <div class="large-2 column"><strong>Step 1:</strong></div>
                        <div class="large-5 column url">Provide a link to your college application:</div>
                        <div class="large-5 column">
                            <input type="text" name="url" id="url" placeholder="Url here" required/>
                            <span class="hide-error url-error"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="large-2 column"><strong>Step 2:</strong></div>
                        <div class="large-5 small-5 column pixel">Place your pixel on your submit page</div>
                        <div class="large-5 small-5 column">
                            <div class="comp"></div>
                            <p class="pixel-1">Auto-generate pixel</p>
                            <div class="copy-btn">
                                <span class="clipboard hide-error">Text Copied</span>
                                <input type="button" class="clip btn copy" value="Copy">
                            </div>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='admin-agreement-checkbox large-12 column step-4'>
                            <input id='admin-agreement-check-step-4' class='agreement-checkbox' type='checkbox' value="inquiry_check"/>
                            <label for='admin-agreement-check-step-4'><b>Check, if you want Plexuss to call every application or inquiry</b></label>
                        </div>
                    </div>
                    <input type="hidden" name="rep_company" id="company-value-2"/>
                    <input type="hidden" name="email" id="email-2"/>
                    <input type="hidden" name="id" id="signup_id_2" value=""/>
                    <input type="hidden" name="service" id="service-2" value=""/>
                    <div class='large-12 column submit-container-1 step-4'>
                        <button class='admin-signup-button step-4'>Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class='row third-tab hidden-div'>
        <div class='large-12 column'>
            <span class="bck"> < back</span>
            <h3 style="text-align:center;">Clicks</h3>
        </div>
    </div>
    <div class="row third-tab hidden-div">
        <div class='large-12 column'>
            <div class="dwnl-cont">
                <form>
                    <input type="hidden" value="{{csrf_token()}}" name="_token">
                    <div class="row">
                        <div class="large-2 column"><strong>Step 1:</strong></div>
                        <div class="large-5 column">Provide a link to your destination:</div>
                        <div class="large-5 column">
                            <input type="text" name="url" id="url" placeholder="Url here" required/>
                        </div>
                        <div class='text-center'>
                            <span class="hide-error url-error"></span>
                        </div>
                    </div>
                    <div class="row">
                        <label>Add notes / instructions</label>
                        <textarea name="note" id="note" required></textarea>
                    </div>
                    <input type="hidden" name="email" id="email-3"/>
                    <input type="hidden" name="id" id="signup_id_3" value=""/>
                    <input type="hidden" name="service" id="service-3" value=""/>
                    <input type="hidden" name="rep_company" id="company-value-3"/>
                    <div class='large-12 column submit-container-1 step-4'>
                        <button class='admin-signup-button step-4'>Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class='row last-tab hidden-div'>
        <div class='large-12 column'>
            <span class="bck"> < back</span>
            <h3 style="text-align:center;">Leads</h3>
        </div>
    </div>
    <div class="row last-tab hidden-div">
        <div class='large-12 column'>
            <div class="dwnl-cont">
                <div class="lead-content">
                    <form>
                        <input type="hidden" value="{{csrf_token()}}" name="_token">
                        <div class="row">
                            <div class="large-12 column">Please select the options that apply to you</div>
                        </div><br/><br/>
                        <div class="row">
                            <input id='admin-agreement-check-1-step-4' class='agreement-checkbox' name="posting_instruction" type='checkbox'/>
                            <label for='admin-agreement-check-1-step-4'><b>Do you have posting instructions? </b></label>
                        </div>
                        <br/>
                        <div class="row">
                            <input id='admin-agreement-check-2-step-4' class='agreement-checkbox' type='checkbox' name="verify_lead"/>
                            <label for='admin-agreement-check-2-step-4'><b>Would you like Plexuss to call and verify some or all of your leads?</b></label>
                        </div>
                        <br/>
                        <div class="row">
                            <p><u>Restrictions may apply</u></p>
                        </div>
                        <input type="hidden" name="email" id="email-4"/>
                        <input type="hidden" name="id" id="signup_id_4" value=""/>
                        <input type="hidden" name="service" id="service-4" value=""/>
                        <input type="hidden" name="rep_company" id="company-value-4"/>
                        <div class='large-12 column submit-container-2 step-4'>
                            <button class='admin-signup-button step-4'>Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
