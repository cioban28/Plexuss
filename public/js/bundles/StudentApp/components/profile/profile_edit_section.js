import React , {Component} from 'react';


/*****************************************************************
*  editing sectino for Edit Public Profile
*  must have at least 2 child components:
*      [0] = to thedisplay, [1] = to the editing section
*
******************************************************************/
export default class Profile_edit_section extends Component{
	
	constructor(props){
		super(props);

		this.state = {
			editing: false,
		}
	}

	componentDidMount(){
		let { autoOpenEdit } = this.props;
		if (autoOpenEdit === true) { this.setState({editing: true}) }
	}

    _onCancelEditing = () => {
        const { onCancelEditing } = this.props; // Incase a section wants to call a custom function

        onCancelEditing && onCancelEditing();

        this.setState({ editing: false });
    }

    _onEnableEditing = () => {
        const { onEnableEditing } = this.props; // Incase a section wants to call a custom function

        onEnableEditing && onEnableEditing();

        this.setState({ editing: true });
    }

	render(){
		let {editing} = this.state;
		let {editable, saveHandler, submittable, section, hideSaveCancel} = this.props;
        let containerClasses = "edit-prof-section fadeIn ";
        switch (section) {
			case 'basic': containerClasses += 'basic-section';break;
			case 'education': containerClasses += "education";break;
			case 'claim-to-fame': containerClasses += "claim-to-fame";break;
			case 'objective': containerClasses += "objective";break;
			case 'occupation': containerClasses += "occupation";break;
			case 'projects-publications':  containerClasses += "projects-publications";break;
        }

// console.log("children: ", this.props.children);

		return(
			<div className={containerClasses}>
				

				{this.props.children &&

				 	<div>
				 		{!editing ? 
				 			<div className="FadeIn">
								{this.props.children[0]} 
								{editable && <div className="edit-btn" onClick={this._onEnableEditing}></div> }
							</div>
							:
							<div className="fadeIn">
								<div className="close-btn fadeIn" onClick={this._onCancelEditing}>&times;</div>

								{this.props.children[1]}

								{!!hideSaveCancel ? null :
									<div className="mt30 prof-section-action-btns">
	                                    <div className="cancel-btn" onClick={this._onCancelEditing}>Cancel</div>

										{submittable ?
											<div className="save-btn" onClick={() => saveHandler( () => this.setState({editing: false}))}>Save</div>
											:
											<div className="save-btn disabled">Save</div>}
									</div>
								}
							</div>}
					</div>

				}
			
			</div>
		);
	}
}