import React from 'react';
import Modal from 'react-modal';
import { removeFriend, blockFriend , getNetworkingData} from './../../api/post'
Modal.setAppElement(document.getElementById('_SocialApp_Component'))
class DeleteConnection extends React.Component{
  constructor() {
    super();
    this.state = {
      optionBox: false,
      modalIsOpen: true,
    };
    this.openModal = this.openModal.bind(this);
    this.afterOpenModal = this.afterOpenModal.bind(this);
    this.closeModal = this.closeModal.bind(this);
    this.deleteConnection = this.deleteConnection.bind(this);
  }

  openModal() {
    let { deleteModal } = this.props;
    this.setState({modalIsOpen: deleteModal});
  }
  afterOpenModal() {
    // this.subtitle.style.color = '#f00';
  }
  closeModal() {
    let { confirmDeleteConnection } = this.props;
    confirmDeleteConnection();
    this.setState({modalIsOpen: false});
  }
  deleteConnection(id, type){
      let { logInUserId } = this.props;
    if (type == 'Connection') {
        let obj = {};
        obj.user_one_id = logInUserId;
        obj.user_two_id = id;
        obj.relation_status = 'Declined';
        obj.action_user = logInUserId;
        removeFriend(obj);
        getNetworkingData();
      this.setState({modalIsOpen: false});
    } else{

      this.setState({modalIsOpen: false});
    }
  }
  render(){
    let { entityName, id } = this.props;
    return(
      <span>
        <Modal
          isOpen={this.state.modalIsOpen}
          onAfterOpen={this.afterOpenModal}
          onRequestClose={this.closeModal}
          className="delete-modal"
          contentLabel="Delete connection"
        >
          <div className="delete_article_container">
            <div className="modal_heading">
                Delete {entityName}
                <div className="modal_x" style={{float: 'right'}} onClick={this.closeModal}>&#10005;</div>
            </div>
            <div className="delete_article_block">
              <div className="modal_message">
                Are you sure you want to delete this connection?
              </div>
            </div>
            <div className="action_button cancel" onClick={this.closeModal}> Cancel </div>
            <div className="action_button delete" onClick={() => this.deleteConnection(id, entityName)}> Delete </div>
          </div>
        </Modal>
      </span>
    )
  }
}

export default DeleteConnection;
