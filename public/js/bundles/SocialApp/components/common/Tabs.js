import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux'

import Tab from './Tab';

class Tabs extends Component {
  static propTypes = {
    children: PropTypes.instanceOf(Array).isRequired,
  }

  constructor(props) {
    super(props);

    this.state = {
      activeTab: this.props.children[0].props.label,
    };
  }

  onClickTabItem = (tab) => {
    this.setState({ activeTab: tab });
  }

  componentWillReceiveProps(np){
    // console.log("---np", this.props,'=------prevprops', prevprops, this.props.children)
    np.dratfsTabToShow && this.setState({activeTab: this.props.children[1].props.label})
  }

  componentDidUpdate(prevProps){
  }


  render() {
    const {
      onClickTabItem,
      props: {
        children,
      },
      state: {
        activeTab,
      }
    } = this;

    return (
      <div className="tabs">
        <ol className="tab-list" style={{}}>
          {children.map((child) => {
            const { label } = child.props;

            return (
              <Tab
                activeTab={activeTab}
                key={label}
                label={label}
                onClick={onClickTabItem}
              />
            );
          })}
        </ol>
        <div className="tab-content">
          {children.map((child) => {
            if (child.props.label !== activeTab) return undefined;
            return child.props.children;
          })}
        </div>
      </div>
    );
  }
}


const mapStateToProps = state => {
  return{
    dratfsTabToShow: state.articles && state.articles.setDraftsTab
  }
}
export default connect(mapStateToProps)(Tabs);
