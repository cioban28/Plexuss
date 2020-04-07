'use strict';

var stripe = Stripe(stripeKey);
var elements = stripe.elements();
var COUNTRIES_WITHOUT_POSTCODES = ["Angola", "Antigua and Barbuda", "Aruba", "Bahamas", "Belize", "Benin", "Botswana", "Burkina Faso", "Burundi", "Cameroon", "Central African Republic", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Cote d'Ivoire", "Djibouti", "Dominica", "Equatorial Guinea", "Eritrea", "Fiji", "French Southern Territories", "Gambia", "Ghana", "Grenada", "Guinea", "Guyana", "Hong Kong", "Ireland", "Jamaica", "Kenya", "Kiribati", "Macao", "Malawi", "Mali", "Mauritania", "Mauritius", "Montserrat", "Nauru", "Netherlands Antilles", "Niue", "North Korea", "Panama", "Qatar", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Sao Tome and Principe", "Saudi Arabia", "Seychelles", "Sierra Leone", "Solomon Islands", "Somalia", "South Africa", "Suriname", "Syria", "Tanzania, United Republic of", "Timor-Leste", "Tokelau", "Tonga", "Trinidad and Tobago", "Tuvalu", "Uganda", "United Arab Emirates", "Vanuatu", "Yemen", "Zimbabwe"];
var savedErrors = {};
var count = 0;
var nameError = "";
$('#stripe-wrapper-name').keyup(function(){
  var isChecked = $('#creditCard').prop('checked');
  var value = $('#stripe-wrapper-name').val();
  if(isChecked){
    if(value){
      $('#stripe-wrapper-name').removeClass('invalid-input');
      nameError = "";
      count = 0;
      var i;
      var errorMessages = "";
      for(i in savedErrors){
        if(savedErrors[i] != null && savedErrors[i] != undefined){
          errorMessages += "<span class='dot'></span>" + savedErrors[i] + "<br>";
        }
      }
      $('.message').html(errorMessages);
      if($.isEmptyObject(savedErrors)){
        $('#error-box').addClass('d-none');
      }
    }
    else{
      $('#error-box').removeClass('d-none');
      $('#stripe-wrapper-name').addClass('invalid-input');
      if(count == 0){
        var i;
        var errorMessages = "";
        for(i in savedErrors){
          if(savedErrors[i] != null && savedErrors[i] != undefined){
            errorMessages += "<span class='dot'></span>" + savedErrors[i] + "<br>";
          }
        }
        $('.message').html(errorMessages);
        nameError = "<span class='dot'></span>" + "Name on Card" + "<br>";
        $('.message').append(nameError);
        count = 1;
      }
    }
  }
});
(function() {
  'use strict';

  var elements = stripe.elements({
    fonts: [
      {
        cssSrc: 'https://fonts.googleapis.com/css?family=Source+Code+Pro',
      }
    ]
  });

  // Floating labels
  var inputs = document.querySelectorAll('.cell.checkout.stripe-wrapper .input');
  Array.prototype.forEach.call(inputs, function(input) {
    input.addEventListener('focus', function() {
      input.classList.add('focused');
    });
    input.addEventListener('blur', function() {
      input.classList.remove('focused');
    });
    input.addEventListener('keyup', function() {
      if (input.value.length === 0) {
        input.classList.add('empty');
      } else {
        input.classList.remove('empty');
      }
    });
  });

  var elementStyles = {
    base: {
      color: '#32325D',
      fontWeight: 500,
      fontFamily: 'Source Code Pro, Consolas, Menlo, monospace',
      fontSize: '16px',
      fontSmoothing: 'antialiased',

      '::placeholder': {
        color: '#CFD7DF',
      },
      ':-webkit-autofill': {
        color: '#e39f48',
      },
    },
    invalid: {
      color: '#E25950',

      '::placeholder': {
        color: '#FFCCA5',
      },
    },
  };

  var elementClasses = {
    focus: 'focused',
    empty: 'empty',
    invalid: 'invalid',
  };

  var cardNumber = elements.create('cardNumber', {
    style: elementStyles,
    classes: elementClasses,
  });
  cardNumber.mount('#stripe-wrapper-card-number');

  var cardExpiry = elements.create('cardExpiry', {
    style: elementStyles,
    classes: elementClasses,
  });
  cardExpiry.mount('#stripe-wrapper-card-expiry');

  var cardCvc = elements.create('cardCvc', {
    style: elementStyles,
    classes: elementClasses,
  });
  cardCvc.mount('#stripe-wrapper-card-cvc');
  registerElements([cardNumber, cardExpiry, cardCvc], 'stripe-wrapper');
})();



function registerElements(elements, checkoutName) {
  var formClass = '.' + checkoutName;
  var checkout = document.querySelector(formClass);

  var form = checkout.querySelector('form');
  var error = checkout.querySelector('.error');
  var errorMessage = error.querySelector('.message');

  function enableInputs() {
    Array.prototype.forEach.call(
      form.querySelectorAll(
        "input[type='text'], input[type='email'], input[type='tel']"
      ),
      function(input) {
        input.removeAttribute('disabled');
      }
    );
  }

  function disableInputs() {
    Array.prototype.forEach.call(
      form.querySelectorAll(
        "input[type='text'], input[type='email'], input[type='tel']"
      ),
      function(input) {
        input.setAttribute('disabled', 'true');
      }
    );
  }

  function triggerBrowserValidation() {
    // The only way to trigger HTML5 form validation UI is to fake a user submit
    // event.
    var submit = document.createElement('input');
    submit.type = 'submit';
    submit.style.display = 'none';
    form.appendChild(submit);
    submit.click();
    submit.remove();
  }

  // Listen for errors from each Element, and show error messages in the UI.
  elements.forEach(function(element, idx) {
    element.on('change', function(event) {
      if (event.error) {
        error.classList.remove('d-none');
        savedErrors[idx] = event.error.message;
        var i;
        var errorMessages = "";
        for(i in savedErrors){
          if(savedErrors[i] != null && savedErrors[i] != undefined){
            errorMessages += "<span class='dot'></span>" + savedErrors[i] + "<br>";
          }
        }
        errorMessages += nameError;
        errorMessage.innerHTML = errorMessages;
      } else {
        savedErrors[idx] = null;

        // Loop over the saved errors and find the first one, if any.
        var nextError = Object.keys(savedErrors)
          .sort()
          .reduce(function(maybeFoundError, key) {
            return maybeFoundError || savedErrors[key];
          }, null);

        if (nextError) {
          // Now that they've fixed the current error, show another one.
          var i;
          var errorMessages = "";
          for(i in savedErrors){
            if(savedErrors[i] != null && savedErrors[i] != undefined){
              errorMessages += "<span class='dot'></span>" + savedErrors[i] + "<br>";
            }
          }
          errorMessages += nameError;
          errorMessage.innerHTML = errorMessages;
        } else {
          // The user fixed the last error; no more errors.
          if(!nameError){
            error.classList.add('d-none');
          }else{
            errorMessage.innerHTML = nameError;
          }
        }
      }
    });
  });

  // Listen on the form's 'submit' handler...
  form.addEventListener('submit', function(e) {
    e.preventDefault();

    // Trigger HTML5 validation UI on the form if any of the inputs fail
    // validation.
    var plainInputsValid = true;
    Array.prototype.forEach.call(form.querySelectorAll('input'), function(
      input
    ) {
      if (input.checkValidity && !input.checkValidity()) {
        plainInputsValid = false;
        return;
      }
    });
    if (!plainInputsValid) {
      triggerBrowserValidation();
      return;
    }

    // Show a loading screen...
    checkout.classList.add('submitting');

    // Disable all inputs.
    disableInputs();

    // Gather additional customer data we may have collected in our form.
    var name = form.querySelector('#' + checkoutName + '-name');
    var address1 = form.querySelector('#' + checkoutName + '-address');
    var country = form.querySelector('#' + checkoutName + '-country');
    var zip = form.querySelector('#' + checkoutName + '-zip');
    var additionalData = {
      name: name ? name.value : undefined,
      address_country: country ? country.value : undefined,
      address_zip: zip ? zip.value : undefined,
    };

    // Use Stripe.js to create a token. We only need to pass in one Element
    // from the Element group in order to create a token. We can also pass
    // in the additional customer data we collected in our form.
    stripe.createToken(elements[0], additionalData).then(function(result) {
      // Stop loading!
      checkout.classList.remove('submitting');

      if (result.token) {
        checkout.classList.add('submitted');
      } else {
        // Otherwise, un-disable inputs.
        enableInputs();
      }
    });
  });

}

$("#creditCard").click(function() {
  $(".hiders").show();
  $(".accordion-radio1").removeClass("in-active");
  $(".accordion-radio2").addClass("in-active");
  $(".accordion-radio3").addClass("in-active");

  $("#card-img-holder").removeClass("img-opacity");
  $("#paypal-img-holder").addClass("img-opacity");
  $("#money-img-holder").addClass("img-opacity");
  if (!$.isEmptyObject(savedErrors) || nameError)
  {
    $("#error-box").removeClass("d-none");
  }
});

$("#paypal").click(function() {
  $(".hiders").hide();
  $(".accordion-radio2").removeClass("in-active");
  $(".accordion-radio1").addClass("in-active");
  $(".accordion-radio3").addClass("in-active");
  $("#error-box").addClass("d-none");

  $("#card-img-holder").addClass("img-opacity");
  $("#paypal-img-holder").removeClass("img-opacity");
  $("#money-img-holder").addClass("img-opacity");
});

$("#money").click(function() {
  $(".hiders").hide();
  $(".accordion-radio3").removeClass("in-active");
  $(".accordion-radio1").addClass("in-active");
  $(".accordion-radio2").addClass("in-active");
  $("#error-box").addClass("d-none");

  $("#card-img-holder").addClass("img-opacity");
  $("#paypal-img-holder").addClass("img-opacity");
  $("#money-img-holder").removeClass("img-opacity");
});



$( function() {
  $.widget( "custom.combobox", {
    _create: function() {
      this.wrapper = $( "<span>" )
        .addClass( "custom-combobox" )
        .insertAfter( this.element );

      this.element.hide();
      this._createAutocomplete();
    },

    _createAutocomplete: function() {
      var selected = this.element.children( ":selected" ),
        value = selected.val() ? selected.text() : "";

      this.input = $( "<input>" )
        .appendTo( this.wrapper )
        .val( value )
        .attr( "title", "" )
        .addClass( "custom-combobox-input" )
        .autocomplete({
          delay: 0,
          minLength: 0,
          source: $.proxy( this, "_source" )
        })
        .tooltip({
          classes: {
            "ui-tooltip": "ui-state-highlight"
          }
        });

      this._on( this.input, {
        autocompleteselect: function( event, ui ) {
          ui.item.option.selected = true;
          this._trigger( "select", event, {
            item: ui.item.option
          });
        },

        autocompletechange: "_removeIfInvalid"
      });
    },

    _source: function( request, response ) {
      var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
      response( this.element.children( "option" ).map(function() {
        var text = $( this ).text();
        if ( this.value && ( !request.term || matcher.test(text) ) )
          return {
            label: text,
            value: text,
            option: this
          };
      }) );
    },

    _removeIfInvalid: function( event, ui ) {
      var country = $('#combobox :selected').text();
        if($.inArray(country,COUNTRIES_WITHOUT_POSTCODES) != -1){
          $('#stripe-wrapper-zip').val('00000');
          $("#stripe-wrapper-zip").prop('disabled', true);
          $("#stripe-wrapper-zip").addClass("input-disable");
        }
        else{
          $('#stripe-wrapper-zip').val('');
          $("#stripe-wrapper-zip").prop('disabled', false);
          $("#stripe-wrapper-zip").removeClass("input-disable");
        }
      // Selected an item, nothing to do
      if ( ui.item ) {
        return;
      }

      // Search for a match (case-insensitive)
      var value = this.input.val(),
        valueLowerCase = value.toLowerCase(),
        valid = false;
      this.element.children( "option" ).each(function() {
        if ( $( this ).text().toLowerCase() === valueLowerCase ) {
          this.selected = valid = true;
          return false;
        }
      });

      // Found a match, nothing to do
      if ( valid ) {
        return;
      }

      // Remove invalid value
      this.input
        .val( "" )
        .attr( "title", value + " didn't match any item" )
        .tooltip( "open" );
      this.element.val( "" );
      this._delay(function() {
        this.input.tooltip( "close" ).attr( "title", "" );
      }, 2500 );
      this.input.autocomplete( "instance" ).term = "";
    },

    _destroy: function() {
      this.wrapper.remove();
      this.element.show();
    }
  });

  $( "#combobox" ).combobox();
  $( "#toggle" ).on( "click", function() {
    $( "#combobox" ).toggle();
  });
});
