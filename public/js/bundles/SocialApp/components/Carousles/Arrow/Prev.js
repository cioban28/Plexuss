import React from 'react'

class PrevArrow extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { styleClassName, className, style, onPrevClick } = this.props;
        var is_display = styleClassName ? 'block' : 'none';
        return (
        <div
            className={className}
            style={{ ...style, 'background': 'url("/images/left-arrow.png")', 'backgroundPositionX': '-2px', 'borderRadius': '30px', 'boxShadow':'0px 2px 6px rgba(0,0,0,.3)', 'display':`${is_display}`}}
            onClick={onPrevClick}
        />
        );
    }
}

export default PrevArrow;