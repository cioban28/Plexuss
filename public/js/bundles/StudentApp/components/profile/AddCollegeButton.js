import React, { Component } from 'react';

const BUTTON_STYLES = {
    width: '50px',
    height: '50px',
    borderRadius: '50%',
    backgroundColor: '#9f9f9f',
    color: '#fff',
    cursor: 'pointer',
    display: 'flex',
    textAlign: 'center',
    alignItems: 'center',
    fontSize: '10pt',
} 
export default class AddCollegeButton extends Component {
    constructor(props) {
        super(props);
    }

    render() {
        const { toggleModal } = this.props;
        return (
            <div onClick={toggleModal} style={BUTTON_STYLES}>
                <span style={{margin: 'auto',}}>Add +</span>
            </div>
        );
    }
}