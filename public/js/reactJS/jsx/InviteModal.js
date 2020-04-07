// \/ Invite Modal \/ //
//
// All class specific dependency classes and functions are within this file.

import React from 'react'
import { bindActionCreators } from 'redux'
import CustomModal from './CustomModal'
import { connect } from 'react-redux'
import * as userActions from './actions/User'
import { filter } from 'lodash'
import { extend } from 'jquery'
import { polyfill } from 'es6-promise';

polyfill();

// Main Class (InviteModal)
class InviteModal extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            step: 'email-provider-select', // 'email-provider-select' || 'invitation-select' || 'emails-sent'
        }

        this._onDone = this._onDone.bind(this);
        this._onSkip = this._onSkip.bind(this);
        this._backToEmailSelect = this._backToEmailSelect.bind(this);
    }

    componentWillReceiveProps(newProps) {
        const { _user } = this.props,
              { _user: _newUser } = newProps;

        if (!_.isEqual(_user.getContactsStatus, _newUser.getContactsStatus) && _newUser.getContactsStatus === 'success' && _newUser.contactList) {
            this.setState({ step: 'invitation-select' });
        }

        if (!_.isEqual(_user.inviteContactsPending, _newUser.inviteContactsPending) && !_newUser.inviteContactsPending && _newUser.inviteContactsStatus === 'success') {
            this.setState({ step: 'emails-sent'});
        }        
    }

    componentDidMount() {
        amplitude.getInstance().logEvent('invite contact show');
    }

    _backToEmailSelect(hasSubmitted) {
        const { updateUserInfo } = this.props;

        updateUserInfo({ selectedContactList: [] });
        this.setState({ step: 'email-provider-select' });

        if (hasSubmitted) {
            amplitude.getInstance().logEvent('invite contact initiate additional');
        }
    }

    _onSkip(stage) {
        const { closeMe } = this.props;

        amplitude.getInstance().logEvent('invite contact skip', { stage });

        closeMe();
    }

    _onDone() {
        const { closeMe } = this.props;

        amplitude.getInstance().logEvent('invite contact done');
        closeMe();
    }

    render() {
        const { closeMe, _user } = this.props,
              { step } = this.state,
              loading = _user.inviteContactsPending;

        return (
            React.createElement(CustomModal, {closeMe:  () => null}, 
                React.createElement("div", {className: "modal initial-invite-modal"}, 
                     step === 'email-provider-select' && React.createElement(EmailProviderSelect, React.__spread({_onSkip: this._onSkip},  this.props)), 
                     step === 'invitation-select' && React.createElement(InvitationSelect, React.__spread({_onSkip: this._onSkip, _backToEmailSelect: () => this._backToEmailSelect()},  this.props)), 
                     step === 'emails-sent' && React.createElement(EmailsSent, {_onDone: this._onDone, _backToEmailSelect: () => this._backToEmailSelect(true), selectedContactList: _user.selectedContactList || [], closeMe: closeMe})
                ), 

                 loading && React.createElement(Loader, null)
            )
        );
    }
}

// Private classes
class EmailProviderSelect extends React.Component {
    constructor(props) {
        super(props);

        this.signinWindow = null;
        this.setInterval = null;

        this._getContacts = this._getContacts.bind(this);
        this._buildEmailProviderButton = this._buildEmailProviderButton.bind(this);
    }

    componentDidMount() {
        const { updateUserInfo } = this.props;

        updateUserInfo({ selectedEmailProvider: 'Google' });
    }

    componentWillReceiveProps(newProps) {
        const { _user, updateUserInfo } = this.props,
              { _user: _newUser } = newProps;

        if (_user.getContactsStatus !== _newUser.getContactsStatus && _newUser.getContactsStatus == 'require-signin' && _newUser.getContactsSignInURL) {
            this.signinWindow.location = _newUser.getContactsSignInURL;

            // Attach event listener when user gets redirected after signing in.
            this.setInterval = setInterval(() => {
                try {
                    if (window.location.host === this.signinWindow.location.host) {
                        this.signinWindow.addEventListener('contact-list', (event) => {
                            if (event.detail) {
                                updateUserInfo({
                                    contactList: event.detail,
                                    getContactsStatus: 'success',
                                });
                                this.signinWindow.close();
                            }
                        });
                        clearInterval(this.setInterval);
                    }
                } catch(exception) {};
            }, 50);

        }
    }

    _getContacts() {
        const { getEmailContacts, _user } = this.props;

        if (!_user.selectedEmailProvider) return;

        this.signinWindow = centeredWindowPopup("", "signinwindow", 800, 600);

        getEmailContacts(_user.selectedEmailProvider);
    }

    _onEmailProviderSelect(provider) {
        const { _user, updateUserInfo } = this.props,
            selectedEmailProvider = _user.selectedEmailProvider;

        if (selectedEmailProvider === provider) {
            this._getContacts();
        }

        updateUserInfo({ selectedEmailProvider: provider });

        amplitude.getInstance().logEvent('invite contact select provider', { provider });
    }

    _buildEmailProviderButton(provider, index) {
        const { updateUserInfo, _user } = this.props,
            isSelected = _user.selectedEmailProvider === provider;

        return (
            React.createElement("div", {key: index, 
                 className:  'email-provider-selection ' + provider + (isSelected ? ' active' : ''), 
                 onClick: () => this._onEmailProviderSelect(provider)}, 
                    React.createElement("div", {className:  'provider-image ' + provider + (isSelected ? ' active' : '') }), 
                    React.createElement("div", {style: {textAlign: 'center', fontSize: '12pt', marginLeft: '-0.4rem'}}, provider)
            )
        )   
    }

    render() {
        const { _onSkip } = this.props,
            emailProviders = ['Google', 'Microsoft', 'Yahoo'];

        return (
            React.createElement("div", {className: "email-provider-container"}, 
                React.createElement("div", {className: "header-text"}, "I know you just met us, but we can help your friends find colleges too! It's easy to invite them."), 
                React.createElement("div", {className: "secondary-header-text"}, "Please select your provider"), 
                React.createElement("div", {className: "email-provider-selection-container"}, 
                     emailProviders.map(this._buildEmailProviderButton) 
                ), 

                React.createElement("div", {className: "smaller-header-text"}, "We’ll import your address book to suggest connections and help you manage your contacts."), 

                React.createElement("div", {className: "action-buttons-container"}, 
                    React.createElement("div", {className: "email-selection-skip-button"}, 
                        React.createElement("span", {onClick:  () => _onSkip('select provider')}, "Skip")
                    ), 

                    React.createElement("div", {className: "email-selection-next-button", onClick: this._getContacts}, 
                        "Next"
                    )
                )
            )
        );
    }
}

class InvitationSelect extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            filterTerm: '',
        }

        this._onInviteSkip = this._onInviteSkip.bind(this);
        this._filterContacts = this._filterContacts.bind(this);
        this._submitContacts = this._submitContacts.bind(this);
        this._toggleSelections = this._toggleSelections.bind(this);
    }

    _filterContacts() {
        const { _user } = this.props,
              { filterTerm } = this.state,
              contactList = _user.contactList || [],
              term = filterTerm.toLowerCase();

        switch(term) {
            case '': 
                return contactList;
            default:
                return filter( contactList, (contact) => (contact.invite_name.toLowerCase().includes(term) || contact.invite_email.toLowerCase().includes(term)) );
        }
    }

    _toggleSelections() {
        const { _user, updateUserInfo } = this.props,
            selectedContactList = _user.selectedContactList || [];
        let newSelection = [];

        if (!_.isEqual(sortContactList(_user.contactList), sortContactList(selectedContactList))) {
            newSelection = _.clone(_user.contactList);
        }

        updateUserInfo({ selectedContactList: newSelection });
    }

    _submitContacts() {
        const { _user, inviteContacts } = this.props,

            selectedContactList = _user.selectedContactList;

        if (_.isEmpty(selectedContactList)) return;

        inviteContacts(selectedContactList);

        amplitude.getInstance().logEvent('invite contact add contacts', { 'contacts added': selectedContactList.length });
    }

    _onInviteSkip() {
        const { _onSkip, _user } = this.props,
            stage = _.isEmpty(_user.selectedContactList) ?  'select-contacts' : 'invite-contacts';

        _onSkip(stage);
    }

    render() {
        const { _user, _backToEmailSelect, closeMe } = this.props,
            contactList = this._filterContacts(),
            selectedContactList = _user.selectedContactList || [],
            allSelected = _.isEqual(sortContactList(contactList), sortContactList(selectedContactList));

        let selectedCount = selectedContactList.length;

        selectedCount = selectedCount !== 0
            ? selectedCount = ' ' + selectedCount + ' '
            : ' ';

        return (
            React.createElement("div", {className: "invitation-select-container"}, 
                React.createElement("div", {className: "header-container"}, 
                    React.createElement("div", {className: "back-button", onClick: _backToEmailSelect}, "Back"), 
                    React.createElement("div", {className: "header-text"}, "Invite your contacts to keep up with their schooling")
                ), 

                React.createElement("div", {className: "contact-search-filter"}, 
                    React.createElement("input", {onChange: (e) => this.setState({ filterTerm: e.target.value }), placeholder: 'Search for contacts here'}), 
                    React.createElement("img", {src: "/images/nav-icons/topnav_search_icon.jpg"})
                ), 

                React.createElement("div", {className: "email-list-container"}, 
                     contactList.length === 0 && 
                        React.createElement("div", null, "This email address has no contacts. ", React.createElement("span", {className: "back-to-select-button", onClick: _backToEmailSelect}, "Click Here"), " to go back to choose a different email."), 
                     contactList.map((entry, index) => React.createElement(EmailEntry, React.__spread({key: index, entry: entry},  this.props))) 
                ), 

                React.createElement("div", {className: "buttons-container"}, 
                    React.createElement("div", {
                        onClick:  this._toggleSelections, 
                        className:  allSelected ? 'unselect-all-button' : 'select-all-button'}, 
                         allSelected ? 'Unselect All' : 'Select All'
                    ), 

                    React.createElement("div", {className: "right-side-buttons"}, 
                        React.createElement("div", {className: "skip-button", onClick: this._onInviteSkip}, "Skip"), 
                        React.createElement("div", {className: "submit-contacts-button", onClick: this._submitContacts}, "Add", selectedCount, "contacts")
                    )
                )

            )
        );
    }
}

class EmailEntry extends React.Component {
    constructor(props) {
        super(props);

        this._onToggle = this._onToggle.bind(this);
    }

    _onToggle() {
        const { entry, toggleSelectedInviteEmail } = this.props;

        toggleSelectedInviteEmail(entry);
    }

    render() {
        const { entry, _user } = this.props,
            selectedContactList = _user.selectedContactList || [],
            isSelected = _.find(selectedContactList, { invite_email: entry.invite_email }),
            firstName = entry.invite_name.split(/\s+/)[0].toString();

        return (
            React.createElement("div", {className: "email-entry"}, 
                React.createElement("div", null, 
                    React.createElement("div", {className: "name-text"},  firstName || 'No Name'), 
                    React.createElement("div", {className: "email-text"},  entry.invite_email)
                ), 
                 isSelected 
                    ?
                    React.createElement("div", {className: "active-email-entry", onClick: this._onToggle}, 
                        React.createElement("div", {className: "select-email-entry-icon checkmark"})
                    )

                    : 
                    React.createElement("div", {className: "inactive-email-entry", onClick: this._onToggle}, 
                        React.createElement("span", {className: "select-email-entry-icon"}, "+")
                    )
            )
        );
    }
}

const EmailsSent = ({ _backToEmailSelect, _onDone, selectedContactList }) => (
    React.createElement("div", {className: "emails-sent-container"}, 
        React.createElement("img", {className: "plexuss-logo-icon", src: "/images/plexuss-mobile-ads/plexuss-app-icon-web-small.png"}), 
        
        React.createElement("div", {className: "first-header-text"}, "You’ve invited ",  selectedContactList.length, " people to Plexuss"), 
        React.createElement("div", {className: "second-header-text"}, "Try adding contacts from another email address to find more contacts."), 
        
        React.createElement("div", {className: "action-buttons-container"}, 
            React.createElement("div", {className: "try-another-email-button", onClick: _backToEmailSelect}, "Try another email"), 
            React.createElement("div", {className: "done-for-now-button", onClick: _onDone}, "Done for now")
        )

    )
)
// End Private classes

// Utility functions
const centeredWindowPopup = (url, title, w, h) => {
    const dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left,
          dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top,
          
          width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width,
          height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height,
          
          left = ((width / 2) - (w / 2)) + dualScreenLeft,
          top = ((height / 2) - (h / 2)) + dualScreenTop,

          newWindow = window.open(url, title, 'scrollbars=yes, menubar=1, resizable=1, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

    if (window.focus) {
        newWindow.focus();
    }

    return newWindow;
}

const sortContactList = (contactList) => contactList.sort((a, b) => a.invite_email.localeCompare(b.invite_email));

// End Utility functions

const mapStateToProps = (state, props) => {
    return {
        _user: state._user,
    };
};

const mapDispatchToProps = (dispatch) => {
    return bindActionCreators(extend({}, userActions), dispatch);
}

const Loader = ({}) => (
    React.createElement("div", {className: "gs-loader"}, 
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

export default connect(mapStateToProps, mapDispatchToProps)(InviteModal);