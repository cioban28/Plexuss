import React from 'react';
import { connectAndChatSubHeadings, promoteYourselfSubHeadings } from './constants';

export function Card(props){
  const { text, cardNo, handleCardClick, handleShowTutorials, activeHeading } = props;
  const cardText = text === promoteYourselfSubHeadings.promotePublicProfile ? 'Public Profile' : text;

  let cardImgLink = ''
  let cardImgClass = ''
  if(!cardNo) {
    cardImgLink = text === connectAndChatSubHeadings.myNetwork
      ? '/social/images/Icons/tab-network-sic.svg'
      : text === connectAndChatSubHeadings.myMessages
        ? '/social/images/Icons/sic-messages-active.svg'
        : 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/noun_lightbulb_1262995.svg';
    cardImgClass = text === connectAndChatSubHeadings.myNetwork || text === connectAndChatSubHeadings.myMessages ? 'iw_30' : 'bulb_img';
  }

  return(
    <li onClick={!!handleCardClick ? handleCardClick.bind(this, text) : handleShowTutorials.bind(this, text)}>
      <div className="need_help_card row">
        <div className="large-3 medium-3 small-3 columns">
        {
          !!cardNo && <div className='card-number'>
            { cardNo }
          </div>
        }
        {
          !cardNo && <img className={cardImgClass} src={cardImgLink} />
        }
        </div>
        <div className={`text large-9 medium-9 small-9 columns ${activeHeading === text ? 'active-heading' : ''}`}>{ cardText }</div>
      </div>
    </li>
  )
}
