<?php 
    $uid_param = isset($plain_uid) ? $plain_uid : 'NULL';
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
                        <?php $blank = false; ?>
                        <form id="missing-field-form" method="post" action="/saveMissingFields">
                        {{ csrf_field() }}
                            @if ($user['fname'] == '') <?php $blank = true; ?>
                                <div class="form-group">
                                    <label for="fname">First Name</label>
                                    <input type="text" class="form-control" id="fname" name="fname" pattern="(([a-zA-Z]+\s*)+){2,}" title="First name is required, must be 2 or more characters and contain only letters" value="{{old('fname')}}" required>
                                </div>
                            @endif
                            @if ($user['lname'] == '') <?php $blank = true; ?>
                                <div class="form-group">
                                    <label for="lname">Last Name</label>
                                    <input type="text" class="form-control" id="lname" name="lname" pattern="(([a-zA-Z]+\s*)+){2,}" title="Last name is required, must be 2 or more characters and contain only letters" value="{{old('lname')}}" required>
                                </div>
                            @endif
                            @if ($user['email'] == '' || $user['email'] == 'none') <?php $blank = true; ?>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" title="Email is required" value="{{old('email')}}" required>
                                    @if ($errors->has('email'))
                                      @foreach($errors->get('email') as $message)
                                            <div class="error">{{$message}}</div>
                                        @endforeach
                                    @endif
                                </div>
                            @endif

                            @if (isset($ad_redirect_campaigns['toggle_country']) && $ad_redirect_campaigns['toggle_country'] != 0)
                                @if ($user['country_id'] == '') <?php $blank = true; ?>
                                    <div class="form-group">
                                        <label for="country">Country @if($ad_redirect_campaigns['toggle_country'] == 2)(optional)@endif</label>
                                        <select class="form-control" name="country" id="country" title="Country is required" @if($ad_redirect_campaigns['toggle_country'] == 1) required @endif>
                                            <option value="">Select Country</option>
                                            @foreach($countries as $country)
                                                <option value="{{$country['id']}}" @if(old('country') !== null && (old('country') == $country['id'])) selected @endif>{{$country['country_name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            @endif

                            @if (isset($ad_redirect_campaigns['toggle_address']) && $ad_redirect_campaigns['toggle_address'] != 0)
                                @if ($user['address'] == '') <?php $blank = true; ?>
                                    <div class="form-group">
                                        <label for="address">Address @if($ad_redirect_campaigns['toggle_address'] == 2)(optional)@endif</label>
                                        <input type="text" class="form-control" id="address" name="address" title="Address is required" value="{{old('address')}}" @if($ad_redirect_campaigns['toggle_address'] == 1) required @endif>
                                    </div>
                                @endif
                            @endif

                            @if (isset($ad_redirect_campaigns['toggle_city']) && $ad_redirect_campaigns['toggle_city'] != 0)
                                @if ($user['city'] == '') <?php $blank = true; ?>
                                    <div class="form-group">
                                        <label for="city">City @if($ad_redirect_campaigns['toggle_city'] == 2)(optional)@endif</label>
                                        <input type="text" class="form-control" id="city" name="city" title="City is required" value="{{old('city')}}" @if($ad_redirect_campaigns['toggle_city'] == 1) required @endif>
                                    </div>
                                @endif
                            @endif

                            @if (isset($ad_redirect_campaigns['toggle_state']) && $ad_redirect_campaigns['toggle_state'] != 0)
                                @if ($user['state'] == '') <?php $blank = true; ?>
                                    <div class="form-group">
                                        <label for="state">State @if($ad_redirect_campaigns['toggle_state'] == 2)(optional)@endif</label>
                                        <input type="text" class="form-control" id="state-intl" name="state" title="State is required" value="{{old('state')}}" @if($ad_redirect_campaigns['toggle_state'] == 1) required @endif>

                                        <select class="form-control" name="state" id="state-us" title="State is required" @if($ad_redirect_campaigns['toggle_state'] == 1) required @endif>
                                            <option value="">Select State</option>
                                            @foreach($states as $state)
                                                <option value="{{$state['state_abbr']}}" @if(old('state') !== null && (old('state') == $state['state_abbr'])) selected @endif>{{$state['state_name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            @endif

                            @if (isset($ad_redirect_campaigns['toggle_zip']) && $ad_redirect_campaigns['toggle_zip'] != 0)
                                @if ($user['zip'] == '') <?php $blank = true; ?>
                                    <div class="form-group">
                                        <label for="zip">Zip @if($ad_redirect_campaigns['toggle_zip'] == 2)(optional)@endif</label>
                                        <input type="text" class="form-control" id="zip" name="zip" title="Zip or postal code is required" value="{{old('zip')}}" @if($ad_redirect_campaigns['toggle_zip'] == 1) required @endif>
                                    </div>
                                @endif
                            @endif

                            @if (isset($ad_redirect_campaigns['toggle_gender']) && $ad_redirect_campaigns['toggle_gender'] != 0)
                                @if ($user['gender'] == '') <?php $blank = true; ?>
                                    <div class="form-group">
                                        <label for="gender">Gender @if($ad_redirect_campaigns['toggle_gender'] == 2)(optional)@endif</label>
                                        <select class="form-control" name="gender" id="gender" title="Gender name is required" @if($ad_redirect_campaigns['toggle_gender'] == 1) required @endif>
                                            <option value="">Select Gender</option>
                                            <option value="m" @if(old('gender') !== null && (old('gender') === 'm')) selected @endif>Male</option>
                                            <option value="f" @if(old('gender') !== null && (old('gender') === 'f')) selected @endif>Female</option>
                                        </select>
                                    </div>
                                @endif
                            @endif

                            @if (isset($ad_redirect_campaigns['toggle_birth_date']) && $ad_redirect_campaigns['toggle_birth_date'] != 0)
                                @if ($user['birth_date'] == '' || $user['birth_date'] == '0000-00-00') <?php $blank = true; ?>
                                    <div class="form-group">
                                        <label for="birth_date">Date Of Birth @if($ad_redirect_campaigns['toggle_birth_date'] == 2)(optional)@endif</label>
                                        <div class='birthday-inputs'>
                                            <input name='b_month' placeholder='MM' title=' ' pattern='(^0?[1-9]$)|(^1[0-2]$)' value='{{old('b_month')}}' @if($ad_redirect_campaigns['toggle_birth_date'] == 1) required @endif />
                                            /
                                            <input name='b_day' placeholder='DD' title=' ' pattern='(^0?[1-9]$)|(^[1-2][0-9]$)|(^[3][0-1]$)' value='{{old('b_day')}}' @if($ad_redirect_campaigns['toggle_birth_date'] == 1) required @endif />
                                            /
                                            <input name='b_year' placeholder='YYYY' title=' ' pattern='[12][0-9]{3}' value='{{old('b_year')}}' @if($ad_redirect_campaigns['toggle_birth_date'] == 1) required @endif />
                                        </div>

                                        <div class="hidden b_month_error error">Invalid month format</div>
                                        <div class="hidden b_day_error error">Invalid day format</div>
                                        <div class="hidden b_year_error error">Invalid year format</div>
                                        <div class="hidden invalid-bday error">Accepted format: MM / DD / YYYY</div>

                                        @if ($errors->has('birth_date'))
                                            @foreach($errors->get('birth_date') as $message)
                                                <div class="error">{{$message}}</div>
                                            @endforeach
                                        @endif

                                        <input id="birth_date" type="hidden" name="birth_date" value="" data-is_required="{{$ad_redirect_campaigns['toggle_birth_date'] == 1}}">
                                    </div>
                                @endif
                            @endif

                            @if (isset($ad_redirect_campaigns['toggle_phone']) && $ad_redirect_campaigns['toggle_phone'] != 0)
                                @if ($user['phone'] == '' || $user['phone'] == ' ') <?php $blank = true; ?>
                                <div class="form-group">
                                    <label for="phone">Phone @if($ad_redirect_campaigns['toggle_phone'] == 2)(optional)@endif</label>
                                    <input class='phone form-control' id="phone" name='phone' @if($ad_redirect_campaigns['toggle_phone'] == 1) required @endif>
                                    <div class="error hidden">Phone number is invalid</div>
                                </div>
                                @endif
                            @endif

                            @if (isset($ad_redirect_campaigns['toggle_gpa']) && $ad_redirect_campaigns['toggle_gpa'] != 0)

                                @if(isset($score))
                                    @if ($user['in_college'] == 1)

                                        @if ($score['overall_gpa'] == '') <?php $blank = true; ?>
                                            <div class="form-group">
                                                <label for="overall-gpa">GPA @if($ad_redirect_campaigns['toggle_gpa'] == 2)(optional)@endif</label>
                                                <input type="number" class="form-control" min="0.01" max="5.00" placeholder="0.01 - 5.00" step="0.01" id="overall-gpa" name="overall-gpa" title="GPA is required" value="{{old('overall-gpa')}}" @if($ad_redirect_campaigns['toggle_phone'] == 1) required @endif>
                                            </div>
                                        @endif
                                    @else
                                        @if ($score['hs_gpa'] == '') <?php $blank = true; ?>
                                            <div class="form-group">
                                                <label for="hs-gpa">GPA</label>
                                                <input type="number" class="form-control" min="0.01" max="5.00" step="0.01" placeholder="0.01 - 5.00" id="hs-gpa" name="hs-gpa" title="GPA is required" value="{{old('hs-gpa')}}" required>
                                            </div>
                                        @endif
                                    @endif
                                @else
                                   @if ($user['in_college'] == 1)
                                        @if ($score['overall_gpa'] == '') <?php $blank = true; ?>
                                            <div class="form-group">
                                                <label for="overall-gpa">GPA</label>
                                                <input type="number" class="form-control" id="overall-gpa" name="overall-gpa" placeholder="0.01 - 5.00" min="0.01" max="5.00" step="0.01" value="" title="GPA is required" required>
                                            </div>
                                        @endif
                                    @else
                                        @if ($score['hs_gpa'] == '') <?php $blank = true; ?>
                                            <div class="form-group">
                                                <label for="hs-gpa">GPA</label>
                                                <input type="number" class="form-control" id="hs-gpa" name="hs-gpa" placeholder="0.01 - 5.00" min="0.01" max="5.00" step="0.01" value="" title="GPA is required" required>
                                            </div>
                                        @endif
                                    @endif

                                @endif
                            @endif
                            <input type="hidden" name="in_college" value="{{$user['in_college']}}">
                            <input type="hidden" name="ad_passthrough_id" value="{{$ad_passthrough_id}}">
                            <input type="hidden" name="redirect_id" value="{{$ad_redirect_campaigns['id']}}">
                            <input type="hidden" name="company_name" value="{{$ad_redirect_campaigns['company']}}">
                            <input type="hidden" name="utm_campaign" value="{{$utm_campaign}}">
                            <input type="hidden" name="utm_source" value="{{$utm_source}}">
                            <input type="hidden" name="uid" value="{{$uid}}">
                            <input type="hidden" name="uiid" value="{{$uiid}}">
                            @if ($user['fname'] != '')
                                <input type="hidden" name="fname" value="{{$user['fname']}}">
                            @endif
                            @if ($user['lname'] != '')
                                <input type="hidden" name="lname" value="{{$user['lname']}}">
                            @endif
                            @if ($user['email'] != '' && $user['email'] != 'none')
                                <input type="hidden" name="email" value="{{$user['email']}}">
                            @endif
                            @if ($user['address'] != '')
                                <input type="hidden" name="address" value="{{$user['address']}}">
                            @endif
                            @if ($user['city'] != '')
                                <input type="hidden" name="city" value="{{$user['city']}}">
                            @endif
                            @if ($user['state'] != '')
                                <input type="hidden" name="state" value="{{$user['state']}}">
                            @endif
                            @if ($user['country_id'] != '')
                                <input type="hidden" name="country" value="{{$user['country_id']}}">
                            @endif
                            @if ($user['gender'] != '')
                                <input type="hidden" name="gender" value="{{$user['gender']}}">
                            @endif
                            @if ($user['birth_date'] != '' && $user['birth_date'] !== '0000-00-00')
                                <input type="hidden" name="birth_date" value="{{$user['birth_date']}}">
                            @endif
                            @if ($user['zip'] != '')
                                <input type="hidden" name="zip" value="{{$user['zip']}}">
                            @endif
                            @if ($user['phone'] != '' && $user['phone'] != ' ')
                                <input type="hidden" name="phone" value="{{$user['phone']}}">
                            @endif

                            <div class="checkbox-field">
                                <input type="checkbox" name="terms_and_policy" id="terms_and_policy" checked="checked" required>
                                <label for="terms_and_policy" style="display: inline;">I agree to the Plexuss <a href='/terms-of-service' target='_blank'>terms of service</a> & <a href='/privacy-policy' target='_blank'>privacy policy</a>.</label>
                            </div>

                            <div class="checkbox-field has-tooltip">
                                <input type="checkbox" name="txt_opt_in" id="txt_opt_in" checked="checked">
                                <label for="txt_opt_in" style="display: inline;">I agree to receive phone calls, pre-recorded messages, and/or text messages about educational services.</label>
                                <span class="tooltiptext">By opting in, I consent to receive phone calls, pre-recorded message and/or text messages about educational services from Plexuss or Plexuss affiliated institutions at the phone number provided, including a wireless number, using automated technology. Submitting this form is required for Plexuss to call or text me, without obligation to attend any institution.</span>
                            </div>

                            @if ($blank == true)
                                <div class="form-group text-center">
                                    <input type="submit" class="btn btn-primary btn-next" value="Next">
                                </div>
                            @endif
                        </form>
                    @endif
                    <div class="clearfix"></div>
                    <br/>
                    @if ($ad_redirect_campaigns['allow_skip'] == 1)
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
                    @endif
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

