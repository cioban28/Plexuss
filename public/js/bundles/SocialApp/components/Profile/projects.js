import React, { Component } from 'react';
import { withRouter } from 'react-router-dom';
import axios from 'axios';
import Slider from "react-slick";

class Projects extends Component{
    constructor(props){
      super(props);

      this.state = {
        currentProject: 0,
        allProjects: [],
      }
    }
    componentDidMount(){
      if ( !!this.props.projectsAndPublications && !!this.props.articles ){
        let { projectsAndPublications, articles } = this.props;
        let combined = projectsAndPublications.concat(articles);
        combined.sort( (a,b) => (a.created_at > b.created_at) ? 1 : -1 );
        this.setState({allProjects: combined});
      }
    }
    render(){
      const settings = {
          infinite: false,
          autoplay: false,
          slidesToShow: 1,
          slidesToScroll: 1,
          afterChange: (current) => this.setState({ currentProject: current }),
      };
      const { visible } = this.props;

        return(
            <div className="profile-widgets">
                <div className="widget-heading">
                  <h2>Projects & Publications</h2>
                </div>
                <div className="widget-content">
                {!!visible ?
                  this.state.allProjects.length > 0 ?
                    <div className="projects-slider">
                      <div className="current-project-indicator">{(this.state.currentProject+1)+" of "+this.state.allProjects.length}</div>
                      <Slider {...settings}> 
                        {this.state.allProjects.map((project, index) => (
                          <SingleProject key={index} project={project} history={this.props.history}/>
                        ))}
                     </Slider>
                    </div>
                    :
                    <p>No Projects or Publications added yet</p>
                  :
                  <span className="private-section">This section is private</span>
                }
                </div>
            </div>
        )
    }
}

class SingleProject extends Component {
  is_mount = false
  constructor(props){
    super(props);

    this.state = {
      linkContent: {},
    }
    this.convertDate = this.convertDate.bind(this);
    this.redirectProject = this.redirectProject.bind(this);
  }
  componentDidMount(){
    this.is_mount = true
    let { project } = this.props;
    if(project && project.url){
      axios({
        method: 'get',
        url: '/social/link-preview-info?url='+project.url,
      })
      .then(res => {
        if (this.is_mount)
          this.setState({
            linkContent: res.data,
          })
      })
      .catch(error => {
      })
    }
    else{
      if (this.is_mount)
        this.setState({
          linkContent: {},
        })
    }
  }

  componentWillUnmount() {
    this.is_mount = false
  }
  
  redirectProject(project) {
    !!project.url ? project.url.includes('social/articles/') ? this.props.history.push('/social/article/'+project.url.split('/')[2]) : window.open(project.url) : !!project.article_title && this.props.history.push('/social/article/'+project.id);
  }
  convertDate(time){
    let months = ['Janurary', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
    time = time.split(/[-|:| ]/);
    let date = months[Number(time[1]) - 1] + " " + time[2] + ", " + time[0]
    return date;
  }

  render(){
    const { project, redirect } = this.props;
    // let imgStyle = { backgroundImage: !!this.state.linkContent && this.state.linkContent.image ? 'url('+this.state.linkContent.image+')' : !!project.image_link ? 'url('+project.image_link+')' : 'url("/social/images/Header.png")' }
    let date = this.convertDate(project.created_at);
    return(
      <div className="single-project-container" onClick={() => this.redirectProject(project)}>
        <div className="single-project-img-contain">
          <img className="single-project-img" src={!!this.state.linkContent && this.state.linkContent.image ? this.state.linkContent.image : !!project.image_link ? project.image_link : '/social/images/Header.png' } />
        </div>
        <div className="single-project-info">
          <div className="single-project-title">
            {project.title || project.article_title}
          </div>
          <div className="single-project-date">
            {date}
            {!!project.article_title && <span>PLEXUSS ARTICLE</span>}
          </div>
        </div>
      </div>
    );
  }
}

export default withRouter(Projects);