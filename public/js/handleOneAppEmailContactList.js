$(document).ready(function() {
    var contactList = $('#contact-list').data('contact-list'),
        event = new Event('contact-list');

    event.detail = contactList;

    if (Array.isArray(event.detail)) {
        setInterval(function() {
            window.dispatchEvent(event);
        }, 200);
    }
});