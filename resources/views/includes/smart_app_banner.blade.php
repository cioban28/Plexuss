@if(isset($currentUrlForSmartBanner) && $currentUrlForSmartBanner == '/checkout/premium')
@else
<!-- Start SmartBanner configuration -->
<meta name="smartbanner:title" content="Plexuss College Application">
<meta name="smartbanner:author" content="Plexuss">
<meta name="smartbanner:price" content="FREE">
<meta name="smartbanner:price-suffix-apple" content=" - On the App Store">
<meta name="smartbanner:price-suffix-google" content=" - In Google Play">
<meta name="smartbanner:icon-apple" content="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/plexuss-app-icon-black-green.png">
<meta name="smartbanner:icon-google" content="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/plexuss-app-icon-black-green.png">
<meta name="smartbanner:button" content="VIEW">
<meta name="smartbanner:button-url-apple" content="http://apple.co/2x0hv8I">
<meta name="smartbanner:button-url-google" content="http://bit.ly/2MSG5U7">
<meta name="smartbanner:enabled-platforms" content="android,ios">
<!-- End SmartBanner configuration -->
<link rel="stylesheet" href="/css/smartbanner.min.css">
<script src="/js/smartbanner.min.js"></script>
@endif
