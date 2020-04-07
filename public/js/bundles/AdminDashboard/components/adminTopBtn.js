import React from 'react';
import createReactClass from 'create-react-class'

export default createReactClass({

	render() {
		var { imgsrc , text, href , revealId } = this.props;

		return (
			<a className="radius button action-bar-btn" href={href} data-reveal-id={revealId}>
				<div className="action-bar-content"><img src={imgsrc} alt="" /></div>
				<div className="action-bar-content">{text || ''}</div>
			</a>
		);
	}
});

