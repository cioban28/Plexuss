import React, { Component } from 'react';
import { connect } from 'react-redux'
import './styles.scss'
import Carousles from '../Carousles'
import { setStep } from '../../actions/carousles'
import Loader from '../common/loading'
import Footer from '../Footer/Front_Footer'
import { Helmet } from 'react-helmet';

class Front_Dashboard extends Component{
  constructor(props){
    super(props);

    this.state = {
      cookie: window.localStorage.getItem('plexuss-gdpr-cookies-agree')
    }
    this.setVisible = this.setVisible.bind(this)
    this.setCookie = this.setCookie.bind(this)
  }

  setVisible() {
    if(!this.props.carousles.isLoading) {
      var items = Object.entries(this.props.carousles.items)
      for (var i=0;i<items.length;i++){
        if(!items[i][1]){
          this.props.setStep(items[i][0])
          break;
        }
      }
    }
  }

  setCookie() {
    window.localStorage.setItem('plexuss-gdpr-cookies-agree', 1);
    this.setState({cookie: 1})
  }

  componentDidMount(){
    window.scrollTo(0, 0)
  }

  componentDidUpdate(prevProps){
    if (this.props.location !== prevProps.location)
      window.scrollTo(0, 0)
  }

  render() {
    let secondClass = this.props.user.data.signed_in == 1 ? {'display': 'none'} : {};
    let thirdClass = this.props.user.data.signed_in == 1 ? {'marginTop': '39%'} : {};
    return (
      <span>
        <Helmet>
					<title>College Network | College Recruiting Academic Network | Plexuss.com</title>
	        <meta name="description" content="The Plexuss College Recruiting Academic Network specializes in high school student recruitng by America's colleges and universities. Join our recruiting network to connect with all colleges and universities in the US." />
	        <meta name="keywords" content="college search" />
				</Helmet>
        {this.props.user.isLoading ? <Loader/> : (
          <div>
            <div className="front-page">
                <div className="front-first">
                  <div className="front-text mbl_none">
                    <div className="big-text">Connecting students with universities, students, and alumni</div>
                    <div className="small-text">Community of over 6 million and growing worldwide</div>
                  </div>
                  <div className="mbl_front_text">
                    <div className="big-text">Connecting students with universities, students, and alumni</div>
                    <div className="small-text">Community of over 6 million and growing worldwide</div>
                  </div>
                </div>
                <div className="front-second" style={secondClass}>
                  <a className="sign-up" href="/signup?utm_source=SEO&utm_medium=frontPage">Sign up</a>
                  <div className="or-txt">
                    <div className='orLine'></div>
                    <div className='ortxt'>Or Sign up with</div>
                    <div className='orLine'></div>
                  </div>
                  <div className="signup-with">
                    <a className="facebook-sign-up" href="/facebook?utm_source=SEO&utm_medium=frontPage">
                      <img src='/images/social/facebook_white.png' className="facebook-white"/>
                      {' Facebook'}
                    </a>
                    <a className="google-sign-up" href="/googleSignin?utm_source=SEO&utm_medium=frontPage">
                      <img src='/images/social/google-logo.svg' className="google-white"/>
                      {' Google'}
                    </a>
                    {/* <a className="linkedin-sign-up" href="/linkedinSignin?utm_source=SEO&utm_medium=frontPage">
                      <img src='/images/social/LinkedIn-in.svg' className="linkedin-white"/>
                      {' LinkedIn'}
                    </a> */}
                  </div>
                  <a className="mbl-login" href="/signin">Login</a>
                </div>
                <div className="front-third">
                  <div className="fb-likes-container clearfix" style={thirdClass}>
                    <iframe src="https://www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2FPlexusscom-465631496904278%2F&width=88&layout=button_count&action=like&show_faces=false&share=false&height=21&appId=663647367028747" 
                      width="100" height="25" style={{border:'none', overflow:'hidden'}} 
                      scrolling="no" frameBorder="0" allowtransparency="true"></iframe>
                  </div>
                </div>
            </div>
            <div className="carousles">
                {this.props.carousles.comps.map((item,index) => <Carousles key={index} tag={item}/>)}
            </div>
            <Footer/>
            {this.state.cookie != 1 && 
            <div className='gdpr-cookies-notification'>
              <div className='gdpr-cookies-icon'></div>
              <div className='gdpr-notification-text'>
                  <div>We use cookies to personalize content and ads, to provide social media features, and to analyze our traffic. We also share information about your use of our site with colleges and partners. By continuing to browse the site you are agreeing to our use of cookies.</div>
                  <div className='mt10'>Plexuss is updating its Terms of Use and Privacy Policy on May 25, 2018. See the updated Terms of Use <a className='gdpr-linkout' href='/terms-of-service' target='_blank'>here</a> and the updated Privacy Policy <a class='gdpr-linkout' href='/privacy-policy' target='_blank'>here</a>.</div>
              </div>
              <div className='gdpr-cookies-agree-button' onClick={this.setCookie}>Yes, I agree</div>
            </div>}
          </div>
        )}
      </span>
    );
  }
}

const mapStateToProps = (state) =>{
  return{
    carousles: state.carousles,
    user: state.user,
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    setStep: (step) => {dispatch(setStep(step))},
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(Front_Dashboard);
