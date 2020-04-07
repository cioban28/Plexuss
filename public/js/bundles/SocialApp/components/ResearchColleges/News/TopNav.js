import React, { Component } from 'react'
import { Link, withRouter } from 'react-router-dom';
import { connect } from 'react-redux';

class TopNav extends Component{
    constructor(props) {
        super(props);

        const { match } = this.props;
        this.state = {
            subcategory: (match && match.params && match.params.name) || '',
            showMoreOptions: true,
        }
        this.handleTabClick = this.handleTabClick.bind(this);
        this.handleMoreOptionsMouseEnter = this.handleMoreOptionsMouseEnter.bind(this);
        this.handleMoreOptionsMouseLeave = this.handleMoreOptionsMouseLeave.bind(this);
    }

    handleTabClick(tabName, fromMoreOptions) {
        if(this.state.subcategory === tabName) return;

        this.setState({ subcategory: tabName, showMoreOptions: false })
        this.props.resetNewsData();
    }

    handleMoreOptionsMouseEnter() {
        this.setState({ showMoreOptions: true });
    }

    handleMoreOptionsMouseLeave() {
        this.setState({ showMoreOptions: false });
    }

    render(){
        const { subcategory, showMoreOptions } = this.state;

        return(
            <div className='social-news-top-nav-cont'>
                <div className="news_top_nav">
                    <div className="news_items_list">
                        <div className={`news_item_name ${!subcategory && 'all-news active'}`}>
                            <Link onClick={this.handleTabClick.bind(this, '')} to='/news'>All</Link>
                        </div>
                        <div className={`news_item_name ${subcategory === 'survival-guides' && 'survival-guides active'}`}>
                            <Link onClick={this.handleTabClick.bind(this, 'survival-guides')} to='/news/subcategory/survival-guides'>Survival Guides</Link>
                        </div>
                        <div className={`news_item_name hide-getting-into-college ${subcategory === 'getting-into-college' && 'getting-into-college active'}`}>
                            <Link onClick={this.handleTabClick.bind(this, 'getting-into-college')} to='/news/subcategory/getting-into-college'>Getting Into College</Link>
                        </div>
                        <div className={`news_item_name hide-ranking ${subcategory === 'ranking' && 'ranking active'}`}>
                            <Link onClick={this.handleTabClick.bind(this, 'ranking')} to='/news/subcategory/ranking'>Ranking</Link>
                        </div>
                        <div className={`news_item_name hide-college-sports ${subcategory === 'college-sports' && 'college-sports active'}`}>
                            <Link onClick={this.handleTabClick.bind(this, 'college-sports')} to='/news/subcategory/college-sports'>College Sports</Link>
                        </div>
                        <div className={`news_item_name hide-financial-aid ${subcategory === 'financial-aid' && 'financial-aid active'}`}>
                            <Link onClick={this.handleTabClick.bind(this, 'financial-aid')} to='/news/subcategory/financial-aid'>Financial Aid</Link>
                        </div>
                        <div className={`news_item_name hide-campus-life ${subcategory === 'campus-life' && 'campus-life active'}`}>
                            <Link onClick={this.handleTabClick.bind(this, 'campus-life')} to='/news/subcategory/campus-life'>Campus Life</Link>
                        </div>
                        <div className='show-for-mobile-only'>
                            <MoreOptions
                                subcategory={subcategory}
                                showMoreOptions={showMoreOptions}
                                handleTabClick={this.handleTabClick}
                                handleMoreOptionsMouseEnter={this.handleMoreOptionsMouseEnter}
                                handleMoreOptionsMouseLeave={this.handleMoreOptionsMouseLeave}
                            />
                        </div>
                    </div>
                    <div className='hide-for-mobile-only'>
                        <MoreOptions
                            subcategory={subcategory}
                            showMoreOptions={showMoreOptions}
                            handleTabClick={this.handleTabClick}
                            handleMoreOptionsMouseEnter={this.handleMoreOptionsMouseEnter}
                            handleMoreOptionsMouseLeave={this.handleMoreOptionsMouseLeave}
                        />
                    </div>
                </div>
            </div>
        )
    }
}

const mapDispatchToProps = dispatch => ({
    resetNewsData: () => { dispatch({ type: 'RESET_NEWS_DATA' }) },
})

export default connect(null, mapDispatchToProps)(withRouter(TopNav));


function MoreOptions(props) {
    const { subcategory, showMoreOptions, handleTabClick, handleMoreOptionsMouseEnter, handleMoreOptionsMouseLeave } = props;

    return (
        <div className="_more_banner" onMouseEnter={handleMoreOptionsMouseEnter} onMouseLeave={handleMoreOptionsMouseLeave}>
            <div className="more_">More</div>
            <i className="fa fa-chevron-down"></i>
            {
                showMoreOptions && <ul className='more-options-list'>
                    <li className={`show-ranking ${subcategory === 'ranking' ? 'ranking active' : ''}`}>
                        <Link onClick={() => handleTabClick('ranking', true)} to='/news/subcategory/ranking'>Ranking</Link>
                    </li>
                    <li className={`show-getting-into-college ${subcategory === 'getting-into-college' ? 'getting-into-college active' : ''}`}>
                        <Link onClick={() => handleTabClick('getting-into-college', true)} to='/news/subcategory/getting-into-college'>Getting Into College</Link>
                    </li>
                    <li className={`show-college-sports ${subcategory === 'college-sports' ? 'college-sports active' : ''}`}>
                        <Link onClick={() => handleTabClick('college-sports', true)} to='/news/subcategory/college-sports'>College Sports</Link>
                    </li>
                    <li className={`show-financial-aid ${subcategory === 'financial-aid' ? 'financial-aid active' : ''}`}>
                        <Link onClick={() => handleTabClick('financial-aid', true)} to='/news/subcategory/financial-aid'>Financial Aid</Link>
                    </li>
                    <li className={`show-campus-life ${subcategory === 'campus-life' ? 'campus-life active' : ''}`}>
                        <Link onClick={() => handleTabClick('campus-life', true)} to='/news/subcategory/campus-life'>Campus Life</Link>
                    </li>
                    <li className={subcategory === 'careers' ? 'careers active' : ''}>
                        <Link to onClick={() => handleTabClick('careers', true)} to='/news/subcategory/careers'>Careers</Link>
                    </li>
                    <li className={subcategory === 'celebrity-alma-mater' ? 'celebrity-alma-mater active' : ''}>
                        <Link to onClick={() => handleTabClick('celebrity-alma-mater', true)} to='/news/subcategory/celebrity-alma-mater'>Celebrity Alma Mater</Link>
                    </li>
                </ul>
            }
        </div>
    )
}
