// uploadedItem.js

import React from 'react'
import selectn from 'selectn'
import createReactClass from 'create-react-class'

import { setNewItem, removeItem } from './../../../../../actions/overviewActions'
import { YOUTUBE_EMBED_START, YOUTUBE_EMBED_END, YOUTUBE_URL, VIMEO_EMBED, VIMEO_URL } from './../../../International/constants'

export default createReactClass({
	_getImage(){
		let { item } = this.props,
			_item = {...item};

		_item.name = item.url.split('/').pop();
		_item.bg = {backgroundImage: 'url('+item.url+')'};

		return _item;
	},

	_getVideo(){
		let { item } = this.props,
			_item = {...item};

		if( _item.is_youtube === 1 ){
			_item.source = YOUTUBE_EMBED_START+_item.video_id+YOUTUBE_EMBED_END;
			_item.name = YOUTUBE_URL+_item.video_id;
		}else if( _item.is_youtube === 3 ){
			_item.source = VIMEO_EMBED+_item.video_id;
			_item.name = VIMEO_URL+_item.video_id;
		}

		return _item;
	},

	_isSelected(){
		let { dispatch, overview, item } = this.props,
			_item = item.is_youtube ? this._getVideo() : this._getImage(),
			new_item = _item.is_youtube ? 'new_vid' : 'new_img';

		return _item.id === selectn(new_item+'.id', overview) ? 'selected' : '';
	},

	render(){
		let { dispatch, item } = this.props,
			_item = item.is_youtube ? this._getVideo() : this._getImage(),
			isSelected = this._isSelected();

		return (
			<div className={"overview-item "+isSelected}>
				<div
					style={ _item.bg || {} }
					onClick={ () => dispatch( setNewItem(_item) ) }
					className="thumb">

						{ _item.video_id && <iframe
												width="100%"
												height="100%"
												src={ _item.source }
												frameBorder="0"
												allowFullScreen /> }

				</div>

				<div
					onClick={ () => dispatch( setNewItem(_item) ) }
					className="name">
					{ _item.name || '' }
				</div>

				<div className="remove">
					<div onClick={ () => dispatch( removeItem(_item) ) }>Remove</div>
				</div>
			</div>
		);
	}
});
