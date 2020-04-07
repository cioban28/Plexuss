<!doctype html>
<html class="no-js" lang="en">

  <head>
    @include('private.headers.header')
    @include('includes.facebook_event_tracking')
    @include('includes.hotjar_for_plexuss_domestic')
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/css/get_started/build/css/intlTelInput.css">
    <link rel="stylesheet" href="/css/get_started/build/css/demo.css">
    <link rel="stylesheet" type="text/css" href="/css/get_started/get_started_step9.css">
  </head>

  <body id="{{$currentPage or ''}}" class="stylish-scrollbar-mini">
    <div class="plex-top">
      @if (!isset($country_based_on_ip) || $country_based_on_ip !== 'US')
        <a href="/international-students"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/plex_full_logo.png" alt="Plexuss logo"></a>
      @else
        <a href="/"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/plex_full_logo.png" alt="Plexuss logo"></a>
      @endif
    </div>


    <div id="get_started_step9" class="gs_step">
      <div class="tel-template">
        <dir class="header">
          <img src="/css/get_started/images/ringer.png">
        </dir>
        <div class="main-body">
          <div class="heading">We need at least your phone number to assist you in applying to university...</div>
          <form class="checkmark-form">
            <input id="phone" name="phone" type="tel">
            <span id="error-msg" class="hide"></span>
            <div class="row checkmark-container">
              <div class="columns img-container">
                <img src="/css/get_started/images/WhatsApp_Logo.png">
              </div>
              <label class="columns container">I have whatsApp
                <input type="checkbox" id="whatsapp">
                <span class="checkmark"></span>
              </label>
            </div>
            <div class="next-btn-container">
              <a href="/checkout/premium" onclick="validateNumber();" class="next-btn">Next</a>
            </div>
          </form>
        </div>
      </div>
    </div>

    @include('private.footers.footer')

    <script src="/css/get_started/build/js/intlTelInput.js"></script>
    <script>
      var errorInNumber = '';
      var input = document.querySelector("#phone");
      var errorMsg = document.querySelector("#error-msg");
      var whatsapp = document.querySelector("#whatsapp");
      // here, the index maps to the error code returned from getValidationError - see readme
      var errorMap = [ "Invalid number", "Invalid country code", "Too short", "Too long", "Invalid number"];

      // initialise plugin
      var iti = window.intlTelInput(input, {
        utilsScript: "/css/get_started/build/js/utils.js?1537727621611"
      });

      var reset = function() {
        input.classList.remove("error");
        errorMsg.innerHTML = "";
        errorMsg.classList.add("hide");
      };

      // on blur: validate
      input.addEventListener('blur', function() {
        reset();
        if (input.value.trim()) {
          if (iti.isValidNumber()) {
            errorInNumber = '';
          } else {
            input.classList.add("error");
            var errorCode = iti.getValidationError();
            errorMsg.innerHTML = errorMap[errorCode];
            errorMsg.classList.remove("hide");
            errorInNumber = 'error';
          }
        }
      });
      // on keyup / change flag: reset
      input.addEventListener('change', reset);
      input.addEventListener('keyup', reset);

      function validateNumber(){
        if(errorInNumber == ''){
          var getCode = iti.getSelectedCountryData();
          var countryCode = getCode.dialCode;
          var formData = new FormData( $('form')[0] )
          formData.append('step', '9');
          formData.append('countryCode', countryCode);
          formData.append('whatsapp', whatsapp.checked);
          $.ajax({
              url: '/get_started/save',
              type: 'POST',
              data: formData, 
              enctype: 'multipart/form-data',
              contentType: false,
              processData: false,
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
          }).done(function(data){
            window.location.replace("https://plexuss.com/checkout/premium") 
          });
        }else{
          return false;
        }
      }
    </script>
  </body>
</html>
