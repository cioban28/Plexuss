import React, { Component } from 'react'
import Dropzone from 'react-dropzone'
const thumb = {
    display: 'inline-flex',
    borderRadius: 2,
    marginRight: 8,
    width: 100,
    height: 100,
    boxSizing: 'border-box'
};
const thumbInner = {
    display: 'flex',
    minWidth: 0,
    overflow: 'hidden'
}
const img = {
    display: 'block',
    width: 'auto',
    height: '100%'
};
class ImageUpload extends Component{
    render(){
        const {files, onDrop, onDropRejected, removeImage} = this.props;
        const thumbs = files.map((file, index) => (
          <div style={thumb} key={index}>
            <div style={thumbInner}>
              <img
                src={file.preview}
                style={img}
              />
            </div>
          </div>
        ));    
        return(
            <section className="drop_zone_banner">
                {
                    files.length > 0 &&
                    <div className="with_image">
                        <div className="">
                            <div>
                                {thumbs}
                            </div> 
                            { <div onClick={removeImage} style={{cursor: 'pointer'}}>
                                <i className="fa fa-trash" aria-hidden="true"></i>
                            </div> }
                        </div>
                        <Dropzone accept="image/*" onDrop={onDrop} onDropRejected={onDropRejected}>
                            {({getRootProps, getInputProps}) => (
                                <div {...getRootProps()} className="drop_zone_imag_banner1">
                                    <input {...getInputProps()} />
                                    To replace your cover image, drag a file here or <span className="browse">browse</span> to upload
                                </div>
                            )}
                        </Dropzone>
                    </div> ||
                    files.length == 0 &&
                    <Dropzone accept="image/*" onDrop={onDrop} onDropRejected={onDropRejected}>
                        {({getRootProps, getInputProps}) => (
                            <div {...getRootProps()} className="drop_zone_imag_banner">
                                <input {...getInputProps()} />
                                Drag a file here or <span className="browse">browse</span> to upload (Required)
                            </div>
                        )}
                    </Dropzone>
                }
            </section>
        )
    }
}
export default ImageUpload;