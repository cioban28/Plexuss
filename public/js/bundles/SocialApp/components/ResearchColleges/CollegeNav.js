import React, { Component } from 'react'
import './styles.scss'

class CollegeNav extends Component {
     render(){
        return (
          <div>
            <div className="college-pages-navbar">
                <a href="/college" className="">
                    Colleges
                </a>
                <a href="/college-majors"  className="">
                    Majors
                </a>
                <a href="/scholarships"  className="">
                    Scholarships
                </a>
                <a href="/ranking"  className="active">
                    Ranking
                </a>
                <a href="/comparison"  className="">
                    Compare Colleges
                </a>
            </div>

            <div className="college-pages-navbar-mobile">
                <div className="page-listed">
                    Ranking
                    <div className="college-nav-arrow"></div>
                </div>

                <div className="college-nav-options">
                    <a href="/college" className="">
                        Colleges
                    </a>


                    <a href="/college-majors"  className="">
                        Majors
                    </a>

                    <a href="/scholarships"  className="">
                        Scholarships
                    </a>

                    <a href="/ranking"  className="active">
                        Ranking
                    </a>


                    <a href="/comparison"  className="">
                        Compare Colleges
                    </a>

                </div>

            </div>
          </div>
       )
    }
}

export default CollegeNav
