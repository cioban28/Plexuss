import React, { Component } from 'react'
import Switch from "react-switch";

class SubNotifications extends Component{
    render(){
        let { text, htmlFor, handleChange, id, checked } = this.props;
        return(
            <div className="subnotifications_container">
                <div className="subtext_container">{ text }</div>
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
                </div>
            </div>
        )
    }
}
export default SubNotifications