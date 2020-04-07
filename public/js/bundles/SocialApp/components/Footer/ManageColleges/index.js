import React, { Component } from 'react'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import './styles.scss'
import MenuHeader from './../common/MenuHeader/index'
import { setRenderManageCollegesIndex } from './../../../actions/college';

class ManageColleges extends Component{
    componentWillReceiveProps(nextProps) {
        if(nextProps.renderManageCollegesIndex) {
            this.props.setRenderManageCollegesIndex(false);
        }
    }

    render(){
        return(
            <div>
                <div className="mbl_banner">
                    <MenuHeader title={'MANAGE COLLEGES'}/>
                    <ul className="mbl_list">
                        <MenuCard img={'/images/social/Heart-Outline.png'} message={'My Favorites'} link={'/social/manage-colleges/favorites'}/>
                        <MenuCard img={'/images/social/Subtraction 29.png'} message={'My Recommendations'} link={'/social/manage-colleges/rec-by-plex'}/>
                        <MenuCard img={'/images/social/Schools seeking you.png'} message={'Colleges recruiting you'} link={'/social/manage-colleges/colleges-rec'}/>
                        <MenuCard img={'/images/social/noun_Eye_339819_000000.png'} message={'Colleges viewing you'} link={'/social/manage-colleges/colleges-view'}/>
                        <MenuCard img={'/images/social/noun_test_1243956_000000.png'} message={'My Applications'} link={'/social/manage-colleges/application'}/>
                        <MenuCard img={'/images/social/scholarships.png'} message={'My Scholarships'} link={'/social/manage-colleges/scholarship'}/>
                        <MenuCard img={'/images/social/trash.png'} message={'Trash'} link={'/social/manage-colleges/trash'}/>
                    </ul>
                </div>
            </div>
        )
    }
}
class MenuCard extends Component{
    render(){
        let { message, img, link } = this.props;
        return(
            <li>
              <Link className="row menu_card" to={link}>
                <div className="small-2 columns">
                    <img src={img} alt="" className="menu_icon"/>
                </div>
                <div className="small-9 columns">
                    <div className="message">{message}</div>
                </div>
                <div className="small-1 columns">
                    <div> <img src="/images/mobile_menu_arrow.png" className="arrow_img" alt=""/> </div>
                </div>
              </Link>
            </li>
        )
    }
}

const mapStateToProps = state => {
    return {
        renderManageCollegesIndex: state.colleges.renderManageCollegesIndex,
    }
}

const mapDispatchToProps = dispatch => {
    return {
       setRenderManageCollegesIndex: (value) => { dispatch(setRenderManageCollegesIndex(value)) },
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(ManageColleges)
