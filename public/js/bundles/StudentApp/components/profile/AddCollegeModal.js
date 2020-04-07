import React, { Component } from 'react';
import Modal from 'react-responsive-modal';
import Autocomplete from 'react-autocomplete';
import { isEmpty } from 'lodash';

const EMPTY_ARRAY = [];

const LOGO_PREFIX = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/';

export default class AddCollegeModal extends Component {
    constructor(props) {
        super(props);

        this.state = {
            selectedCollege: '',
            searchQuery: '',
        }

        this.requestTimeout = null;
    }

    _onChange = (event) => {
        const { searchForColleges } = this.props;
        const value = event.target.value;

        clearTimeout(this.requestTimeout);

        if (!isEmpty(value)) {
            this.requestTimeout = setTimeout(() => {
                searchForColleges(value);
            }, 500);
        }

        this.setState({ searchQuery: value });
    }

    _onCollegeAdd = (college_id) => {
        const { onCollegeAdd, toggleModal } = this.props;

        onCollegeAdd(college_id);
        toggleModal();
    }

    render() {
        const { isOpen, _profile, toggleModal } = this.props;
        const { selectedCollege, searchQuery } = this.state;

        const { collegesWithLogos, searchForCollegesWithLogosPending } = _profile;

        return (
            <Modal open={isOpen} onClose={toggleModal} closeOnEsc={false} classNames={{modal: 'add-college-modal', overlay: 'add-college-overlay'}} little>
                <h4>Add a college to your list</h4>
                <div className='add-college-modal-secondary-header'>Search and select a college you want to like</div>
                <div className='college-search-autocomplete-container'>
                    <Autocomplete
                        inputProps={{ placeholder: 'Search' }}
                        wrapperStyle={{display: 'inline-block', width: '100%'}}
                        getItemValue={(college) => college.id.toString()}
                        items={collegesWithLogos || EMPTY_ARRAY }
                        renderItem={(item, isHighlighted) => (
                            <div className='college-search-result' style={{ background: isHighlighted ? 'lightgray' : 'white' }}>
                                <img src={LOGO_PREFIX + item.logo_url} />
                                <div className='college-search-result-school-name'>
                                    {item.school_name}
                                </div>
                            </div>
                        )}
                        value={searchQuery}
                        onChange={this._onChange}
                        onSelect={this._onCollegeAdd}
                    />
                    { searchForCollegesWithLogosPending && <div className='mini-spinner'></div> }
                </div>
            </Modal>
        );
    }
}