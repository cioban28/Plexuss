import { connect } from 'react-redux';
import Modal from 'react-modal';
import React, { Component } from 'react';

import { closeModal } from '../../actions/modal';

class CustomModal extends Component {

  render() {

    return (
      <div style={{...this.props.style}}>
        <Modal
          className={this.props.reactClassName}
          contentLabel={this.props.label}
          isOpen={this.props.isOpen}
          onRequestClose={this.props.handleClose}
          style={{...this.props.style}}
        >
          { this.props.children }
        </Modal>
      </div>
    );
  }
}

function mapStateToProps(state) {
  return {
    isOpen: state.modal.isOpen,
  }
}

function mapDispatchToProps(dispatch){
  return({
    handleClose: () => { dispatch(closeModal()) }
  })
}

export default connect(mapStateToProps, mapDispatchToProps)(CustomModal);
