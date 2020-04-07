import React from 'react';
import {Link} from 'react-router';
import PropTypes from 'prop-types';


/**********************************************************
*  Option on me "home" page
*
***********************************************************/
export default class ProfileOption extends React.Component{

  constructor(props){
    super(props);
  }

  render(){
    let {icon, iconStyle, title , description, link} = this.props;

    return(
      <div className="_profileOption clearfix">
        { link !== '/college-application'
                    ?
                    <Link to={link}>
              <div className="left" >
                {icon ? <img src={icon} style={iconStyle} />

                  :
                  <div>
                    {this.props.children}
                  </div>}
              </div>

              <div className="right">
                <div className="title">{title || ' '}</div>
                <div className="option-desc">{description || ' '}</div>
                <div className="arrow">&rsaquo;</div>
              </div>
            </Link>

                    :
                    <a href={link}>
                        <div className="left" >
                            {icon ? <img src={icon} style={iconStyle} />

                                :
                                <div>
                                    {this.props.children}
                                </div>}
                        </div>

                        <div className="right">
                            <div className="title">{title || ' '}</div>
                            <div className="option-desc">{description || ' '}</div>
                            <div className="arrow">&rsaquo;</div>
                        </div>

                    </a> }

      </div>
    );
  }
};

ProfileOption.proptypes = {
  description : PropTypes.string.isRequired,
  title: PropTypes.string.isRequired,

}
