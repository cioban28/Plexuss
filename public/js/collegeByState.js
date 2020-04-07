$(document).ready(function() {
    $(document).on('click', '.state-description-container .read-more-button', function(event) {
        var contentContainer = $('.state-description-container .content-container');

        if (contentContainer.hasClass('read-more')) {
            contentContainer.removeClass('read-more');
            $(this).find('.read-more-text').html('Read More');
            $(this).find('.down-arrow').html('&#8964;');
        } else {
            contentContainer.addClass('read-more');
            $(this).find('.read-more-text').html('Read Less');
            $(this).find('.down-arrow').html('');
        }
    });
});