import React, { Component } from 'react'

class Title extends Component{
    render(){
        let { title, handleTitle } = this.props;
        return(
            <div className="subject-area">
                <div className="block-headings">
                    <span>
                        Title
                    </span>
                </div>
                <input type="text" name="title" value={title} onChange={handleTitle}/>
            </div>
        )
    }
}
export default Title;