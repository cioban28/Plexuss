<!doctype html>
<html class="no-js" lang="en">
<head>
    @include('public.headers.header')
</head>
<body class='admin-signup-body'>
<div class="admin-signup-topnav-container clearfix">
    <!-- logo -->
    <div class="admin-signup-logo">
        <a href="#"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/premium_page/plexuss-white.png" alt="Plexuss"/></a>
    </div>

    @if(isset($onboardingInfo) && count($onboardingInfo) > 0)
        @if($onboardingInfo['signup_complete'] == 0)
            <div class="admin-signup-steps-icon">
                <div class='admin-step-icon step-1'>
                    <div class='sprite active'></div>
                    <div class='step-checkmark @if (isset($signed_in) && $signed_in == 1) active  @endif'></div>
                    <div class='step-text @if (!isset($signed_in) || $signed_in == 0) active @endif'>Step 1</div>
                </div>
                <div class='admin-step-icon step-2'>
                    <div class='sprite @if (isset($signed_in) && $signed_in == 1) active  @endif'></div>
                    <div class='step-checkmark'></div>
                    <div class='step-text @if (isset($signed_in) && $signed_in == 1) active  @endif'>Step 2</div>
                </div>
                <div class='admin-step-icon step-3'>
                    <div class='sprite'></div>
                    <div class='step-checkmark'></div>
                    <div class='step-text'>Step 3</div>
                </div>
                <div class='admin-step-icon step-4'>
                    <div class='sprite'></div>
                    <div class='step-checkmark'></div>
                    <div class='step-text'>Step 4</div>
                </div>
            </div>
        @elseif ($onboardingInfo['signup_complete'] == 1)
            <div class="admin-signup-steps-icon">
                <div class='admin-step-icon step-1'>
                    <div class='sprite active'></div>
                    <div class='step-checkmark active'></div>
                    <div class='step-text active'>Step 1</div>
                </div>
                <div class='admin-step-icon step-2'>
                    <div class='sprite active'></div>
                    <div class='step-checkmark active'></div>
                    <div class='step-text active'>Step 2</div>
                </div>
                <div class='admin-step-icon step-3'>
                    <div class='sprite active'></div>
                    <div class='step-checkmark active'></div>
                    <div class='step-text active'>Step 3</div>
                </div>
                <div class='admin-step-icon step-4'>
                    <div class='sprite active'></div>
                    <div class='step-checkmark active'></div>
                    <div class='step-text active'>Step 4</div>
                </div>
            </div>
        @endif
    @else
        <div class="admin-signup-steps-icon">
            <div class='admin-step-icon step-1'>
                <div class='sprite active'></div>
                <div class='step-checkmark @if (isset($signed_in) && $signed_in == 1) active  @endif'></div>
                <div class='step-text @if (!isset($signed_in) || $signed_in == 0) active @endif'>Step 1</div>
            </div>
            <div class='admin-step-icon step-2'>
                <div class='sprite @if (isset($signed_in) && $signed_in == 1) active  @endif'></div>
                <div class='step-checkmark'></div>
                <div class='step-text @if (isset($signed_in) && $signed_in == 1) active  @endif'>Step 2</div>
            </div>
            <div class='admin-step-icon step-3'>
                <div class='sprite'></div>
                <div class='step-checkmark'></div>
                <div class='step-text'>Step 3</div>
            </div>
            <div class='admin-step-icon step-4'>
                <div class='sprite'></div>
                <div class='step-checkmark'></div>
                <div class='step-text'>Step 4</div>
            </div>
        </div>
    @endif

        <div class='invisible-div' data-signed_in={{isset($signed_in) && $signed_in}}></div>
</div>

@yield('content')

@include('private.includes.ajax_loader')

@include('public.footers.footer')
</body>
</html>
