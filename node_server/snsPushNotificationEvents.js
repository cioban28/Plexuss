// /node_server/snsPushNotificationEvents.js

// aws
const AWS = require('aws-sdk')

// add credentials
AWS.config.update({
    apiVersion: '2010-03-31',
    region: process.env.AWS_REGION,
    accessKeyId: process.env.SNS_ACCESS_KEY_ID,
    secretAccessKey: process.env.SNS_SECRET_KEY_ID,
});

// create new instance
const SNS = new AWS.SNS();

// promise that publishes message to AWS SNS to push to device
const _publishToSNSEndpoint = ({ endpoint = {}, message = {} }) => {
    return new Promise((resolve, reject) => {
        const defaultMsg = 'Hello World 2';
        const { platform, device_token, num_unread_msg, msg } = message;
        const badge = Number.isFinite(num_unread_msg) ? num_unread_msg : 1;

        const Message = JSON.stringify({
            default: msg+'_default' || defaultMsg,
            APNS_SANDBOX: JSON.stringify({
                aps: {
                    alert: msg || defaultMsg,
                    sound: 'default',
                    badge,
                    priority: 'high',
                    "content-available": 1,
                },
                _notification: message,
            })
        });

        const params = {
            Message,
            MessageStructure: 'json',
            TargetArn: endpoint.EndpointArn
        };

        SNS.publish(params, (err, data) => {
            if (err) reject( err ); // throw error if err
            resolve( data ); // else resolve
        });

    })
}

// promise that gets and returns all device endpoints for a particular platform app arn
const _getAllSNSApplicationEndpoints = () => {

    return new Promise((resolve, reject) => {
        const params = {
            PlatformApplicationArn: process.env.DEV_AWS_PLATFORM_APP_ARN, /* required */
        };

        SNS.listEndpointsByPlatformApplication(params, function(err, data) {
            if( err ) reject( err );
            resolve( data );
        });
    })

}

// promise that creates a new device endpoint for a particular platform app and returns that new endpoint
const _createPlatformEndpoint = endpointArn => {
    

    return new Promise((resolve, reject) => {

        const params = {
            PlatformApplicationArn: process.env.DEV_AWS_PLATFORM_APP_ARN,
            Token: "b4bfc925d339dc5530b90fa3c9e91a746b00bd7ead7b7584988f3fa9090be859", // device token
            Attributes: {
                "Enabled": "true",
                "Token": "b4bfc925d339dc5530b90fa3c9e91a746b00bd7ead7b7584988f3fa9090be859", // device token
            }
        };

        SNS.createPlatformEndpoint(params, (err, data) => {
            if (err) reject( err ); // throw error if err
            resolve( data ); // else resolve
        });

    });

}

// promise that returns attributes of a particular device endpoint
const _getEndpointAttributes = endpointArn => {

    return new Promise((resolve, reject) => {

        const params = {
            EndpointArn: process.env.DEV_AWS_PLATFORM_APP_ARN,
        };

        SNS.getEndpointAttributes(params, (err, data) => {
            // throw error if err
            if (err) reject( err );
            // else resolve
            resolve( data );
        });

    });

}

module.exports = {

	pushNewNotification: ({ message }) => {
        // console.log('notification message: ', message);
	    const allEndpoints = _getAllSNSApplicationEndpoints();
	    let endpointFound = null;

	    allEndpoints.then(({ Endpoints }) => {
	        // console.log('success! ', Endpoints);

	        for(let endpoint of Endpoints){

	            // look for the endpoint with the token that matches this users device token
	            if( endpoint.Attributes.Token === message.device_token ){
	                endpointFound = endpoint;
	                break;
	            }

	        }

	        // if endpoint found, publish message to this endpoint
	        // else create new platform endpoint, then publish to that new endpoint
	        if( endpointFound ){
	            // console.log('endpoint found!');

	            const published = _publishToSNSEndpoint({endpoint: endpointFound, message});

	            published.then(success => {
	                // console.log('successfully published!', success);

	            }).catch(fail => {
	                // console.log('publish failed: ', fail);
	            });
	        }else{
	            // console.log('no endpoint found');
	        }

	    }).catch(err => {
	        // console.log('error: ', err);

	        // const endpoint = _createPlatformEndpoint()

	        // endpoint.then(end => {
	        //     console.log('endpoint success!! ', end);
	        // });

	        // endpoint.catch(er => {
	        //     console.log('endpoint fail ', er);
	        // })

	    });
	}
	
}