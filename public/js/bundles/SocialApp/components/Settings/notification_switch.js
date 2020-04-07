import React, { Component } from 'react'
import Switch from "react-switch";

class Notification_switch extends Component{
    render(){
        let { text, id, htmlFor, checked, handleChange, handleDetail, subOpen } = this.props
        return(
            <div className={"notifications_container "+(subOpen && 'sub-open')}>
                <div className="text_container">{text}</div>
                <div className="swicth_details_container">
                    <label htmlFor={htmlFor} className="switch">
                        <Switch
                            checked={checked}
                            onChange={handleChange}
                            uncheckedIcon={
                                <div className="unChecKIcon">OFF</div>
                            }
                            checkedIcon={
                                <div className="checkIcon">ON</div>
                            }
                            className="react-switch"
                            onColor="#2AC56C"
                            offColor="#DDDDDD"
                            id={id}
                            height={24}
                        />
                    </label>
                    <div className="details" onClick={() => handleDetail()}>
                        <span>Details</span>
                        <span className="carat_container"><i className="fa fa-caret-down"></i></span>
                    </div>
                </div>
            </div>
        )
    }
}

export default Notification_switch