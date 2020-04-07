import axios from 'axios';

export const updateData = (data) => {
    return {
        type: 'PIXEL_TRACKING:UPDATE',
        payload: data,
    }
}

export const removePixelTestAdClicks = (adLink) => {
    adLink['utm_source'] = 'test_test_test';

    return (dispatch) => {
        dispatch({
            type: 'PIXEL_TRACKING:UPDATE', 
            payload: { removePixelTestPending: true }
        });

        axios({
            method: 'POST',
            url: '/sales/ajax/removePixelTestAdClicks',
            data: {adLink}

        }).then(response => {
            if (response.data == 'success') {
                dispatch({
                    type: 'PIXEL_TRACKING:UPDATE_AD_LINKS', 
                    payload: { removePixelTestPending: false, adLink: adLink }
                });
            } else {
                alert('something went wrong');
                dispatch({
                    type: 'PIXEL_TRACKING:UPDATE', 
                    payload: { removePixelTestPending: false }
                });
            }

        }).catch(response => {
            dispatch({
                type: 'PIXEL_TRACKING:UPDATE', 
                payload: { removePixelTestPending: false }
            });
        });
    }
}

export const checkPixelTracked = (adLink) => {
    adLink['utm_source'] = 'test_test_test';

     return (dispatch) => {
        dispatch({
            type: 'PIXEL_TRACKING:UPDATE', 
            payload: { checkPixelTrackedPending: true }
        });

        axios({
            method: 'POST',
            url: '/sales/ajax/checkPixelTracked',
            data: { adLink }

        }).then(response => {
            if (response.data == 'success') {
                dispatch({
                    type: 'PIXEL_TRACKING:REMOVE_AD_LINK', 
                    payload: { checkPixelTrackedPending: false, adLink: adLink }
                });
            } else {
                alert('something went wrong');
                dispatch({
                    type: 'PIXEL_TRACKING:UPDATE', 
                    payload: { checkPixelTrackedPending: false }
                });
            }
        }).catch(response => {
            dispatch({
                type: 'PIXEL_TRACKING:UPDATE', 
                payload: { checkPixelTrackedPending: false }
            });
        });
    }
}

export const getPixelTrackedTestingLogs = () => {
    return (dispatch) => {
        dispatch({
            type: 'PIXEL_TRACKING:UPDATE', 
            payload: { getPixelTrackedTestingLogsPending: true }
        });

        axios({
            method: 'GET',
            url: '/sales/ajax/getPixelTrackedTestingLogs',
        }).then(response => {
            dispatch({
                type: 'PIXEL_TRACKING:UPDATE', 
                payload: { getPixelTrackedTestingLogsPending: false, pixelTrackedTestingLogs: response.data }
            });
        }).catch(response => {
            dispatch({
                type: 'PIXEL_TRACKING:UPDATE', 
                payload: { getPixelTrackedTestingLogsPending: false }
            });
        });
    }
}