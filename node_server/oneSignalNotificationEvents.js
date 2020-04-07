// /node_server/oneSignalNotificationEvents.js

const axios = require('axios');
const request = require('request');

const CREATE_NOTIFICATION_API = 'https://onesignal.com/api/v1/notifications'

const HEADERS = {
    'content-type': 'application/json',
    'authorization': 'Basic '+process.env.ONESIGNAL_REST_KEY,
}

module.exports = {

	pushNewNotification: ({ message }) => {

        const newNotification = _createNotification({ message }); // is Promise, just don't need to do anything on success/fail right now

	}
	
}

const _createNotification = ({ message = {} }) => {

    const { msg, device_token, platform } = message;
    const platform_device_id_key = platform === 'android' ? 'include_player_ids' : 'include_ios_tokens';

    const payload = {
        app_id: process.env.ONESIGNAL_APP_ID,
        headings: {en: 'Plexuss'},
        // subtitle: {en: msg || 'New message'},
        contents: {en: msg || 'New message'},
        message: msg || 'New Message',
        content_available: true,
        [platform_device_id_key]: [device_token],
        data: message
    }

    return new Promise((resolve, reject) => {

        request({
            method: 'POST',
            uri: CREATE_NOTIFICATION_API,
            headers: HEADERS,
            json: true,
            body: payload
        }, (error, response, body) => {
            if(!body.errors) resolve( body );
            else reject( body.errors );
        });

    });

}