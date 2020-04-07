import React, { Component } from 'react'
class ImportContacts extends Component{
    render(){
        let { setSubComponent, importContactsCount } = this.props;
        return(
            <div className="import_contacts">
                <div className="plexuss_images_parent">
                    <img src="/social/images/settings/Group 1365.png" />
                </div>
                <div className="_heading">
                    {"You've imported "+importContactsCount+" contacts"}
                </div>
                <div className="_desc_import_contacts">
                    Try adding contacts from another email address or click done to start making connections!
                </div>
                <div className="_btn_parent">
                    <div className="try_another_email" onClick={()=>setSubComponent('importContacts')}>Try another email</div>
                    <div className="done_for_now" onClick={()=>setSubComponent('importedContacts')}>Done for now</div>
                </div>
            </div>
        )
    }
}
export default ImportContacts;