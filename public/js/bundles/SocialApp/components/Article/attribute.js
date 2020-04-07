import React, { Component } from 'react'
class Attribute extends Component{
    render(){
        let { imageSrc, count, name, classname, handleScrollToElement } = this.props;
        return(
            <div className="attribute" onClick={(e) => handleScrollToElement(e)}>
                <div className="image_banner">
                    <img className={classname} src={imageSrc} alt=""/>
                </div>
                <div className="attribute_count">
                    {count}
                </div>
                <div className="attribute_name">
                    {name}
                </div>
            </div>
        )
    }
}
export default Attribute;