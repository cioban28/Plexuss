import React, { Component } from 'react'
import './styles.scss'
class IncognitoComment extends Component {
    render(){
        return(
            <div className="incognito_comment">
                <div className="img_container"><img src="/social/images/settings/active_options/noun_Ghost_367889_000000.png" alt=""/></div>
                <div>You are in incognito mode, please turn it off to post or comment.</div>
            </div>
        )
    }
}
export default IncognitoComment;