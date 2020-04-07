import React, { Component } from 'react'

class LiveNews extends Component {
    render(){
       var handleClick = this.props.handleClick;
       return (
         <section>
           <ul className="newslist">
             <li className="news-head">
               <i className="fa fa-angle-left angle-left" onClick={() => handleClick('home')}></i>
               <span> Live News</span>
             </li>
             <li>
               <a href="#">
                 <div>1h</div>
                 <img src="/social/images/live-news.png" />
                 <span>University of California Applications Open</span>
               </a>
             </li>
             <li>
               <a href="#">
                 <div>1h</div>
                 <img src="/social/images/live-news.png" />
                 <span>University of California Applications Open</span>
               </a>
             </li>
           </ul>
         </section>
       );
    }
 }
export default LiveNews;