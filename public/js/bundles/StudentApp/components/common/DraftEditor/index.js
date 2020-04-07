// /DraftEditor/index.js

import React from 'react'
import { connect } from 'react-redux'
import { stateToHTML } from 'draft-js-export-html'
import { stateFromHTML } from 'draft-js-import-html'
import { Editor, EditorState, RichUtils, convertToRaw } from 'draft-js'

import './styles.scss'
let content=null

class DraftEditor extends React.Component {
	constructor(props) {
		super(props)
		this.state = {
			_content: null,
		}

		this._onChange = this._onChange.bind(this)
		this._onStyleChange = this._onStyleChange.bind(this)
		this._getWordCount = this._getWordCount.bind(this)
	}

	componentWillMount(){
		let { _state, name } = this.props;
		this.state._content = stateFromHTML(_state[name] || '');
	}

	componentDidMount(){
		this.refs._draft.focus();
	}
	
	componentWillReceiveProps(np){
		let { _state, name } = np;
		this.setState({_content: stateFromHTML(_state[name] || '')});
	}

	_onChange(editorState){
		let { dispatch, action, name, minLen, maxLen } = this.props,
			count = this._getWordCount(editorState),
			valid = true;

		if( minLen ) valid = count >= minLen;
		if( maxLen ) valid = count <= maxLen;

		dispatch( action({
			editorState,
			[name]: stateToHTML(editorState.getCurrentContent()),
			[name+'_word_count']: count,
			[name+'_valid']: valid,
		}) );
	}

	_onStyleChange(style, e){
		let { editorState, maxLen } = this.props;
		this._onChange(RichUtils.toggleInlineStyle(editorState, style));
	}

	_getWordCount(nextEditorState){
		let { editorState } = this.props,
			count = 0,
			_editorState = editorState;

		if( nextEditorState ) _editorState = nextEditorState;

		if( _editorState ){
			let content = convertToRaw(_editorState.getCurrentContent());

			// loop through content.blocks to read each line
			// a newline is its own element in array
			_.each(content.blocks, b => {
				// if text is not empty, split and count
				if( b.text ) count += b.text.trim().split(' ').length;
			});
		}

		return count;
	}

	componentWillMount(){
		let { _state, name } = this.props;
		this.state._content = stateFromHTML(_state[name] || '');
	}


	componentDidMount(){
		this.refs._draft.focus();
	}	

	render(){
		let { editorState, minLen, maxLen } = this.props,
			{ _content } = this.state,
			word_count = this._getWordCount(),
			outOfRange = false, msg = '';

		if( minLen || maxLen ){
			if( word_count < minLen ){
				outOfRange = true;
				msg = 'Too short';

			}else if( word_count > maxLen ){
				outOfRange = true;
				msg = 'Too long';
			}
		}

		return (
			<article id="_draft">

				<nav>
					<div onClick={ this._onStyleChange.bind(this, 'BOLD') }>B</div>
					<div onClick={ this._onStyleChange.bind(this, 'ITALIC') }>I</div>
					<div onClick={ this._onStyleChange.bind(this, 'UNDERLINE') }>U</div>
				</nav>

				<section className="editor">
					<Editor 
						ref={'_draft'}
						editorState={ editorState || EditorState.createWithContent(_content) } 
						onChange={ this._onChange } />
				</section>

				{ (maxLen || minLen) && 
					<div className={"word-count "+(outOfRange ? 'at_max' : '')}>
						Words Entered: { word_count || 0 } { msg && <small>({msg})</small>}
					</div> 
				}
			</article>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		_user: state._user,
	};
};

export default connect(mapStateToProps)(DraftEditor);