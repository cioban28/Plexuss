import React, { Component } from 'react';

export default class AddPublication extends Component {
    constructor(props) {
        super(props);
    }

    render() {
        const { _toggleFields } = this.props;

        return (
            <div onClick={_toggleFields} className='add-publication-button'>
                <div className='add-publication-plus-button'>+</div>
                <div className='add-publication-text'>Add a project</div>
            </div>
        );
    }
}