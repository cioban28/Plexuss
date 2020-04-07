<?php
    $uid_param = isset($plain_uid) ? $plain_uid : 'NULL';
    
    $require_gpa = false;

    if ((!isset($user['in_college']) || $user['in_college'] == 1) && !isset($score['overall_gpa'])) {
        $require_gpa = true;
    } else if (isset($user['in_college']) && $user['in_college'] == 0 && !isset($score['hs_gpa'])) {
        $require_gpa = true;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{$title}}</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="/css/intlTelInput.css">
    <link rel="stylesheet" href="/css/userMissingFields.css?v=1.06">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <!-- Amplitude Analytics snippet -->
    <!--Global variable ... -->
    @if (isset($signed_in) && $signed_in == 1)
    <script>
        var AmplitudeData =  <?php echo json_encode(get_defined_vars()); ?>;   
    </script>
    @endif

    <script src="/js/amplitude.js?v=1.00"></script>
    <!-- end Amplitude -->

    <!-- Google Analytics snippet -->
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', 'UA-26730803-6', 'auto');
        ga('require', 'displayfeatures');
        ga('send', 'pageview');
    </script>
    <!-- end Google Analytics -->

    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window,document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
         fbq('init', '1428934937356789'); 
        fbq('track', 'PageView');
    </script>

    <noscript>
         <img height="1" width="1" 
        src="https://www.facebook.com/tr?id=1428934937356789&ev=PageView
        &noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->

    <!-- Hotjar Tracking Code for https://plexuss.com/ -->
    <script>
        (function(h,o,t,j,a,r){
            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:676403,hjsv:6};
            a=o.getElementsByTagName('head')[0];
            r=o.createElement('script');r.async=1;
            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
            a.appendChild(r);
        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    </script>

</head>

<body background="{{$ad_redirect_campaigns['background']}}" class="company-background" data-plain_uid="{{$plain_uid}}">

    <div class="padding-1"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="logo">
                    <img src="{{$ad_redirect_campaigns['logo']}}" class="logo-img"/>
                    @if ($ad_redirect_campaigns['company'] == 'edx')
                        <p class="logo-content">Qualification Questions</p>
                    @endif
                </div>
            </div>
            <div class="col-md-7">
                <div class="form-back">
                    <div class="para top_message text-center">{!!$ad_redirect_campaigns['top_message']!!}</div>
                    @if (!$errors->isEmpty())
                        <div class='error'>Found some errors when attempting to submit the application, please fix the problems below and submit again.</div>
                        <br />
                    @endif
                    @if(isset($user) || $user != '')
                        <form id="missing-field-form" method="post" action="/saveMissingFields">
                            {{ csrf_field() }}
                            @if($from_united_states && isset($require_gpa) && $require_gpa)
                                    <h4 class='underline'>GPA</h4>
                                    <div class="form-group">
                                        <label for="converted_gpa">GPA</label>
                                        <input type="number" class="form-control" min="0.01" max="5.00" placeholder="0.01 - 5.00" step="0.01" id="converted_gpa" name="converted_gpa" title="GPA must be between 0.01 and 5.00" value="{{old('overall-gpa')}}") required>
                                    </div>
                                    <hr />
                            @endif

                            @if (!$from_united_states && isset($require_gpa) && $require_gpa)
                                <h4 class='underline'>GPA</h4>
                                <div class='gpa-converter'>
                                    <h6 class='gpa-converter-message'>Please enter your current GPA based on your county's grading scale. We will then convert it to a US GPA.</h6>

                                    <label for="gpa-converter-country">Select your country</label>
                                    <select class='form-control' name='gpa-converter-country' id='gpa-converter-country' data-all_countries="{{$countries}}">
                                        <option value="">Select a country...</option>
                                        @foreach($countries as $country)
                                            <option value="{{$country['id']}}" @if(isset($user['country_id']) && $user['country_id'] == $country['id']) selected @endif>{{$country['country_name']}}</option>
                                        @endforeach
                                    </select>

                                    <div class='form-group no-converter hidden'>
                                        <h6 class='no-gpa-converter-message'>Unfortunately, we do not have a converter ready for your selected country.</h6>
                                        <div class="form-group">
                                            <label for="converted_gpa">GPA</label>
                                            <input type="number" class="form-control" min="0.01" max="5.00" placeholder="0.01 - 5.00" step="0.01" id="converted_gpa" name="converted_gpa" title="GPA is required" value="{{old('overall-gpa')}}") required>
                                        </div>
                                    </div>

                                    <div class='form-group grading-scales-container hidden'>
                                        <label for="gpa-scales-select">Select Grading Scale</label>
                                        <select class='form-control' title="Must select a grading scale" name='gpa-scales-select' id='gpa-scales-select'>
                                            <!-- jQuery append here -->                                            
                                        </select>
                                    </div>

                                    <div class='form-group actual-gpa-converter hidden'>
                                        <label for='actual-gpa-converter'>Please enter your GPA</label>
                                        <div class='gpa-conversion-inputs'>
                                            <!-- jQuery append here -->
                                        </div>
                                    </div>
                                </div>
                                <hr />
                            @endif
                            <div class='full-scores-bottom-section-container'>
                                <h4 class='underline'>Scores</h4>
                                <div class='toggle-test-scores-container'>
                                    <input type="checkbox" name="toggle-test-scores" id="toggle-test-scores">
                                    <label class='small-label' for="toggle-test-scores">I have not taken any of the following tests and/or do not wish to provide any scores</label>
                                </div>
                                <div class='scores-bottom-section-container'>
                                    {{-- Start United States Scores --}}
                                    @if (!isset($score) || !isset($score['sat_math']) || !isset($score['sat_reading_writing']) || !isset($score['sat_total']) || !isset($score['sat_reading']) || !isset($score['sat_writing']) )
                                        <div class='form-group'>
                                            <label for="sat">SAT (optional)</label>
                                            <div>
                                                <input type="checkbox" name="pre-2016-check" id="pre-2016-check" @if (isset($score['is_pre_2016_sat']) && $score['is_pre_2016_sat'] == 1) checked @endif>
                                                <label class='small-label' for="pre-2016-check">I took the SAT before 2016</label>
                                            </div>
                                            <div class="sat-fields-container form-control post-2016 @if(isset($score['is_pre_2016_sat']) && $score['is_pre_2016_sat'] == 1) hidden @endif">
                                                <div>
                                                    <label class='small-label' for="sat_math">Math (200-800)</label>
                                                    <input name="sat_math" id="sat_math" placeholder="200 - 800" value="{{$score['sat_math']}}" class="form-control" title="Must be between 200 and 800" pattern="([2-7][0-9]{2}|800)" />
                                                </div>

                                                <div>
                                                    <label class='small-label' for="sat_reading_writing">Read/Write (200-800)</label>
                                                    <input name="sat_reading_writing" id="sat_reading_writing" placeholder="200 - 800" value="{{$score['sat_reading_writing']}}" class="form-control" title="Must be between 200 and 800" pattern="([2-7][0-9]{2}|800)" />
                                                </div>

                                                <div>
                                                    <label class='small-label' for="sat_total">Total (400-1600)</label>
                                                    <input name="sat_total" id="sat_total" placeholder="400 - 1600" value="{{$score['sat_total']}}" class="form-control" title="Must be between 400 and 1600" pattern="([4-8][0-9]{2}|9[0-8][0-9]|99[0-9]|1[0-5][0-9]{2}|1600)" />
                                                </div>
                                            </div>

                                            <div class="sat-fields-container form-control pre-2016 @if(!isset($score['is_pre_2016_sat']) || $score['is_pre_2016_sat'] == 0) hidden @endif">
                                                <div>
                                                    <label class='small-label' for="sat_math">Math (200-800)</label>
                                                    <input name="sat_math" id="sat_math" placeholder="200 - 800" value="{{$score['sat_math']}}" class="form-control" title="Must be between 200 and 800" pattern="([2-7][0-9]{2}|800)" disabled />
                                                </div>

                                                <div>
                                                    <label class='small-label' for="sat_reading">Reading (200-800)</label>
                                                    <input name="sat_reading" id="sat_reading" placeholder="200 - 800" value="{{$score['sat_reading']}}" class="form-control" title="Must be between 200 and 800" pattern="([2-7][0-9]{2}|800)" disabled />
                                                </div>

                                                <div>
                                                    <label class='small-label' for="sat_writing">Writing (200-800)</label>
                                                    <input name="sat_writing" id="sat_writing" placeholder="200 - 800" value="{{$score['sat_writing']}}" class="form-control" title="Must be between 200 and 800" pattern="([2-7][0-9]{2}|800)" disabled />
                                                </div>

                                                <div>
                                                    <label class='small-label' for="sat_total">Total (400-1600)</label>
                                                    <input name="sat_total" id="sat_total" placeholder="400-1600" value="{{$score['sat_total']}}" class="form-control" title="Must be between 400 and 1600" pattern="([4-8][0-9]{2}|9[0-8][0-9]|99[0-9]|1[0-5][0-9]{2}|1600)" disabled />
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if (!isset($score) || !isset($score['act_english']) || !isset($score['act_math']) || !isset($score['act_composite']))
                                        <div class='form-group'>
                                            <label for="act">ACT (optional)</label>
                                            <div class="act-fields-container form-control">
                                                <div>
                                                    <label class='small-label' for="act_english">English (1-36)</label>
                                                    <input name="act_english" id="act_english" placeholder="1 - 36" value="{{$score['act_english']}}" class="form-control" title="Must be between 1 and 36" pattern="([1-9]|[12][0-9]|3[0-6])" />
                                                </div>

                                                <div>
                                                    <label class='small-label' for="act_math">Math (1-36)</label>
                                                    <input name="act_math" id="act_math" placeholder="1 - 36" value="{{$score['act_math']}}" class="form-control" title="Must be between 1 and 36" pattern="([1-9]|[12][0-9]|3[0-6])" />
                                                </div>

                                                <div>
                                                    <label class='small-label' for="act_composite">Composite (1-36)</label>
                                                    <input name="act_composite" id="act_composite" placeholder="1 - 36" value="{{$score['act_composite']}}" class="form-control" title="Must be between 1 and 36" pattern="([1-9]|[12][0-9]|3[0-6])" />
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if (!isset($score) || !isset($score['gre_verbal']) || !isset($score['gre_quantitative']) || !isset($score['gre_analytical']))
                                        <div class='form-group'>
                                            <label for="gre">GRE (optional)</label>
                                            <div class="gre-fields-container form-control">
                                                <div>
                                                    <label class='small-label' for="gre_verbal">Verbal (130-170)</label>
                                                    <input name="gre_verbal" id="gre_verbal" placeholder="130 - 170" value="{{$score['gre_verbal']}}" class="form-control" title="Must be between 130 and 170" pattern="(1[3-6][0-9]|170)" />
                                                </div>

                                                <div>
                                                    <label class='small-label' for="gre_quantitative">Quantitative (130-170)</label>
                                                    <input name="gre_quantitative" id="gre_quantitative" placeholder="130 - 170" value="{{$score['gre_quantitative']}}" class="form-control" title="Must be between 130 and 170" pattern="(1[3-6][0-9]|170)" />
                                                </div>

                                                <div>
                                                    <label class='small-label' for="gre_analytical">Analytical (0-6)</label>
                                                    <input name="gre_analytical" id="gre_analytical" placeholder="0 - 6" value="{{$score['gre_analytical']}}" class="form-control" title="Must be between 0 and 6" pattern="([0-6])" />
                                                </div>
                                            </div>
                                        </div>

                                    @endif

                                    @if (!isset($score) || !isset($score['gmat_total']))
                                        <div class="form-group">
                                            <label for="gmat_total">GMAT Total (optional)</label>
                                            <input class='form-control' type="number" id="gmat_total" placeholder="200 - 800" name="gmat_total" min="200" max="800" title="Must be between 200 and 800" />
                                        </div>
                                    @endif
                                    {{-- End United States Scores --}}

                                    {{-- Start International Scores --}}
                                    @if (!$from_united_states)
                                        @if (!isset($score) || !isset($score['toefl_total']))
                                            <div class="form-group">
                                                <label for="toefl_total">TOEFL Total (optional)</label>
                                                <input class='form-control' type="number" id="toefl_total" placeholder="0 - 90" name="toefl_total" min="0" max="90" title="Must be between 0 and 90" />
                                            </div>
                                        @endif

                                        @if (!isset($score) || !isset($score['toefl_ibt_total']))
                                            <div class="form-group">
                                                <label for="toefl_ibt_total">TOEFL IBT Total (optional)</label>
                                                <input class='form-control' type="number" id="toefl_ibt_total" placeholder="0 - 120" name="toefl_ibt_total" min="0" max="120" title="Must be between 0 and 120" />
                                            </div>
                                        @endif

                                        @if (!isset($score) || !isset($score['toefl_pbt_total']))
                                            <div class="form-group">
                                                <label for="toefl_pbt_total">TOEFL PBT Total (optional)</label>
                                                <input class='form-control' type="number" id="toefl_pbt_total" placeholder="310 - 677" name="toefl_pbt_total" min="310" max="677" title="Must be between 310 and 677" />
                                            </div>
                                        @endif

                                        @if (!isset($score) || !isset($score['ielts_total']))
                                            <div class="form-group">
                                                <label for="ielts_total">IETLS Total (optional)</label>
                                                <input class='form-control' type="number" id="ielts_total" placeholder="0 - 9" name="ielts_total" min="0" max="9" title="Must be between 0 and 9" />
                                            </div>
                                        @endif
                                    @endif
                                    {{-- End International Scores --}}
                                </div>
                            </div>

                            <input type="hidden" name="section" value="scores">
                            @if (isset($user['in_college']))
                                <input type="hidden" name="in_college" value="{{$user['in_college']}}">
                            @endif
                            <input type="hidden" name="ad_passthrough_id" value="{{$ad_passthrough_id}}">
                            <input type="hidden" name="redirect_id" value="{{$ad_redirect_campaigns['id']}}">
                            <input type="hidden" name="company_name" value="{{$ad_redirect_campaigns['company']}}">
                            <input type="hidden" name="utm_campaign" value="{{$utm_campaign}}">
                            <input type="hidden" name="utm_source" value="{{$utm_source}}">
                            <input type="hidden" name="uid" value="{{$uid}}">
                            <input type="hidden" name="uiid" value="{{$uiid}}">

                            <div class="form-group text-center">
                                <input type="submit" class="btn btn-primary btn-next" value="Next">
                            </div>
                        </form>
                    @endif
                    <div class="clearfix"></div>
                    <br/>
                    <div class="text-center">
                        <a href="{{'/passthruIntermission/'.$ad_redirect_campaigns['company'].'/'.$ad_redirect_campaigns['id'].'/'.$ad_passthrough_id.'/'.$uid_param.'/NULL/'.$utm_source.'/skip'}}" class="skip">

                            Skip & Take me to
                            @if (isset($ad_redirect_campaigns['label']))
                                {{$ad_redirect_campaigns['label']}}
                            @else
                                Partner page
                            @endif

                        </a>
                    </div>
                </div>
                <div class="padding-2"></div>
                <div class="text-center">
                    <a class="plexuss-link" href="{{$stay_on_plexuss_link}}">Stay On Plexuss</a>
                </div>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>
    <div class="padding-1"></div>
    <div class="padding-1"></div>
    <div class="padding-1"></div>
    <div class="padding-1"></div>
    <div class="padding-1"></div>
    <div class="padding-1"></div>
    <div class="clearfix"></div>
    @include('private.includes.ajax_loader')
<script src="/js/lodash/lodash.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/additional-methods.min.js"></script>
<script type="text/javascript" src="/daterangepicker/moment.min.js"></script>
<script src="/js/intlTelInput.js?8"></script>
<script src="/js/userMissingFields.js?v=1.04"></script>
</body>
</html>

