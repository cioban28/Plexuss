Plex = typeof Plex == 'undefined' ? {} : Plex;

// Desktop Notifications
Plex.notifications = {
};

Plex.notifications.notify = function(message, icon) {
	// If icon is not set, use Plexuss logo
	icon = icon || '/images/plexussLogoLetterBlack.png';

	var options = {
		body: message,
		icon: icon,
	}

	var notification = null;

	if (!("Notification" in window)) {
		return;

	} else if (Notification.permission === "granted") {
	    notification = new Notification('Plexuss', options);

  	} else if (Notification.permission !== "denied") {
	    Notification.requestPermission(function (permission) {
			if (permission === "granted") {
				notification = new Notification('Plexuss', options);
			}
	    });
	}
}
// End Desktop Notifications

$(document).ready(function() {
	if (Notification.permission === 'default') {
		Notification.requestPermission();
	}
});
