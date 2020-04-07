$(document).ready(function() {
    if (typeof Plex === 'undefined') Plex = {};

    Plex.adminSignUps = {
        collegeSelected: null,
        titleTimeout: null,
        nameTimeout: null,
        yearTimeout: null,
        blurbTimeout: null,
        defaultSchoolIcon: "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png",
        defaultSchoolBackground: "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/default-college-page-photo_overview.jpg",
        step_1: $('.admin-step-signup.step-1'),
        step_2: $('.admin-step-signup.step-2'),
        step_3: $('.admin-step-signup.step-3'),
        completed_step: $('.admin-steps-complete'),
        showLoader: function() {
            $('.manage-students-ajax-loader').show();
        },
        hideLoader: function() {
            $('.manage-students-ajax-loader').hide();
        },
        verifyPhoneTimeout: null,
    };

    var steps = [Plex.adminSignUps.step_1, Plex.adminSignUps.step_2, Plex.adminSignUps.step_3, Plex.adminSignUps.completed_step];

    _.each(steps, function(step, index) {
        var stepNumber = (index + 1);
        if (step.is(':visible')) {
            mixpanel.track('admin-view-sign-up-step' + stepNumber, {});

            if (stepNumber === 4) {
                mixpanel.track('admin-view-sign-up-waiting-approval', {});
            }
        }
    });

    Plex.adminSignUps.previewIMGUpload = function(img_input) {
        if (img_input.files && img_input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('.dynamic-profile-preview-container .user-image-icon').css('background-image', 'url(' + e.target.result + ')');
            }

            reader.readAsDataURL(img_input.files[0]);
        }
    }

    Plex.adminSignUps.validateForm = function(form, submit_clicked) {
        var valid = true,
            required_inputs = form.find('[required]');

        required_inputs.each(function() {
            // Verify no inputs are invalid
            if ($('.error').is(':visible') && !$('.error').closest('.alert.alert-danger').length) {
                if (submit_clicked) { $('.error').siblings('input').focus(); }
                valid = false;
            }

            // Verify all inputs have values
            if ($(this).val() === '' || ( $(this).val() == 'on' && !$(this).is(':checked') )) {
                if (submit_clicked) { $(this).focus(); }

                valid = false;
                return false;
            }
        });

        return valid;
    }

    Plex.adminSignUps.validateHours = function(select_input) {
        var open_select = $('.hours-select.open-hour'),
            close_select = $('.hours-select.close-hour'),
            open = open_select.val(),
            close = close_select.val(),

            moment_open = moment(open, ["h:mm A"]),
            moment_close = moment(close, ["h:mm A"]),

            type = select_input.hasClass('open-hour') ? 'open' : 'close';

        if (moment_open >= moment_close) {
            $('.day.active').data('hours')[type] = 'choose';
            select_input.val('choose');
            return false;
        }

        return true;
    }

    Plex.adminSignUps.validatePhone = function() {
        var full_phone = $('#country-code-select').val() + $('#phone-number').val(),
            valid = true;

        $.ajax({
            url: '/phone/validatePhoneNumber',
            type: 'POST',
            data: {phone: full_phone},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).done(function(response) {
            if (response.error == false) {
                $('.phone-error-msg.error').fadeOut(200);
                return;
            }
            $('.phone-error-msg.error').fadeIn(200);
        }).fail(function() {
            $('.phone-error-msg.error').fadeIn(200);
        });
    }

    Plex.adminSignUps.validateNewCollegeEntry = function() {
        var valid = true,
            parent = $('#add-new-college-container'),
            inputs = ['name', 'country', 'city', 'state'];
        
        _.each(inputs, input => {
            var element = parent.find('[name=new_college_' + input + ']');
            if (!element.val()) {
                element.focus();
                valid = false;
                return false;
            }
        });

        return valid;
    }

    Plex.adminSignUps.validateCollegeSelection = function() {
        var college = Plex.adminSignUps.selectedCollege,
            valid = true;

        if (!college || !college.state || !college.city || !college.school_name) {
            valid = false;
            $('#rep_college_search').focus();
        }

        return valid;
    }

    $.ui.autocomplete.prototype._resizeMenu = function() {
        var ul = this.menu.element;
        ul.outerWidth(this.element.outerWidth());
    }

    // Initial load
 
    $('#rep_selected_college').fadeOut(200);
 
    $('.datepicker').datepicker({
        constrainInput: false,
        changeMonth: true,
        changeYear: true,
        maxDate: moment().format('MM/DD/YYYY'),
    });
    
    $('.admin-step-signup.step-2 .datepicker').val(moment().format('MM/DD/YYYY'));
    
    $(document).foundation('tooltip', 'reflow');

    $("select[name='country_code'] option").each(function() {
        $(this).attr("data-label", $(this).text());
    });

    // End Initial load

    $("select[name='country_code']").on("focus", function() {
        $(this).find("option").each(function() {
            $(this).text($(this).attr("data-label"));
        });
    }).on("change mouseleave", function() {
        $(this).focus();
        $(this).find("option:selected").text($(this).val());
        $(this).blur();
    }).on("blur", function() {
        $(this).find("option:selected").text($(this).val());
    }).change();

    $(document).on('click', '.admin-signup-button.step-1', function(event) {
        event.preventDefault();

        var form = $(this).closest('form'),
            valid = Plex.adminSignUps.validateForm(form, true),
            formData = null;

        if (valid) {
            formData = new FormData(form[0]);

            Plex.adminSignUps.showLoader();

            $.ajax({
                url: '/postAdminSignup',
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            }).done(function(response){
                if (response.status == 'success') {
                    Plex.adminSignUps.step_1.fadeOut(0);
                    Plex.adminSignUps.step_3.fadeOut(0);

                    $('.admin-signup-steps-icon .step-text.active').removeClass('active');
                    $('.admin-signup-steps-icon .admin-step-icon.step-1 > .step-checkmark').addClass('active');

                    $('.admin-signup-steps-icon .admin-step-icon.step-2 > .sprite').addClass('active');
                    $('.admin-signup-steps-icon .admin-step-icon.step-2 > .step-text').addClass('active');

                    $('.user-details-container .user-name').html(response.fname + ' ' + response.lname);
                    $('#rep_fname').val(response.fname);
                    $('#rep_lname').val(response.lname);
                    $('#rep_email').val(response.email);

                    Plex.adminSignUps.step_2.fadeIn(200);

                    if (!response.email.endsWith('.edu')) {
                        mixpanel.track('admin-sign-up-non-edu', { Email: response.email });
                    }

                    mixpanel.track('admin-view-sign-up-step2', {});

                } else {
                    $('.admin-step-signup.step-1').html(response);
                    if (response.includes('<li class="error">The email has already been taken.</li>')) {
                        mixpanel.track('admin-sign-up-preexisting', { Email: $('#email').val() });
                    }
                }
                
                Plex.adminSignUps.hideLoader();
            });
        }
    });

    $(document).on('click', '.admin-signup-button.step-2', function(event) {
        event.preventDefault();
        var form = $(this).closest('form'),
            valid = Plex.adminSignUps.validateForm(form, true);

        if (valid) {
            Plex.adminSignUps.step_1.fadeOut(0);
            Plex.adminSignUps.step_2.fadeOut(0);

            $('.admin-signup-steps-icon .step-text.active').removeClass('active');
            $('.admin-signup-steps-icon .admin-step-icon.step-2 > .step-checkmark').addClass('active');

            $('.admin-signup-steps-icon .admin-step-icon.step-3 > .sprite').addClass('active');
            $('.admin-signup-steps-icon .admin-step-icon.step-3 > .step-text').addClass('active');
            
            Plex.adminSignUps.step_3.fadeIn(200);
            mixpanel.track('admin-view-sign-up-step3', {});
        }
    });

    $(document).on('click', '.admin-signup-button.step-3', function(event) {
        event.preventDefault();

        var form = $(this).closest('form'),
            form_step_2 = $('.admin-step-signup.step-2 form'),
            formData = new FormData(form[0]),
            formData_step_2 = new FormData(form_step_2[0]),
            service_checkboxes = $('.admin-step-signup.step-3 .service-checkboxes :checkbox'),
            services = [],
            days_of_operation = {};


        valid = Plex.adminSignUps.validateCollegeSelection() && Plex.adminSignUps.validateForm(form, true);

        if (valid) {

            Plex.adminSignUps.showLoader();

            for(var pair of formData_step_2.entries()) {
                if (pair[0] == '_token') { continue; } // First form already contains token.
                formData.append(pair[0], pair[1]);
            }

            formData.append('college_selected', JSON.stringify(Plex.adminSignUps.selectedCollege));

            if (Plex.adminSignUps.selectedCollege.id) {
                formData.append('college_id', Plex.adminSignUps.selectedCollege.id);
            }

            // Remove unneeded data
            formData.delete('rep_college_search');
            formData.delete('new_college_name');
            formData.delete('new_college_country');
            formData.delete('new_college_city');
            formData.delete('new_college_state');

            if (formData.get('admin-profile-photo').size !== 0) {
                mixpanel.track('admin-upload-photo', { Location: 'signup flow' });
            }

            $.ajax({
                url: '/postAdminApplication',
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            }).done(function(response) {
                if (response == 'success') {
                    Plex.adminSignUps.step_1.fadeOut(0);
                    Plex.adminSignUps.step_2.fadeOut(0);
                    Plex.adminSignUps.step_3.fadeOut(0);

                    $('.admin-signup-steps-icon .step-text.active').removeClass('active');
                    $('.admin-signup-steps-icon .admin-step-icon.step-3 > .step-checkmark').addClass('active');

                    Plex.adminSignUps.completed_step.fadeIn(200);

                    mixpanel.track('admin-complete-signup', {});
                    mixpanel.track('admin-view-sign-up-step4', {});

                } else {
                    alert('Failed submitting application, try again later.');
                }

                Plex.adminSignUps.hideLoader();
            });
        }

    });

    $(document).on('change keyup', '#about-company-blurb', function(event) {
        var blurb = $(this).val(),
            length = blurb.length,
            charactersLeftText = $('.admin-step-signup.step-3 .blub-characters-container .blurb-characters-left'),
            previewBlurb = $('.dynamic-profile-preview-side.back .preview-blurb-text');


        clearInterval(Plex.adminSignUps.blurbTimeout);
        
        charactersLeftText.html(150 - length);

        switch (charactersLeftText.html()) {
            case '150':
                charactersLeftText.removeClass('zero');
                previewBlurb.html('[Small blurb about yourself]');
                break;

            case '0':
                charactersLeftText.addClass('zero');
                previewBlurb.html(blurb);
                break;

            default:
                charactersLeftText.removeClass('zero');
                previewBlurb.html(blurb);
        }

        previewBlurb.css('background-color', '#ffeac8;');

        Plex.adminSignUps.blurbTimeout = window.setTimeout(function() {
            previewBlurb.css('background-color', 'transparent');
        }, 1000);
    });

    $(document).on('change keyup', 'input[name=working_since_date]', function(event) {
        var date = $(this).val(),
            previewYear = $('.user-details-container .working-since-year'),
            isError = $(this).siblings('.error').is(':visible'),
            thisMoment = null;

        clearInterval(Plex.adminSignUps.yearTimeout);

        if (isError) {
            previewYear.html('[Year]');
        } else {
            previewYear.html( moment(date).year() );
        }

        previewYear.css('background-color', '#ffeac8;');

        Plex.adminSignUps.yearTimeout = window.setTimeout(function() {
            previewYear.css('background-color', 'transparent');
        }, 1000);
    });

    $(document).on('change keyup', '#rep_title', function(event) {
        var value = $(this).val(),
            previewTitle = $('.user-details-container .user-title');

        clearInterval(Plex.adminSignUps.titleTimeout);

        switch (value) {
            case '':
                previewTitle.html('[Title]');
                break;

            default:
                previewTitle.html(value);
        }

        previewTitle.css('background-color', '#ffeac8;');

        Plex.adminSignUps.titleTimeout = window.setTimeout(function() {
            previewTitle.css('background-color', 'transparent');
        }, 1000);

    });

    $(document).on('keyup', '#rep_fname, #rep_lname', function(event) {
        var fullName = $('#rep_fname').val() + ' ' + $('#rep_lname').val(),
            previewName = $('.user-details-container .user-name');

        clearInterval(Plex.adminSignUps.nameTimeout);

        previewName.html(fullName);

        previewName.css('background-color', '#ffeac8;');

        Plex.adminSignUps.nameTimeout = window.setTimeout(function() {
            previewName.css('background-color', 'transparent');
        }, 1000);
    });

    $(document).on('click', '.show-toggle-checkboxes-container .view-preview-button, #preview-modal .toggle-image-preview-container .image-preview-text', function(event) {
        if (!$(this).hasClass('college') && !$(this).hasClass('front')) return;

        var type = $(this).hasClass('college') ? 'college' : 'front';

        Plex.adminSignUps.togglePreviewImage(type);

        if ($(this).hasClass('view-preview-button'))
            $('#preview-modal').foundation('reveal', 'open');
    });

    Plex.adminSignUps.togglePreviewImage = function(type) {
        switch (type) {
            case 'college':
                $('#preview-modal').find('.preview-image-container img').attr('src', 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/profile_on_college_preview.jpg');
                $('#preview-modal .toggle-image-preview-container .image-preview-text.college').addClass('active');
                $('#preview-modal .toggle-image-preview-container .image-preview-text.front').removeClass('active');
                break;

            case 'front':
                $('#preview-modal').find('.preview-image-container img').attr('src', 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/profile_on_frontpage_preview.jpg');
                $('#preview-modal .toggle-image-preview-container .image-preview-text.front').addClass('active');
                $('#preview-modal .toggle-image-preview-container .image-preview-text.college').removeClass('active');
                break;
        }
    }

    $(document).on('click', '.admin-step-signup.step-3 .view-sample-image-button', function (event) {
        $('#preview-modal').foundation('reveal', 'open');
    });

    $(document).on('change', '#admin-profile-photo', function(event) {
        event.preventDefault();
        if ($(this).val() !== '') {
            Plex.adminSignUps.previewIMGUpload(this);
        } else {
            $('.dynamic-profile-preview-container .user-image-icon').css('background-image', 'url(https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png)');
        }
    });

    $(document).on('keypress change', '.admin-step-signup form input', function(event) {
        var form = $(this).closest('form'),
            button = form.find('.admin-signup-button'),
            valid = Plex.adminSignUps.validateForm(form);
    });

    $(document).on('keypress', 'form input:not(#new-service-name)', function(e) {
        return e.which !== 13;
    });

    $(document).on('keypress', '#new-service-name', function (event) {
        if (event.which === 13) {
            $('.add-admin-service-btn').first().click();
            return false;
        }
    });

    $(document).on('keypress', '.hours-container .hours-select', function (event) {
        if (event.which === 13) {
            return false;
        }
    });

    $(document).on('click', '.add-admin-service-btn', function(event) {
        var field = $('#new-service-name'),
            checkbox_container = $(this).siblings('.service-checkboxes'),
            service_name = field.val().trim(),
            parsed_id = null,
            count = 0; 

        if (service_name == '') {
            field.focus();
            return;
        }

        parsed_id = 'admin-service-' + service_name.toLowerCase().split(/\s+/).join('-');

        // Incase of duplicate IDs, we will increment a counter.
        while($('#' + parsed_id).length > 0) {
            count++;
            parsed_id = 'admin-service-' + service_name.toLowerCase().split(/\s+/).join('-') + '-' + count;
        }

        checkbox_container.append(
            "<div class='user-added-service mt10 service'>" +
                "<input id='" + parsed_id + "' type='checkbox' checked />" +
                "<label for='" + parsed_id + "'>" + service_name + "</label>" +
                "<div class='new-service-remove-btn'>Remove</div>" +
            "</div>" );

        field.val('');
    });

    $(document).on('click', '.admin-step-signup.step-3 .new-service-remove-btn', function(event) {
        event.preventDefault();
        var parent = $(this).closest('.user-added-service');
        parent.remove();
    });

    $(document).on('change', '#country-code-select', Plex.adminSignUps.validatePhone);

    $(document).on('change input', '#phone-number', function() {
        clearInterval(Plex.adminSignUps.verifyPhoneTimeout);

        Plex.adminSignUps.verifyPhoneTimeout = setTimeout(function() {
            Plex.adminSignUps.validatePhone();
        }, 500);
    });

    $(document).on('click', '.continue-to-plexuss-btn', function(event) {
        window.location = '/'; 
    });

    // Validation checker
    $(document).foundation({
        abide: {
            patterns: {
                name: /^([a-zA-Z\'\-. ])+$/,
                passwordpattern: /^(?=[^\d_].*?\d)\w(\w|[!@#$%*]){7,20}/,
                date: /^\d{2}\/\d{2}\/\d{4}$/,
            },
            validators: {
                monthChecker: function(el, required, parent) {
                    var value = $(el).val(),
                        form = $(el).closest('form'),
                        button = form.find('.admin-signup-button');

                    if ( ($.isNumeric(value) && value <= 12 && value > 0 )|| value.trim() === '' ) {
                        $('.datedMonthError').css('display', 'none');
                        return true;
                    } else{
                        $('.datedMonthError').css({
                            'display': 'inline-block',
                            'margin-bottom': '2px'
                        });
                        return false;
                    };
                },
                dayChecker: function(el, required, parent) {
                    var value = $(el).val(),
                        form = $(el).closest('form'),
                        button = form.find('.admin-signup-button');

                    if ( ($.isNumeric(value) && value <= 31 && value >= 1)  || value.trim() === '' ) {
                        $('.datedDayError').css('display', 'none');
                        return true;
                    } else{
                        $('.datedDayError').css({
                            'display': 'inline-block',
                            'margin-bottom': '2px'
                        });
                        return false;
                    };
                },
                yearChecker: function(el, required, parent) {
                    var value = $(el).val();
                    var currentDate = (new Date).getFullYear();
                    var minAgeAllowed = currentDate - 13;
                    var form = $(el).closest('form');
                    var button = form.find('.admin-signup-button');

                    if( value.trim() === ''){
                        $('.datedYearError').css('display', 'none');
                        $('.datedUnderAge').css('display', 'none');
                        return true;
                    }
                    else if( !$.isNumeric(value) || value > currentDate || value <=  currentDate - 100 ){
                        $('.datedYearError').css({
                            'display': 'inline-block',
                            'margin-bottom': '2px'
                        });
                        return false;
                    } else if ( value > minAgeAllowed) {
                        $('.datedUnderAge').css({
                            'display': 'inline-block',
                            'margin-bottom': '2px'
                        });

                        $('.agenotice').css({
                            'color':'#FF7F00',
                            'font-weight':'bold'
                        });
                        return false;
                    }else{
                        $('.datedYearError').css('display', 'none');
                        $('.datedUnderAge').css('display', 'none');
                        return true;
                    };
                },
            }
        }
    });

    Plex.adminSignUps.toggleSelectedCollege = function(option) {
        if (option !== 'on' && option !== 'off') return;

        var college = Plex.adminSignUps.selectedCollege;

        switch (option) {
            case 'on':
                $('#rep_selected_college .college-container .college-image').html('<img src="' + (college.logo_url || Plex.adminSignUps.defaultSchoolIcon) + '" />');
                $('#rep_selected_college .college-container .college-label h5').html(college.school_name);
                $('#rep_selected_college .college-container .college-label p').html(college.city + ', ' + college.state);
                $('.dynamic-profile-preview-container .dynamic-profile-preview-side.front').css('background-image', 'url(' + (college.school_background || Plex.adminSignUps.defaultSchoolBackground) + ')');
                $('.dynamic-profile-preview-container .college-image-icon').css('background-image', 'url(' + (college.logo_url || Plex.adminSignUps.defaultSchoolIcon) + ')');
                $('#add-new-college-container').fadeOut(0);
                $('#rep_college_search').fadeOut(0);
                $('#rep_selected_college').fadeIn(0);

                break;

            case 'off':
                Plex.adminSignUps.selectedCollege = null;

                $('#rep_selected_college').fadeOut(0);
                $('#rep_college_search').fadeIn(0);

                break;
        }
    }

    $(document).on('click', '.change-college-button', function(event) {
        event.preventDefault();
       
        Plex.adminSignUps.toggleSelectedCollege('off');
    });

    $(document).on('focus', '#rep_college_search', function(event) {
        var uiComponent = $('.ui-autocomplete.ui-front.ui-menu.ui-widget.ui-widget-content');

        if (uiComponent.find('.ui-menu-item').length > 0)
            $('.ui-autocomplete.ui-front.ui-menu.ui-widget.ui-widget-content').fadeIn(200);
    });

    $(document).on('click', '.ui-autocomplete .add-new-college-button', function(event) {
        event.preventDefault();

        $('#rep_college_search').fadeOut(0);

        $('#add-new-college-container').fadeIn(0);
    });

    $(document).on('click', '#add-new-college-container .add-college-back-button', function(event) {
        event.preventDefault();

        Plex.adminSignUps.selectedCollege = null;

        $('#rep_college_search').fadeIn(0);

        $('#add-new-college-container').fadeOut(0);
    });

    $(document).on('click', '#add-new-college-container .submit-new-college-button', function(event) {
        var valid = Plex.adminSignUps.validateNewCollegeEntry();

        if (valid) {
            Plex.adminSignUps.selectedCollege = {
                id: null, // If id is null, it means its a new college.
                country_code: $('#add-new-college-container').find('[name=new_college_country]').val(),
                school_name: $('#add-new-college-container').find('[name=new_college_name]').val(),
                state: $('#add-new-college-container').find('[name=new_college_state]').val(),
                city: $('#add-new-college-container').find('[name=new_college_city]').val(),
            }

            Plex.adminSignUps.toggleSelectedCollege('on');
        }
    });

    $("#rep_college_search").autocomplete({
        source: '/ajax/searchCollegeWithBackgroundImage/',
        create: function() {
            $(this).data('ui-autocomplete')._renderItem = function( ul, item ) {
                var inner_html = '<div class="list_item_container">' +
                                    '<div class="autocomplete-image"><img src="' + item.logo_url + '" ></div>' + 
                                    '<div class="autocomplete-label">' +
                                        '<h5><b>' + item.school_name + '</b></h5>' +
                                        '<p>' + item.city + ', ' + item.state + '</p>' +
                                    '</div>' +
                                 '</div>';
                                 
                return $("<li></li>")
                        .data("item.autocomplete", item)
                        .append(inner_html)
                        .appendTo(ul);
            };
        },
        open: function( event, ui ) {
            $('.ui-autocomplete.ui-front.ui-menu.ui-widget.ui-widget-content')
                .append('<li class="add-new-college-button">Don\'t see your university?</li>');
        },
        focus: function( event, ui ) {
            return false;
        },
        select: function( event, ui ) {
            Plex.adminSignUps.selectedCollege = $.extend({}, ui.item);

            Plex.adminSignUps.toggleSelectedCollege('on');

            return false;
        },
    });

});