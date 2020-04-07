$(document).ready(function() {
    Plex = {};

    Plex.userMissingFields = {
        schoolNameTimeout: null,        
        professionNameTimeout: null,
        selected_gpa_scale: null,
        current_gpa_scales: [],
        all_countries_data: [],
        all_majors_data: [],
        selected_majors: [],
        selected_countries: [],
    };

    var form = $('#missing-field-form');
    var school_name = $('#school_name');

    form && form.validate({
        submitHandler: function(f) {
            if (validatePhoneIfExists()) {
                if ($('#phone').length > 0 && $('#phone').val().length > 0) {
                    f.elements['phone'].value = $('#phone').intlTelInput('getNumber');
                }

                if ($('#birth_date').length > 0) {
                    var valid_birthday = validateBirthday();
                    if (!valid_birthday) return false;

                    f.elements['birth_date'].value = getBirthday();
                }

                if ($('#major_name').length > 0) {
                    var valid_major = validateMajors();
                    if (!valid_major) return false;

                    f.elements['selected_majors'].value = JSON.stringify(Plex.userMissingFields.selected_majors);
                }

                if ($('#selected_country_name').length > 0) {
                    var valid_selected_country = validateSelectedCountries();

                    if (!valid_selected_country) return false;

                    f.elements['selected_countries'].value = JSON.stringify(Plex.userMissingFields.selected_countries);
                }

                $('.manage-students-ajax-loader').show();

                f.submit();

                return true;
            }
            return false;
          }
    });

    $(document).on('change input', '#school_name', function(event) {
        var collegeInput = $('input[name=in_college]').length > 0 
            ? $('input[name=in_college]') 
            : $('select[name=in_college]');

        var inHighSchool = collegeInput.val() == 0;
        var schools = [];

        clearInterval(Plex.userMissingFields.schoolNameTimeout);

        Plex.userMissingFields.schoolNameTimeout = setTimeout(function() {
            $.ajax({
                type: 'POST',
                url: (inHighSchool ? '/ajax/searchForHighSchools' : '/ajax/searchForColleges'),
                data: { input: $('#school_name').val() },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            }).done(function(response) {
                if (Array.isArray(response)) {
                    schools = response.map(function(school) {
                        return school.school_name;
                    });
                }

                $('#school_name').autocomplete({
                    source: schools,
                }).autocomplete('search');
            });
        }, 1000);
    });

    $(document).on('change input', '#profession_name', function(event) {
        var professions = [];

        clearInterval(Plex.userMissingFields.professionNameTimeout);

        Plex.userMissingFields.professionNameTimeout = setTimeout(function() {
            $.ajax({
                type: 'POST',
                url: '/ajax/searchForProfessions',
                data: { input: $('#profession_name').val() },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            }).done(function(response) {
                if (Array.isArray(response)) {
                    professions = response.map(function(profession) {
                        return profession.profession_name;
                    });
                }

                $('#profession_name').autocomplete({
                    source: professions,
                }).autocomplete('search');
            });
        }, 1000);
    });

    $(document).on('focus', '#school_name, #profession_name, #major_name', function(event) {
        try {
            $(this).autocomplete('search');
        } catch (err) {
            // Do nothing, probably because autocomplete not initialized yet.
        }
    });

    $(document).on('change', '#toggle-test-scores', function(event) {
        var isActive = $(this).is(':checked');
        var scoresSection = $('.scores-bottom-section-container');

        if (isActive) {
            scoresSection.find('input').prop('disabled', 'disabled');
            scoresSection.fadeOut(200);
        } else {
            scoresSection.find('input').prop('disabled', false);
            scoresSection.fadeIn(200);
        }
    });

    function validateBirthday() {
        var is_required = $('#birth_date').data('is_required') == 1;

        var birthday = getBirthday();

        if (!birthday && !is_required) {
            return true;
        } 

        var valid = moment(birthday, 'YYYY-MM-DD').isValid();

        var errorDiv = $('.invalid-bday');

        if (!valid && birthday) {
            errorDiv.show();
        } else {
            errorDiv.hide();
        }

        return valid;
    }

    $(document).on('change input', '.birthday-inputs input', function(event) {
        var valid = $(this).valid();
        var b_month_error = $('.b_month_error');
        var b_day_error = $('.b_day_error');
        var b_year_error = $('.b_year_error');

        // Hide all error messages
        b_month_error.hide();
        b_day_error.hide();
        b_year_error.hide();

        // If not valid, show error message
        if (!valid) {
            switch (event.target.name) {
                case 'b_month':
                    b_month_error.show();
                    break;

                case 'b_day':
                    b_day_error.show();
                    break;

                case 'b_year':
                    b_year_error.show();
                    break;
            }
        }

        validateBirthday();
    });

    function getBirthday() {
        var month = $('input[name="b_month"]').val();
        var day = $('input[name="b_day"]').val();
        var year = $('input[name="b_year"]').val();

        if (!year || !day || !month) { return ''; }

        return year + '-' + month + '-' + day;
    }

    function validatePhoneIfExists() {
        var valid = true;

        if ($('#phone').length > 0) {
            var phone = $('#phone').intlTelInput('getNumber');
            
            if (phone && phone.length > 0) {
               valid = $('#phone').intlTelInput('isValidNumber');

               if (valid) {
                    $('#phone').closest('.form-group').find('.error').hide();
               } else {
                    $('#phone').closest('.form-group').find('.error').show();
               }
            }

            return valid;
        } else {
            return true;
        }
    }

    $(document).on('change input', '#phone', function(event) {
       var valid = $('#phone').intlTelInput('isValidNumber');
       var isRequired = $(this).prop('required');

       if (!isRequired && !$(this).val()) valid = true;
       
       if (valid) {
            $('#phone').closest('.form-group').find('.error').hide();
       } else {
            $('#phone').closest('.form-group').find('.error').show();
       }
    })

    updateStateInput();

    var plain_uid = $('body').data('plain_uid') || '';

    amplitude.getInstance().logEvent('view pass-through page', { user_id: plain_uid });
    
    var changedInputs = [];

    if ($('#birth_date').length > 0) {
        $("#birth_date").datepicker({
          changeMonth: true,
          changeYear: true,
          dateFormat: 'yy-mm-dd',
          yearRange: "c-30:+0",
        });
    }

    if ($('#phone').length > 0) {
        $('#phone').intlTelInput({
            utilsScript: "/js/phoneUtils.js"
        });
    }

    $(document).on('change', '[name=country]', function(event) {
        updateStateInput();
    });

    $(document).on('input', function(event) {
        var plain_uid = $('body').data('plain_uid') || '';

        if (event.target.name) {
                
            var alreadyExists = changedInputs.indexOf(event.target.name) !== -1;

            if (alreadyExists) return;

            amplitude.getInstance().logEvent('pass-through input changed', {Field: event.target.name, user_id: plain_uid});
            
            changedInputs.push(event.target.name);
        }
    });

    $(document).on('click', '.plexuss-link', function(event) {
        var plain_uid = $('body').data('plain_uid') || '';

        amplitude.getInstance().logEvent('pass-through stay on plexuss clicked', { user_id: plain_uid });
    });

    $(document).on('click', 'a.skip', function(event) {
        var plain_uid = $('body').data('plain_uid') || '';

        amplitude.getInstance().logEvent('pass-through skip to ad clicked', { user_id: plain_uid });
    });

    $(document).on('click', '.btn.btn-primary.btn-next', function(event) {
        var plain_uid = $('body').data('plain_uid') || '';
        var form = $('#missing-field-form');

        if (form && form.valid()) {
            amplitude.getInstance().logEvent('pass-through save information clicked', { user_id: plain_uid });
        }
    });

    $(document).on('change input', 'input[name=unconverted_gpa], select[name=unconverted_gpa]', function(event) {
        var value = $(this).val();
        var valid = $(this).valid();
        var converted_gpa_input = $('input[name=converted_gpa]');
        var conversion_loader = $('.conversion-loader');
        var scale = Plex.userMissingFields.selected_gpa_scale;
        
        if (!valid || !scale || !value) return;

        conversion_loader.fadeIn(200);
        converted_gpa_input.addClass('loading');
        $.ajax({
            type: 'GET',
            url: '/ajax/convertToUnitedStatesGPA/' + scale.id + '/' + value + '/' + scale.conversion_type,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).done(function(response) {
            conversion_loader.fadeOut(200);
            converted_gpa_input.removeClass('loading');
            converted_gpa_input.val(parseFloat(response).toFixed(2));
            converted_gpa_input.valid(); // Validate converted_gpa.
        });
    });

    $(document).on('click', '.single-major-remove-button, .single-country-remove-button', function(event) {
        var id = null;
        var selected_list = [];
        var updateFunction = null;

        if ($(this).hasClass('single-major-remove-button')) {
            id = $(this).data('major_id');
            selected_list = Plex.userMissingFields.selected_majors;
            updateFunction = updateSelectedMajorsView;

            Plex.userMissingFields.selected_majors = _.filter(selected_list, function(item) {
                return item.id != id;
            });

        } else if ($(this).hasClass('single-country-remove-button')) {
            id = $(this).data('country_id');
            selected_list = Plex.userMissingFields.selected_countries;
            updateFunction = updateSelectedCountriesView;

            Plex.userMissingFields.selected_countries = _.filter(selected_list, function(item) {
                return item.id != id;
            });
        }

        updateFunction && updateFunction();
    });

    $(document).on('change select', '#selected_country_name', function(event) {
        var selected_list = Plex.userMissingFields.selected_countries;

        var foundCountry = _.find(Plex.userMissingFields.all_countries_data, function(country) {
            return country.id == event.target.value;
        });

        if (foundCountry) {
            var foundSelected = _.findIndex(selected_list, function(country) {
                return country.id == event.target.value;
            });

            // Selected country is not a duplicate, add to list.
            if (foundSelected == -1) {
                Plex.userMissingFields.selected_countries.push(foundCountry);

                updateSelectedCountriesView();
            }
        }
    });

    $(document).on('change select', '#gpa-converter-country', function(event) {
        var noConversionContainer = $('.form-group.no-converter');
        var gradingScalesContainer = $('.form-group.grading-scales-container');
        var conversion_input_container = $('.actual-gpa-converter')

        var country_id = event.target.value;

        if (!country_id) return;

        conversion_input_container.fadeOut(200);

        $.ajax({
            type: 'GET',
            url: '/ajax/getGPAGradingScales/' + country_id,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).done(function(response) {

            if (Array.isArray(response) && !_.isEmpty(response)) {
                noConversionContainer.fadeOut(200);
                gradingScalesContainer.fadeOut(200);
                buildAndShowGradingScales(response);

            } else {
                gradingScalesContainer.fadeOut(200);
                noConversionContainer.fadeIn(200);
            }

        });

    });

    $(document).on('change select', '#gpa-scales-select', function(event) {
        if (event.target.value) {
            buildConversionInputContainer(event.target.value);
        }
    });


    $(document).on('change', '#pre-2016-check', function(event) {
        var checked = $(this).is(':checked');
        var preSATContainer = $('.sat-fields-container.pre-2016');
        var postSATContainer = $('.sat-fields-container.post-2016');

        if (checked) {
            postSATContainer.find('input').prop('disabled', 'disabled');
            preSATContainer.find('input').prop('disabled', false);

            postSATContainer.hide();
            preSATContainer.show();
        } else {
            preSATContainer.find('input').prop('disabled', 'disabled');
            postSATContainer.find('input').prop('disabled', false);

            preSATContainer.hide();
            postSATContainer.show();
        }

    });

    function buildConversionInputContainer(gradingScaleSelection) {
        var current_gpa_scales = Plex.userMissingFields.current_gpa_scales;
        var scale = _.find(current_gpa_scales, function(scale) {
            return scale.id == gradingScaleSelection;
        });

        switch (scale.conversion_type) {
            case 0:
                buildConversionTypeZero(scale);
                break;

            case 1:
                buildConversionTypeOne(scale);
                break;

            default: 
                // Do nothing
        }
    }

    function buildConversionTypeOne(scale) {
        var selected_country = $('#gpa-converter-country');

        var selected_country_id = selected_country.val();

        var all_countries = selected_country.data('all_countries');

        var country_data = _.find(all_countries, function(country) {
            return country.id == selected_country_id;
        });

        var country_code = 'us';

        if (country_data) {
            country_code = country_data.country_code.toLowerCase();
        }

        var conversion_input_container = $('.actual-gpa-converter');
        var conversion_inputs = conversion_input_container.find('.gpa-conversion-inputs');

        if (!scale) return;

        Plex.userMissingFields.selected_gpa_scale = scale;

        conversion_inputs.html('');

        var max = parseFloat(scale.grading_scale_max).toFixed(2);
        var min = parseFloat(scale.grading_scale_min).toFixed(2);

        conversion_inputs.html(
            '<div class="conversion-input-container">' +
                '<span class="flag flag-'+country_code+'"></span>' +
                '<input type="number" placeholder="'+min+' - '+max+'" name="unconverted_gpa" title="Please enter a number between '+min+' and '+max+'" min="'+min+'" max="'+max+'" step="0.01" value="">' +
            '</div>' +
            '<div class="conversion-input-container">' +
                '<span class="flag flag-us"></span>' +
                '<span class="conversion-loader"></span>' +
                '<input type="number" placeholder="0.01 - 5.00" name="converted_gpa" title="Please enter a number between 0.01 and 5.00" min="0.01" max="5.00" step="0.01" value="" required>' +
            '</div>'
        );

        conversion_input_container.fadeIn(200);
    }

    function buildConversionTypeZero(scale) {
        if (!scale) return;

        Plex.userMissingFields.selected_gpa_scale = scale;

        var conversion_input_container = $('.actual-gpa-converter');
        var conversion_inputs = conversion_input_container.find('.gpa-conversion-inputs');
        var selected_country = $('#gpa-converter-country');
        var selected_country_id = selected_country.val();
        var all_countries = selected_country.data('all_countries');
        var country_data = _.find(all_countries, function(country) {
            return country.id == selected_country_id;
        });

        var country_code = 'us';

        if (country_data) {
            country_code = country_data.country_code.toLowerCase();
        }

        var html = '';

        var convertedField =             
            '<div class="conversion-input-container">' +
                '<span class="flag flag-us"></span>' +
                '<span class="conversion-loader"></span>' +
                '<input type="number" placeholder="0.01 - 5.00" name="converted_gpa" title="Please enter a number between 0.01 and 5.00" min="0.01" max="5.00" step="0.01" value="">' +
            '</div>';

        var selectField = 
            '<div class="conversion-input-container">' +
                '<span class="flag flag-'+country_code+'"></span>' +
                '<select name="unconverted_gpa" title="Please select your grade from dropdown" value="" required>';

        scale.options.forEach(function(option) {
            selectField += '<option value="'+option.id+'">'+option.name+'</option>';
        });

        // Close the select field
        selectField += 
                '</select>' +
            '</div>';

        html = (selectField + convertedField);

        conversion_inputs.html(html);

        $('select[name=unconverted_gpa]').change();

        conversion_input_container.fadeIn(200);
    }

    function buildAndShowGradingScales(gradingScales) {
        Plex.userMissingFields.current_gpa_scales = gradingScales;
        var gradingScalesContainer = $('.form-group.grading-scales-container');
        var gradingScalesSelector = gradingScalesContainer.find('select[name=gpa-scales-select]');
        var is_solo = false;

        gradingScalesSelector.html('').prop('required', 'required');

        gradingScalesSelector.append('<option value="">Select a grading scale...</option>');

        gradingScales.forEach(function(scale) {
            if (scale.is_solo == 1) {
                is_solo = true;
            } else {
                gradingScalesSelector.append('<option value="' + scale.id + '">' + scale.name + '</option>');
            }
        });

        if (is_solo) {
            buildConversionInputContainer(gradingScales[0]['id']);
        } else {
            gradingScalesContainer.fadeIn(200);
        }
    }

    // Use the selected countries list to create a new view.
    function updateSelectedCountriesView() {
        var selected_countries = Plex.userMissingFields.selected_countries;
        var countries_view = $('.selected-countries-view');

        if (countries_view.length == 0) { return; }

        countries_view.html('');

        selected_countries.forEach(function(country) {
            countries_view.append(
                "<div class='single-country'>" +
                    "<div class='single-country-remove-button' data-country_id='"+ country.id +"'>&times;</div>" +
                    "<div>"+ country.country_name +"</div>" +
                "</div>");
        });
    }

    function updateStateInput() {
        var country_id = $('[name=country]');
        var usStateInput = $('#state-us');
        var intlStateInput = $('#state-intl');

        if (country_id.length > 0 && country_id.val() == 1) {
            usStateInput.prop('disabled', false);
            usStateInput.show();

            intlStateInput.prop('disabled', true);
            intlStateInput.hide();

        } else {
            intlStateInput.prop('disabled', false);
            intlStateInput.show();

            usStateInput.prop('disabled', true);
            usStateInput.hide();
        }
    }

    function setupMajorAutocomplete() {
        Plex.userMissingFields.all_majors_data = $('#major_name').data('all_majors');

        if (Plex.userMissingFields.all_majors_data && Plex.userMissingFields.all_majors_data.length > 0) {
            var all_majors = Plex.userMissingFields.all_majors_data.map(function(major) {
                return major.name;
            });

            if (all_majors && all_majors.length > 0) {
                $('#major_name').autocomplete({
                    source: all_majors,
                    select: function(event, ui) {
                        var major = _.find(Plex.userMissingFields.all_majors_data, function(major) { return major.name == ui.item.label });

                        var found = _.findIndex(Plex.userMissingFields.selected_majors, function(major) { return major.name == ui.item.label });

                        if (found == -1) {
                            Plex.userMissingFields.selected_majors.push(major);
                            updateSelectedMajorsView();
                        }

                        validateMajors();
                    },
                });
            }
        }
    }

    function updateSelectedMajorsView() {
        var selected_majors = Plex.userMissingFields.selected_majors;
        var majors_view = $('.selected-majors-view');

        if (majors_view.length == 0) { return; }

        majors_view.html('');

        selected_majors.forEach(function(major) {
            majors_view.append(
                "<div class='single-major'>" +
                    "<div class='single-major-remove-button' data-major_id='"+ major.id +"'>&times;</div>" +
                    "<div>"+ major.name +"</div>" +
                "</div>");
        });
    }

    function validateMajors() {
        var selected_majors = Plex.userMissingFields.selected_majors;
        var error_message = $('.major-error');

        var valid = selected_majors.length > 0;
    
        if (valid) {
            error_message.hide();
        } else {
            error_message.show();
        }

        return valid;
    }

    function validateSelectedCountries() {
        var selected_countries = Plex.userMissingFields.selected_countries;
        var error_message = $('.select-countries-error');

        var valid = selected_countries.length > 0;
    
        if (valid) {
            error_message.hide();
        } else {
            error_message.show();
        }

        return valid;
    }

    // Initial load below
    if ($('#major_name').length > 0 && $('#major_name').data('all_majors').length > 0) {
        setupMajorAutocomplete();
    }

    if ($('#selected_country_name').length > 0 && $('#selected_country_name').data('all_countries').length > 0) {
        Plex.userMissingFields.all_countries_data = $('#selected_country_name').data('all_countries');
    }

    if ($('#gpa-converter-country').length > 0 && $('#gpa-converter-country').val()) {
        $('#gpa-converter-country').change(); // Trigger change to get grading scale if set
    }

    if ($('.scores-bottom-section-container').find('div').length == 0) {
        $('.full-scores-bottom-section-container').hide();
    }
});