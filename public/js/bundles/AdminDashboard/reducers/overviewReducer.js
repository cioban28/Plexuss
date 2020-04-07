// overviewReducer.js

import _ from 'lodash'
import { YOUTUBE_EMBED_START, YOUTUBE_EMBED_END, YOUTUBE_URL, VIMEO_EMBED, VIMEO_URL } from './../components/cms/International/constants'

var init = {};

export default (state = init, action) => {

	switch( action.type ){

		case 'PENDING':
		case 'RESET_SAVED':
		case 'GET_APP_LINK':
		case 'EDIT_APP_LINK':
		case 'SAVE_APP_LINK':
		case 'OVERVIEW:UPDATE':
		case 'APP_LINK_PENDING':
		case 'OVERVIEW:GET_APP_REQ':
		case 'OVERVIEW:SAVE_PENDING':
		case 'SET_NEW_OVERVIEW_ITEM':
		case 'OVERVIEW:SAVED_APP_REQ':
			return {...state, ...action.payload};

		case 'GET_OVERVIEW_DATA_DONE':
			var newState = {...state, ...action.payload}

			if( newState.images && newState.images.length > 0 ){
				var item = newState.images[0];
				item.name = item.url.split('/').pop();
				item.bg = {backgroundImage: 'url('+item.url+')'};
				newState.new_img = item;
			}

			if( newState.videos && newState.videos.length > 0 ){
				var item = newState.videos[0];

				if( item.is_youtube === 1 ){
					item.source = YOUTUBE_EMBED_START+item.video_id+YOUTUBE_EMBED_END;
					item.name = YOUTUBE_URL+item.video_id;
				}else if( item.is_youtube === 3 ){
					item.source = VIMEO_EMBED+item.video_id;
					item.name = VIMEO_URL+item.video_id;
				}

				newState.new_vid = item;
			}

			return newState;

		case 'SAVE_OVERVIEW_ITEM':
			var pay = action.payload,
				newState = {...state, ..._.omit(pay, 'item')};

			// which_list should be in payload - it will be either 'images' or 'videos'
			if( pay.which_list ){

				// check if user already uploaded this item
				let already_exist = _.find(newState[pay.which_list], {url: pay.item.url});

				// if it it's not in list yet, add it
				if( !already_exist ) newState[pay.which_list] = newState[pay.which_list] ? [...newState[pay.which_list], pay.item] : [pay.item];

				newState[pay.which_item] = {};
			}

			return newState;

		case 'REMOVE_OVERVIEW_ITEM':
			var pay = action.payload,
				newState = {...state, pending: pay.pending, saved: pay.saved};

			newState[pay.which_list] = _.reject(newState[pay.which_list].slice(), {id: pay.item.id});

			return newState;

		case 'UPDATE_OVERVIEW_CONTENT':
			var pay = action.payload,
				newState = {...state};

			if( !newState.content ) newState.content = {overview_content: {}, overview_source: ''};

			newState.content[pay.name] = pay.val; 

			return newState;

		default:
			return state;
	}
	
}