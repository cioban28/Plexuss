import React from 'react'
class NextArrow extends React.Component {
    constructor(props) {
        super(props)
        this.clickArrow = this.clickArrow.bind(this)
    }

    clickArrow() {
        this.props.onNextClick(this.props.currentSlide, this.props.slideCount)
    }
    render() {
        const { className, style } = this.props;
        return (
        <div
            className={className}
            style={{ ...style, 'background': 'url("/images/right-arrow.png")', 'borderRadius': '30px', 'boxShadow':'0px 2px 6px rgba(0,0,0,.3)'}}
            onClick={this.clickArrow}
        />
        );
    }
}

export default NextArrow;