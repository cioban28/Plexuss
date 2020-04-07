import React , { Component } from 'react'
import { connect } from 'react-redux'
import Header from './content_header'
import Switch from "react-switch";
import Modal from 'react-modal';
import ReactTooltip from 'react-tooltip'
import { saveUserAccountPrivacy, deleteAccount } from './../../api/user'

const customStyles = {
  content : {
    top                   : '50%',
    left                  : '50%',
    right                 : 'auto',
    bottom                : 'auto',
    marginRight           : '-50%',
    transform             : 'translate(-50%, -50%)',
    padding: "20px 50px 50px"
  }
};

class Privacy extends Component{
    constructor(props){
        super(props);
        this.state={
            is_incognito: false,
            receive_messages: true,
            receive_requests: true,
            appear_in_search: true,
            appear_in_viewed: true,
            appear_in_suggestions: true,
            show_lname: true,
            show_profile_pic: true,
            show_school: true,

            pending: false,

            showDeleteModal: false,
            deleteReason: "",
        }
        this.updateSettings = this.updateSettings.bind(this);
        this.updateIncognito = this.updateIncognito.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.handleIncognito = this.handleIncognito.bind(this);
        this.closeModal = this.closeModal.bind(this);
        this.deleteUserAccount = this.deleteUserAccount.bind(this);
    }
    componentDidMount(){
        if(!!this.props.account_settings){
            this.updateSettings();
        }
    }
    componentDidUpdate(prevProps, prevState){
        if(this.state.is_incognito != prevState.is_incognito){
            this.updateIncognito();
        }
        if(this.props.account_settings != prevProps.account_settings){
            this.updateSettings();
            this.setState({pending: false});
        }
    }

    updateSettings(){
        let { account_settings } = this.props;
        this.setState({
            is_incognito: !!account_settings.is_incognito,
            receive_messages: !!account_settings.receive_messages,
            receive_requests: !!account_settings.receive_requests,
            appear_in_search: !!account_settings.appear_in_search,
            //appear_in_viewed: account_settings.appear_in_viewed,
            appear_in_suggestions: !!account_settings.appear_in_suggestions,
            show_lname: !!account_settings.show_lname,
            show_profile_pic: !!account_settings.show_profile_pic,
            show_school: !!account_settings.show_school,
        })
    }
    updateIncognito(){
        if(this.state.is_incognito === true){
            this.setState({
                receive_messages: false,
                receive_requests: false,
                appear_in_search: false,
                appear_in_viewed: false,
                appear_in_suggestions: false,
                show_lname: false,
                show_profile_pic: false,
                show_school: false,
            })
        } else {
            this.setState({
                receive_messages: true,
                receive_requests: true,
                appear_in_search: true,
                appear_in_viewed: true,
                appear_in_suggestions: true,
                show_lname: true,
                show_profile_pic: true,
                show_school: true,
            })
        }
    }
    handleChange(setting){
        this.setState({
            [setting]: !this.state[setting],
        })
    }
    handleIncognito(){
        this.setState({
            is_incognito: !this.state.is_incognito,
        })
    }
    onSave(){
        this.setState({
            pending: true,
        })
        let settings = {
            is_incognito: !!this.state.is_incognito ? 1 : 0,
            receive_messages: !!this.state.receive_messages ? 1 : 0,
            receive_requests: !!this.state.receive_requests ? 1 : 0,
            appear_in_search: !!this.state.appear_in_search ? 1 : 0,
            // appear_in_viewed: this.state.appear_in_viewed,
            appear_in_suggestions: !!this.state.appear_in_suggestions ? 1 : 0,
            show_lname: !!this.state.show_lname ? 1 : 0,
            show_profile_pic: !!this.state.show_profile_pic ? 1 : 0,
            show_school: !!this.state.show_school ? 1 : 0,
        }
        saveUserAccountPrivacy(settings);
    }
    closeModal(){
        this.setState({ showDeleteModal: false })
    }
    deleteUserAccount(reason){
        let obj = {
            deactivate_suggestion: reason,
        }
        deleteAccount(obj);
    }

    render(){
        return(
            <div className="large-9 medium-9 small-12 upper_container columns">
                <div className="setting_content_container setting_privacy_container">
                    <Header backClickHandler={this.props.backClickHandler} imgSrc={'/social/images/settings/active_options/noun_Privacy_1039048_000000.png'} title={'PRIVACY'} imgClass={'privacy'}/>
                    <div className="content_container">
                        <div className="explanation">
                            At Plexuss we value your privacy and want our community members to have full control of the data on their accounts.
                        </div>
                        <div className="privacy_container">
                            <Table state={this.state} handleChange={this.handleChange} />
                            <div className="privacy-incognito">
                                {/* <div className="sub_hading_container">
                                    <div className="img_container">
                                        <img src="/social/images/settings/active_options/noun_Ghost_367889_000000.png" alt=""/>
                                    </div>
                                    <div className="sub_hading">Incognito Mode</div>
                                    <div className="info_icon" data-tip data-for='info'>i</div>
                                    <ReactTooltip id='info' place='right' effect='solid' className="react_tooltip">
                                        <InfoTooltip />
                                    </ReactTooltip>
                                </div> */}
                                 <div className={"privacy-save-btn " + (this.state.pending && 'disabled')} onClick={() => this.onSave()}>{!!this.state.pending ? 'Saving...' : 'Save Settings'}</div>
                                {/* <div className="privacy_switch">
                                    <label htmlFor='normal-switch3' className="switch">
                                        <Switch
                                            checked={this.state.is_incognito}
                                            onChange={this.handleIncognito}
                                            uncheckedIcon={
                                                <div className="unChecKIcon">OFF</div>
                                            }
                                            checkedIcon={
                                                <div className="checkIcon">ON</div>
                                            }
                                            className="react-switch"
                                            onColor="#2AC56C"
                                            offColor="#DDDDDD"
                                            id='icon-switch3'
                                            height={24}
                                        />
                                    </label>
                                </div> */}
                            </div>
                            <div className="privacy_footer">
                                <span onClick={() => this.setState({showDeleteModal: true})}>Delete Account</span>
                            </div>
                        </div>
                    </div>
                    <DeleteAccountModal isOpen={this.state.showDeleteModal} closeModal={this.closeModal} deleteUserAccount={this.deleteUserAccount} />
                </div>
            </div>
        )
    }
}
function InfoTooltip(){
    return(
        <div className="info_tooltip_container">
            <div className="info_tooltip_header">
                <div className="img_conttainer"><img src="/social/images/settings/active_options/noun_Ghost_367889_000000.png" alt=""/></div>
                <div className="info_tooltip_heading">What is incognito mode?</div>
            </div>
            <div className="info_tooltip_description">
                Incognito mode allows you be anonymous and hide your profile info from the public, but colleges can still see.
                <br /> 
                <br />               
                You will not show up as friend requests, or suggestions while you are in incognito mode. You cannot message in incognito mode as well
            </div>
        </div>
    )
}

function Table(props){
    let { state, handleChange } = props;
    return(
        <div className="privacy_table">
            <div className="privacy_table-header">Manage your Visibility</div>
            {/* <TableRow id={'privacy-setting1'} text={'Receive messages from other users'} checked={state.receive_messages} handleChange={() => handleChange('receive_messages')} disabled={state.is_incognito} /> */}
            <TableRow id={'privacy-setting2'} text={'Receive connection requests'} checked={state.receive_requests} handleChange={() => handleChange('receive_requests')} disabled={state.is_incognito} />
            {/* <TableRow id={'privacy-setting3'} text={'Appear in searches'} checked={state.appear_in_search} handleChange={() => handleChange('appear_in_search')} disabled={state.is_incognito} /> */}
            {/* <TableRow id={'privacy-setting4'} text={'Appear in also viewed'} checked={state.appear_in_viewed} handleChange={() => handleChange('appear_in_viewed')} disabled={!state.is_incognito} /> */}
            <TableRow id={'privacy-setting5'} text={'Appear in suggestions'} checked={state.appear_in_suggestions} handleChange={() => handleChange('appear_in_suggestions')} disabled={state.is_incognito} />
            <TableRow id={'privacy-setting6'} text={'Show last name'} checked={state.show_lname} handleChange={() => handleChange('show_lname')} disabled={state.is_incognito} />
            <TableRow id={'privacy-setting7'} text={'Show profile picture'} checked={state.show_profile_pic} handleChange={() => handleChange('show_profile_pic')} disabled={state.is_incognito} />
            <TableRow id={'privacy-setting8'} text={'Show school information'} checked={state.show_school} handleChange={() => handleChange('show_school')} disabled={state.is_incognito} />
        </div>
    )
}

function TableRow(props){
    let { text, checked, handleChange, id, disabled } = props;
    return(
        <div className="row privacy_table_row">
            <div className="large-9 medium-9 small-9 columns privacy-row-text">
                { text }
            </div>
            <div className="large-3 medium-3 small-3 columns tick_icon">
                <label className="switch">
                    <Switch
                        checked={checked}
                        onChange={handleChange}
                        uncheckedIcon={
                            <div className="unChecKIcon">OFF</div>
                        }
                        checkedIcon={
                            <div className="checkIcon">ON</div>
                        }
                        className="react-switch"
                        onColor="#2AC56C"
                        offColor="#DDDDDD"
                        id={id}
                        height={24}
                        disabled={disabled}
                    />
                </label>
            </div>
        </div>
    )
}

class DeleteAccountModal extends Component {
    constructor(props){
        super(props);

        this.state= {
            text: "",
        }
        this.handleTextChange = this.handleTextChange.bind(this);
    }
    handleTextChange(event){
        this.setState({text: event.target.value});
    }
    render(){
        let{ deleteUserAccount, isOpen, closeModal } = this.props;
        return (
            <Modal
                isOpen={isOpen}
                onRequestClose={closeModal}
                style={customStyles}
            >
                <div className="delete-account-modal">
                    <div className="del-acc-modal-close" onClick={() => closeModal()}>&#10005;</div>
                    <div className="del-acc-title">We are sad to see you go...</div>
                    <div className="del-acc-subtitle">Before you go, what could we be doing better?</div>
                    <textarea value={this.state.text} onChange={this.handleTextChange} />
                    <div className="del-acc-warning">Please note that this will permanently delete your account and all data associated with it. If you wish to return, you will need to create a new account.</div>
                    <div className="del-acc-btns row">
                        <div className="del-acc-cancel small-12 medium-2 columns" onClick={() => closeModal()}>Cancel</div>
                        <div className="del-acc-delete small-12 medium-2 columns" onClick={() => deleteUserAccount(this.state.text)}>Delete</div>
                    </div>
                </div>
            </Modal>
        )
    }
}

const mapStateToProps = (state) =>{
  return{
    user: state.user,
    account_settings: state.setting.setting.account_settings,
  }
}
export default connect(mapStateToProps, null)(Privacy);