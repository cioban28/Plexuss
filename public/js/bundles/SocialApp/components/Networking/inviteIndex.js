import React, { Component } from 'react'
import Invitations from './invitation'
import ImportContacts from './importContacts'
import ImportMessage from './ImportMessage'
import { Helmet } from 'react-helmet';

class InviteIndex extends Component{
    constructor(props){
        super(props)
        this.state={
            renderComponent: 'importedContacts',
        }
        this._renderSubComp = this._renderSubComp.bind(this);
        this.setSubComponent = this.setSubComponent.bind(this);
    }
    componentDidMount(){
        const { messageBox } = this.props;
        if(messageBox){
            this.setState({
                renderComponent: 'message',
            });
        }
    }
    _renderSubComp(){
        const { importContactsCount } = this.props;
        switch(this.state.renderComponent){
          case 'importedContacts': return <Invitations setSubComponent={this.setSubComponent}/>
          case 'importContacts': return <ImportContacts setSubComponent={this.setSubComponent}/>
          case 'message': return <ImportMessage setSubComponent={this.setSubComponent} importContactsCount={importContactsCount}/>
        }
    }
    setSubComponent(subComponent){
        this.setState({
            renderComponent: subComponent
        })
    }
    render(){
        return(
            <div>
                <Helmet>
                  <title>College Social Networking | Import Contacts | Plexuss</title>
                  <meta name="description" content="In this section, you will be able to import your contacts" />
                  <meta name="keywords" content="Colleges Network" />
                </Helmet>
               {this._renderSubComp()}
            </div>
        )
    }
}
export default InviteIndex;
