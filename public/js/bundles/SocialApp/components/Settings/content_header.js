import React, { Component } from 'react'
class Header extends Component {
    render(){
        let { imgSrc, title, imgClass, backClickHandler } = this.props;
        return(
            <div className="header_container small-12">
                <div className="show-for-small-only small-3" style={{fontSize: "19px", cursor: "pointer"}} onClick={backClickHandler}>
                    <span>
                        <i className="fa fa-angle-left" />
                    </span>
                    <span>&nbsp;Back</span>
                </div>
                <div className="img_container hide-for-small-only">
                    <img className={imgClass} src={imgSrc} alt=""/>
                </div>
                <div className="heading hide-for-small-only">
                    {title}
                </div>
                <div className="small-9 show-for-small-only">
                    <div style={{marginLeft: `${title === 'PRIVACY' ? '50%' : '0%'}`, float: "right", display: "-webkit-inline-box"}}>
                        <div className="img_container">
                            <img className={imgClass} src={imgSrc} alt=""/>
                        </div>
                        <div className="heading">
                            {title}
                        </div>
                    </div>
                </div>

            </div>
        )
    }
}

export default Header;