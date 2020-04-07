import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import { connect } from 'react-redux'
import './styles.scss'

class PrivacyPolicy extends Component{
  constructor(props){
    super(props);

    this.state = {

    }
  }

  componentDidMount() {
    window.scrollTo(0, 0)
  }
  
  componentDidUpdate(prevProps) {
      if (this.props.location !== prevProps.location)
        window.scrollTo(0, 0)
  }

  render() {
    return (
      <div className="policy-background">
        <div className="policy-about"><img src="/images/policy/policy_about.svg"></img></div>
        <div className="policy-main-area">
          <h3 className="policy-title">PLEXUSS PRIVACY POLICY</h3>
          <div className="policy-content">
            <h4 className="policy-subtitle">PLEXUSS PRIVACY POLICY</h4>
            <div className="policy-subcontent">
              <p>Effective Date:</p>
              <p>This Privacy Policy describes how PLEXUSS, INC. (“PLEXUSS,” “we,” “our,” or “us”) collects, uses, and shares information about you and applies to your use of any PLEXUSS website, mobile application, or digital property that links to this Privacy Policy and all features, content, and other services that we own, control and make available through such online service location (collectively, the “Service”), regardless of how you access or use it, whether via computer, mobile device, consumer electronics device, or otherwise.</p>
              <p>To use many of PLEXUSS’ features, you will need to create a personal profile and account. To create a profile on PLEXUSS and utilize the Service, you will need to provide data including your name, email address and/or mobile number, and a password as well as pertinent information regarding your educational objectives. You will also have the option of providing PLEXUSS with additional information when you set-up your profile. Further, If you register for a PLEXUSS premium service or product such as MyCounselor, you will need to provide payment card (e.g., debit/credit card) and billing information.</p>
              <p>By viewing, accessing, registering, creating a profile/account or otherwise using the Service, you agree to our <a className="link">Terms of Use</a> and consent to our collection, use and disclosure practices, and other activities as described in this Privacy Policy, and any additional privacy statements that may be posted on an applicable part of the Service. If you do not agree and consent, please discontinue use of the Service, and uninstall all Service downloads and applications.</p>
              <p>Our practices and other activities described in this Privacy Policy and our Terms of Service reply to all use, viewing, and accessing of the Service, regardless of whether or not you have set-up a personal profile or account on PLEXUSS.</p>
            </div>
          </div>

          <div className="policy-content">
            <h4 className="policy-subtitle">OUR DATA PRACTICES</h4>
            <div className="policy-subcontent">
              <p>The most important benefit of creating a profile or account on PLEXUSS is that we use and share your information to help you connect with colleges, universities, employers,  the agents of colleges/universities, and other providers of education-related products and services so you can receive information and resources directly from them and through the Service. Information you and your family may receive includes information from colleges and universities (and their agents), as well as other education-related products and services such as: (a) scholarships, student loans and financial aid, college admissions consulting, test preparation/and tutorial services, firms that market for colleges and universities, extra-curricular enrichment and recognition programs; (b) financial services companies that offer products relevant to education or the pursuit thereof; (c) career, employment and military opportunities; and (d) non-profit organizations, companies offering educational products & services, and government agencies.</p>
              <p>Below is a table of contents which you can click on to link to each section of the Privacy Policy. At the end of each section, you can click on a link to return to this table of contents.</p>
              <ol className="roman-upper-ol-start" start="1">
                <li>Information Collection</li>
                <ol className="latin-upper-ol-start" start="1">
                  <li>Information You Provide to Us</li>
                  <li>Information We Collect Automatically</li>
                  <li>Information from Other Sources</li>
                </ol>
                <li>Use of Information</li>
                <li>Information Sharing</li>
                <ol className="latin-upper-ol-start" start="1">
                  <li>Your PLEXUSS User Profile & Account</li>
                  <li>Post, Likes, Follows, Comments, Messages</li>
                  <li>Get Recruited Feature</li>
                  <li>Non-Affiliated Parties</li>  
                  <li>Other Information We Share</li>  
                </ol>
                <li>Phone Number and Address Verification</li>
                <li>Sweepstakes, Contests, and Promotions</li>
                <li>Information You Disclose Publicly or To Others</li>
                <li>Third-Party Services and Social Features</li>
                <li>Analytics Services, Advertising, and Online Tracking</li>
                <li>Your Choices About How We Use and Disclose Your Information</li>
                <ol className="latin-upper-ol-start" start="1">
                  <li>Accessing and Changing Information</li>
                  <li>Tracking Technologies Generally</li>
                  <li>Analytics Services and Advertising Tracking Technologies</li>
                  <li>Communications</li>  
                </ol>
                <li>Your California Privacy Rights</li>
                <li>Children’s Privacy</li>
                <li>Data Security</li>
                <li>International Users</li>
                <li>Our Mobile Applications</li>
                <li>Changes to this Privacy Policy</li>
                <li>Applicable Law</li>
                <li>Dispute Resolution</li>
                <li>For Additional Information</li>
              </ol>
              <p>PLEXUSS reserves the right to access individual users’ accounts or profiles for technical and administrative purposes and for security reasons. The information obtained in such a manner will not be processed or made available to third parties unless required by law.</p>
              <p>PLEXUSS also reserves the right to disable any user name, password or other identifier, whether chosen by you or provided by us, at any time in our sole discretion for any or no reason, including if, in our opinion, you have violated any provision of these Terms of Use.</p>
            </div>
          </div>

          <div className="policy-content">
            <div className="gray-border">
              <h4 className="policy-subtitle gray">INFORMATION COLLECTION</h4>
            </div>
            <div className="policy-subcontent">
              <h4 className="policy-subtitle-italic">Information You Provide to Us</h4>
              <p>The Service, and/or we or our subcontractors, may collect information you provide directly to us and/or our subcontractors via the Service. For example, we collect information when you use the Service, set-up or change your personal profile for the Service, subscribe to notifications, make requests, post on the Service, participate in activities, or communicate or transact with us through the Service. In addition, when you interact with Third-Party Services (as defined below), you may be able to provide information to those third parties. Our Service, we and/or contractors, and or Third-Party Services, may collect information that may include: (i) personally identifiable information, which is information that identifies you personally, such as your first and last name, email address, phone number, address, financial, or payment card information (“Personal Information”); and (ii) demographic information, such as your gender, age, zip code and interests (“Demographic Information”). Except to the extent required by applicable law, Demographic Information is “non-Personal Information” (i.e., data that is not Personal Information under this Privacy Policy). In addition, Personal Information, including without limitation, Personal Profile Information, once “de-identified” (i.e., the removal, obscuring or modification of personal identifiers to make data no longer personally identifiable, including through anonymization, pseudonymization or hashing) is also non-Personal Information and may be used and shared without obligation to you, except as prohibited by applicable law. We make no assurances that the de-identified data is not capable of re-identification. To the extent any non-Personal Information is combined by or on behalf of PLEXUSS with Personal Information PLEXUSS itself collects directly from you via the Service, we will treat the combined data as your Personal Profile Information under this Privacy Policy.</p>
              <p>We may post user testimonials on the Service including ones you may generate, which may contain personally identifiable information like a video along with your first and last name. We will obtain your consent via email or via other means prior to posting the testimonial if we post your name and video along with your testimonial.</p>
              <h4 className="policy-subtitle-italic">Information We Collect Automatically</h4>
              <p>We and our subcontractors may automatically collect certain information about you when you access or use our Service (“Usage Information”). Usage Information may include your IP address, device identifier, Ad ID, browser type, operating system characteristics, data regarding network connected hardware (e.g., computer or mobile device), and information about your use of our Service, such as the time and duration of your visit and how you arrived at our Service. Except to the extent required by applicable law, PLEXUSS does not consider Usage Information to be Personal Information. However, Usage Information may be combined with your Personal Information. To the extent that we combine Usage Information with your Personal Information, we will treat the combined information as Personal Information under this Privacy Policy.</p>
              <p>The methods that may be used on the Service to collect Usage Information include:</p>
              <ol start="1">
                <li>Log Information: Log information is data about your use of the Service, such as IP address, browser type, Internet service provider, referring/exiting pages, operating system, date/time stamps, and related data, which may be stored in log files or otherwise.</li>
                <li>Information Collected by Cookies and Other Tracking Technologies: Cookies, web beacons (also known as “tracking pixels”), embedded scripts, location-identifying technologies, fingerprinting, device recognition technologies, in-app tracking methods and other tracking technologies now and hereafter developed (“Tracking Technologies”) may be used to collect information about interactions with the Service or emails, including information about your browsing and activity behavior.</li>
                <li>Cookies: A cookie is a small text file that is stored on a user’s device which may be session ID cookies or tracking cookies. Session cookies make it easier for you to navigate the Service and expire when you close your browser. Tracking cookies remain longer and help in understanding how you use the Service, and enhance your user experience. Cookies may remain on your hard drive for an extended period of time. If you use your browser’s method of blocking or removing cookies, some, but not all types of cookies may be deleted and/or blocked and as a result, some features and functionalities of the Service may not work. A Flash cookie (or locally shared object) is a data file that may be placed on a device via the Adobe Flash plug-in that may be built-in to or downloaded by you to your device. HTML5 cookies can be programmed through HTML5 local storage. Flash cookies and HTML5 cookies are locally stored on your device other than in the browser, and browser settings won’t control them. To identify certain types of locally shared objects on your computer, visit your settings and make adjustments. The Service may associate some or all of these types of cookies with your devices.</li>
                <li>Web Beacons (“Tracking Pixels”): Web beacons are small graphic images, also known as “Internet tags” or “clear gifs,” embedded in web pages and email messages. Web beacons may be used, without limitation, to count the number of visitors to our Service, to monitor how users navigate the Service, and to count how many particular articles or links were actually viewed.</li>
                <li>Embedded Scripts: An embedded script is programming code designed to collect information about your interactions with the Service. It is temporarily downloaded onto your computer from our web server, or from a third party with which we work, and is active only while you are connected to the Service, and deleted or deactivated thereafter.</li>
                <li>Location-identifying Technologies: GPS (global positioning systems) software, geo-filtering and other location-aware technologies locate (sometimes precisely) you for purposes such as verifying your location and delivering or restricting content based on your location. If you use our Services from your mobile device, that device will send us data about your location based on your phone settings. We will ask you to opt-in before we use GPS or other tools to identify your precise location.</li>
                <li>Fingerprinting: Collection and analysis of information from your device, such as, without limitation, your operating system, plug-ins, system fonts and other data, are for purposes of identification and/or tracking.</li>
                <li>Device Recognition Technologies: Technologies, including application of statistical probability to data sets, as well as linking a common unique identifier to different device use (e.g., Facebook ID), attempt to recognize or make assumptions about users and devices (e.g., that a user of multiple devices is the same user or household).</li>
                <li>In-app Tracking Methods: There are a variety of Tracking Technologies that may be included in mobile applications, and these are not browser-based like cookies and cannot be controlled by browser settings. Some use device identifiers, or other identifiers such as “Ad IDs” to associate app user activity to a particular app.</li>
              </ol>
              <p>We are giving you notice of the Tracking Technologies and your choices regarding them explained in the “Analytics Services, Advertising, and Online Tracking” and “Your Choices” sections below, so that your consent to encountering them is meaningfully informed and to track user activity across apps.</p>
              <h4 className="policy-subtitle-italic">Information from Other Sources</h4>
              <p>We may also obtain information about you from other sources and combine that with information we collect about you. To the extent we combine such third-party-sourced information with Personal Information we collect directly from you on the Service, we will treat the combined information as Personal Information under this Privacy Policy. We are not responsible for the accuracy of the information provided by third parties or third-party practices.</p>
            </div>
          </div>

          <div className="policy-content">
            <h4 className="policy-subtitle">USE OF INFORMATION</h4>
            <div className="policy-subcontent">
              <p>We may use information about you for any purposes not inconsistent with our statements under this Privacy Policy or prohibited by applicable law, including to:</p>
              <ol start="1">
                <li>Create and service your profile or account;</li>
                <li>Fulfill, confirm, and communicate with you regarding your transactions and data delivery;</li>
                <li>Respond to your comments, questions, and requests, and provide support;</li>
                <li>Send you technical notices, updates, security alerts, information regarding changes to our policies, and support and administrative messages;</li>
                <li>Prevent and address fraud, breach of policies or terms, and threats or harm;</li>
                <li>Monitor and analyze trends, usage, and activities;</li>
                <li>Conduct research, including focus groups and surveys;</li>
                <li>Improve our Service or other PLEXUSS digital properties, mobile applications, marketing efforts, products and services; and</li>
                <li>Send you advertisements and communicate with you regarding our and third-party</li>
                <li>products, services, offers, promotions, rewards and events we think you may be interested in (for information about how to manage these communications, see “Your Choices” below).</li>
              </ol>
            </div>
          </div>

          <div className="policy-content">
            <h4 className="policy-subtitle">INFORMATION SHARING</h4>
            <div className="policy-subcontent">
              <h4 className="policy-subtitle-italic">Your PLEXUSS User Profile & Account</h4>
              <p>Your PLEXUSS profile is fully visible to all other PLEXUSS users as well as PLEXUSS’ education, colleges and universities (including their agents), and our education-related services partners. Your PLEXUSS profile and its contents may also be visible on third-party search engines that PLEXUSS does not control (e.g. Google, Yahoo, etc.).</p>
              <h4 className="policy-subtitle-italic">Posts, Likes, Follows, Comments, Messages</h4>
              <p>Our Services allow viewing and sharing information including through posts, likes, follows and comments.</p>
              <ol className="dot-ol-start" start="1">
                <li>When you share an article or a post (e.g., an update, image, video or article) on PLEXUSS it can be viewed by everyone and re-shared anywhere.</li>
                <li>Users, colleges and universities, our education service partners, and others will be able to find and see your user generated content, including your name (and photo if you have provided one).</li>
                <li>Any information you share on the Service pages or profiles of colleges, universities, and others will be viewable by it and others who visit those pages.</li>
                <li>When you follow a person, college/university, or her, you are visible to others and that “page owner” as a follower.</li>
                <li>We may let colleges, universities, and education service partners know when you view their profile or page.</li>
                <li>When you like or re-share or comment on another’s content (including ads), others will be able to view these “social actions” and associate it with you (e.g., your name, profile and photo if you provided it).</li>
              </ol>
              <h4 className="policy-subtitle-italic">Get Recruited</h4>
              <p>We may share your Personal Information with our non-affiliated third-party business partners, colleges/universities, service providers, and vendors to enable them to assist us in providing you the educational programming or other products or services you request through our Service or to provide us with support for our internal and business operations, such as customer service, data processing, data storage, research, analytics, web hosting, marketing, providing suggestions and strategies to improve our business processes and the services we provide, and delivery of promotional or transactional materials.</p>
              <h4 className="policy-subtitle-italic">Non-Affiliated Parties</h4>
              <p>We may share your Personal Information with non-affiliated companies, parties, or organizations (with whom we maintain business relationships) when you engage in certain activities through the Services or services that are sponsored or provided by them, including, but not limited to requesting or purchasing products or services offered by these third parties, electing to receive information or communications from them, or electing to participate in contests, sweepstakes, games, scholarships, or other programs sponsored or provided by these third parties, in whole or in part.</p>
              <p>In addition, from time to time, we may share Personal Information (such as e-mail addresses and other contact information such as name, email address and phone number) with selected third parties, so they may offer educational or educational-related products and services that we believe may be of interest to our users. To opt-out of our sharing of your information with such third parties, see the <b className="with-italic">Your Choices about How We Use and Disclose Your Information</b> section.</p>
              <h4 className="policy-subtitle-italic">Other Information We Share</h4>
              <p>Our subcontractors and vendors may receive, or be given access to your information, without limitation, Personal Information and Usage Information, in connection with their work on our behalf. We may also share information about you as follows:</p>
              <ol start="1">
                <li>To comply with the law, law enforcement or other legal process, and except as prohibited by applicable law, in response to a government request;</li>
                <li>To protect the rights, property, life, health, security and safety of us, the Service or any third party;</li>
                <li>In connection with, or during negotiations of, any proposed or actual merger, purchase, sale or any other type of acquisition or business combination of all or any portion of our assets, or transfer of all or a portion of our business to another company;</li>
                <li>With our affiliated units, companies, and divisions for internal business purposes (“Affiliates”), such as updating your profile on affiliated online services and delivering college and university matches to users of our affiliate online services;</li>
                <li>With our Affiliates, business partners, and other third parties for their own business purposes, including direct marketing purposes (California residents have certain rights set forth in “Your California Privacy Rights” below);</li>
                <li>For a purpose disclosed elsewhere in this Privacy Policy, or at the time you provide Personal Information; and</li>
                <li>With your consent or at your direction.</li>
              </ol>
              <p>Without limiting the foregoing, in our sole discretion, PLEXUSS may share aggregated information which does not identify you or de-identified information about you with third parties or affiliates for any purpose as permitted by applicable law. Without limiting the generality of the foregoing, we and third parties may convert your Personal Information to non-Personal Information, including without limitation through hashing it or substituting a unique identifier for the Personal Information and we and third parties may use and share that data as permitted by applicable law, including to match data attributes from other sources and to send targeted advertisements.</p>
              <p>For your options regarding targeted advertising, click <a className="link">here</a>. For such practices by third parties, consult their privacy policies.</p>
              <p>We cannot ensure that all of your private communications and other Personal Information will never be disclosed in ways not otherwise described in this Privacy Policy. For example, we may be forced to disclose information to the government or third parties under certain circumstances, third parties may unlawfully intercept or access transmissions or private communications, or users may abuse or misuse your personal information that they unlawfully collect from the Service. We will try to protect your privacy, however, we do not promise, and you should not expect, that your Personal Information or private communications will always remain private.</p>
            </div>
          </div>

          <div className="policy-content">
            <h4 className="policy-subtitle">PHONE NUMBER AND ADDRESS VERIFICATION</h4>
            <div className="policy-subcontent">
              <p>If applicable to the Services you are using, we work with service providers to verify your address and phone number, in order to accurately match you to college, university, or other education services provider, if applicable.</p>
            </div>
          </div>

          <div className="policy-content">
            <h4 className="policy-subtitle">SWEEPSTAKES, CONTESTS, PROMOTIONS</h4>
            <div className="policy-subcontent">
              <p>We may offer sweepstakes, contests, surveys, and other promotions (each, a “Promotion”) jointly sponsored or offered by third parties that may require submitting Personal Information. If you voluntarily choose to enter a Promotion, your Personal Information may be disclosed to third parties for administrative purposes and as required by law (e.g., on a winners list). By entering, you agree to the official rules that govern that Promotion, and may, except where prohibited by applicable law, allow the sponsor and/or other parties to use your name, voice and/or likeness in advertising or marketing materials.</p>
            </div>
          </div>

          <div className="policy-content">
            <h4 className="policy-subtitle">INFORMATION YOU DISCLOSE PUBLICLY OR TO OTHERS</h4>
            <div className="policy-subcontent">
              <p>Parts of our Service may permit you to submit reviews, messages, ideas, photographs, user profiles, writings, music, video, audio recordings, computer graphics, pictures, data, questions, comments, suggestions or other content, including Personal Information for public availability (collectively, “User Content”), such as on message boards or your personal profile, and in association with your PLEXUSS profile/account. We or others may store, display, reproduce, publish, distribute or otherwise use User Content online or offline in any media or format (currently existing or hereafter developed) and may or may not attribute it to you. Please keep in mind that if you share User Content, others have the ability to access and share it with third parties. PLEXUSS is not responsible for the privacy, security, accuracy, use, or misuse of any User Content that you disclose or receive from third parties via our Service. Please also the section on Third-Party Services and Social Features below.</p>
            </div>
          </div>

          <div className="policy-content">
            <h4 className="policy-subtitle">THIRD-PARTY SERVICES AND SOCIAL FEATURES</h4>
            <div className="policy-subcontent">
              <p>Our Service includes hyperlinks to, or may include on or in connection with our Service, websites, locations, platforms, or services operated by third parties (“Third-Party Service(s)“). These Third-Party Services may use their own cookies, web beacons, embedded scripts, location-identifying technologies, in-app tracking methods, and other Tracking Technologies to independently collect information about you and may solicit Personal Information from you.</p>
              <p>Certain functionalities on the Service permit interactions that you initiate between the Service and certain Third-Party Services, such as third-party social networks (“Social Features”). Examples of Social Features include “liking” or “sharing” our content and otherwise connecting our Service to a Third-Party Service. If you use Social Features, and potentially other Third- Party Services, information you post or provide access to may be publicly displayed on our Service or by the Third-Party Service that you use. Similarly, if you post information on a Third- Party Service that references our Service (e.g., by using a hashtag associated with PLEXUSS in a tweet or status update), your post may be used on or in connection with our Service. Also, both PLEXUSS and the third party may have access to certain information about you and your use of our Service and the Third-Party Service. To the extent we combine information from Third-Party Services with Personal Information we collect directly from you on the Service, we will treat the combined information as Personal Information under this Privacy Policy.<br/>The information collected and stored by third parties like Facebook, Instagram and Twitter remains subject to their privacy practices, including whether they continue to share information with us, the types of information shared, and your choices on what is visible to others on Third-Party Services.</p>
              <p>We are not responsible for and make no representations regarding the policies or business practices of any third parties or Third-Party Services and encourage you to familiarize yourself with and consult their privacy policies and terms of use.</p>
              <p>If you submit information to an advertiser or other third party that is made available on or through the Service, the information obtained during your visit to that advertiser’s website or application, and the information you give to the advertiser will be governed by the advertiser’s privacy policy. For further information on such advertiser’s use of your information, please visit the applicable privacy policy of such advertisers. Advertisers made available on this site have separate policy practices for which PLEXUSS has no responsibility or liability.</p>
            </div>
          </div>

          <div className="policy-content">
            <h4 className="policy-subtitle">ANALYTICS SERVICES, ADVERTISING, AND ONLINE TRACKING</h4>
            <div className="policy-subcontent">
              <p>We may engage and work with third parties to serve advertisements on our behalf on the Service and/or on third-party services and to provide analytics services about the use of our Service and the performance of our ads and content on third-party services. In addition, we may participate in online advertising networks and exchanges that display relevant advertisements to our Service visitors, on our Service and on third-party services and off of our Service, based on their interests as reflected in their browsing of the Service and certain third-party services. These entities may use cookies and other Tracking Technologies to automatically collect information about you and your activities, such as registering a unique identifier for your device and tying that to your online activities on and off of our Service. We may use this information to analyze and track data, determine the popularity of certain content, deliver advertising and content targeted to your interests on the Service and third-party services and better understand your online activity.</p>
              <p>Some information about your use of the Service and certain third-party services may be collected using Tracking Technologies across time and services and used by PLEXUSS and third parties for purposes such as to associate different devices you use, and deliver relevant and retargeted ads (“Interest-based Ads”) and/or other content to you on the Service and certain third-party services.</p>
              <p>Your browser settings may allow you to automatically transmit a “Do Not Track” signal to online services you visit. Note there is not yet an industry consensus as to what site and app operators should do with regard to these signals. Accordingly, we do not monitor or take action with respect to “Do Not Track” signals or other mechanisms. For more information on “Do Not Track,” visit http://www.allaboutdnt.com.</p>
              <p>You may have certain choices regarding Tracking Technologies as explained in the next section.</p>
            </div>
          </div>

          <div className="policy-content">
            <h4 className="policy-subtitle">YOUR CHOICES ABOUT HOW WE USE AND DISCLOSE YOUR INFORMATION</h4>
            <div className="policy-subcontent">
              <h4 className="policy-subtitle-italic">Accessing and Changing Information</h4>
              <p>You may access, review, correct, and update certain profile and account information you have submitted to us by contacting us <a className="link">here</a>. We will make a good faith effort to make the changes in PLEXUSS then-active databases as soon as practicable, but it may not be possible to completely change your information. We reserve the right to retain data as required by applicable law; and for so long as reasonably necessary to fulfill the purposes for which the data is retained as permitted by applicable law (e.g., business records).</p>
              
              <h4 className="policy-subtitle-italic">Tracking Technologies Generally</h4>
              <p>Regular cookies may generally be disabled or removed by tools available as part of most commercial browsers, and in some instances blocked in the future by selecting certain settings. Browsers offer different functionalities and options so you may need to set them separately. Also, tools from commercial browsers may not be effective with regard to Flash cookies (also known as locally shared objects), HTML5 cookies or other Tracking Technologies. For information on disabling Flash cookies, go to Adobe’s website <a className="link">here</a>. Please be aware that if you disable or remove these technologies, some parts of the Service may not work and that when you revisit the Service, your ability to limit browser-based Tracking Technologies is subject to your browser settings and limitations.</p>
              <p>App-related Tracking Technologies in connection with non-browser usage (e.g., most functionality of a mobile app) can only be disabled by uninstalling the app. To uninstall an app, follow the instructions from your operating system or handset manufacturer.</p>
              <p>Your browser settings may allow you to automatically transmit a “Do Not Track” signal to online services you visit. Note, however, there is no consensus among industry participants as to what “Do Not Track” means in this context. Like many online services, we currently do not alter our practices when we receive a “Do Not Track” signal from a visitor’s browser. To find out more about “Do Not Track,” you can visit <a className="link">here</a>, but we are not responsible for the completeness or accuracy of this third party information. Some third parties, however, may offer you choices regarding their Tracking Technologies. One way to potentially identify cookies on our Site is to add the free <a className="link">Ghostery plug-in</a> to your browser, which according to Ghostery will display for you traditional, browser-based cookies associated with the websites (but not mobile apps) you visit and privacy and opt-out policies and options of the parties operating those cookies. PLEXUSS is not responsible for the completeness or accuracy of this tool or third-party choice notices or mechanisms. For specific information on some of the choice options offered by third party analytics and advertising providers, see the next section.</p>
              
              <h4 className="policy-subtitle-italic">Analytics Services and Advertising Tracking Technologies</h4>
              <p>You may exercise choices regarding the use of cookies from Google Analytics by going <a className="link">here</a> or downloading the Google Analytics Opt-out Browser Add-on. You may choose whether to receive Interest-based Advertising by submitting opt-outs. Some of the advertisers and subcontractors that perform advertising-related services for us and our partners may participate in the Digital Advertising Alliance’s (“DAA”) Self-Regulatory Program for Online Behavioral Advertising. To learn more about how you can exercise certain choices regarding Interest-based Advertising, visit <a className="link">here</a>, and <a className="link">here</a> for information on the DAA’s opt-out program for mobile apps. Some of these companies may also be members of the Network Advertising Initiative (“NAI”). To learn more about the NAI and your opt-out options for their members, see <a className="link">here</a>. Please be aware that, even if you are able to opt out of certain kinds of Interest-based Advertising, you may continue to receive other types of ads. Opting out only means that those selected members should no longer deliver certain Interest-based Advertising to you, but does not mean you will no longer receive any targeted content and/or ads (e.g., from other ad networks). PLEXUSS is not responsible for effectiveness of, or compliance with, any third-parties’ opt-out options or programs or the accuracy of their statements regarding their programs. However, we support the ad industry’s <a className="link">Self-regulatory Principles for Online Behavioral Advertising</a> and expect that ad networks we directly engage to serve you Interest-based Ads will do so as well, though we cannot guaranty their compliance.</p>
            </div>
          </div>

          <div className="policy-content">
            <h4 className="policy-subtitle">COMMUNICATIONS</h4>
            <div className="policy-subcontent">
              <p>You can opt out of receiving certain promotional email communications from us at any time by following the instructions provided in emails to click on the unsubscribe link, or if available by changing your communication preferences by logging onto your profile. Please note that your opt-out is limited to the email address and will not affect subsequent requests or profiles you set-up. If you opt-out of only certain communications, other communications may continue. Even if you opt out of receiving promotional communications, we may, subject to applicable law, continue to send you non-promotional communications, such as those about your account, transactions, servicing, or our ongoing business relations.</p>
              <p>By providing a phone number you consent to be contacted at that number, including promotional phone calls related to us, colleges and universities including their agents, and educational related companies we think you may find of interest, and administrative texts regarding the Service that do not include advertising messages. For promotional calls, you may opt-out of pre-recorded calls by following the automated prompts and for live promotional calls you may opt-out by telling the caller. Such an opt-out will prospectively end our promotional calls to you by PLEXUSS, and we will no longer share your number with third-parties for their promotional calling, unless you subsequently opt back in. However, you will need to opt-out of any third parties that received your number prior to your opting out with PLEXUSS, directly with those third parties. For text messages, you may withdraw consent by replying “STOP” to PLEXUSS texts, but if you thereafter provide a number to receive texts you will have opted back in. We may offer separate text subscription programs for different purposes, in which case you must opt-out of each one separately. If you have provided multiple numbers, you must opt-out of each number separately. These calls and texts may be made using autodialers and/or pre-recorded messages. You are not required to consent as a condition of purchasing any property, goods or services and no purchase is necessary to subscribe. Calls from or to us may be recorded; if you do not consent to call recording, discontinue the call.</p>
            </div>
          </div>

          <div className="policy-content">
            <h4 className="policy-subtitle">YOUR CALIFORNIA PRIVACY RIGHTS</h4>
            <div className="policy-subcontent">
              <p>California’s “Shine the Light” law permits customers in California to request certain details about how certain types of their information are shared with third parties and, in some cases, affiliates, for those third parties’ and affiliates’ own direct marketing purposes. Under the law, a business should either provide California customers certain information upon request or permit California customers to opt in to, or opt out of, this type of sharing.</p>
              <p>PLEXUSS provides California residents with the option to opt-out of sharing “personal information” as defined by California’s “Shine the Light” law with third parties for such third parties own direct marketing purposes. California residents may exercise that opt-out, and/or request information about PLEXUSS compliance with the “Shine the Light” law, by contacting PLEXUSS <a className="link">here</a>, or by sending a written request to:</p>
              <div className="inner-content">
                PLEXUSS, INC.<br/>Attention: Compliance Officer<br/>231 Market Place<br/>Suite 241<br/>San Ramon, California 94583
              </div>
              <p>Requests must include “California Privacy Rights Request” in the subject line of your request and include your name, street address, city, state, and ZIP code. Please note that PLEXUSS is not required to respond to requests made by means other than through the provided email address or mailing address.</p>
              <p>Any California residents under the age of eighteen (18) who have registered to use the Service, and who have posted content or information on the Service, can request removal by contacting us <a className="link">here</a>, or by sending a written request to:</p>
              <div className="inner-content">
                PLEXUSS, INC.<br/>Attention: Compliance Officer<br/>231 Market Place<br/>Suite 241<br/>San Ramon, California 94583
              </div>
              <p>Your written request should detail where the content or information is posted and attesting that you posted it. We will then make reasonable good faith efforts to remove the post from prospective public view or anonymize it so the minor cannot be individually identified to the extent required by applicable law. Please note that the removal process cannot ensure complete or comprehensive removal. For instance, third-parties may have republished or archived content by search engines and others that we do not control.</p>
              <p>The Service discloses its tracking practices (including across time and third-party services) <a className="link">here</a> and its practices regarding “Do not track” signals <a className="link">here</a>.</p>
            </div>
          </div>

          <div className="policy-content">
            <h4 className="policy-subtitle">CHILDREN’S PRIVACY</h4>
            <div className="policy-subcontent">
              <p>Our Service is not intended for nor targeted toward children under the age of thirteen (13). We do not knowingly collect personal information as defined by the United States Children’s Online Privacy Protection Act (“COPPA”) from children under the age of thirteen (13), and if we learn that we have collected such information, we will delete the information in accordance with COPPA. If you are a child under the age of thirteen (13), you are not permitted to use the Service and should not send any information about yourself to us through the Service. If you are a parent or guardian and believe we have collected information in a manner not permitted by COPPA, please contact us <a className="link">here</a>.</p>
              <p>Any California residents under the age of eighteen (18) who have registered to use the Service, and who posted content or information on the Service, can request removal by contacting PLEXUSS <a className="link">here</a> or by sending a written request to:</p>
              <div className="inner-content">
                PLEXUSS, INC.<br/>Attention: Compliance Officer<br/>231 Market Place<br/>Suite 241<br/>San Ramon, California 94583
              </div>
              <p>When you contact us detailing where the content or information is posted and attesting that you posted it. PLEXUSS will then make reasonable good faith efforts to remove the post from prospective public view or anonymize it so the minor cannot be individually identified to the extent required by applicable law. This removal process cannot ensure complete or comprehensive removal. For instance, third-parties may have republished or archived content by search engines and others that PLEXUSS does not control.</p>
            </div>
          </div>

          <div className="policy-content">
            <h4 className="policy-subtitle">DATA SECURITY</h4>
            <div className="policy-subcontent">
              <p>We take reasonable measures to protect Personal Information from loss, theft, misuse and unauthorized access, disclosure, alteration and destruction. Nevertheless, transmission via the internet and online digital storage are not completely secure and we cannot guarantee the security of your information collected through the Service. To help protect you and others, we and our subcontractors may (but make no commitment to) monitor use of the Service, and may collect and use related information including Personal Profile Information and other Personal Information for all purposes not prohibited by applicable law or inconsistent with this Privacy Policy, including without limitation, to identify fraudulent activities and transactions; prevent abuse of and investigate and/or seek prosecution for any potential threats to or misuse of the Service; ensure compliance with our Terms of Use and this Privacy Policy; investigate violations of or enforce these agreements; and otherwise to protect the rights and property of PLEXUSS, its partners, and users. Monitoring may result in the collection, recording, and analysis of online activity or communications through our Service. If you do not consent to these conditions, please discontinue your use of the Service.</p>
            </div>
          </div>

          <div className="policy-content">
            <h4 className="policy-subtitle">INTERNATIONAL USERS</h4>
            <div className="policy-subcontent">
              <p>The Service was created by and is controlled, operated and administered by PLEXUSS, INC., a Delaware corporation, or its agents from offices within the United States of America utilizing servers located in the United States of America. We make no representation that materials on this Service are appropriate or available for transmission to or from, or use in, locations outside of the jurisdiction(s) stated above and accessing any Service from any jurisdiction where such Service’s contents are illegal is prohibited. You may not use this Service or export the materials in violation of import or export laws and regulations. If you access the Service from a location outside of the United States, you are responsible for compliance with all local laws. If you are accessing the Service from outside of the United States, please be aware that information collected through the Service may be transferred to, processed, stored and used in the United States Data protection laws in the United States. may be different from those of your country of residence. Your use of the Service or provision of any information therefore constitutes your consent to the transfer to and from, processing, usage, sharing and storage of your information, including Personal Information, in the United States as set forth in this Privacy Policy.</p>
            </div>
          </div>

          <div className="policy-content">
            <h4 className="policy-subtitle">OUR MOBILE APPLICATIONS</h4>
            <div className="policy-subcontent">
              <p>With respect to our mobile apps (<b>“App(s)”</b>), you can stop all collection of data generated by use of the App by uninstalling the App. Also, you may be able to exercise specific privacy choices, such as enabling or disabling certain location-based or other services, by adjusting the permissions in your mobile device or in App settings. However, other means of establishing or estimating location in connection with Service use (e.g., IP address, connecting to or proximity to wi-fi, Bluetooth, beacons, or networks, etc.) may persist. See also the prior section regarding the DAA’s mobile Interest-based Advertising choices.</p>
            </div>
          </div>

          <div className="policy-content">
            <h4 className="policy-subtitle">CHANGES TO THIS PRIVACY POLICY</h4>
            <div className="policy-subcontent">
              <p>We reserve the right to revise and reissue this Privacy Policy at any time. Any changes will be effective immediately upon posting of the revised Privacy Policy. Subject to applicable law, your continued use of our Service indicates your consent to the privacy policy posted. Your continued use of the Service indicates your consent to the Privacy Policy then posted. If you do not agree, please discontinue use of the Service, and uninstall Service downloads and applications.</p>
            </div>
          </div>

          <div className="policy-content">
            <h4 className="policy-subtitle">APPLICABLE LAW</h4>
            <div className="policy-subcontent">
              <p>The Service is the property of PLEXUSS of San Ramon, California. As such, the laws of the State of California, U.S.A. shall exclusively govern these legal notices and any dispute arising, directly or indirectly, from Your use of this site or the PLEXUSS digital platform, without regard to conflicts of law principles. By using the Service, you and PLEXUSS agree that the laws of the State of California, U.S.A. shall exclusively govern any claims and disputes between you and PLEXUSS; including but not limited to any claims or disputes that arise as it relates to this Privacy Policy.</p>
            </div>
          </div>

          <div className="policy-content">
            <h4 className="policy-subtitle">DISPUTE RESOLUTION</h4>
            <div className="policy-subcontent">
              <p>You and PLEXUSS agree to not commence any action, litigation, or proceeding of any kind whatsoever against the other or any other party in any way arising from or relating to any claims of disputes that relate to your use of the Service or this Privacy Policy, including, but not limited to, contract, equity, tort, fraud, and statutory claims, in any forum other than the United States District Court for the Northern District of California or, if such court does not have subject matter jurisdiction, the courts of the State of California sitting in Contra Costa County only and any appellate court from any thereof. You and PLEXUSS irrevocably and unconditionally submits to the exclusive jurisdiction of such courts and agrees to bring any such action, litigation, or proceeding only in the United States District Court for the Northern District of California or, if such court does not have subject matter jurisdiction, the courts of the State of California sitting in Contra Costa County only. You and PLEXUSS agree that a final judgment in any such action, litigation, or proceeding is conclusive and may be enforced in other jurisdictions by suit on the judgment or in any other manner provided by law.</p>
            </div>
          </div>

          <div className="policy-content">
            <h4 className="policy-subtitle">FOR ADDITIONAL INFORMATION</h4>
            <div className="policy-subcontent">
              <p>For any requests relating to your Personal Information, or if you have any questions about this Privacy Policy, please contact us <a className="link">here</a> or via mail at:</p>
              <div className="inner-content">
                PLEXUSS, INC.<br/>Attention: Compliance Officer<br/>231 Market Place<br/>Suite 241<br/>San Ramon, California 94583
              </div>
            </div>
          </div>

        </div>
      </div>
    );
  }
}

const mapStateToProps = (state) =>{
  return{
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(PrivacyPolicy);
