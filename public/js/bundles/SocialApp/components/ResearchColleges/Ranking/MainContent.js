import React, { Component } from 'react'
import Modal from 'react-modal';
import '../styles.scss'
import './xstyles.scss'



class MainContent extends Component {

    constructor(props) {
        super(props);
        this.state = { showModal: false, showMagnifier_1: false, showMagnifier_2: false, showMagnifier_3: false }
    }

    setUnsetVideoModal() {
      this.setState({showModal: !(this.state.showModal)})
    }

    setUnsetPicture1Modal () {
      this.setState({showMagnifier_1: !(this.state.showMagnifier_1)})
    }

    setUnsetPicture2Modal () {
      this.setState({showMagnifier_2: !(this.state.showMagnifier_2)})
    }

    setUnsetPicture3Modal () {
      this.setState({showMagnifier_3: !(this.state.showMagnifier_3)})
    }

    render(){
        return (
                <div>
                    { this.state.showModal &&
                      <div style={{padding: "50%"}} className="modal_for_ranking">   
                        <Modal style={{width: '60%' , height: '60%' , margin: 'auto auto', padding: "10%" }} isOpen={this.state.showModal} > 
                          <div style={{height: "10%", float: "right"}}>
                            <button id="x" style={{backgroundColor: "white", fontWeight: 'bold'}} onClick={( ) => this.setUnsetVideoModal()}>
                                X
                            </button>
                          </div>

                          <div style={{height: '90%', width: "100%", padding: "0px"}} className="flex-video">
                            <iframe scrolling="no" src="//www.youtube.com/embed/O73eOnoTtPE?version=3&amp;showinfo=0&amp;controls=1&amp;rel=0"  ></iframe>
                          </div>
                        </Modal>
                      </div>
                    }

                    { this.state.showMagnifier_1 && 
                      <div style={{padding: "50%"}} className="modal_for_ranking" >
                        <Modal style={{width: '60%' , height: '60%' , margin: 'auto auto', padding: "10%" }} isOpen={this.state.showMagnifier_1}> 
                          <div style={{height: "10%", float: "right"}}>
                            <button id = "x" style={{backgroundColor: "white", fontWeight: 'bold'}} onClick={( ) => this.setUnsetPicture1Modal()}>
                                X
                            </button>
                          </div>
                          <div>
                            <img style={{height: '80%', width: "100%", padding: "0px"}} src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/ranking/images/ranking-table-1.PNG" alt="Plexuss College Rank"/>
                          </div>
                        </Modal>
                      </div>
                    }

                    { this.state.showMagnifier_2 && 
                      <div style={{padding: "50%"}} className="modal_for_ranking">
                        <Modal style={{width: '60%' , height: '60%' , margin: 'auto auto', padding: "10%" }} isOpen={this.state.showMagnifier_2}> 
                          <div style={{height: "10%", float: "right"}}>
                            <button id = "x" style={{backgroundColor: "white", fontWeight: 'bold'}} onClick={( ) => this.setUnsetPicture2Modal()}>
                                X
                            </button>
                          </div>
                          <div>
                            <img style={{height: '80%', width: "100%", padding: "0px"}} src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/ranking/images/ranking-table-2.PNG" alt="Plexuss College Rank"/>
                          </div>
                        </Modal>
                      </div>
                    }

                    { this.state.showMagnifier_3 && 
                      <div style={{padding: "50%"}} className="modal_for_ranking">
                        <Modal style={{width: '60%' , height: '60%' , margin: 'auto auto', padding: "10%" }} isOpen={this.state.showMagnifier_3}> 
                          <div style={{height: "10%", float: "right"}}>
                            <button id = "x" style={{backgroundColor: "white", fontWeight: 'bold'}} onClick={( ) => this.setUnsetPicture3Modal()}>
                                X
                            </button>
                          </div>
                          <div>
                            <img style={{height: '80%', width: "100%", padding: "0px"}} src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/ranking/images/ranking-table-3.PNG" alt="Plexuss College Rank"/>
                          </div>
                        </Modal>
                      </div>
                    }

                    <div className="row plex-ranking-meaning-container">
                        <div className="column small-12">
                            
                            <div className="row plex-college-rank-header">
                                <div className="column small-12 small-only-text-center">
                                    What are Plexuss College Rankings?
                                </div>
                            </div>

                            <div className="row banner-img-src" data-interchange={"[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/ranking/images/ranking-header.jpg, (default)], [https://s3-us-west-2.amazonaws.com/asset.plexuss.com/ranking/images/ranking-header.jpg, (large)]"}>
                                <div className="column small-12">
                                </div>
                            </div>

                            <div className="ranking-index-content">
                       
                                <p>College rankings can be a useful tool for a student in their college search process. However, the amount of college rankings out there can make things confusing. Comparing the ranking of these different sources can be time consuming and students may also find it difficult to determine whether a particular source is reputable or not. To alleviate some of these problems, Plexuss.com has created Plexuss College Rankings.</p>
                           
                                <img data-reveal-id="college-ranking-video" onClick={( ) => this.setUnsetVideoModal()} src="/images/pages/ranking-video.png" alt="Plexuss College Video" />
                           
                                <p>In addition to conveniently gathering rankings of several sources on our site, we've aggregated the college rankings of what we consider to be the most reputable ranking sources in order to create our own ranking system. Think of Plexuss College Rankings as a rank summary of sorts, a convenient and overall ranking system which easily tells you what college ranking systems agree on. Because our rankings are based on the aggregation of other college rankings, the weight of college characteristics it uses highly depends on the criteria utilized by the sources that we've chosen. The benefit in doing this is that with Plexuss College Rankings nearly all of the college characteristics that students and college experts care about are taken into account.</p>
                            


                           
                                <h4><strong>Selection of College Ranking Sources</strong></h4>
                           
                                <p>We examined all of the college rankings that we could find and chose five which we believe to have the most methodologically sound criteria. Ultimately, we chose rankings by <a href="http://colleges.usnews.rankingsandreviews.com/best-colleges" target="_blank">U.S. News (National University Rankings)</a>, <a href="http://www.forbes.com/top-colleges/" target="_blank">Forbes' America's Top Colleges</a>, <a href="http://www.timeshighereducation.co.uk/world-university-rankings/" target="_blank">Times Higher Education World University Rankings (Reuters)</a>, <a href="http://www.topuniversities.com/qs-world-university-rankings" target="_blank">QS World University Rankings</a>, and <a href="http://www.shanghairanking.com/" target="_blank">The Academic Ranking of World Universities (Shanghai Rankings)</a>.</p>
                           
                                <div className=" thumbnail-container">
                                    <img onClick={( ) => this.setUnsetPicture1Modal()} className="thumbnail-to-fullview" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/ranking/images/ranking-table-1_thumbnail.jpg" alt="plexuss ranking table" data-img-name="ranking-table-1.PNG" />
                                </div>
                                <p><small>Ranking criteria categorized and grouped into percentages to reflect overall weight given to each category by each ranking source.</small></p>
                           
                                <p>In an effort to make our rankings more impartial, we included rankings produced by non-US countries such as Times Higher Education, QS World University Rankings (United Kingdom) and Academic Ranking of World Universities (China). We also took into account a ranking's popularity, general reception, and availability of full methodology when deciding our sources. Though not as established as the other ranking systems we've selected, we decided to use Forbes' rankings as a reference to diversify the weights used by our aggregated ranking criteria as displayed in the table above.</p>
                           


                           
                                        <h4><strong>Plexuss College Rankings Calculation</strong></h4>
                                   
                                        <p><i>Warning</i>: The following section goes into the exact details of how we came up with our rankings. We suggest skipping this section unless you are  truly interested in discovering how our rankings are calculated and are not bored by numbers.</p>
                                    
                                        <p>To aggregate our ranking sources, we first standardized all rankings on a similar, compatible scale ranging between 0 and 100. We did this by converting college ranks to scores which utilize percentiles.</p>
                                   

                               
                                <p>For each ranking source, each college's rank is converted to a percentage which indicates the number of schools that are ranked higher or equal to when compared to that college. Assuming no ties for first place, a college which takes the #1 spot on a ranking system would receive 0% (see Princeton University in the table above, which displays a sample of college rankings from U.S. News). To make these scores more intuitive, each converted score is subtracted from 1, and then the resulting value is multiplied by 100. This now means that schools that are better ranked have higher adjusted scores, and that all schools will have an adjusted score between 0 and 100.</p>
                               
                                <div className=" thumbnail-container">
                                    <img onClick={( ) => this.setUnsetPicture2Modal()} className="thumbnail-to-fullview" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/ranking/images/ranking-table-2_thumbnail.jpg" alt="plexuss ranking table" data-img-name="ranking-table-2.PNG" />
                                </div>

                               
                                    <p>With 202 schools ranked in U.S. News' 2014-2015 National Universities Ranking, schools positioning around rank 100 would receive a converted score close to 50 (see Auburn University, University of Dayton, and Buffalo State SUNY). Meanwhile, colleges ranking near 150 would receive an adjusted score around 25 (see the University of Mississippi).</p>
                                    <p>The same process is applied to other ranking sources. After conducting some more adjustments (explained more below), the average of the adjusted scores for each college are taken. Schools are then sorted based on these averaged scores; the college with the highest average adjusted score is given the Plexuss rank of "1", the college with the second highest average adjusted score is given the Plexuss rank of "2", and so on.</p>
                               
                                    <p>We dealt with missing rankings by giving colleges with four or five rankings the highest ranks. Colleges with three rankings are ranked next, followed by colleges with only two rankings, and finally, colleges with only a single ranking. Colleges that are not ranked by any of the sources that we chose are not assigned a Plexuss College Ranking. Colleges with four or five rankings are ranked together (instead of separately) to give leeway to what some may consider as arbitrary reasoning for why some of our sources excluded ranking certain colleges. Take for example, the fact that Times Higher Education Rankings automatically excluded colleges which have produced fewer than 1000 research articles between the years 2007 and 2011.</p>
                               

                                <div className="thumbnail-container">
                                    <img onClick={( ) => this.setUnsetPicture3Modal()} className="thumbnail-to-fullview" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/ranking/images/ranking-table-3_thumbnail.jpg" alt="plexuss ranking table" data-img-name="ranking-table-3.PNG" />
                                </div>

                           
                                <p>
                                    Colleges which were given a ranged ranking such as "200 - 225" are given a ranking equivalent to the average of the numbers in that range (i.e., the ranged ranking of 200-225 gives the average  212.5). This applies to Times Higher Education and Shanghai rankings. For sources which rank international colleges, rankings are adjusted as if the original source were only ranking colleges in the United States. For example, QS World University Rankings (2014 - 2015) ranked Harvard as 4th internationally, however, we are displaying an adjusted rank of 2 for Harvard because it is the second highest ranked university in the United States. 
                                </p>
                               
                            </div>



                            <div className="row small-text-ranking ">
                                <div className="column small-12">
                                    <h4><strong>A suggestion to students</strong></h4>
                                </div>
                                <div className="column small-12">
                                    <p><i>We do not recommend using Plexuss College Rankings as the sole criteria in your college selection process.</i></p>
                                </div>
                                <div className="column small-12">
                                    <p>
                                        Colleges that are on top of our rankings will not necessarily be the best colleges for all students. We recommend using these rankings in conjunction with other features on our site. <a href="/comparison" target="_blank">Compare colleges</a> based on the college characteristics that are important to you. Take into account your engagement and interaction with colleges through our <a href="/portal" target="_blank">recruitment portal</a> and <a href="/chat" target="_blank">chat</a>. Come up with your decision based on criteria that matters most to you.
                                    </p>
                                </div>
                                <div className="column small-12">
                                    <p>
                                        Ultimately, we want you to decide which college is best for you, and <a href="/ranking/listing" target="_blank">Plexuss College Rankings</a> is just one of the many tools that we are providing to help get you there.
                                    </p>
                                </div>
                                <div className="column small-12">
                                    <p>
                                        <strong>Should you have questions, criticisms or feedback on how we could improve Plexuss College Rankings please contact us at support&commat;plexuss.com .</strong>
                                    </p>
                                </div>
                                <div className="column small-12">
                                    <p className="bottom-text"><small >Ranking calculations used in this article are from Plexuss’ 2014-2015 College Rankings. Our current rankings may not reflect the numbers listed here.</small></p>
                                </div>
                            </div>

                        </div>
                </div>
            </div>
        )
    }
}

export default MainContent


