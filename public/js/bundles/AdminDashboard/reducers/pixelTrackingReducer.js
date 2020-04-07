import { filter } from 'lodash';

var init = {};

export default (state = init, action) => {
    switch( action.type ){
        case 'PIXEL_TRACKING:UPDATE':
            return { ...state, ...action.payload };

        case 'PIXEL_TRACKING:UPDATE_AD_LINKS':
            return updateAdLinks(state, action.payload);

        case 'PIXEL_TRACKING:REMOVE_AD_LINK':
            return removeAdLink(state, action.payload);
        
        default:
            return state;
    }
}

const updateAdLinks = (state, payload) => {
    const newState = {...state};
    const currentAdLink = payload.adLink;

    const adLinks = newState.ad_links;

    adLinks.forEach(link => {
        if (link.cid == currentAdLink.cid) {
            link.hasNavigated = true;
        }
    });

    newState['removePixelTestPending'] = payload.removePixelTestPending;
    newState['removePixelTestStatus'] = 'success';
    newState['currentAdLink'] = currentAdLink;
    newState['ad_links'] = adLinks;

    return newState;
}

const removeAdLink = (state, payload) => {
    const newState = {...state};
    const currentAdLink = payload.adLink;
    const adLinks = newState.ad_links;

    newState['ad_links'] = filter(adLinks, (link) => link.cid !== currentAdLink.cid);
    newState['checkPixelTrackedPending'] = payload.checkPixelTrackedPending;

    return newState;
}