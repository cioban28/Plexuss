import React, { Component } from 'react';
import { Link } from 'react-router-dom'
class Card extends Component{
    constructor(props){
        super(props);
        this.state={checkMark: false}
        this.toggleCheckMark = this.toggleCheckMark.bind(this);
    }
    toggleCheckMark(){
        this.setState({checkMark: !this.state.checkMark});
    }
    render(){
        let { img, text, imgClass } = this.props;
        return(
            <li>
                <Link to={!!this.props.href ? this.props.href : ''} className="list_item_research row" onClick={this.toggleCheckMark}>
                    <div className="columns img_parent">
                        <img className={imgClass} src={img} />
                    </div>
                    <div className="text columns">
                        <div className="title">
                            {text}
                        </div>
                    </div>
                </Link>
            </li>
        )
    }
}

export default Card;