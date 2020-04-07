import React, { Component } from 'react'
class HomeRightBar extends React.Component {
    render(){
       let { articles, handleClick } = this.props;
       return (
         <section>
           <ul className="rightbar-list">
             <li className="more_mbl">
               More  
             </li>
             <li>
               <a onClick={() => handleClick('my-articles')}>
                 <img src="/social/images/write-article.svg" />
                 <span>{'My Articles ('}{articles && Object.keys(articles).length + ')'}</span>
               </a>
             </li>
           </ul>
         </section>
       );
    }
 }
 export default HomeRightBar;