import React from 'react'
import Parser from 'html-react-parser';

function Content(props){
    let { content } = props;
    return(
        <div className="content">
            {
                Parser(content)
            }
        </div>
    )
}
export default Content;