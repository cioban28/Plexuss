<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

<!-- Files inlcuded in signupin_all:
      <script src="/js/vendor/modernizr.min.js"></script>
      <script src="/js/prod_ready/foundation/foundation.min.js"></script>
      <script src="/js/prod_ready/foundation/foundation.abide.min.js"></script>
-->

<script >
      $(document).ready(function(){
            $(document).on('keyup', 'input[name="month"]', function(e){
                 var val = e.target.value;
                 var dayInput = $('input[name="day"]');

                 if(val.length > 1)
                        dayInput.focus();

            });

            $(document).on('keyup', 'input[name="day"]', function(e){
                 var val = e.target.value;
                 var yearInput = $('input[name="year"]');

                 if(val.length > 1)
                        yearInput.focus();

            });
      });
</script>
<script src="/js/prod_ready/signupin_all.min.js"></script>
<script src="/js/prod_ready/foundation/foundation.tooltip.min.js"></script>

<script>
	$(document).foundation({
    	abide : {
    		patterns: {
    			passwordpattern: /^(?=[^\d_].*?\d)\w(\w|[!@#$%*]){7,20}/,
                  internationalName: /^[^±!@£$%^&*_+#\\/<>?:;|=.,\\(\\)\\{\\}0-9]+$/,
                  name: /^([A-Za-z]+\s*)+$/,
                  phone: /^\s*(?:\+?(\d{1,3}))?[-. (]*(\d{3})[-. )]*(\d{3})[-. ]*(\d{4})(?: *x(\d+))?\s*$/

                  /*  /^\s*(?:\+?(\d{1,3}))?[-. (]*(\d{3})[-. )]*(\d{3})[-. ]*(\d{4})(?: *x(\d+))?\s*$/  */
      		},
      		validators:{
      			monthChecker: function(el, required, parent){
      				var value = $(el).val();
      				if ( $.isNumeric(value) && value <= 12 && value >= 0 ) {
      					$('.datedMonthError').css('display', 'none');
      					clearAgeError();
      					return true;
      				} else{
      					$('.datedMonthError').css({
      						'display': 'inline-block',
      						'margin-bottom': '2px'
      					});
      					setAgeError();
      					return false;
      				};
      			},
      			dayChecker: function(el, required, parent){
      				var value = $(el).val();
      				if ( $.isNumeric(value) && value <= 31 && value >= 1 ) {
      					$('.datedDayError').css('display', 'none');
      					clearAgeError();
      					return true;
      				} else{
      					$('.datedDayError').css({
      						'display': 'inline-block',
      						'margin-bottom': '2px'
      					});
      					setAgeError();
      					return false;
      				};
      			},
      			yearChecker: function(el, required, parent){
      				var value = $(el).val();
      				var currentDate = (new Date).getFullYear();
      				var minAgeAllowed = currentDate - 13;

      				if( !$.isNumeric(value) || value > currentDate || value <=  currentDate - 100  ){
      					$('.datedYearError').css({
      						'display': 'inline-block',
      						'margin-bottom': '2px'
      					});
      					setAgeError();
      					return false;
      				} else if ( value > minAgeAllowed ) {
      					$('.datedUnderAge').css({
      						'display': 'inline-block',
      						'margin-bottom': '2px'
      					});

      					setAgeError();
      					$('.agenotice').css({
      						'color':'#FF7F00',
      						'font-weight':'bold'
      					});
      					return false;
      				} else{
      					$('.datedYearError').css('display', 'none');
      					$('.datedUnderAge').css('display', 'none');
      					clearAgeError();
      					return true;
      				};
      			}
			}
		}
    });
	
	function setAgeError(){
		$('.formDateWrapper').addClass('invalid');
	}

	function clearAgeError(){
		$('.formDateWrapper').removeClass('invalid');
	}

	$('#form input[name="password"]').on('invalid', function () {
		$('.passError').show().css('display', 'inline-block');;
	}).on('valid', function () {
		$('.passError').css('display', 'none');;
	});


      /*************** validate phone number with twilio ********/
      function regValidatePhoneWithTwilio(full_phone){

            $.ajax({
                  url: '/phone/validatePhoneNumber',
                  type: 'POST',
                  data:  {phone: full_phone},
                  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
            }).done(function(data){
                  //if no error validating phone number, hide error msg
                  //else show error message


                  if( data && !data.error ){
                        // console.log('a good number');
                        // console.log($('.phone-wrapper .error'));
                        $('.phone-wrapper .error').hide();
                        // $('.reg-phone').removeAttr('data-invalid');
                        
                  }else{
                        $('.phone-wrapper .error').show();
                        // $('.reg-phone').attr('data-invalid', '');

                  }

            });
            
      };


      ////////////////////
      $(document).ready(function(){


            ////////////
            $(document).on('click', '.country-code-cont', function(e){
                 
                  $('.country-code-dropdown').toggle();

                  if($('.country-code-dropdown').is(':visible')){
                        
                        $(document).one('click', function(e){

                              if( $(e.target).closest('.country-code-dropdown').length == 0 && 
                                    $(e.target).closest('.country-code-cont').length == 0)
                                    $('.country-code-dropdown').hide();
                        });
                  }
            });

            ///////////
            $(document).on('click', '.country-code', function(){
                  var text = $(this).text();
                  var codeTok = text.split('(');
                  var code = codeTok[0];
                  var phone = $('.reg-phone').val().replace(/[-(). ]/g, '');

                  var fullNum = code.replace(/\s/g, '') + ' '  + $('.reg-phone').val().replace(/[-(). ]/g, '');
                   
                  $('.current-code').text(code);
                  $('.hiddenCountryCode').val(code.replace(/\s/g, ''));

                  $('.country-code-dropdown').hide();

                  regValidatePhoneWithTwilio(fullNum);
            });

            ///////////
            $('.reg-phone[name="phone"]').on('keyup', function(){
                  var fullNum = $('.current-code').text().replace(/\s/g, '')  + ' '  +  $('.reg-phone').val().replace(/[-(). ]/g, '');
                  // fullNum = fullNum.replace(/\s/g, '');

                  regValidatePhoneWithTwilio(fullNum);
            });



            /////////////

            $('.signupModal-back').click(function(){

                  $('.signupModal-back').fadeOut();
                  $('.signupModal-wrapper').slideUp(250);
            });


      });



</script>
<script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
      ga('create', 'UA-26730803-6', 'auto');
      ga('require', 'displayfeatures');
      ga('send', 'pageview');
</script>

<script type="text/javascript">
      setTimeout(function(){var a=document.createElement("script");
      var b=document.getElementsByTagName("script")[0];
      a.src=document.location.protocol+"//dnn506yrbagrg.cloudfront.net/pages/scripts/0026/9382.js?"+Math.floor(new Date().getTime()/3600000);
      a.async=true;a.type="text/javascript";b.parentNode.insertBefore(a,b)}, 1);
</script>