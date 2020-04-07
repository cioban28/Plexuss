import React from 'react'
import ReactLoading from "react-loading";
import './styles.scss'
export function SpinningBubbles(){
    return(
        <div className="home_loader">
            <ReactLoading type={'spinningBubbles'} color="#000" height={'40px'} width={'40px'}/>
        </div>
    )
}

export function Bubbles(){
    return(
        <div className="post_loader">
            <ReactLoading type={'bubbles'} color="#000" height={'40px'} width={'40px'}/>
        </div>
    )
}

export function BubblesForMsg(){
    return(
        <div className="msg-loader">
            <ReactLoading type={'bubbles'} color="#919191" height={'20px'} width={'20px'}/>
        </div>
    )
}

export function LoggingBubble(){
    return(
        <div className="log_loader">
            <div className="center_loader">
                <ReactLoading type={'spinningBubbles'} color="#000" height={'50px'} width={'50px'}/>
            </div>
        </div>
    )
}

export function SicMsgsBubble(){
    return(
        <div className="sic-msg-loader">
            <ReactLoading type={'spinningBubbles'} color="#000" height={'24px'} width={'24px'}/>
        </div>
    )
}

export function ThreadMsgsBubble(){
    return(
        <div className="msg-thread-loader">
            <ReactLoading type={'spinningBubbles'} color="#000" height={'24px'} width={'24px'}/>
        </div>
    )
}

export function ConvoMsgsBubble(){
    return(
        <div className="msg-thread-loader">
            <ReactLoading type={'spinningBubbles'} color="#000" height={'40px'} width={'40px'}/>
        </div>
    )
}