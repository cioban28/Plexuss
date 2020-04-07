import React, { Component } from 'react';
import { connect } from 'react-redux'
import {withRouter} from 'react-router-dom'
import { getSchools } from '../../api/step';
import { setCaps } from '../../actions/step';
import _ from 'lodash'

class Step6 extends Component {
    constructor(props) {
        super(props)
        this.state = {
            save_route: '/get_started/save',
			get_route: '/get_started/getDataFor/step',
			step_num: this.props.currentPage,
			is_valid: false,
			is_sending: false,
            isLoading: false,
			back_route: null,
			next_route: null,
            redirect_url: null,
            showRedirectModal: false,
			save_btn_classes: 'right btn submit-btn text-center',
			cards: ['nrccuaSchools', 'secondBox', 'thirdBox'],
			save_has_been_clicked: !1,
            eddy_cards: [],
			picks: [],
			coveted: false,
            nrccua_cards: [[],[]],
        }
        this._setLoader = this._setLoader.bind(this)
        this.getCoveted = this.getCoveted.bind(this)
        this.save = this.save.bind(this)
        this._addSchool = this._addSchool.bind(this)
        this._filterCardsBasedOnCaps = this._filterCardsBasedOnCaps.bind(this)
        this._openLinkModal = this._openLinkModal.bind(this)
        this._removePick = this._removePick.bind(this)
        this._combineAllCards = this._combineAllCards.bind(this)
    }

    _setLoader(isLoading) {
        this.setState({
            isLoading: isLoading,
        });
    }

    _getSchools() {
        const _this = this;

        this.setState({ pending_card_list: true });
        getSchools()
        .then(() => {
            _this.setState({ pending_card_list: false});
        });
    }

	getCoveted(coveted){
		this.setState({
			covted: coveted
		});
    }
    
    save(e){
		e.preventDefault();

        const { picks } = this.state;

		var _this = this;

		this.setState({is_sending: !0});

		$.ajax({
            url: '/get_started/getRecruitedStepDone',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {done: 1, selected_nrccua_colleges: picks},
            type: 'POST'
        }).done(function(data){
            window.location.href = _this.state.next_route;
            // currently not working, need to find way to redirect between multiple Routers
            // if(!_.isEmpty(_this.state.picks)){
            //     _this.props.history.push(_this.state.next_route);
            // }
            // _this.props.history.push('/home');
		});

        if (!_.isEmpty(picks)) {
            $.ajax({
                url: '/ajax/getBingBackground/' + picks[0].school_name,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'GET',
            }).done(function(url) {
                $('body.step-7').css('background-image', 'url(' + url + ')');
            });
        }

        amplitude.getInstance().logEvent('profile_step_finish');
    }
    
    _addSchool(school){
        const ro_caps = this.props.ro_caps,
            newCaps = [];

        amplitude.getInstance().logEvent('add_college', { School: school.school_name, Type: school.ro_name, } );

        ro_caps.forEach(ro_cap => {
            if (ro_cap.ro_id == school.ro_id) {
                ro_cap.cap -= 1;
            }

            newCaps.push(ro_cap);
        });

		var newPicks = this.state.picks.slice();

        newPicks.push(school);
        this.props.setCaps(newCaps)

		this.setState({picks: newPicks});
	}

    _filterCardsBasedOnCaps(cards){
        const ro_caps = this.props.ro_caps,
            ro_ids_with_limit_reached = [],
            filtered_cards = [];

        ro_caps.forEach(ro_cap => {
            if (ro_cap.cap == 0) {
                ro_ids_with_limit_reached.push(ro_cap.ro_id);
            }
        });

        cards.forEach(cardStack => {
            let filter = cardStack.filter(card => ro_ids_with_limit_reached.indexOf(parseInt(card.ro_id)) === -1);
            filtered_cards.push(filter);
        });

        return filtered_cards;
    }

    _openLinkModal(school, url){
        this.setState({ ad_school: school, linkout: url, showRedirectModal: true });
    }

	_removePick(pick){
		var picks = this.state.picks.slice(),
			pickFound = null,
			_this = this;

		pickFound = _.find(picks, pick);
        picks = _.reject(picks, pickFound);
        _this.setState({picks: picks});
	}

    _combineAllCards(all_cards){
        if (!all_cards) {
            return [];
        }

        const all_combined_cards = [].concat(...all_cards),
            post_cards = [], linkout_cards = [], click_cards = [];


        all_combined_cards.forEach((college) => {
            switch(college.type) {
                case 'post':
                    post_cards.push(college);
                    break;

                case 'linkout': 
                    linkout_cards.push(college);
                    break;

                case 'click':
                    click_cards.push(college);
                    break;

                default:
                    break;
            }
        });

        // Order by post -> linkout -> click
        const orderedCards = [].concat(post_cards).concat(linkout_cards).concat(click_cards);

        return orderedCards;
    }

    componentWillMount(){	
		var classes = this.state.save_btn_classes, prev, next, num,
			Carousel = null, carou = [], _this = this;

		// Facebook event tracking
        fbq('track', 'GetStarted_Step7_CollegeRecruitment_Page');

		//build prev step route
		num = parseInt(this.state.step_num);
		prev = num - 1;
        next = num + 1;
        this.setState({
            back_route: '/get_started/'+prev,
            next_route: '/get_started/'+next
        })

        this._getSchools();
    }
    
    render() {
		document.body.className = 'step-6'
        var s = this.state,
			_this = this;

        const { eddy_cards, all_cards, ad_school, showRedirectModal } = this.state,

            filtered_cards = this._filterCardsBasedOnCaps(this.props.all_cards),

            all_combined_cards = this._combineAllCards(filtered_cards);

		return (
			<div className="step-container">
                <div>
                    <div className="row reco-container">

                        <div className="column small-12 medium-3">
                            <h3>Top College Recommendations</h3>
                            <div className="add-dir">{"Click 'Add' to engage with the college"}</div>
                            <div className="skip-dir">{'Click skip to get more recommendations'}</div>
                        </div>

                        <div className='hide-for-small-only'>
                            { filtered_cards.map(function(box, i){
                                return <RecommendationCard 
                                            key={ i } 
                                            eddy={ null }
                                            box={ box }
                                            ro_caps={ _this.props.ro_caps }
                                            route={ s.get_route }
                                            original_card_list={ box }
                                            pending_card_list={ s.pending_card_list }
                                            addSchool={ _this._addSchool }
                                            coveted={ _this.getCoveted }
                                            _setLoader={ _this._setLoader }
                                            _openLinkModal={ _this._openLinkModal } />
                            }) }

                        </div>

                        <div className='show-for-small-only'>
                            <RecommendationCard 
                                key={ all_combined_cards } 
                                eddy={ null }
                                box={ all_combined_cards } 
                                ro_caps={ _this.props.ro_caps }
                                route={ s.get_route }
                                original_card_list={ all_combined_cards }
                                pending_card_list={ s.pending_card_list }
                                addSchool={ _this._addSchool }
                                coveted={ _this.getCoveted } 
                                _setLoader={ _this._setLoader }
                                _openLinkModal={ _this._openLinkModal } />
                        </div>
                    </div>

                    <div className="row list-container">
                        <div className="column small-12 medium-3 small-text-center medium-text-right">
                            {'My Picks'}
                        </div>

                        <div className="column small-12 medium-9 small-text-center medium-text-left">
                            { s.picks.map(function(pk){
                                return <Pick key={pk.id} pick={pk} removePick={ _this._removePick } />
                            }) }
                        </div>
                    </div>

                    <div className="row save-container">
                        <div className="column small-12 small-text-center medium-text-right">
                            <button className="button radius save" onClick={ this.save } disabled={this.state.pending_card_list}>Next</button>
                        </div>
                    </div>
                </div>

				{ (s.is_sending || s.isLoading) ? <Loader /> : null }
			</div>
		);
    }
}


class Pick extends Component{
    constructor(props) {
        super(props)
        this.state = {
            hovering: false,
			openModal: false,
			base_url: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/',
        }

        this._showTip = this._showTip.bind(this)
        this._hideTip = this._hideTip.bind(this)
        this._remove = this._remove.bind(this)
        this._openModal = this._openModal.bind(this)
        this._closeModal = this._closeModal.bind(this)
    }

	_showTip(){
		this.setState({hovering: true});
	}

	_hideTip(){
		this.setState({hovering: false});
	}

	_remove(){
		var p = this.props;
		p.removePick( p.pick );
		this._closeModal();
	}

	_openModal(){
		this.setState({openModal: true});
	}

	_closeModal(){
		this.setState({openModal: false});
	}

	render(){
		var pick = this.props.pick,
			s = this.state,
			logo = {
				backgroundImage: 'url('+ (pick ? s.base_url+pick.logo_url : '') + ')',
			};

		return (
			<div className="pick-container">
				<div className="text-right remove" onClick={ this._openModal }>{'x'}</div>
				<div 
					onMouseEnter={ this._showTip } 
					onMouseLeave={ this._hideTip } 
					onTouchStart={ this._showTip } 
					onTouchEnd={ this._hideTip }
					className="img-container">
						<div className="bg-img" style={logo} />
						{ s.hovering ? <Tip title={ pick.school_name } /> : null }
				</div>

				<Modal 
					open={ s.openModal }
					pick={ pick }
					confirmRemove={ this._remove }
					cancelRemove={ this._closeModal } />
			</div>
		);
	}
}

class RecommendationCard extends Component {
    constructor(props) {
        super(props)
        this.state = {
            route: '',
			skip: 0,
			take: 10,
			card_list: [],
			pending: false,
			showEddy: false,
			cardsPassed: 0,
		}
		this._getCards = this._getCards.bind(this)
		this._add = this._add.bind(this)
		this._addSchoolsToList = this._addSchoolsToList.bind(this)
		this._nextCard = this._nextCard.bind(this)
		this._updateCollegeViewCount = this._updateCollegeViewCount.bind(this)
		this._currentCard = this._currentCard.bind(this)
		this._buildRoute = this._buildRoute.bind(this)
		this._increaseSkip = this._increaseSkip.bind(this)
		this._skip = this._skip.bind(this)
		this._destroyEddy = this._destroyEddy.bind(this)
    }

    _getCards(){
		var route = this._buildRoute(),
			_this = this;

		this.setState({pending: true});

		$.ajax({
			url: route,
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function(data) {
			if( data.carousel && _.isArray(data.carousel) && data.carousel.length > 0 ){
				_this._increaseSkip();
				_this._addSchoolsToList(data.carousel);
			}

			if(typeof data.coveted != "undefined"){
				 _this.props.coveted(data.coveted);
			}
		});
	}

	_addSchoolsToList(data){
		this.setState({
			card_list: this.state.card_list.concat(data),
			pending: false,
		});

		/* 	
			 only want to trigger view count for the very first round of schools
			 when we make an ajax call to get more, that school will have already been viewed, 
			 and we'll have 11 in the list after the second fetch, so we don't want to 
			 add a double view count for that last school 
		*/
		if( this.state.card_list.length <= 10 ) this._updateCollegeViewCount();
	}

	_nextCard(){
		var cards = this.state.card_list.slice(),
			showEd = false;

		cards.shift();
		this.setState({card_list: cards})

		// only want to show eddyAd after first card, so after first shift, 
		// permanently increase cardsPassed so that it never comes back in here
		if( this.state.cardsPassed === 0 ){
			showEd = true;
			this.setState({cardsPassed: 1})
		}

		// if( cards.length === 1 ) this._getCards();

		this._updateCollegeViewCount();
		this.setState({
			card_list: cards,
			showEddy: showEd,
		});
	}

	_updateCollegeViewCount(){
		var current_card = this._currentCard();

		if( current_card ){
			$.ajax({
	            url: '/get_started/savePickACollegeView',
	            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	            data: {college_id: current_card.college_id},
	            type: 'POST'
	        }).done(function(ret){
				// console.log(ret);
			});
		}
	}

	_currentCard(){
		var cards = this.state.card_list;
		return cards.length > 0 ? this.state.card_list[0] : null;
	}

	_buildRoute(){
		var p = this.props;
		return p.route + '_' + p.box + '_' + this.state.skip + '_' + this.state.take;
	}

	_increaseSkip(){
		this.setState({skip: this.state.skip + this.state.take})
	}

	_add(e){
		e.preventDefault();
		var p = this.props,
			_this = this,
			school = this._currentCard(),
			container = $('#recruitmeModal'),
            {_setLoader, _openLinkModal} = p;

        p.addSchool( school ); //add school to picks list

        _setLoader(true);

        $.ajax({
            url: '/ajax/homepage/saveGetStartedThreeCollegesPins',
            type: 'POST',
            data: { ro_id: school.ro_id, college_id: school.college_id },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).done((response) => {
            _setLoader(false);
            
            if (response.type === 'linkout' || response.type === 'click') {
                _openLinkModal(school, response.url);
            }
            
            _this._nextCard(); //remove this school from list

        });
	}

	_skip(e){
        e.preventDefault();

        const school = this._currentCard();

        amplitude.getInstance().logEvent('skip_college', { School: school.school_name } );

		this._nextCard();
	}

	_destroyEddy(){
		// on ad click, remove add
		this.setState({showEddy: false});
	}

    componentDidMount() {
        const { original_card_list } = this.props;

        if (!_.isEmpty(original_card_list)) {
            this.setState({ card_list: original_card_list});
        }
    }

	componentWillReceiveProps(newProps) {
        const { original_card_list } = this.props,
              { original_card_list: newOriginal_card_list } = newProps;

        if (!_.isEqual(original_card_list, newOriginal_card_list)) {
            this.setState({ card_list: newOriginal_card_list });
        }
    }

	render(){
		var s = this.state,
			p = this.props,
			card = this._currentCard();

		return (
			<div className="column small-12 medium-3 text-center">
				{
					card ?
					<div className="reco-card">
						<School card={card} />

                        { (card.type == 'linkout' || card.type == 'click')
                            ?

                            <div className="clearfix action-btns">
                                <div className='learn-more-button left' onClick={ this._add }>Visit</div>
                                <div className="left skip" onClick={ this._skip }>Skip</div>
                            </div>

                            :
    						<div className="clearfix action-btns">
    							<div className="left add" onClick={ this._add }>Add</div>
    							<div className="left skip" onClick={ this._skip }>Skip</div>
    						</div> }

						{/* only show eddy if recommendation card has eddy id, card has eddy_found prop, and showEddy is true - should show after the first card in reco Card 2 */}
						{ p.eddy && card.eddy_found === 'true' && s.showEddy ? <EddyAd eddy={p.eddy} destroyEddy={this._destroyEddy} /> : null }
						{ p.pending_card_list ? <Small_Loader /> : null }
					</div>                    
					:
					<NoMoreSchools pending={p.pending_card_list} />
				}

                { (card && (card.type == 'linkout' || card.type == 'click')) && <div className='sponsored-notice-text'>Sponsored</div> }

			</div>
		);
	}
}

class EddyAd extends Component {
    constructor(props) {
        super(props)
        this.state = {
            show: true
        }
        this._initEddy = this._initEddy.bind(this)
        this._destroyEddy = this._destroyEddy.bind(this)
        this._makeImpression = this._makeImpression.bind(this)
        this._adClicked = this._adClicked.bind(this)
    }

	_initEddy(){
		// not working - c62499c0-b398-45fe-82cc-e285ca8fe22a
		// old token working - ae39a601-2a7e-4229-a528-f1ab8b30f66c
		$('#'+this.props.eddy).eddyAd({
			placementtoken: 'ae39a601-2a7e-4229-a528-f1ab8b30f66c',
			useIframe: false,
			testmode: false,
			isWizard: false
		});
	}

	_destroyEddy(){
		// on ad click, remove add
		this.props.destroyEddy();
	}

	_makeImpression(){
		// track ad view
		$.ajax({
            url: '/addAdImpression',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {slug: 'get_started', company: 'eddy'},
            type: 'POST'
        }).done(function(data){
			// console.log('impression made: ', data);
		});
	}

	_adClicked(){
		// track ad click
		$.ajax({
            url: '/adClicked',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {slug: 'get_started', company: 'eddy'},
            type: 'POST'
        }).done(function(data){
			// console.log('ad clicked: ', data);
		});
	}

    componentWillMount(){
		$(document).on('click', '.advertisement, .reco-card .add, .reco-card .skip', this._destroyEddy);
	}

	componentDidMount(){
		this._initEddy();
		this._makeImpression();
	}

	componentWillUnmount(){
		$(document).off('click', '.advertisement, reco-card .add, .reco-card .skip', this._destroyEddy);
    }
    
	render(){
		var p = this.props,
			s = this.state;

		return ( s.show ) ?
			<div 
				id={p.eddy} 
				onClick={ this._adClicked }
				className="advertisement stylish-scrollbar-mini" />
			:
			null
	}
}

class NoMoreSchools extends Component{
	render(){
		var p = this.props;

		return (
			<div className="no-more-schools">
				{ p.pending ? 
					<div className="no-msg">Getting more schools...</div>
					: <div className="no-msg">No more schools in this list</div>
				}
						
				{ p.pending ? <Small_Loader /> : null }
			</div>
		);
	}
}

class School extends Component{
    constructor(props) {
        super(props)
        this.state = {
            showStats: false,
			logo_base: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/',
			max: 47,
        }
        this.showStats = this.showStats.bind(this)
        this.hideStats = this.hideStats.bind(this)
    }

	showStats(){
		this.setState({showStats: true});
	}

	hideStats(){
		this.setState({showStats: false});
	}

	render(){
		var s = this.state,
			card = this.props.card,
			logo = {
				backgroundImage: 'url('+(card ? ( s.logo_base + (card.logo_url || 'default-missing-college-logo.png') ) : '')+')',
			},
			school_name = card ? card.school_name : '',
            containerClasses = 'data-container' + ((card.type == 'linkout' || card.type == 'click') ? ' ad-redirect' : '');

		if( school_name.length > s.max ){
			school_name = school_name.slice(0, s.max)+'...';
		}

		return (
			<div className={containerClasses}>
				<div className="bgimg" style={logo} />
				<div className="name">{ school_name }</div>
				<div className="location">{ card ? (card.city || '') + ', ' + (card.state || '') : '' }</div>	
                { (card.type == 'linkout' || card.type == 'click') && <div className='online-notice-text'>Online</div> }
			</div>
		);
	}
}

/*
	** don't need these right now - but these go in the School component, in the data-container, under location
	*<div className="category">National University</div>*
	* s.showStats ? <Stats card={card} out={this.hideStats} /> : null *
*/

class Modal extends Component{
	render(){
		var p = this.props;

		return ( p.open ) ?
			<div className="modal">
				<div className="wrapper">
					<div className="text-center">{'Remove ' + p.pick.school_name + '?'}</div>
					<div className="text-center">
						<div className="remove-confirm" onClick={ p.confirmRemove }>Remove</div>
						<div className="cancel-confirm" onClick={ p.cancelRemove }>Cancel</div>
					</div>
				</div>
			</div>
			:
			null
	}
}

class Tip extends Component{
	render(){
		return (
			<div className="tip">
				<div>{ this.props.title }</div>
				<div className="arrow"></div>
			</div>
		);
    }
}

class Small_Loader extends Component{
	render(){
		return (
			<div className="pick-loader">
				<Loader size="sm" />
			</div>
		);
	}
}

class Loader extends Component{
	constructor(props) {
		super(props)
	}
	render(){
		var classes = 'gs-loader ';
		if (this.props.size) classes += this.props.size;
		return(
			<div className={classes}>
				<svg width="70" height="20">
          <rect width="20" height="20" x="0" y="0" rx="3" ry="3">
              <animate attributeName="width" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
              <animate attributeName="height" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
              <animate attributeName="x" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
              <animate attributeName="y" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
          </rect>
          <rect width="20" height="20" x="25" y="0" rx="3" ry="3">
              <animate attributeName="width" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
              <animate attributeName="height" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
              <animate attributeName="x" values="35;25;25;25;35" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
              <animate attributeName="y" values="10;0;0;0;10" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
          </rect>
          <rect width="20" height="20" x="50" y="0" rx="3" ry="3">
              <animate attributeName="width" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
              <animate attributeName="height" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
              <animate attributeName="x" values="60;50;50;50;60" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
              <animate attributeName="y" values="10;0;0;0;10" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
          </rect>
      </svg>
			</div>
		);
	}
}

const mapStateToProps = (state) =>{
    return{
        all_cards: state.steps.all_cards,
        ro_caps: state.steps.ro_caps,
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
        setCaps: (caps) => {dispatch(setCaps(caps))},
    }
}

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Step6));