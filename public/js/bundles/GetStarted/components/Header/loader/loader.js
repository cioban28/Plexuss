import React from 'react'
import ReactLoading from "react-loading";
import './styles.scss'
export function SpinningBubbles(){
  // alert('rendering loader');
  // console.log('rendering loader');
    return(
        <div className="home_loader">
            <ReactLoading type={'spinningBubbles'} color="#000" height={'40px'} width={'40px'}/>
        </div>
    )
}
