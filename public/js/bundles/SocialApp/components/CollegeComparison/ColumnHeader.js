import React, { Component } from 'react';
import './styles.scss';

class ColumnHeader extends Component {

  render() {
    const { handleSearchCollege, key } = this.props;

    return (
      <div className='owl-item active large-4 medium-4 small-4' style={{width: "100%"}}>
        <div className='item text-center pos-rel' data-slugs="">
          <div className='comapreSchooltitleArea'></div>

          <div className='border-right-gray border-bottom-gray' data-fieldfor="college_logo">
            <div className='show-for-small battle-icon mobile-battle'>
              <img src="/images/colleges/compare/battle-black.png" title="" alt="" />
            </div>

            <div id='addSchoolBoxCompareCol' className='addSchoolBox' onClick={handleSearchCollege}>
              <a className='hide-for-small desktopClickToadd' href="#" data-reveal-id="selectSchoolPopup">
                <img id="clickToAddSchoolImg" src="/images/colleges/compare/addclick.jpg" style={{verticalAlign: 'middle', margin: '0px auto', textAlign: 'center'}} alt="" />
              </a>

              <a className='show-for-small' href="#" data-reveal-id="selectSchoolPopup">
                <img src="/images/colleges/compare/mobile-add.png" className='compare-school-logo' style={{verticalAlign: 'middle'}} alt="" />
              </a>
              <div className='college-name'></div>
            </div>
          </div>
        </div>
      </div>
    );
  }
}

export default ColumnHeader;
