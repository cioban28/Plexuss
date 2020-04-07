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
                        <form id="missing-field-form" method="post" action="/saveMissingFields">
                            {{ csrf_field() }}
                            @if ($user['country_id'] == '')
                                <div class="form-group">
                                    <label for="country">Country</label>
                                    <select class="form-control" name="country" id="country" title="Country is required" required>
                                        <option value="">Select Country</option>
                                        @foreach($countries as $country)
                                            <option value="{{$country['id']}}" @if(old('country') !== null && (old('country') == $country['id'])) selected @endif>{{$country['country_name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            @if ($user['address'] == '')
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input type="text" class="form-control" id="address" name="address" title="Address is required" value="" required>
                                </div>
                            @endif

                            @if ($user['city'] == '')
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" class="form-control" id="city" name="city" title="City is required" value="{{old('city')}}" required>
                                </div>
                            @endif

                            @if ($user['state'] == '')
                                <div class="form-group">
                                    <label for="state">State/Province/Region @if (!isset($from_united_states) || $from_united_states == false) (optional) @endif</label>
                                    <input type="text" class="form-control" id="state-intl" name="state" title="State is required" value="{{old('state')}}">

                                    <select class="form-control" name="state" id="state-us" title="State is required" required>
                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                            <option value="{{$state['state_abbr']}}" @if(old('state') !== null && (old('state') == $state['state_abbr'])) selected @endif>{{$state['state_name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            @if ($user['zip'] == '')
                                <div class="form-group">
                                    <label for="zip">Zip @if (!isset($from_united_states) || $from_united_states == false) (optional) @endif</label>
                                    <input type="text" class="form-control" id="zip" name="zip" title="Zip or postal code is required" value="{{old('zip')}}" @if (isset($from_united_states) && $from_united_states == true) required @endif>
                                </div>
                            @endif

                            @if ($user['phone'] == '' || $user['phone'] == ' ')
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input class='phone form-control' id="phone" name='phone' required>
                                    <div class="error hidden">Phone number is invalid</div>
                                </div>
                            @endif

                            <div class="checkbox-field has-tooltip">
                                <input type="checkbox" name="txt_opt_in" id="txt_opt_in" checked="checked">
                                <label for="txt_opt_in" style="display: inline;">I agree to receive phone calls, pre-recorded messages, and/or text messages about educational services.</label>
                                <span class="tooltiptext">By opting in, I consent to receive phone calls, pre-recorded message and/or text messages about educational services from Plexuss or Plexuss affiliated institutions at the phone number provided, including a wireless number, using automated technology. Submitting this form is required for Plexuss to call or text me, without obligation to attend any institution.</span>
                            </div>

                            @if ($user['country_id'] != '')
                                <input type="hidden" name="country" value="{{$user['country_id']}}">
                            @endif
                            <input type="hidden" name="section" value="contact">
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

