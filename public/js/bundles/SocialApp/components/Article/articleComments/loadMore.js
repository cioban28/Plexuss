import React, { Component } from 'react'

class LoadMore extends Component{
    render(){
        let { handleLoadAllComments, loadAllComment } = this.props;
        return(
            <div className="load_more_parent" onClick={() => handleLoadAllComments()}>
                { loadAllComment ? 'LOAD LESS COMMENTS' : 'LOAD MORE COMMENTS'}
            </div>
        )
    }
}
export default LoadMore;