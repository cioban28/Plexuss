// \/ Invite Modal \/ //
//
// All class specific dependency classes and functions are within this file.

import React from 'react'
import { bindActionCreators } from 'redux'
import CustomModal from './../common/CustomModal'
import { connect } from 'react-redux'
import * as profileActions from './../../actions/Profile'
import * as userActions from './../../actions/User'

import { filter } from 'lodash'

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
        const { closeMe, _profile, _user } = this.props,
              { step } = this.state;

        return (
            <CustomModal closeMe={ () => null }>
                <div className="modal initial-invite-modal">
                    { step === 'email-provider-select' && <EmailProviderSelect _onSkip={this._onSkip} {...this.props} /> }
                    { step === 'invitation-select' && <InvitationSelect _onSkip={this._onSkip} _backToEmailSelect={() => this._backToEmailSelect()} {...this.props} /> }
                    { step === 'emails-sent' && <EmailsSent _onDone={this._onDone} _backToEmailSelect={() => this._backToEmailSelect(true)} selectedContactList={_user.selectedContactList || []} closeMe={closeMe}  />}
                </div>
            </CustomModal>
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
            <div key={index} 
                 className={ 'email-provider-selection ' + provider + (isSelected ? ' active' : '') }
                 onClick={() => this._onEmailProviderSelect(provider)}>
                    <div className={ 'provider-image ' + provider + (isSelected ? ' active' : '') } />
                    <div style={{textAlign: 'center', fontSize: '12pt', marginLeft: '-0.4rem'}}>{provider}</div>
            </div>
        )   
    }

    render() {
        const { _onSkip } = this.props,
            emailProviders = ['Google', 'Microsoft', 'Yahoo'];

        return (
            <div className='email-provider-container'>
                <div className='header-text'>I know you just met us, but we can help your friends find colleges too! It's easy to invite them.</div>
                <div className='secondary-header-text'>Please select your provider</div>
                <div className='email-provider-selection-container'>
                    { emailProviders.map(this._buildEmailProviderButton) }
                </div>

                <div className='smaller-header-text'>We’ll import your address book to suggest connections and help you manage your contacts.</div>

                <div className='action-buttons-container'>
                    <div className='email-selection-skip-button'>
                        <span onClick={ () => _onSkip('select provider') }>Skip</span>
                    </div>

                    <div className='email-selection-next-button' onClick={this._getContacts}>
                        Next
                    </div>
                </div>
            </div>
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
        this._submitContacts = this._submitContacts.bind(this);
        this._toggleSelections = this._toggleSelections.bind(this);
    }

    _filterContacts = () => {
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
            <div className='invitation-select-container'>
                <div className='header-container'>
                    <div className='back-button' onClick={_backToEmailSelect}>Back</div>
                    <div className='header-text'>Invite your contacts to keep up with their schooling</div>
                </div>

                <div className='contact-search-filter'>
                    <input onChange={(e) => this.setState({ filterTerm: e.target.value })} placeholder={'Search for contacts here'} />
                    <img src='/images/nav-icons/topnav_search_icon.jpg' />
                </div>

                <div className='email-list-container'>
                    { contactList.length === 0 && 
                        <div>This email address has no contacts. <span className='back-to-select-button' onClick={_backToEmailSelect}>Click Here</span> to go back to choose a different email.</div> }
                    { contactList.map((entry, index) => <EmailEntry key={index} entry={entry} {...this.props} />) }
                </div>

                <div className='buttons-container'>
                    <div 
                        onClick={ this._toggleSelections }
                        className={ allSelected ? 'unselect-all-button' : 'select-all-button' }>
                        { allSelected ? 'Unselect All' : 'Select All' }
                    </div>

                    <div className='right-side-buttons'>
                        <div className='skip-button' onClick={this._onInviteSkip}>Skip</div>
                        <div className='submit-contacts-button' onClick={this._submitContacts}>Add{selectedCount}contacts</div>
                    </div>
                </div>

            </div>
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
            <div className='email-entry'>
                <div>
                    <div className='name-text'>{ firstName || 'No Name' }</div>
                    <div className='email-text'>{ entry.invite_email }</div>
                </div>
                { isSelected 
                    ?
                    <div className='active-email-entry' onClick={this._onToggle}>
                        <div className='select-email-entry-icon checkmark' />
                    </div>

                    : 
                    <div className='inactive-email-entry' onClick={this._onToggle}>
                        <span className='select-email-entry-icon'>&#43;</span>
                    </div> }
            </div>
        );
    }
}

const EmailsSent = ({ _backToEmailSelect, _onDone, selectedContactList }) => (
    <div className='emails-sent-container'>
        <img className='plexuss-logo-icon' src='/images/plexuss-mobile-ads/plexuss-app-icon-web-small.png' />
        
        <div className='first-header-text'>You’ve invited { selectedContactList.length } people to Plexuss</div>
        <div className='second-header-text'>Try adding contacts from another email address to find more contacts.</div>
        
        <div className='action-buttons-container'>
            <div className='try-another-email-button' onClick={_backToEmailSelect}>Try another email</div>
            <div className='done-for-now-button' onClick={_onDone}>Done for now</div>
        </div>

    </div>
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
        _profile: state._profile,
    };
};

const mapDispatchToProps = (dispatch) => {
    return bindActionCreators(Object.assign({}, profileActions, userActions), dispatch);
}

export default connect(mapStateToProps, mapDispatchToProps)(InviteModal);