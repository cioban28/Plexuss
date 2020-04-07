// CustomModal/index.js

import React from 'react'

export default React.createClass({
    componentWillMount(){
        let { closeMe } = this.props;
        if( closeMe ) document.addEventListener('click', (e) => { if( e.target.id === '_CustomModal' ) closeMe(); });
    },

    componentWillUnmount(){
        let { closeMe } = this.props;
        if( closeMe ) document.removeEventListener('click', (e) => { if( e.target.id === '_CustomModal' ) closeMe(); });
    },

    render(){
        let { children, classes } = this.props;

        return (React.createElement("section", {id: "_CustomModal"},  children ));
    }
});