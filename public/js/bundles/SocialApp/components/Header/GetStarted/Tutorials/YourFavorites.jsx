import React from 'react';
import './styles.scss';

function YourFavorites(props) {
  return (
    <div id='your-favorites'>
      <table style={{width: '100%'}}>
        <tr>
          <td>1. Your Favorites</td>
          <td>2. Recommended by Plexuss</td>
        </tr>
        <tr>
          <td>3. Colleges recruiting you</td>
          <td>4. Colleges viewing you</td>
        </tr>
        <tr>
          <td>5. My Applications</td>
          <td>6. My Scholarships</td>
        </tr>
      </table>

      <div className='mtb-20'>
        <span>You can access your My Colleges Portal from the navigation bar. <img className='sic-icon-img' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/unnamed.png' /></span>
        <img className='mt-20' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/My Colleges indicator.PNG' />
      </div>

      <div className='mb-20'>
        <h5>1. <img className='sic-icon-img' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/favorites icon.png' /> Your Favorites</h5>
      </div>
    </div>
  )
}

export default YourFavorites;
