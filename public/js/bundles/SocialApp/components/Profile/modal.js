import React, { Component } from 'react';
import Modal from 'react-modal';
import { connect } from 'react-redux'
import { removeFriend, blockFriend } from './../../api/post'
import DeleteConnection from './../Networking/DeleteConnection'
import { addInConversationArray, addNewThread } from './../../actions/messages'
import { saveMessage, addThreadApi, getThreaData } from './../../api/messages'

const customStyles = {
    content : {
      top                   : '50%',
      left                  : '50%',
      right                 : 'auto',
      bottom                : 'auto',
      marginRight           : '-50%',
      transform             : 'translate(-50%, -50%)',
      border                : 'none',
      background            : 'transparent',
    }
};
Modal.setAppElement(document.getElementById('_SocialApp_Component'))
class BModal extends Component{
    constructor() {
        super();
        this.state = {
          showMenu: false,
          modalIsOpen: false,
          deleteModal: false,
        }
        this.toggleMenu = this.toggleMenu.bind(this);
        this.openModal = this.openModal.bind(this);
        this.afterOpenModal = this.afterOpenModal.bind(this);
        this.closeModal = this.closeModal.bind(this);
        this.openModalAndCloseMenu = this.openModalAndCloseMenu.bind(this);
        this.friendRemove = this.friendRemove.bind(this)
        this.blockPerson = this.blockPerson.bind(this);
        this.openChatBox = this.openChatBox.bind(this);
        this.confirmDeleteConnection = this.confirmDeleteConnection.bind(this);
    }
    confirmDeleteConnection(){
        this.setState({deleteModal: !this.state.deleteModal});
    }
    toggleMenu() {
        this.setState({
          showMenu: !this.state.showMenu
        });
    }
    openModal() {
        this.setState({modalIsOpen: true});
    }
    afterOpenModal() {
        // this.subtitle.style.color = '#f00';
    }
    closeModal() {
        this.setState({
          modalIsOpen: false,
        });
    }
    openModalAndCloseMenu(){
        this.setState({
          modalIsOpen: true,
          showMenu: false,
        });
    }
    friendRemove(){
        let { id, logInUserId } = this.props;
        let obj = {};
        obj.user_one_id = logInUserId;
        obj.user_two_id = id;
        obj.relation_status = 'Declined';
        obj.action_user = logInUserId;
        removeFriend(obj);
    }
    blockPerson(){
        let { id, logInUserId } = this.props;
        let obj = {};
        obj.user_one_id = logInUserId;
        obj.user_two_id = id;
        obj.relation_status = 'Blocked';
        obj.action_user = logInUserId;
        blockFriend(obj);
    }
    openChatBox(){
        let { user, logInUserId, id, messageThreads } = this.props;
        let index = messageThreads.findIndex(thread => thread.thread_type_id == id);
        let thread_room_list = ['post:room:'+id, 'post:room:'+logInUserId];
        if(index == -1){
            let msg = {
                message: 'You have added in your friend list',
                thread_id: -1,
                to_user_id: id,
                thread_type: 'users',
                user_id: logInUserId,
                thread_room: id+':'+logInUserId,
                thread_room_list: thread_room_list,
            };
            saveMessage(msg)
            .then(res=>{
                this.props.addNewThread(res.data);
                let data = {
                    thread_room: 'post:room:'+id,
                    id: res.data,
                    user_thread_room: 'post:room:'+logInUserId,
                }
                addThreadApi(data)
                let _id={
                    id: res.data,
                }
                getThreaData(_id)
                .then(()=>{
                    let t_id = {
                        thread_id: res.data,
                    }
                    this.props.addInConversationArray(t_id);
                    this.setThread(res.data);
                })
            })
        }else{
            this.props.addInConversationArray(messageThreads[index]);
        }
    }
    render(){
        let { user, accountSettings } = this.props;
        return(
            <span>
                <span>
                    <a className="button profile_button connected" onClick={this.toggleMenu}>
                        <div className="btn_hover">
                            <i className="fa fa-check" aria-hidden="true" ></i>
                            <span>Connected</span>
                            <i className="fa fa-caret-down" aria-hidden="true" ></i>
                        </div>
                        {this.state.showMenu &&
                            <div className="connect-options-menu">
                                <div className="rbr_btn btn_hover" onClick={this.confirmDeleteConnection} >Remove</div>
                                {/* <div className="rbr_btn btn_hover" onClick={this.openModalAndCloseMenu}>BLOCK</div> */}
                                {/* <div className="rbr_btn btn_hover">Report Abuse</div> */}
                            </div>
                        }
                    </a>
                    {(accountSettings === null || !!accountSettings && !!accountSettings.receive_messages) && <a className="button profile_button btn_hover" onClick={this.openChatBox}> <span>Message</span> </a> }
                </span>
                <Modal
                    isOpen={this.state.modalIsOpen}
                    onAfterOpen={this.afterOpenModal}
                    onRequestClose={this.closeModal}
                    style={customStyles}
                    contentLabel="Block Modal"
                >
                    <BlockModal user={user} blockPerson={this.blockPerson}/>
                </Modal>
                {
                    this.state.deleteModal &&
    
                    <DeleteConnection  id={this.props.id} logInUserId={this.props.user ? this.props.user.user_id : ''} entityName= "Connection" deleteModal={this.state.deleteModal} confirmDeleteConnection={this.confirmDeleteConnection}/>
                }
            </span>
        )
    }
  }

  class BlockModal extends Component{
      render(){
          let { user, blockPerson } = this.props;
          return(
                <div className="block_modal">
                    <div className="title">Are you sure you want to block</div>
                    <div className="name">{user.fname} {user.lname}</div>
                    <div className="button block_btn modal_btn" onClick={() => blockPerson()}>BLOCK USER</div>
                    <div className="button cancel_btn modal_btn">CANCEL</div>
                    <div className="footer_note">You will not see their profile, photos, comments or posts.</div>
                </div>
          )
      }
  }
function mapStateToProps(state){
    return{
        messageThreads: state.messages.messageThreads && state.messages.messageThreads.topicUsr,
    }
}
function mapDispatchToProps(dispatch) {
    return({
        addInConversationArray: (thread) => { dispatch(addInConversationArray(thread))},
        addNewThread: (id) => { dispatch(addNewThread(id))},
    })
}
export default connect(mapStateToProps, mapDispatchToProps)(BModal);