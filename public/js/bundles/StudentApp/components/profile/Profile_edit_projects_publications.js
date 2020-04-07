import React, {Component} from 'react';
import { connect } from 'react-redux';
import { isEmpty } from 'lodash';
import {searchForMajors} from './../../actions/Profile';
import Edit_section from './profile_edit_section';
import Publication from './Publication';
import AddPublication from './AddPublication';
import InfoTooltip from './InfoTooltip';
import Profile_edit_privacy_settings from './profile_edit_privacy_settings'

const articlesTooltipContent =
    <span>These are articles you have selected to share on your profile</span>;
const newTitleTooltipContent =
    <span>The title must be 45 characters or less</span>;
const newLinkTooltipContent = 
    <span>The link must be a valid URL and should include the protocol, for example: <b>https:</b>//www.plexuss.com</span>;

class Profile_edit_projects_publications extends Component{
    constructor(props){
        super(props);

        this.state = {
            submittable: false,
            openFields: false,
            newLink: '',
            newTitle: '',
            newLinkValid: '',
            newTitleValid: '',
        }
    }

    _onCancelEditing = () => {
        this.setState({ openFields: false });
    }

    _validate = () => {
        const potentialKeys = ['newLink', 'newTitle'];
        const { newLink, newTitle } = this.state;
        const validation = {};

        let valid = null;

        potentialKeys.forEach((key) => {
            switch (key) {
                case 'newLink':
                    try {
                        new URL(newLink); // Will throw error if not a valid URL.
                        valid = true;
                    } catch(exception) {
                        valid = false;
                    }
                    break;
                case 'newTitle':
                    valid = !!newTitle; // Just make sure it is not empty
                    break;
                
                default:
                    // Nothing yet;
            }

            validation[`${key}Valid`] = valid;
        });

        validation['submittable'] = validation['newLinkValid'] && validation['newTitleValid'];

        this.setState(validation);
    }

    _save = (callback) => {
        const { newLink, newTitle } = this.state;
        const { insertPublicProfilePublication } = this.props;

        const customCallback = () => { 
            this.setState({ openFields: false });
            callback();
        };

        insertPublicProfilePublication({ url: newLink, title: newTitle, callback: customCallback });

        this.setState({ newLink: '', newTitle: '' });
    }

    _onChange = (event) => {
        this.setState({[event.target.id]: event.target.value}, this._validate);
    }

    _toggleFields = () => {
        const { openFields } = this.state;

        this.setState({ openFields: !openFields });
    }

    _buildPublication = (publication, index) => {
        return (
            <Publication
                key={index}
                publication_id={publication.id}
                url={publication.url}
                shortDescription={publication.title} />
        );
    }

    _buildEditModePublication = (publication, index) => {
        const { removePublicProfilePublication } = this.props;

        return (
            <Publication 
                key={index}
                removePublication={removePublicProfilePublication}
                publication_id={publication.id}
                url={publication.url}
                editMode={true}
                shortDescription={publication.title} />
        );
    }

    _buildArticle = (article, index) => {
        return (
            <Publication
                key={index}
                publication_id={article.id}
                url={'social/article/'+article.id}
                shortDescription={article.article_title} />
        );
    }
    _buildEditModeArticle = (article, index) => {
        return (
            <Publication
                key={index}
                publication_id={article.id}
                url={'social/article-editor/'+article.id}
                shortDescription={article.article_title} />
        );
    }

    render() {
        const { submittable, openFields, newLink, newTitle, newLinkValid, newTitleValid } = this.state;
        const { _profile, articles } = this.props;
        const { projectsAndPublications } = _profile;
        let projectArticles = isEmpty(articles) ? [] : articles.filter(article => article.project_and_publication == 1)
        return(
            <Edit_section autoOpenEdit={this.props.autoOpenEdit} editable={true} saveHandler={this._save} submittable={submittable} onCancelEditing={this._onCancelEditing} section={'projects-publications'} hideSaveCancel={!openFields}>
                {/* Preview section */}
                <div>
                    <div className="green-title">Projects & Publications
                        <Profile_edit_privacy_settings section="projects_publications" initial={!!_profile.public_profile_settings ? !!_profile.public_profile_settings.projects_publications ? _profile.public_profile_settings.projects_publications : null : null}/>
                    </div>
                    { isEmpty(projectsAndPublications) && <div style={{marginTop: '1em', color: '#d3d3d3'}}>No projects or publications added yet.</div> }
                    <div className='profile-projects-publications-container'>
                        { !isEmpty(projectsAndPublications) && projectsAndPublications.map(this._buildPublication) }
                    </div>
                    {!isEmpty(projectArticles) &&
                        <span>
                            <div className="green-title">Articles <InfoTooltip type='dark' content={articlesTooltipContent} id={'article-tip'} /></div>
                            <div className='profile-projects-publications-container'>
                                { !isEmpty(projectArticles) && projectArticles.map(this._buildArticle) }
                            </div>
                        </span>
                    }
                </div>

                {/* Edit section */}
                <div>
                    <div className="green-title">Projects & Publications</div>
                    <div className='profile-projects-publications-container'>
                        { !isEmpty(projectsAndPublications) && projectsAndPublications.map(this._buildEditModePublication) }

                        { !openFields && <AddPublication _toggleFields={this._toggleFields} /> }
                    </div>

                    { openFields && 
                        <div className='add-publication-fields-container'>
                         <div>New Project/Publication Title <InfoTooltip type='dark' content={newTitleTooltipContent} id={'new-publication-title-tip'} /></div>
                            <input id={'newTitle'} className={newTitleValid === false && 'invalid-field'}  onChange={(event) => this._onChange(event)} value={newTitle} placeholder='The Global Student Network' /> 

                            <div>New Project/Publication Link <InfoTooltip type='dark' content={newLinkTooltipContent} id={'new-publication-tip'} /></div>
                            <input id={'newLink'} className={newLinkValid === false && 'invalid-field'} onChange={(event) => this._onChange(event)} value={newLink} placeholder={'https://www.plexuss.com'} />
                        </div>
                    }
                    {!isEmpty(projectArticles) &&
                        <span>
                            <div className="green-title">Articles <InfoTooltip type='dark' content={articlesTooltipContent} id={'article-tip'} /></div>
                            <div className='profile-projects-publications-container'>
                                { !isEmpty(projectArticles) && projectArticles.map(this._buildEditModeArticle) }
                            </div>
                        </span>
                    }
                </div>
            </Edit_section>
        );

    }
}
const mapStateToProps = (state) =>{
    return{
        articles: state.articles && state.articles.userArticles,
    }
}
export default connect(mapStateToProps)(Profile_edit_projects_publications);