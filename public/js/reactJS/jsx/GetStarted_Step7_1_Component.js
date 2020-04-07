// GetStarted_Step7_Component.jsx

import CircularProgressbar from 'react-circular-progressbar';
import CustomModal from './CustomModal'
import { Provider } from 'react-redux'
import store from './stores/getStartedStore'
import InviteModal from './InviteModal';

var percentage;

function wasRedirected(){
    return JSON.parse(sessionStorage.getItem('college_id'));
};

function currentPercentage(pct){
    if(pct) percentage = pct;
    return percentage;
};

function justInquired(data){
	var elem = null, checkmark = '<span class="check">&#x02713;</span>';

	_.each(data, function(school){
		elem = $('.recruit-me-pls[data-id="'+school+'"]');
		if( elem.length > 0 ) elem.parent().html(checkmark);
	});
};

var GetStarted_Step7_Component = React.createClass({displayName: "GetStarted_Step7_Component",
	getInitialState: function(){
		return {
			save_route: '/get_started/save',
			get_route: '/get_started/getDataFor/step',
            majorDetails: $('#get_started_step7').data('major_details'),
			step_num: null,
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
            stepView: 'recommendations', // 'recommendations' || 'what-to-do-next' || 'premium-upgrade' || 'advertisement'
            nrccua_cards: [[],[]],
            all_cards: [[],[],[]],
            ro_caps: [],
		};
	},

	componentWillMount: function(){	
		var classes = this.state.save_btn_classes, prev, next, num,
			Carousel = null, carou = [], _this = this,
            url = window.location.href;

        if (url.indexOf('/next-steps') !== -1) {
            this.setState({stepView: 'what-to-do-next'});
            return;
        }

		// Facebook event tracking
        fbq('track', 'GetStarted_Step7_CollegeRecruitment_Page');

		//get current step num
		this.state.step_num = $('.gs_step').data('step');
		this.state.get_route += this.state.step_num;

		//build prev step route
		num = parseInt(this.state.step_num);
		prev = num - 1;
		next = num + 1;
		this.state.back_route = '/get_started/'+prev;
		this.state.next_route = '/get_started/'+next;

        this._getSchools();
        // this._getNRCCUASchools();
        // this._getEddySchools();
	},

    _setLoader: function(isLoading) {
        this.setState({
            isLoading: isLoading,
        });
    },

    _getSchools: function() {
        const _this = this;

        this.setState({ pending_card_list: true });
        $.ajax({
            url: '/ajax/homepage/getGetStartedThreeCollegesPins',
            type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

        }).done(function(cardsObj) {
            const cards = [],
                ro_caps = cardsObj.caps;

            for (const key in cardsObj) {
                if (key.indexOf('tab') !== -1)
                    cards.push(cardsObj[key]);
            }

            _this.setState({ all_cards: cards, pending_card_list: false, ro_caps: ro_caps });

        });
    },

	getCoveted: function(coveted){
		this.setState({
			covted: coveted
		});

	},
    
    _getNRCCUASchools: function(){
        const _this = this;

        this.setState({ pending_card_list: true });
        $.ajax({
            url: this.state.get_route + '_nrccuaSchools_0_203',
            type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

        }).done(function(data) {
            _this.setState({ pending_card_list: false });

            _this.setState({ all_cards: data.carousel });

            if (typeof data.coveted != "undefined") {
                _this.getCoveted(data.coveted);
            }
        });
    },

    _getEddySchools: function(){
        const _this = this;

        this.setState({ pending_eddy_card_list: true });

        $.ajax({
            url: this.state.get_route + '_educationDynamics_0_203',
            type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

        }).done(function(data) {
            if (!_.isEmpty(data.carousel)) {
                _this.setState({ pending_eddy_card_list: true, eddy_cards: data.carousel });
            }
        })
    },

    _splitIntoArrays: function(schools, numberOfArrays){
        const cards = [];

        for (let i = 0; i < numberOfArrays; i++) {
            cards.push([]);
        }

        let iterator = 0;

        schools.forEach(school => {
            cards[iterator].push(school);
            iterator = (iterator + 1) % cards.length;
        });

        // this.setState({ nrccua_cards: cards });

        return cards;
    },

	save: function(e){
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
			if(!_this.state.coveted){
				// window.location.href = "/premium-plans";

                _this.setState({ stepView: 'what-to-do-next', is_sending: !1 });
			}
			else{
				window.location.href = _this.state.next_route;
			}
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
	},

	_addSchool: function(school){
        const { ro_caps } = this.state,
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

		this.setState({picks: newPicks, ro_caps: newCaps});
	},

    _filterCardsBasedOnCaps: function(cards){
        const { ro_caps } = this.state,
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
    },

    _openLinkModal: function(school, url){
        this.setState({ ad_school: school, linkout: url, showRedirectModal: true });
    },

	_removePick: function(pick){
		var picks = this.state.picks.slice(),
			pickFound = null,
			_this = this;

		pickFound = _.findWhere(picks, pick);
        picks = _.reject(picks, pickFound);
        _this.setState({picks: picks});

		// if( pickFound ){
		// 	$.ajax({
	 //            url: '/ajax/recruiteme/adduserschooltotrash',
	 //            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	 //            data: {obj: JSON.stringify({"0": ''+pickFound.id})},
	 //            type: 'POST'
	 //        }).done(function(ret){
		// 		picks = _.reject(picks, pickFound);
		// 		_this.setState({picks: picks});
		// 	});
		// }
	},

    _combineAllCards: function(all_cards){
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
    },

	render: function(){
		var s = this.state,
			_this = this;

        const { eddy_cards, all_cards, ad_school, showRedirectModal } = this.state,

            filtered_cards = this._filterCardsBasedOnCaps(all_cards),

            all_combined_cards = this._combineAllCards(filtered_cards);

		return (
			React.createElement("div", {className: "step-container"}, 
                 this.state.stepView === 'recommendations' &&
                    React.createElement("div", null, 
        				React.createElement("div", {className: "row reco-container"}, 

        					React.createElement("div", {className: "column small-12 medium-3"}, 
        						React.createElement("h3", null, "Top College Recommendations"), 
        						React.createElement("div", {className: "add-dir"}, "Click 'Add' to engage with the college"), 
        						React.createElement("div", {className: "skip-dir"}, 'Click skip to get more recommendations')
        					), 

                            React.createElement("div", {className: "hide-for-small-only"}, 
            					 filtered_cards.map(function(box, i){
            						return React.createElement(RecommendationCard, {
            									key:  i, 
            									eddy:  null, 
            									box:  box, 
                                                ro_caps:  s.ro_caps, 
            									route:  s.get_route, 
                                                original_card_list:  box, 
                                                pending_card_list:  s.pending_card_list, 
            									addSchool:  _this._addSchool, 
            									coveted:  _this.getCoveted, 
                                                _setLoader:  _this._setLoader, 
                                                _openLinkModal:  _this._openLinkModal})
            					}) 

                            ), 

                            React.createElement("div", {className: "show-for-small-only"}, 
                                React.createElement(RecommendationCard, {
                                    key:  all_combined_cards, 
                                    eddy:  null, 
                                    box:  all_combined_cards, 
                                    ro_caps:  s.ro_caps, 
                                    route:  s.get_route, 
                                    original_card_list:  all_combined_cards, 
                                    pending_card_list:  s.pending_card_list, 
                                    addSchool:  _this._addSchool, 
                                    coveted:  _this.getCoveted, 
                                    _setLoader:  _this._setLoader, 
                                    _openLinkModal:  _this._openLinkModal})
                            )
        				), 

        				React.createElement("div", {className: "row list-container"}, 
        					React.createElement("div", {className: "column small-12 medium-3 small-text-center medium-text-right"}, 
        						'My Picks'
        					), 

        					React.createElement("div", {className: "column small-12 medium-9 small-text-center medium-text-left"}, 
        						 s.picks.map(function(pk){
        							return React.createElement(Pick, {key: pk.id, pick: pk, removePick:  _this._removePick})
        						}) 
        					)
        				), 

        				React.createElement("div", {className: "row save-container"}, 
        					React.createElement("div", {className: "column small-12 small-text-center medium-text-right"}, 
        						React.createElement("button", {className: "button radius save", onClick:  this.save}, "Finish!")
        					)
        				)
                    ), 

                 this.state.stepView === 'what-to-do-next' && 
                    React.createElement(WhatToDoNext, {
                        majorDetails: this.state.majorDetails, 
                        disableInviteModal: this.state.disableInviteModal, 
                        onBack: () => this.setState({ stepView: 'recommendations' }), 
                        onSelection: (route) => this.setState({ stepView: 'advertisement', nextRoute: route }), 
                        onPremiumPress: () => this.setState({ stepView: 'premium-upgrade'})}), 

                 this.state.stepView === 'premium-upgrade' && 
                    React.createElement(PremiumUpgradeInfo, {
                        onBack: () => this.setState({ stepView: 'what-to-do-next', disableInviteModal: true })}), 

                 this.state.stepView === 'advertisement' && 
                    React.createElement(Advertisement, {nextRoute: this.state.nextRoute}), 

                 showRedirectModal && 
                    React.createElement(RedirectNotice, {
                        url: this.state.linkout, 
                        school: ad_school, 
                        onClose: () => this.setState({ showRedirectModal: false })}), 

				 (s.is_sending || s.isLoading) ? React.createElement(Loader, null) : null
			)
		);
	}
});

const RedirectNotice = ({ url, school, onClose }) => {
    const onContinue = () => {
        amplitude.getInstance().logEvent('click_ad_redirect', {Partner: 'Eddy', Content: school.school_name} );
        window.open(url, '_blank');
        onClose();
    }

    const thisOnClose = () => {
        amplitude.getInstance().logEvent('click_ad_close', {Partner: 'Eddy', Content: school.school_name} );
        onClose();
    }

    return (
        React.createElement(CustomModal, {closeMe:  thisOnClose }, 
            React.createElement("div", {className: "modal get-started-redirect-modal"}, 
                React.createElement("div", {className: "closeMe", onClick:  thisOnClose }, "×"), 
                "You are about to be redirected to one of our Partners", 
                React.createElement("div", {onClick: onContinue, className: "get-started-redirect-button"}, "Continue")
            )
        )
    );
};

// Routes for WhatToDoNext
const DO_NEXT_ROUTES = [
    { label: 'Search For Colleges', route: '/college' },
    { label: 'Find Scholarships', route: '/scholarships' },
    { label: 'College Rankings', route: '/ranking' },
    { label: 'Compare Colleges', route: '/comparison' },
    { label: 'College News', route: '/news' },
    { label: 'College Admission Essays', route: '/news/catalog/college-essays' },
    { label: 'Find Majors', route: '/college', alternative_route: '/search' },
    { label: 'Upgrade to Premium', route: ''},
];

class WhatToDoNext extends React.Component {
    constructor(props) {
        super(props);

        this._buildOption = this._buildOption.bind(this);
        this._onPremium = this._onPremium.bind(this);
        this._onSelect = this._onSelect.bind(this);

        this.state = {
            showInviteModal: true,
        }
    }

    componentDidMount() {
        $('#get_started_breakcrumb').hide();
    }

    _onSelect(option) {
        const { onSelection, majorDetails } = this.props;

        let url_params = "";

        amplitude.getInstance().logEvent('click_recommended_action', {Section: option.label, Type: 'Site Page'} );

        if (option.label === 'Find Majors' && !_.isEmpty(majorDetails)) {
            url_params = `?type=majors&department=${majorDetails.category_slug}&term=${majorDetails.name}`;

            onSelection(option.alternative_route + url_params);
        } else {
            onSelection(option.route);
        }
    }

    _onPremium() {
        const { onPremiumPress } = this.props;
        
        amplitude.getInstance().logEvent('click_recommended_action', {Section: 'Upgrade to Premium', Type: 'Plexuss Monetization'} );

        onPremiumPress();
    }

    _buildOption(option, index) {
        const classNames = 'do-next-option' + ((option.label.indexOf('Upgrade to Premium') !== -1) ?  ' premium-option' : ''); 
        
        const onPress = option.label.indexOf('Upgrade to Premium') !== -1
            ? this._onPremium
            : () => this._onSelect(option);

        return (
            React.createElement("div", {className: classNames, onClick: onPress}, 
                 option.label
            )
        );
    }

    render() {
        const { onBack, disableInviteModal } = this.props;

        return (
            React.createElement("div", {className: "what-to-do-next-container step_container"}, 

                React.createElement("div", {className: "do-next-header"}, "What would you like to do next?"), 

                React.createElement("div", {className: "do-next-options-container"}, 
                     DO_NEXT_ROUTES.map(this._buildOption) 
                ), 

                 (!disableInviteModal && this.state.showInviteModal) && React.createElement(InviteModal, {closeMe: () => this.setState({ showInviteModal: false })})

            )
        );
    }
}

const PREMIUM_SAMPLE_COLLEGS = [
    {name: 'Harvard', label: 'Harvard University', logo_url: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/Harvard_University.png'},
    {name: 'MIT', label: 'Massachusetts Institute of Technology', logo_url: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/Massachusetts_Institute_of_Technology.png'},
];

class PremiumUpgradeInfo extends React.Component {
    constructor(props) {
        super(props);
    }

    _buildStaticSampleCollege(college, index) {
        return (
            React.createElement("div", {className: "sample-college"}, 
                React.createElement("div", {className: "college-logo"}, 
                    React.createElement("img", {src: college.logo_url})
                ), 
                React.createElement("div", {className: "college-label"}, college.label)
            )
        )
    }

    render() {
        const { onBack } = this.props;

        return (
            React.createElement("div", {className: "premium-upgrade-info-container"}, 
                React.createElement("div", {className: "top-container"}, 
                    React.createElement("div", {onClick: onBack, className: "back-button-chevron"}, "‹"), 
                    React.createElement("div", {className: "plexuss-logo-orange"}, React.createElement("img", {src: "/images/plexuss-white-p.png"}))
                ), 

                React.createElement("div", {className: "header-text"}, "Join Plexuss Premium Today!"), 
                React.createElement("div", {className: "smaller-header-text"}, "By becoming premium, you will have access to:"), 

                React.createElement("div", {className: "premium-details-container"}, 
                    React.createElement("div", {className: "upgrade-description"}, 
                        React.createElement("img", {className: "unlock-icon", src: "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/unlock-icon.png"}), 
                        React.createElement("div", {className: "upgrade-text"}, "Unlock 50 college essays that got students into top universities such as:")
                    ), 

                    React.createElement("div", {className: "sample-colleges-container"}, 
                         PREMIUM_SAMPLE_COLLEGS.map(this._buildStaticSampleCollege) 
                    )
                ), 

                React.createElement("div", {onClick: () => window.open('/checkout/premium', '_blank'), className: "inquire-upgrade-button"}, "Upgrade to premium for $499"), 
                React.createElement("div", {className: "back-button"}, React.createElement("span", {onClick: onBack}, "Back"))
            )
        )
    }
}


const EDX_COLLEGES = [
    {name: 'Harvard', label: 'Harvard University', 'logo_route': 'https://plexuss.com/adRedirect?company=edx&utm_source=getstarted_qualitycourses_logo_harvard&cid=1', text_route: 'https://plexuss.com/adRedirect?company=edx&utm_source=getstarted_qualitycourses_text_harvard&cid=1', logo_url: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/Harvard_University.png'},
    {name: 'MIT', label: 'Massachusetts Institute of Technology (MIT)', 'logo_route': 'https://plexuss.com/adRedirect?company=edx&utm_source=getstarted_qualitycourses_logo_mit&cid=1', text_route: 'https://plexuss.com/adRedirect?company=edx&utm_source=getstarted_qualitycourses_text_mit&cid=1', logo_url: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/Massachusetts_Institute_of_Technology.png'},
    {name: 'Chicago', label: 'University of Chicago', 'logo_route': 'https://plexuss.com/adRedirect?company=edx&utm_source=getstarted_qualitycourses_logo_mit&cid=1', text_route: 'https://plexuss.com/adRedirect?company=edx&utm_source=getstarted_qualitycourses_text_uchicago&cid=1', logo_url: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/University_of_Chicago.gif'},
    {name: 'Berkeley', label: 'University of California Berekley', 'logo_route': 'https://plexuss.com/adRedirect?company=edx&utm_source=getstarted_qualitycourses_logo_ucb&cid=1', text_route: 'https://plexuss.com/adRedirect?company=edx&utm_source=getstarted_qualitycourses_text_ucb&cid=1', logo_url: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/University_of_California_Berkeley.png'},
    {name: 'Columbia', label: 'Columbia University', 'logo_route': 'https://plexuss.com/adRedirect?company=edx&utm_source=getstarted_qualitycourses_logo_columbia&cid=1', text_route: 'https://plexuss.com/adRedirect?company=edx&utm_source=getstarted_qualitycourses_text_columbia&cid=1', logo_url: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/Columbia_University_in_the_City_of_New_York_170692.png'},
];

class Advertisement extends React.Component {
    constructor(props) {
        super(props);

        this._handleCountDown = this._handleCountDown.bind(this);
        
        this.state = {
            showPlexussLoader: false,
        }
    }

    componentDidMount() {
        const { nextRoute } = this.props;

        $('.plex-top').hide();
        $('#get_started_breakcrumb').hide();
        $('.configuring-account-top').css('display', 'flex');
        $('body').css({'background-image': 'none', 'background-color': '#000'});

        this._handleCountDown();

        $(document).on('click', '.configuring-account-top .configuring-account-continue-button', (event) => {
            window.location.href = nextRoute;
        });
    }

    _buildCollegeOptions(college, index) {
        return (
            React.createElement("div", {className: "college-option"}, 
                React.createElement("div", {className: 'college-option-logo ' + college.name, onClick: () => window.open(college.logo_route, '_blank')}, 
                    React.createElement("img", {className: "college-option-logo-image", src: college.logo_url})
                ), 
                React.createElement("div", {className: "college-option-label", onClick: () => window.open(college.text_route, '_blank')}, college.label)
            )
        );
    }

    _handleCountDown() {
        let interval = null,
            count = 3;

        interval = setInterval(() => {
            if (count == 0) {
                clearInterval(interval);
                $('.configuring-account-top .configuring-account-continue-button').show();
                this.setState({ showPlexussLoader: true });
            }

            $('#configuring-account-count').html((count && '(' + count + ')') || '...');

            count--;

        }, 1000);
    }

    render() {
        const { nextRoute } = this.props;

        return (
            React.createElement("div", {className: "full-screen-advertisement-container"}, 
                 this.state.showPlexussLoader && React.createElement(PlexussLoader, {nextRoute: nextRoute}), 
                React.createElement("div", {className: "edx-ad-container"}, 
                    React.createElement("img", {
                        className: "edx-logo", 
                        src: "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/ad_copies/edx-logo.png", 
                        onClick: () => window.location.href = 'https://plexuss.com/adRedirect?company=edx&utm_source=getstarted_qualitycourses_img_edxlogo&cid=1'}), 
                    
                    React.createElement("div", {className: "edx-description"}, "EdX offers the highest quality courses from institutions who share our commitment to excellence in teaching and learning."), 
                    
                    React.createElement("div", {
                        className: "learn-more-button", 
                        onClick: () => window.location.href = 'https://plexuss.com/adRedirect?company=edx&utm_source=getstarted_qualitycourses_cta_lrnmore&cid=1'}, 
                            "Learn more"
                    )

                ), 

                React.createElement("div", {className: "college-options"}, 
                    React.createElement("div", {className: "college-options-header"}, "Take free courses from:"), 
                     EDX_COLLEGES.map(this._buildCollegeOptions), 
                    React.createElement("div", {className: "more-button-option", onClick: () => window.location.href = 'https://plexuss.com/adRedirect?company=edx&utm_source=getstarted_qualitycourses_text_more&cid=1'}, "+ More")
                )
            )
        );
    }
}

class PlexussLoader extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            percentage: 0,
        };
    }

    componentDidMount() {
        const { nextRoute } = this.props;

        let count = 0,
            interval = null;

        interval = setInterval(() => {
            count++;

            this.setState({ percentage: parseFloat(count / 2) * 100 });

            if (count === 2) {
                clearInterval(interval);
                window.location.href = nextRoute;
            }

        }, 1000);
    }

    render() {
        return (
            React.createElement("div", {className: "plexuss-loader-container"}, 
                React.createElement("div", {className: "absolute-container"}, 
                    React.createElement("img", {className: "plexuss-logo-image", src: "/images/plexussLogoLetterBlack.png"}), 
                    React.createElement("div", {style: {position: 'relative'}}, 
                        React.createElement(CircularProgressbar, {className: "CircularProgressbar", background: true, percentage: this.state.percentage})
                    )
                )
            )
        );
    }
}

var Pick = React.createClass({displayName: "Pick",
	getInitialState: function(){
		return {
			hovering: false,
			openModal: false,
			base_url: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/',
		};
	},

	_showTip: function(){
		this.setState({hovering: true});
	},

	_hideTip: function(){
		this.setState({hovering: false});
	},

	_remove: function(){
		var p = this.props;
		p.removePick( p.pick );
		this._closeModal();
	},

	_openModal: function(){
		this.setState({openModal: true});
	},

	_closeModal: function(){
		this.setState({openModal: false});
	},

	render: function(){
		var pick = this.props.pick,
			s = this.state,
			logo = {
				backgroundImage: 'url('+ (pick ? s.base_url+pick.logo_url : '') + ')',
			};

		return (
			React.createElement("div", {className: "pick-container"}, 
				React.createElement("div", {className: "text-right remove", onClick:  this._openModal}, 'x'), 
				React.createElement("div", {
					onMouseEnter:  this._showTip, 
					onMouseLeave:  this._hideTip, 
					onTouchStart:  this._showTip, 
					onTouchEnd:  this._hideTip, 
					className: "img-container"}, 
						React.createElement("div", {className: "bg-img", style: logo}), 
						 s.hovering ? React.createElement(Tip, {title:  pick.school_name}) : null
				), 

				React.createElement(Modal, {
					open:  s.openModal, 
					pick:  pick, 
					confirmRemove:  this._remove, 
					cancelRemove:  this._closeModal})
			)
		);
	}
});

var RecommendationCard = React.createClass({displayName: "RecommendationCard",
	getInitialState: function(){
		return {
			route: '',
			skip: 0,
			take: 10,
			card_list: [],
			pending: false,
			showEddy: false,
			cardsPassed: 0,
		};
	},

    componentDidMount() {
        const { original_card_list } = this.props;

        if (!_.isEmpty(original_card_list)) {
            this.setState({ card_list: original_card_list});
        }
    },

	componentWillReceiveProps(newProps) {
        const { original_card_list } = this.props,
              { original_card_list: newOriginal_card_list } = newProps;

        if (!_.isEqual(original_card_list, newOriginal_card_list)) {
            this.setState({ card_list: newOriginal_card_list });
        }
    },

	_getCards: function(){
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
	},

	_addSchoolsToList: function(data){
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
	},

	_nextCard: function(){
		var cards = this.state.card_list.slice(),
			showEd = false;

		cards.shift();
		this.state.card_list = cards;

		// only want to show eddyAd after first card, so after first shift, 
		// permanently increase cardsPassed so that it never comes back in here
		if( this.state.cardsPassed === 0 ){
			showEd = true;
			this.state.cardsPassed = 1;
		}

		// if( cards.length === 1 ) this._getCards();

		this._updateCollegeViewCount();
		this.setState({
			card_list: cards,
			showEddy: showEd,
		});
	},

	_updateCollegeViewCount: function(){
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
	},

	_currentCard: function(){
		var cards = this.state.card_list;
		return cards.length > 0 ? this.state.card_list[0] : null;
	},

	_buildRoute: function(){
		var p = this.props;
		return p.route + '_' + p.box + '_' + this.state.skip + '_' + this.state.take;
	},

	_increaseSkip: function(){
		this.state.skip += this.state.take;
	},

	_add: function(e){
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


		// if( school && school.id ){
		// 	$.ajax({
		// 		url: '/ajax/recruiteme/'+school.id,
		// 		type: 'GET',
		// 		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		// 	})
		// 	.done(function(data) {
		// 		container.html(data);
		// 		container.foundation('reveal', 'open');
                
		// 		p.addSchool( school ); //add school to picks list
		// 		_this._nextCard(); //remove this school from list
		// 	});
		// }
	},

	_skip: function(e){
        e.preventDefault();

        const school = this._currentCard();

        amplitude.getInstance().logEvent('skip_college', { School: school.school_name } );

		this._nextCard();
	},

	_destroyEddy: function(){
		// on ad click, remove add
		this.setState({showEddy: false});
	},

	render: function(){
		var s = this.state,
			p = this.props,
			card = this._currentCard();

		return (
			React.createElement("div", {className: "column small-12 medium-3 text-center"}, 
				
					card ?
					React.createElement("div", {className: "reco-card"}, 
						React.createElement(School, {card: card}), 

                         (card.type == 'linkout' || card.type == 'click')
                            ?

                            React.createElement("div", {className: "clearfix action-btns"}, 
                                React.createElement("div", {className: "learn-more-button left", onClick:  this._add}, "Visit"), 
                                React.createElement("div", {className: "left skip", onClick:  this._skip}, "Skip")
                            )

                            :
    						React.createElement("div", {className: "clearfix action-btns"}, 
    							React.createElement("div", {className: "left add", onClick:  this._add}, "Add"), 
    							React.createElement("div", {className: "left skip", onClick:  this._skip}, "Skip")
    						), 

						/* only show eddy if recommendation card has eddy id, card has eddy_found prop, and showEddy is true - should show after the first card in reco Card 2 */
						 p.eddy && card.eddy_found === 'true' && s.showEddy ? React.createElement(EddyAd, {eddy: p.eddy, destroyEddy: this._destroyEddy}) : null, 
						 p.pending_card_list ? React.createElement(Small_Loader, null) : null
					)                    
					:
					React.createElement(NoMoreSchools, {pending: p.pending_card_list}), 
				

                 (card && (card.type == 'linkout' || card.type == 'click')) && React.createElement("div", {className: "sponsored-notice-text"}, "Sponsored")

			)
		);
	}
});

var EddyAd = React.createClass({displayName: "EddyAd",
	getInitialState: function(){
		return {
			show: true
		};
	},

	componentWillMount: function(){
		$(document).on('click', '.advertisement, .reco-card .add, .reco-card .skip', this._destroyEddy);
	},

	componentDidMount: function(){
		this._initEddy();
		this._makeImpression();
	},

	componentWillUnmount: function(){
		$(document).off('click', '.advertisement, reco-card .add, .reco-card .skip', this._destroyEddy);
	},

	_initEddy: function(){
		// not working - c62499c0-b398-45fe-82cc-e285ca8fe22a
		// old token working - ae39a601-2a7e-4229-a528-f1ab8b30f66c
		$('#'+this.props.eddy).eddyAd({
			placementtoken: 'ae39a601-2a7e-4229-a528-f1ab8b30f66c',
			useIframe: false,
			testmode: false,
			isWizard: false
		});
	},

	_destroyEddy: function(){
		// on ad click, remove add
		this.props.destroyEddy();
	},

	_makeImpression: function(){
		// track ad view
		$.ajax({
            url: '/addAdImpression',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {slug: 'get_started', company: 'eddy'},
            type: 'POST'
        }).done(function(data){
			// console.log('impression made: ', data);
		});
	},

	_adClicked: function(){
		// track ad click
		$.ajax({
            url: '/adClicked',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {slug: 'get_started', company: 'eddy'},
            type: 'POST'
        }).done(function(data){
			// console.log('ad clicked: ', data);
		});
	},

	render: function(){
		var p = this.props,
			s = this.state;

		return ( s.show ) ?
			React.createElement("div", {
				id: p.eddy, 
				onClick:  this._adClicked, 
				className: "advertisement stylish-scrollbar-mini"})
			:
			null
	}
});

var NoMoreSchools = React.createClass({displayName: "NoMoreSchools",
	render: function(){
		var p = this.props;

		return (
			React.createElement("div", {className: "no-more-schools"}, 
				 p.pending ? 
					React.createElement("div", {className: "no-msg"}, "Getting more schools...")
					: React.createElement("div", {className: "no-msg"}, "No more schools in this list"), 
				
						
				 p.pending ? React.createElement(Small_Loader, null) : null
			)
		);
	}
});

var Stats = React.createClass({displayName: "Stats",
	getInitialState: function(){
		return {
			img_base: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/',
			default_img: 'no-image-default.png',
		};
	},

	render: function(){
		var p = this.props,
			s = this.state,
			card = p.card,
			bg = {
				backgroundImage: 'url('+(card && card.img_url ? s.img_base+card.img_url : s.img_base+s.default_img )+')',
			};

		return (
			React.createElement("div", {className: "stats-container text-left"}, 
				React.createElement("div", {className: "bg", style: bg}), 
				React.createElement("div", {className: "info"}, 
					React.createElement("div", {className: "text-center title"}, "Why this school?"), 
					React.createElement("div", {className: "labl"},  'Financial Compatibility' ), 
					React.createElement("div", {className: "stat"},  '$' || ''), 
					React.createElement("div", {className: "labl"},  'Start Date' ), 
					React.createElement("div", {className: "stat"},  '11/7/2016' || ''), 
					React.createElement("div", {className: "labl"},  'Major' ), 
					React.createElement("div", {className: "stat"},  'Computer Science' || '')
				), 
				React.createElement("div", {className: "layer", 
					onTouchEnd:  this.props.out, 
					onMouseOut:  this.props.out})
			)
		);
	}
});

var School = React.createClass({displayName: "School",
	getInitialState: function(){
		return {
			showStats: false,
			logo_base: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/',
			max: 47,
		};
	},

	showStats: function(){
		this.setState({showStats: true});
	},

	hideStats: function(){
		this.setState({showStats: false});
	},

	render: function(){
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
			React.createElement("div", {className: containerClasses}, 
				React.createElement("div", {className: "bgimg", style: logo}), 
				React.createElement("div", {className: "name"},  school_name ), 
				React.createElement("div", {className: "location"},  card ? (card.city || '') + ', ' + (card.state || '') : ''), 	
                 (card.type == 'linkout' || card.type == 'click') && React.createElement("div", {className: "online-notice-text"}, "Online")
			)
		);
	}
});

/*
	** don't need these right now - but these go in the School component, in the data-container, under location
	*<div className="category">National University</div>*
	* s.showStats ? <Stats card={card} out={this.hideStats} /> : null *
*/

var Modal = React.createClass({displayName: "Modal",
	render: function(){
		var p = this.props;

		return ( p.open ) ?
			React.createElement("div", {className: "modal"}, 
				React.createElement("div", {className: "wrapper"}, 
					React.createElement("div", {className: "text-center"}, 'Remove ' + p.pick.school_name + '?'), 
					React.createElement("div", {className: "text-center"}, 
						React.createElement("div", {className: "remove-confirm", onClick:  p.confirmRemove}, "Remove"), 
						React.createElement("div", {className: "cancel-confirm", onClick:  p.cancelRemove}, "Cancel")
					)
				)
			)
			:
			null
	}
});

var Tip = React.createClass({displayName: "Tip",
	render: function(){
		return (
			React.createElement("div", {className: "tip"}, 
				React.createElement("div", null,  this.props.title), 
				React.createElement("div", {className: "arrow"})
			)
		);
	}
});

var Small_Loader = React.createClass({displayName: "Small_Loader",
	render: function(){
		return (
			React.createElement("div", {className: "pick-loader"}, 
				React.createElement(Loader, {size: "sm"})
			)
		);
	}
});

var Loader = React.createClass({displayName: "Loader",
	render: function(){
		var classes = 'gs-loader ';
		if( this.props.size ) classes += this.props.size;

		return(
			React.createElement("div", {className: classes}, 
				React.createElement("svg", {width: "70", height: "20"}, 
                    React.createElement("rect", {width: "20", height: "20", x: "0", y: "0", rx: "3", ry: "3"}, 
                        React.createElement("animate", {attributeName: "width", values: "0;20;20;20;0", dur: "1000ms", repeatCount: "indefinite"}), 
                        React.createElement("animate", {attributeName: "height", values: "0;20;20;20;0", dur: "1000ms", repeatCount: "indefinite"}), 
                        React.createElement("animate", {attributeName: "x", values: "10;0;0;0;10", dur: "1000ms", repeatCount: "indefinite"}), 
                        React.createElement("animate", {attributeName: "y", values: "10;0;0;0;10", dur: "1000ms", repeatCount: "indefinite"})
                    ), 
                    React.createElement("rect", {width: "20", height: "20", x: "25", y: "0", rx: "3", ry: "3"}, 
                        React.createElement("animate", {attributeName: "width", values: "0;20;20;20;0", begin: "200ms", dur: "1000ms", repeatCount: "indefinite"}), 
                        React.createElement("animate", {attributeName: "height", values: "0;20;20;20;0", begin: "200ms", dur: "1000ms", repeatCount: "indefinite"}), 
                        React.createElement("animate", {attributeName: "x", values: "35;25;25;25;35", begin: "200ms", dur: "1000ms", repeatCount: "indefinite"}), 
                        React.createElement("animate", {attributeName: "y", values: "10;0;0;0;10", begin: "200ms", dur: "1000ms", repeatCount: "indefinite"})
                    ), 
                    React.createElement("rect", {width: "20", height: "20", x: "50", y: "0", rx: "3", ry: "3"}, 
                        React.createElement("animate", {attributeName: "width", values: "0;20;20;20;0", begin: "400ms", dur: "1000ms", repeatCount: "indefinite"}), 
                        React.createElement("animate", {attributeName: "height", values: "0;20;20;20;0", begin: "400ms", dur: "1000ms", repeatCount: "indefinite"}), 
                        React.createElement("animate", {attributeName: "x", values: "60;50;50;50;60", begin: "400ms", dur: "1000ms", repeatCount: "indefinite"}), 
                        React.createElement("animate", {attributeName: "y", values: "10;0;0;0;10", begin: "400ms", dur: "1000ms", repeatCount: "indefinite"})
                    )
                )
			)
		);
	}
});

ReactDOM.render( React.createElement(Provider, {store: store}, React.createElement(GetStarted_Step7_Component, null)), document.getElementById('get_started_step7') );
