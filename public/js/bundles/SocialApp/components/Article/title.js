import React from 'react'

function Title(props){
    let { title } = props;
    return(
        <div className="article_title">
            { title }
        </div>
    )
}
export default Title;