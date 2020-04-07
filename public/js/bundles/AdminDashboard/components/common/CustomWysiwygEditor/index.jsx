import React from 'react';
import ReactQuill from 'react-quill'
import './styles.scss';

export function CustomWysiwygEditor(props) {
  return (
    <ReactQuill value={props.editorState} placeholder={props.placeholder} onChange={props.onEditorStateChange} />
  )
}
