$(document).ready(function(){if(typeof Plex==='undefined')Plex={};Plex.plexussMobileAd={verifyPhoneTimeout:null,};Plex.plexussMobileAd.validatePhone=function(event){var parent=$(event.target).closest('.phone-field'),full_phone=parent.find('.country-code-select').val()+parent.find('.phone-number').val(),valid=true;$.ajax({url:'/phone/validatePhoneNumber',type:'POST',data:{phone:full_phone},headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},}).done(function(response){if(response.error==false){parent.find('.plexuss-mobile-ad-send-sms-button').prop('disabled',false);return;}}).fail(function(){});}
$("select[name='country_code'] option").each(function(){$(this).attr("data-label",$(this).text());});$("select[name='country_code']").on("focus",function(){$(this).find("option").each(function(){$(this).text($(this).attr("data-label"));});}).on("change mouseleave",function(){$(this).focus();$(this).find("option:selected").text($(this).val());$(this).blur();}).on("blur",function(){$(this).find("option:selected").text($(this).val());}).change();$(document).on('change','.plexuss-mobile-ad-sms-container .country-code-select',function(event){var parent=$(event.target).closest('.phone-field'),button=parent.find('.plexuss-mobile-ad-send-sms-button');button.html('<span>Send an SMS</span>');button.prop('disabled',true);Plex.plexussMobileAd.validatePhone(event);});$(document).on('input','.plexuss-mobile-ad-sms-container .phone-number',function(event){var parent=$(event.target).closest('.phone-field'),button=parent.find('.plexuss-mobile-ad-send-sms-button');clearInterval(Plex.plexussMobileAd.verifyPhoneTimeout);button.html('<span>Send an SMS</span>');button.prop('disabled',true);Plex.plexussMobileAd.verifyPhoneTimeout=setTimeout(function(){Plex.plexussMobileAd.validatePhone(event);},500);});$(document).on('click','.plexuss-mobile-ad-sms-container .plexuss-mobile-ad-send-sms-button',function(event){var $_this=$(this),parent=$(this).closest('.phone-field'),full_phone=$(this).siblings('[name="country_code"]').val()+$(this).siblings('[name="phone_number"]').val();$(this).html('<span>Sending...</span>');$(this).prop('disabled',true);$.ajax({url:'/phone/plexussAppSendInvitation',type:'POST',data:{phone:full_phone},headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},}).done(function(response){if(response=='success'){$_this.html('<span>SMS Sent</span>');$_this.prop('disabled',true);}else{parent.siblings('.plexuss-mobile-ad-sms-message').css({color:'#cc0000'}).html(response);$_this.html('<span>Send an SMS</span>');}}).fail(function(error){$_this.html('<span>Send an SMS</span>');});});});