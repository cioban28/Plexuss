
/* PLEXUSS ONBOARDING SIGNUP*/
$(document).ready(function() {
    if (typeof Plex === 'undefined') Plex = {};

    Plex.onboardingSignUps = {
        collegeSelected: null,
        titleTimeout: null,
        nameTimeout: null,
        yearTimeout: null,
        blurbTimeout: null,
        defaultSchoolIcon: "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/default-missing-college-logo.png",
        defaultSchoolBackground: "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/default-college-page-photo_overview.jpg",
        step_1: $('.admin-step-signup.step-1'),
        step_2: $('.admin-step-signup.step-3'),
        step_3: $('.admin-step-signup.step-4'),
        // step_4: $('.admin-step-signup.step-4'),
        completed_step: $('.admin-steps-complete'),
        showLoader: function () {
            $('.manage-students-ajax-loader').show();
        },
        hideLoader: function () {
            $('.manage-students-ajax-loader').hide();
        },
        verifyPhoneTimeout: null,
    };

    var steps = [Plex.onboardingSignUps.step_1, Plex.onboardingSignUps.step_2, Plex.onboardingSignUps.step_3, Plex.onboardingSignUps.completed_step];

    _.each(steps, function (step, index) {
        var stepNumber = (index + 1);
        if (step.is(':visible')) {
            mixpanel.track('b2b-view-resources-step' + stepNumber, {});

            if (stepNumber === 4) {
                mixpanel.track('b2b-view-resources-waiting-approval', {});
            }
        }
    });

    $(document).foundation({
        abide: {
            patterns: {
                name: /^([a-zA-Z\'\-. ])+$/,
                passwordpattern: /^(?=[^\d_].*?\d)\w(\w|[!@#$%*]){7,20}/,
                date: /^\d{2}\/\d{2}\/\d{4}$/,
            },
            validators: {
                monthChecker: function (el, required, parent) {
                    var value = $(el).val(),
                        form = $(el).closest('form'),
                        button = form.find('.admin-signup-button');

                    if (($.isNumeric(value) && value <= 12 && value > 0) || value.trim() === '') {
                        $('.datedMonthError').css('display', 'none');
                        return true;
                    } else {
                        $('.datedMonthError').css({
                            'display': 'inline-block',
                            'margin-bottom': '2px'
                        });
                        return false;
                    }
                    ;
                },
                dayChecker: function (el, required, parent) {
                    var value = $(el).val(),
                        form = $(el).closest('form'),
                        button = form.find('.admin-signup-button');

                    if (($.isNumeric(value) && value <= 31 && value >= 1) || value.trim() === '') {
                        $('.datedDayError').css('display', 'none');
                        return true;
                    } else {
                        $('.datedDayError').css({
                            'display': 'inline-block',
                            'margin-bottom': '2px'
                        });
                        return false;
                    }
                    ;
                },
                yearChecker: function (el, required, parent) {
                    var value = $(el).val();
                    var currentDate = (new Date).getFullYear();
                    var minAgeAllowed = currentDate - 13;
                    var form = $(el).closest('form');
                    var button = form.find('.admin-signup-button');

                    if (value.trim() === '') {
                        $('.datedYearError').css('display', 'none');
                        $('.datedUnderAge').css('display', 'none');
                        return true;
                    }
                    else if (!$.isNumeric(value) || value > currentDate || value <= currentDate - 100) {
                        $('.datedYearError').css({
                            'display': 'inline-block',
                            'margin-bottom': '2px'
                        });
                        return false;
                    } else if (value > minAgeAllowed) {
                        $('.datedUnderAge').css({
                            'display': 'inline-block',
                            'margin-bottom': '2px'
                        });

                        $('.agenotice').css({
                            'color': '#FF7F00',
                            'font-weight': 'bold'
                        });
                        return false;
                    } else {
                        $('.datedYearError').css('display', 'none');
                        $('.datedUnderAge').css('display', 'none');
                        return true;
                    }
                    ;
                },
            }
        }
    });

    $(document).on('click', '.admin-signup-button.step-1', function (event) {
        event.preventDefault();

        var form = $(this).closest('form'),
            valid = Plex.onboardingSignUps.validateForm(form, true),
            formData = null;

        if (valid) {
            formData = new FormData(form[0]);

            Plex.onboardingSignUps.showLoader();

            $.ajax({
                url: '/postOnboardingSignup',
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            }).done(function (response) {
                if (response.status == 'success') {
                    Plex.onboardingSignUps.step_1.fadeOut(0);

                    $('.admin-signup-steps-icon .admin-step-icon.step-1 > .sprite').removeClass('active');
                    $('.admin-signup-steps-icon .admin-step-icon.step-1 > .step-text').removeClass('active');
                    $('.admin-signup-steps-icon .admin-step-icon.step-1 > .step-checkmark').addClass('active');

                    $('.admin-step-signup .step-1').addClass('hidden');
                    $('.admin-step-signup .step-3').removeClass('hidden');

                    $('.admin-signup-steps-icon .admin-step-icon.step-2 > .sprite').addClass('active');
                    $('.admin-signup-steps-icon .admin-step-icon.step-2 > .step-text').addClass('active');

                    $('.user-details-container .user-name').html(response.fname + ' ' + response.lname);
                    $('#rep_fname').val(response.fname);
                    $('#rep_lname').val(response.lname);
                    $('#rep_email').val(response.email);
                    $('#rep_id').val(response.id);
                    $('#rep_birth_date').val(response.birth_date);

                    Plex.onboardingSignUps.step_2.fadeIn(200);
                } else {
                    $('.admin-step-signup.step-1').html(response);
                    if (response.includes('<li class="error">The email has already been taken.</li>')) {
                        mixpanel.track('admin-sign-up-preexisting', {Email: $('#email').val()});
                    }
                }

                Plex.onboardingSignUps.hideLoader();
            });
        }
    });

    Plex.onboardingSignUps.validateForm = function (form, submit_clicked) {
        var valid = true,
            required_inputs = form.find('[required]');

        required_inputs.each(function () {
            // Verify no inputs are invalid
            if ($('.error').is(':visible') && !$('.error').closest('.alert.alert-danger').length) {
                if (submit_clicked) {
                    $('.error').siblings('input').focus();
                }
                valid = false;
            }

            // Verify all inputs have values
            if ($(this).val() === '' || ($(this).val() == 'on' && !$(this).is(':checked'))) {
                if (submit_clicked) {
                    $(this).focus();
                }

                valid = false;
                return false;
            }
        });

        return valid;
    }

    $(document).on('change keyup', '#about-company-blurb', function (event) {
        var blurb = $(this).val(),
            length = blurb.length,
            charactersLeftText = $('.admin-step-signup.step-3 .blub-characters-container .blurb-characters-left'),
            previewBlurb = $('.dynamic-profile-preview-side.back .preview-blurb-text');


        clearInterval(Plex.onboardingSignUps.blurbTimeout);

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

        Plex.onboardingSignUps.blurbTimeout = window.setTimeout(function () {
            previewBlurb.css('background-color', 'transparent');
        }, 1000);
    });

    $(document).on('change keyup', 'input[name=working_since_date]', function (event) {
        var date = $(this).val(),
            previewYear = $('.user-details-container .working-since-year'),
            isError = $(this).siblings('.error').is(':visible'),
            thisMoment = null;

        clearInterval(Plex.onboardingSignUps.yearTimeout);

        if (isError) {
            previewYear.html('[Year]');
        } else {
            previewYear.html(moment(date).year());
        }

        previewYear.css('background-color', '#ffeac8;');

        Plex.onboardingSignUps.yearTimeout = window.setTimeout(function () {
            previewYear.css('background-color', 'transparent');
        }, 1000);
    });

    $(document).on('change keyup', '#rep_title', function (event) {
        var value = $(this).val(),
            previewTitle = $('.user-details-container .user-title');

        clearInterval(Plex.onboardingSignUps.titleTimeout);

        switch (value) {
            case '':
                previewTitle.html('[Title]');
                break;

            default:
                previewTitle.html(value);
        }

        previewTitle.css('background-color', '#ffeac8;');

        Plex.onboardingSignUps.titleTimeout = window.setTimeout(function () {
            previewTitle.css('background-color', 'transparent');
        }, 1000);

    });

    $('.datepicker').datepicker({
        constrainInput: false,
        changeMonth: true,
        changeYear: true,
        maxDate: moment().format('MM/DD/YYYY'),
    });

    $('.admin-step-signup.step-2 .datepicker').val(moment().format('MM/DD/YYYY'));

    $(document).foundation('tooltip', 'reflow');

    Plex.onboardingSignUps.previewIMGUpload = function (img_input) {
        if (img_input.files && img_input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('.dynamic-profile-preview-container .user-image-icon').css('background-image', 'url(' + e.target.result + ')');
            }

            reader.readAsDataURL(img_input.files[0]);
        }
    }

    Plex.onboardingSignUps.togglePreviewImage = function (type) {
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

    $(document).on('change', '#admin-profile-photo', function (event) {
        event.preventDefault();
        if ($(this).val() !== '') {
            Plex.onboardingSignUps.previewIMGUpload(this);
        } else {
            $('.dynamic-profile-preview-container .user-image-icon').css('background-image', 'url(https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png)');
        }
    });

    $(document).on('click', '.show-toggle-checkboxes-container .view-preview-button, #preview-modal .toggle-image-preview-container .image-preview-text', function (event) {
        if (!$(this).hasClass('college') && !$(this).hasClass('front')) return;

        var type = $(this).hasClass('college') ? 'college' : 'front';

        Plex.onboardingSignUps.togglePreviewImage(type);

        if ($(this).hasClass('view-preview-button'))
            $('#preview-modal').foundation('reveal', 'open');
    });

    $(document).on('focusout', 'input[name=company]', function (event) {
        var company = $(this).val();

        if (!company) return;

        company = company.split(/\s+/).join('_');
        
        $.ajax({
            url: '/ajax/checkCompany',
            type: 'POST',
            data: {company: company},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        })
            .done(function (result) {
                if (result === 'exist') {
                    $('.org').html('Company already exist.');
                    $("button").attr("disabled", "disabled");
                } else {
                    $('.org').html('');
                    $("button").prop("disabled", false);
                }
            });
    });

    $(document).on('click', '.admin-signup-button.step-3', function (event) {
        event.preventDefault();

        var form = $(this).closest('form'),
            valid = Plex.onboardingSignUps.validateForm(form, true),
            formData = null;

        if (valid) {
            formData = new FormData(form[0]);

            Plex.onboardingSignUps.showLoader();

            if (formData.get('admin-profile-photo').size !== 0) {
                mixpanel.track('admin-upload-photo', {Location: 'signup flow'});
            }

            $.ajax({
                url: '/postOnboardingApplication',
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            }).done(function (response) {
                if (response.status == 'success') {
                    Plex.onboardingSignUps.step_1.fadeOut(0);
                    Plex.onboardingSignUps.step_2.fadeOut(0);

                    $('.admin-signup-steps-icon .admin-step-icon.step-1 > .sprite').removeClass('active');
                    $('.admin-signup-steps-icon .admin-step-icon.step-1 > .step-text').removeClass('active');
                    $('.admin-signup-steps-icon .admin-step-icon.step-1 > .step-checkmark').addClass('active');

                    $('.admin-signup-steps-icon .admin-step-icon.step-2 > .sprite').removeClass('active');
                    $('.admin-signup-steps-icon .admin-step-icon.step-2 > .step-text').removeClass('active');
                    $('.admin-signup-steps-icon .admin-step-icon.step-2 > .step-checkmark').addClass('active');

                    $('.admin-step-signup .step-1').addClass('hidden');
                    $('.admin-step-signup .step-3').addClass('hidden');
                    $('.admin-step-signup .step-4').removeClass('hidden');

                    $('.admin-signup-steps-icon .admin-step-icon.step-3 > .sprite').addClass('active');
                    $('.admin-signup-steps-icon .admin-step-icon.step-3 > .step-text').addClass('active');

                    $('#rep_company').val(response.company.split(/\s+/).join('_'));
                    $("#signup_email").val(response.email);
                    $("#signup_id").val(response.id);

                    Plex.onboardingSignUps.step_3.fadeIn(200);

                } else {
                    alert('Failed submitting application, try again later.');
                }

                Plex.onboardingSignUps.hideLoader();
            });
        }

    });

    $(document).on('click', '#college-app', function (event) {
        event.preventDefault();
        $(".main-tab").addClass('hidden-div');
        $(".first-tab").removeClass('hidden-div');
        var company = $("#rep_company").val();
        var url = "https://plexuss.com/trackPixel?company=" + company;
        $(".comp").html('<img src="' + url + '" height="1" width="1" style="display:none;">');
        $("#company-value-1").val(company);
        var email = $("#signup_email").val();
        $("#email-1").val(email);
        $("#service").val("Get students to complete College Application");
        var id = $("#signup_id").val();
        $("#signup_id_1").val(id);
        $("#service-1").val("Get students to complete College Application");
    });

    $(document).on('click', '#complete-form', function (event) {
        event.preventDefault();
        $(".main-tab").addClass('hidden-div');
        $(".second-tab").removeClass('hidden-div');
        var company = $("#rep_company").val();
        var url = "https://plexuss.com/trackPixel?company=" + company;
        $(".comp").html('<img src="' + url + '" height="1" width="1" style="display:none;">');
        $("#company-value-2").val(company);
        var email = $("#signup_email").val();
        $("#email-2").val(email);
        $("#service").val("Get students to complete Form");
        var id = $("#signup_id").val();
        $("#signup_id_2").val(id);
        $("#service-2").val("Get students to complete Form");
    });

    $(document).on('click', '#site-click', function (event) {
        event.preventDefault();
        $(".main-tab").addClass('hidden-div');
        $(".third-tab").removeClass('hidden-div');
        var company = $("#rep_company").val();
        $(".comp").html(company);
        $("#company-value-3").val(company);
        var email = $("#signup_email").val();
        $("#email-3").val(email);
        $("#service").val("Drive students to site via Clicks");
        var id = $("#signup_id").val();
        $("#signup_id_3").val(id);
        $("#service-3").val("Drive students to site via Clicks");
    });

    $(document).on('click', '#lead', function (event) {
        event.preventDefault();
        $(".main-tab").addClass('hidden-div');
        $(".last-tab").removeClass('hidden-div');
        var company = $("#rep_company").val();
        $(".comp").html(company);
        $("#company-value-4").val(company);
        var email = $("#signup_email").val();
        $("#email-4").val(email);
        $("#service").val("Post Leads");
        var id = $("#signup_id").val();
        $("#signup_id_4").val(id);
        $("#service-4").val("Post Leads");
    });

    $(document).on('click', '.bck', function (event) {
        event.preventDefault();
        $(".main-tab").removeClass('hidden-div');
        $(".first-tab").addClass('hidden-div');
        $(".second-tab").addClass('hidden-div');
        $(".third-tab").addClass('hidden-div');
        $(".last-tab").addClass('hidden-div');
    });

    $(document).on('click', '.admin-signup-button.step-4', function (event) {
        event.preventDefault();

        var form = $(this).closest('form'),
            valid = Plex.onboardingSignUps.validateForm(form, true),
            formData = null;

        if (valid) {
            formData = new FormData(form[0]);

            Plex.onboardingSignUps.showLoader();

            $.ajax({
                url: '/postAdRedirectCampaign',
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            }).done(function (response) {
                if (response.status == 'success') {
                    Plex.onboardingSignUps.step_1.fadeOut(0);
                    Plex.onboardingSignUps.step_2.fadeOut(0);
                    Plex.onboardingSignUps.step_3.fadeOut(0);

                    $('.admin-signup-steps-icon .admin-step-icon.step-1 > .sprite').removeClass('active');
                    $('.admin-signup-steps-icon .admin-step-icon.step-1 > .step-text').removeClass('active');
                    $('.admin-signup-steps-icon .admin-step-icon.step-1 > .step-checkmark').addClass('active');

                    $('.admin-signup-steps-icon .admin-step-icon.step-2 > .sprite').removeClass('active');
                    $('.admin-signup-steps-icon .admin-step-icon.step-2 > .step-text').removeClass('active');
                    $('.admin-signup-steps-icon .admin-step-icon.step-2 > .step-checkmark').addClass('active');

                    $('.admin-signup-steps-icon .admin-step-icon.step-3 > .sprite').removeClass('active');
                    $('.admin-signup-steps-icon .admin-step-icon.step-3 > .step-text').removeClass('active');
                    $('.admin-signup-steps-icon .admin-step-icon.step-3 > .step-checkmark').addClass('active');

                    $('.admin-step-signup .step-1').addClass('hidden');
                    $('.admin-step-signup .step-3').addClass('hidden');
                    $('.admin-step-signup .step-4').addClass('hidden');
                    $('.admin-steps-complete').removeClass('hidden');

                    $('.admin-signup-steps-icon .admin-step-icon.step-4 > .sprite').addClass('active');
                    $('.admin-signup-steps-icon .admin-step-icon.step-4 > .step-text').addClass('active');
                    $('.admin-signup-steps-icon .admin-step-icon.step-4 > .step-checkmark').addClass('active');

                    $('#mrk').html('Thank You ' + response.name);
                    Plex.onboardingSignUps.completed_step.fadeIn(200);

                } else {
                    alert('Failed submitting application, try again later.');
                }

                Plex.onboardingSignUps.hideLoader();
            });
        }

    });

    $(document).on('click', '.continue-to-plexuss-btn', function (event) {
        window.location = '/';
    });

    $(document).on('click', '.term-service', function (event) {
        event.preventDefault();

        $('.get-sign-up').addClass('hidden-div');
        $('.admin-signup-terms').removeClass('hidden-div');
        if (!($('#admin-agreement-check-step-1').is(":checked"))) {
            $("button").attr("disabled", "disabled");
        }

        var fname = $('#fname').val();
        $('#f_name').val(fname);
        var lname = $('#lname').val();
        $('#l_name').val(lname);
        var email = $('#email').val();
        $('#user_email').val(email);
        var pwd = $('#password').val();
        $('#pwd').val(pwd);
        var m = $('#m').val();
        $('#month').val(m);
        var d = $('#d').val();
        $('#day').val(d);
        var y = $('#y').val();
        $('#year').val(y);

    });

    $(document).on('change', '#admin-agreement-check-step-2', function (event) {
        event.preventDefault();
        // $('#admin-agreement-check-step-1').prop('checked', true);
        $("button").prop("disabled", false);
    });

    $(document).on('click', '.admin-signup-button.step-2', function (event) {
        event.preventDefault();
        var form = $(this).closest('form'),
            valid = Plex.onboardingSignUps.validateForm(form, true);

        if (valid) {
            formData = new FormData(form[0]);

            Plex.onboardingSignUps.showLoader();

            $.ajax({
                url: '/postOnboardingSignup',
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            }).done(function (response) {
                if (response.status == 'success') {
                    Plex.onboardingSignUps.step_1.fadeOut(0);

                    $('.admin-signup-steps-icon .admin-step-icon.step-1 > .sprite').removeClass('active');
                    $('.admin-signup-steps-icon .admin-step-icon.step-1 > .step-text').removeClass('active');
                    $('.admin-signup-steps-icon .admin-step-icon.step-1 > .step-checkmark').addClass('active');

                    $('.admin-step-signup .step-1').addClass('hidden');
                    $('.admin-step-signup .step-3').removeClass('hidden');

                    $('.admin-signup-steps-icon .admin-step-icon.step-2 > .sprite').addClass('active');
                    $('.admin-signup-steps-icon .admin-step-icon.step-2 > .step-text').addClass('active');

                    $('.user-details-container .user-name').html(response.fname + ' ' + response.lname);
                    $('#rep_fname').val(response.fname);
                    $('#rep_lname').val(response.lname);
                    $('#rep_email').val(response.email);
                    $('#rep_id').val(response.id);
                    $('#rep_birth_date').val(response.birth_date);

                    Plex.onboardingSignUps.step_2.fadeIn(200);
                } else {
                    $('.admin-step-signup.step-1').html(response);
                    if (response.includes('<li class="error">The email has already been taken.</li>')) {
                        mixpanel.track('admin-sign-up-preexisting', {Email: $('#email').val()});
                    }
                }

                Plex.onboardingSignUps.hideLoader();
            });
        } else {
            $('.bck.step-2').removeClass('hidden-div');
            alert("Please fill the all fields in the previous form");
        }
    });

    $(document).on('click', '.bck.step-2', function (event) {
        event.preventDefault();
        $('.get-sign-up').removeClass('hidden-div');
        $('.admin-signup-terms').addClass('hidden-div');
        $('#admin-agreement-check-step-2').prop('checked', false);
        // $('.bck.step-2').addClass('hidden-div');
    });

    Plex.onboardingSignUps.validatePhone = function () {
        var full_phone = $('#country-code-select').val() + $('#phone-number').val(),
            valid = true;

        $.ajax({
            url: '/phone/validatePhoneNumber',
            type: 'POST',
            data: {phone: full_phone},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).done(function (response) {
            if (response.error == false) {
                $('.phone-error-msg.error').fadeOut(200);
                return;
            }
            $('.phone-error-msg.error').fadeIn(200);
        }).fail(function () {
            $('.phone-error-msg.error').fadeIn(200);
        });
    }

    $(document).on('change', '#country-code-select', Plex.onboardingSignUps.validatePhone);

    $(document).on('focusout', 'input[name=phone_number]', Plex.onboardingSignUps.validatePhone);

    $(document).on('change input', '#phone-number', function () {
        clearInterval(Plex.onboardingSignUps.verifyPhoneTimeout);

        Plex.onboardingSignUps.verifyPhoneTimeout = setTimeout(function () {
            Plex.onboardingSignUps.validatePhone();
        }, 500);
    });

    $(document).on('focusout change keyup', '#url', function (event) {
        var url = $(this).val();
        var regexp = /^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:[/?#]\S*)?$/;

        var parent = $(this).closest('.dwnl-cont');

        if (regexp.test(url)) {
            parent.find('.url-error').addClass('hide-error');
            parent.find('button').attr("disabled", false);
        } else {
            parent.find('.url-error').removeClass('hide-error');
            parent.find('.url-error').html('The URL must begin with the protocol. For example: https://www.plexuss.com/signup');
            parent.find('button').attr("disabled", "disabled");
        }
    });

    $(document).on('click', '.clip', function (event) {
        event.preventDefault();
        var copyText = $('.comp').html();
        var tempInput = document.createElement("input");
        tempInput.style = "position: absolute; left: -1000px; top: -1000px";
        tempInput.value = copyText;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        $('.clipboard').removeClass('hide-error');
        $('.clipboard').show().delay(5000).fadeOut();
    });
});
