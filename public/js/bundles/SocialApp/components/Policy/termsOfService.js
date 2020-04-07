import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import { connect } from 'react-redux'
import './styles.scss'

class TermsOfService extends Component{
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
            <h3 className="policy-title">PLEXUSS TERMS OF USE</h3>
            <div className="policy-content">
                <h4 className="policy-subtitle">PLEXUSS TERMS OF USE</h4>
                <div className="policy-subcontent">
                    <p>Effective Date:</p>
                    <p>Welcome to Plexuss.com, which is owned and operated by Plexuss, Inc, a Delaware corporation (referred to as “PLEXUSS,” “the Company,” “We,” or “Us”) of San Ramon, California in the United States of America.</p>
                    <p>The following terms and conditions (“Terms of Use” or “Terms”), govern your access or use of any online service location (e.g., plexuss.com, PLEXUSS mobile app, and all related digital properties) that posts a link to these Terms (“Sites” and “Apps”). It also applies to your use of all features, widgets, plug-ins, applications, content, downloads and/or other services that we own and control and make available through a website or application, and/or that post or link to these Terms (collectively, with the Sites and Apps, the "Service"), regardless of how you access or use it, whether via computer, mobile device or otherwise.</p>
                    <p>THESE TERMS GOVERN YOUR ACCESS AND USE OF THE SERVICE WHETHER AS A VISITOR, GUEST, OR A REGISTERED USER WITH A PROFILE OR ACCOUNT.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">ACCEPTANCE OF THE TERMS OF USE</h4>
                <h4 className="policy-subtitle">By using the Service, you accept these Terms of Use<br/>and the terms and conditions of our Privacy Policy.</h4>
                <div className="policy-subcontent">
                    <p>Please read the Terms of Use carefully before you start to use the Service. By using the Service or by clicking to accept or agree to the Terms of Use when this option is made available to you, you accept and agree to be bound and abide by these Terms of Use and our Privacy Policy, incorporated herein by reference. If you do not want to agree to these Terms of Use or the Privacy Policy, you must not access or use the Service</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">CHILDREN’S PRIVACY AND USE OF OUR SERVICE</h4>
                <div className="policy-subcontent">
                    <p>Our Service is not intended for nor targeted toward children under the age of thirteen (13). We do not knowingly collect personal information as defined by the United States Children’s Online Privacy Protection Act (“COPPA”) from children under the age of thirteen (13), and if we learn that we have collected such information, we will delete the information in accordance with COPPA. If you are a child under the age of thirteen (13), you are not permitted to use the Service and should not send any information about yourself to us through the Service. If you are a parent or guardian and believe we have collected information in a manner not permitted by COPPA, please contact us via email at <a className="link">compliance@plexuss.com.</a> </p>
                    <p>Any California residents under the age of eighteen (18) who have registered to use the Service, and who posted content or information on the Service, can request removal by contacting PLEXUSS <a className="link">here</a> or by sending a written request to:</p>
                    <div className="inner-content">
                        PLEXUSS, INC.<br/>Attention: Compliance Officer<br/>231 Market Place<br/>Suite 241<br/>San Ramon, California 94583
                    </div>
                    <p>When you contact us detailing where the content or information is posted and attesting that you posted it. PLEXUSS will then make reasonable good faith efforts to remove the post from prospective public view or anonymize it so the minor cannot be individually identified to the extent required by applicable law. This removal process cannot ensure complete or comprehensive removal. For instance, third-parties may have republished or archived content by search engines and others that PLEXUSS does not control.</p>
                    <p>The Service is offered and available to users who are 13 years of age or older. By using this Service, you represent and warrant that you are of legal age to form a binding contract with PLEXUSS and meet all of the foregoing eligibility requirements. If you do not meet all of these requirements, you must not access or use the Service</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">CHANGES TO THE TERMS OF USE</h4>
                <div className="policy-subcontent">
                    <p>PLEXUSS may revise and update these Terms of Use from time to time in our sole discretion. All changes are effective immediately when we post them, and apply to all access to and use of the Service thereafter. However, any changes to the dispute resolution provisions set forth in Governing Law and Jurisdiction will not apply to any disputes for which the parties have actual notice on or prior to the date the change is posted on the Service, which may also be found at all times by visiting plexuss.com/terms.</p>
                    <p>Your continued use of the Service following the posting of revised Terms of Use means that you accept and agree to the changes. You are expected to check this page from time to time so you are aware of any changes, as they are binding on you.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">ACCESS AND SECURITY</h4>
                <div className="policy-subcontent">
                    <p>PLEXUSS reserves the right to withdraw or amend this Service, and any service or material we provide through the Service, in our sole discretion without notice. We will not be liable if for any reason all or any part of the Service is unavailable at any time or for any period. From time to time, we may restrict access to some parts of the Service, or the entire Service, to users, including registered users with a profile.</p>
                    <p>You are responsible for:</p>
                    <ol start="1">
                        <li>Making all arrangements necessary for you to have access to the Service.</li>
                        <li>Ensuring that all persons who access the Service through your internet connection are aware of these Terms of Use and comply with them.</li>
                        <li>To access the Service or some of the resources it offers, you may be asked to provide certain registration details or other information. It is a condition of your use of the Service that all the information you provide on the Service is correct, current and complete. You agree that all information you provide to register with this Service or otherwise, including but not limited to the use of any interactive or social features of the Service, is governed by our Privacy Policy, and you consent to all actions we take with respect to your information consistent with our Privacy Policy.</li>
                        <li>If you choose, or are provided with, a user name, password or any other piece of information as part of our security procedures, you must treat such information as confidential, and you must not disclose it to any other person or entity. You also acknowledge that your profile and account is personal to you and agree not to provide any other person with access to this Service or portions of it using your user name, password or other security information. You agree to notify us immediately of any unauthorized access to or use of your user name or password or any other breach of security. You also agree to ensure that you exit from your account at the end of each session. You should use particular caution when accessing your account from a public or shared computer so that others are not able to view or record your password or other personal information.</li>
                    </ol>
                    <p>PLEXUSS reserves the right to access individual users’ accounts or profiles for technical and administrative purposes and for security reasons. The information obtained in such a manner will not be processed or made available to third parties unless required by law.</p>
                    <p>PLEXUSS also reserves the right to disable any user name, password or other identifier, whether chosen by you or provided by us, at any time in our sole discretion for any or no reason, including if, in our opinion, you have violated any provision of these Terms of Use.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">PROFILE AND ACCOUNT INFORMATION</h4>
                <div className="policy-subcontent">
                    <p>By setting up or registering for a personal profile with the Service, you agree to receive notices and information about your account and related updates electronically. You must have the ability to receive and retain electronic communications before you accept these Terms of Use and in order to create a PLEXUSS profile or account. Electronic communication will be the primary or sole form of communication to notify you about account updates, privacy changes, and information about your PLEXUSS premium services (e.g. MyCounselor) if you are a paid subscriber. You must enter a valid and active e-mail address at the time of registration and profile set-up in order to receive the aforementioned notifications about your account. Failure to do so as well as any consequences that arise as a result of not maintaining a valid or active e-mail address with your PLEXUSS profile and account will be your sole responsibility. We reserve the right to provide information about your account or our service by non-electronic means, at our sole discretion.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">INTELLECTUAL PROPERTY RIGHTS</h4>
                <div className="policy-subcontent">
                    <p>The Service and its entire contents, features and functionality (including but not limited to all information, software, text, displays, images, video and audio, and the design, selection and arrangement thereof), are owned by PLEXUSS, its licensors or other providers of such material and are protected by United States and international copyright, trademark, patent, trade secret and other intellectual property or proprietary rights laws.</p>
                    <p>These Terms of Use permit you to use the Service for your personal, non-commercial use only. You must not reproduce, distribute, modify, create derivative works of, publicly display, publicly perform, republish, download, store or transmit any of the material on our Service, except as follows:</p>
                    <p>You may store files that are automatically cached by your browser for display enhancement purposes.</p>
                    <p>If we provide desktop, mobile or other applications for download, you may download a single copy to your computer or mobile device solely for your own personal, non-commercial use, provided you agree to be bound by our end user license agreement for such applications.<br/>If we provide social media features with certain content, you make take such actions as are enabled by such features.</p>
                    <p>You must not:</p>
                    <ol start="1">
                        <li>Modify copies of any materials from the Service.</li>
                        <li>Use any illustrations, photographs, video or audio sequences or any graphics without prior written permission from PLEXUSS.</li>
                        <li>Delete or alter any copyright, trademark or other proprietary rights notices from copies of materials from the Service</li>
                        <li>You must not access or use for any commercial purposes any part of the Service or any services or materials available through the Service.</li>
                    </ol>
                    <p>If you wish to make any use of material on the Service other than that set out in this section, please address your request to: <a className="link">support@plexuss.com</a></p>
                    <p>If you print, copy, modify, download or otherwise use or provide any other person with access to any part of the Service breach of the Terms of Use, your right to use the Service will cease immediately and you must, at our option, return or destroy any copies of the materials you have made. No right, title or interest in or to the Service or any content on the Service is transferred to you, and all rights not expressly granted are reserved by the Company. Any use of the Service not expressly permitted by these Terms of Use is a breach of these Terms of Use and may violate copyright, trademark and other laws.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">PAYMENTS AND PLEXUSS PREMIUM SERVICES</h4>
                <div className="policy-subcontent">
                    <p>All payments made to the Company to access Premium or paid features of the Service, including but not limited to MyCounselor, must be made from a payment source on which you are the named Account holder. We currently only accept payments made by a major credit card (i.e., VISA, MasterCard, DISCOVER or American Express) or through PayPal.</p>
                    <p>You agree that we will not be liable for any loss caused by any unauthorized use of your credit card or any other method of payment by a third party in connection with the Service.</p>
                    <p>Any attempt to defraud the Service through the use of credit cards or other methods of payment will result in immediate termination of your account and civil and/or criminal prosecution. In the case of suspected or fraudulent payment, including use of stolen credentials, by anyone, or any other fraudulent activity, we reserve the right to block your account and profile. We shall be entitled to inform any relevant authorities or entities (including credit reference agencies) of any payment fraud or other unlawful activity, and may employ collection services to recover payments.</p>
                    <p>The Company may use third party electronic payment processors and/or financial institutions (“ESPs”) to process financial transactions. You irrevocably authorize us, as necessary, to instruct such ESPs to handle such transaction and you irrevocably agree that Company may give such instructions on your behalf in accordance with your requests as submitted on the Service. You agree to be bound by the terms and conditions of use of each applicable ESP, and in the event or conflict between these Terms and the ESP’s terms and conditions then these Terms shall prevail.</p>
                    <p>To provide continuous service, the Company automatically renews all paid subscriptions, including but not limited to those for MyCounselor. Such renewals are generally for the same duration as the original subscription term (for example, a 1-month subscription will renew on a monthly basis and a 1-year subscription will renew on an annual basis). By making a purchase through the Service, you acknowledge that your account will be subject to the above-described automatic renewals. In all cases, if you do not wish your account to renew automatically or if you wish to terminate automatic renewal at any time, please contact us at <a className="link">support@plexuss.com</a>.</p>
                    <p>Please note that your Premium, paid access to the Service may be interrupted as a result of a canceled or expired credit card or due to revocation of your PayPal billing authorization, which the Company has no control over. It is your responsibility to maintain updated payment methods to continue accessing the Service and its services.</p>
                    <p>When your paid plan such as MyCounselor begins, you will receive an e-mail receipt. You may cancel at any time before your subscription renews at the next billing. Upon cancellation, you will not be charged further starting from the date of cancellation. However, you will not receive a refund when you cancel unless you qualify for a refund under our Refund Policy. A copy of our Refund Policy can be found <a className="link">here</a>.<b className='with-underline'>All cancellations and requests for refunds must be made <a className="link">here</a>.</b></p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">TRADEMARKS</h4>
                <div className="policy-subcontent">
                    <p>PLEXUSS, MyCounselor, and all related names, logos, product and service names, designs and slogans are trademarks of the Company or its affiliates or licensors. You must not use such marks without the prior written permission of the Company. All other names, logos, product and service names, designs and slogans on this Service are the trademarks of their respective owners.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">PROHIBITED AND UNLAWFUL USE OF THE SERVICE</h4>
                <div className="policy-subcontent">
                    <p>You may use the Service only for lawful purposes and in accordance with these Terms of Use.</p>
                    <p>You agree not to use the Service:</p>
                    <ol start="1">
                        <li>In any way that violates any applicable federal, state, local or international law or regulation (including, without limitation, any laws regarding the export of data or software to and from the United States of America or other countries).</li>
                        <li>For the purpose of exploiting, harming or attempting to exploit or harm minors in any way by exposing them to inappropriate content, asking for personally identifiable information or otherwise.</li>
                        <li>To send, knowingly receive, upload, download, use or re-use any material that does not comply with the Content Standards set out in these Terms of Use.</li>
                        <li>To transmit, or procure the sending of, any advertising or promotional material without our prior written consent, including any “junk mail,” “chain letter,” “spam” or any other similar solicitation.</li>
                        <li>To impersonate or attempt to impersonate the Company, a Company employee, another user or any other person or entity (including, without limitation, by using e-mail addresses or profile names associated with any of the foregoing).</li>
                        <li>To engage in any other conduct that restricts or inhibits anyone’s use or enjoyment of the Service, or which, as determined by us, may harm the Company or users of the Service or expose them to liability.</li>
                    </ol>
                    <p>Additionally, you agree not to:</p>
                    <ol start="1">
                        <li>Use the Service in any manner that could disable, overburden, damage, or impair the site or interfere with any other party’s use of the Service, including their ability to engage in real time activities through the Service.</li>
                        <li>Use any robot, spider or other automatic device, process or means to access the Service for any purpose, including monitoring or copying any of the material on the Service. This includes the prohibited use of any automated scripts to unlock profiles, scrape the Service, or to access any subscription-based content.</li>
                        <li>Use any manual process to monitor or copy any of the material on the Service or for any other unauthorized purpose without our prior written consent.</li>
                        <li>Use any device, software or routine that interferes with proper functionality of the Service.</li>
                        <li>Introduce any viruses, trojan horses, worms, logic bombs or other material which is malicious or technologically harmful.</li>
                        <li>Attempt to gain unauthorized access to, interfere with, damage or disrupt any parts of the Service, the server on which the Service is stored, or any server, computer or database connected to the Service.</li>
                        <li>Attack the Service via a denial-of-service attack or a distributed denial-of-service attack.</li>
                        <li>Otherwise attempt to interfere with Service functionality.</li>
                    </ol>
                    <p>If you observe any user of the Service to be in violation of any of these prohibited uses, please let us know by clicking here.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">USER CONTENT</h4>
                <div className="policy-subcontent">
                    <p>Parts of our Service may permit you to submit reviews, newsfeeds, articles, ideas, photographs, user profiles, writings, music, video, audio recordings, computer graphics, pictures, data, questions, comments, suggestions or other content, including Personal Information for public availability (collectively, “User Content”), such as on message boards or your personal profile, and in association with your PLEXUSS profile/account.</p>
                    <p>Any User Content you post or otherwise share within the Service, including but not limited to content you add to your profile or other user profiles or in one-on-one messages with other users or persons, will be considered non-confidential and non-proprietary. By providing any User Content on the Service, you grant us and our affiliates and service providers, and each of their and our respective licensees, successors and assigns the right to use, reproduce, modify, perform, display, distribute and otherwise disclose to third parties any such material for any purpose. We or others may store, display, reproduce, publish, distribute or otherwise use User Content online or offline in any media or format (currently existing or hereafter developed) and may or may not attribute it to you.</p>
                    <p>Please keep in mind that if you share User Content, others have the ability to access and share it with third parties. PLEXUSS is not responsible for the privacy, security, accuracy, use, or misuse of any User Content that you disclose or receive from third parties via our Service.</p>
                    <p>All User Content must comply with the Content Standards set out in these Terms of Use.</p>
                    <p>In sharing or posting any User Content, you represent and warrant that:</p>
                    <ol start="1">
                        <li>You own or control all rights in and to the User Content and have the right to grant the license granted above to us and our affiliates and service providers, and each of their and our respective licensees, successors and assigns.</li>
                        <li>All of your User Content does and will comply with these Terms of Use.</li>
                        <li>You understand and acknowledge that you bear full responsibility for any User Content you submit or contribute, and you, not the Company, have full responsibility for such content, including its legality, reliability, accuracy and appropriateness.</li>
                        <li>We are not responsible, or liable to any third party, for the content or accuracy of any User Content posted by you or any other user of the Service.</li>
                    </ol>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">MONITORING, ENFORCEMENT & TERMINATION</h4>
                <div className="policy-subcontent">
                    <p>PLEXUSS has the right to take any of these actions at its sole discretion and with or without notice to you or any other user or party:</p>
                    <ol start="1">
                        <li>Remove or refuse to post any User Content for any or no reason in our sole discretion.</li>
                        <li>Take any action with respect to any User Content that we deem necessary or appropriate in our sole discretion, including if we believe that such User Content violates the Terms of Use, including the Content Standards, infringes any intellectual property right or other right of any person or entity, threatens the personal safety of users of the Service or the public, or could create liability for the Company.</li>
                        <li>Disclose your identity or other information about you to any third party who claims that material posted by you violates their rights, including their intellectual property rights or their right to privacy.</li>
                        <li>Take appropriate legal action, including without limitation, referral to law enforcement, for any illegal or unauthorized use of the Service.</li>
                        <li>Terminate or suspend your access to all or part of the Service for any or no reason, with or without notice, including without limitation, any violation of these Terms of Use.</li>
                        <li>Without limiting the foregoing, we have the right to fully cooperate with any law enforcement authorities or court order requesting or directing us to disclose the identity or other information of anyone posting any materials on or through the Service.</li>
                    </ol>
                    <p>YOU WAIVE AND HOLD HARMLESS THE COMPANY AND ITS AFFILIATES, LICENSEES AND SERVICE PROVIDERS FROM ANY CLAIMS RESULTING FROM ANY ACTION TAKEN BY THE COMPANY/ANY OF THE FOREGOING PARTIES DURING OR AS A RESULT OF ITS INVESTIGATIONS AND FROM ANY ACTIONS TAKEN AS A CONSEQUENCE OF INVESTIGATIONS BY EITHER THE COMPANY/SUCH PARTIES OR LAW ENFORCEMENT AUTHORITIES.</p>
                    <p>You understand and acknowledge that PLEXUSS does not review all material, including but not limited to User Content or content provided by third parties, before it is posted on the Service, and cannot ensure prompt removal of objectionable material after it has been posted. Accordingly, we assume no liability for any action or inaction regarding transmissions, communications or content provided by any user or third party. PLEXUSS shall assume no liability or responsibility to anyone for performance or nonperformance of the activities described in this section.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">PLEXUSS CONTENT STANDARDS</h4>
                <div className="policy-subcontent">
                    <p>These content standards apply to any and all User Content and use of all Interactive Services including, but not limited to, social media features of the Service. User Content must in their entirety comply with all applicable federal, state, local and international laws and regulations. Without limiting the foregoing, User Content must not:</p>
                    <ol start="1">
                        <li>Contain any material which is defamatory, obscene, indecent, abusive, offensive, harassing, violent, hateful, inflammatory or otherwise objectionable.</li>
                        <li>Promote sexually explicit or pornographic material, violence, or discrimination based on race, sex, religion, nationality, disability, gender identity, sexual orientation or age.</li>
                        <li>Infringe any patent, trademark, trade secret, copyright or other intellectual property or other rights of any other person.</li>
                        <li>Violate the legal rights (including the rights of publicity and privacy) of others or contain any material that could give rise to any civil or criminal liability under applicable laws or regulations or that otherwise may be in conflict with these Terms of Use and our Privacy Policy.</li>
                        <li>Be likely to deceive any person.</li>
                        <li>Promote any illegal activity, or advocate, promote or assist any unlawful act.</li>
                        <li>Cause annoyance, inconvenience or needless anxiety or be likely to upset, harass embarrass, alarm or annoy any other person.</li>
                        <li>Impersonate any person, or misrepresent your identity or affiliation with any person or organization.</li>
                        <li>Involve commercial activities or sales, such as contests, sweepstakes and other sales promotions, bartering or advertising.</li>
                        <li>Give the impression that they emanate from or are endorsed by PLEXUSS or any other person or entity, if this is not the case.</li>
                    </ol>
                    <p>If you believe any user of the Service to be in violation of our Content Standards, please let us know by clicking here.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">USER CONTENT AND COPYRIGHT INFRINGEMENT</h4>
                <div className="policy-subcontent">
                    <p>If you believe that any User Content violates your copyright, please contact us immediately by email at <a className="link">compliance@plexuss.com</a> or by mail at this address:</p>
                    <div className="inner-content">
                        PLEXUSS, INC.<br/>Attention: Compliance Officer<br/>231 Market Place<br/>Suite 241<br/>San Ramon, California 94583
                    </div>
                    <p>It is the policy of the Company to terminate the user accounts and profiles of repeat infringers.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">RELIANCE ON INFORMATION PRESENTED ON THE SERVICE</h4>
                <div className="policy-subcontent">
                    <p>The information presented on or through the Service is made available solely for general information purposes. We do not warrant the accuracy, completeness or usefulness of this information. Any reliance you place on such information is strictly at your own risk. We disclaim all liability and responsibility arising from any reliance placed on such materials by you or any other visitor to the Service, or by anyone who may be informed of any of its contents.</p>
                    <p>This Service includes content provided by third parties, including materials provided by other users, bloggers and third-party licensors, syndicators, aggregators and/or reporting services. All statements and/or opinions expressed in these materials, and all articles and responses to questions and other content, other than the content provided by the Company, are solely the opinions and the responsibility of the person or entity providing those materials. These materials do not necessarily reflect the opinion of the Company. We are not responsible, or liable to you or any third party, for the content or accuracy of any materials provided by any third parties.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">CHANGES TO THE SERVICE & CONTENT</h4>
                <div className="policy-subcontent">
                    <p>We may update the content and features on this Service from time to time, but its content is not necessarily complete or up-to-date. Any of the material on the Service may be out of date at any given time, and we are under no obligation to update such material.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">INFORMATION ABOUT YOU AND YOUR USE OF THE SERVICE</h4>
                <div className="policy-subcontent">
                    <p>All information we collect on this Service is subject to our Privacy Policy. By using the Service, you consent to all actions taken by us with respect to your information in compliance with the Privacy Policy which you can view <a className="link">here</a>. California residents may also view our California Privacy Policy <a className="link">here</a>.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">PREMIUM SERVICE PURCHASES AND OTHER TERMS AND CONDITIONS</h4>
                <div className="policy-subcontent">
                    <p>All purchases through our Service or other transactions associated with MyCounselor and other Premium products and services available through the Service are governed by these Terms of Use. Additional terms and conditions may also apply to specific portions, services or features of the Service. All such additional terms and conditions are hereby incorporated by this reference into these Terms of Use.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">LINKING TO THE SERVICE AND SOCIAL MEDIA FUNCTIONALITY</h4>
                <div className="policy-subcontent">
                    <p>You may link to our Service or any applicable component thereof (e.g. plexuss.com), provided you do so in a way that is fair and legal and does not damage our reputation or take advantage of it, but you must not establish a link in such a way as to suggest any form of association, approval or endorsement on our part without our express written consent.</p>
                    <p>This Service may provide certain social media features that enable you to:</p>
                    <ol start="1">
                        <li>Link from your own or certain third-party Services to certain content on this Service.</li>
                        <li>Send e-mails or other communications with certain content, or links to certain content, on this Service.</li>
                        <li>Cause limited portions of content on this Service to be displayed or appear to be displayed on your own or certain third-party websites or services.</li>
                        <li>You may use these features solely as they are provided by us, and solely with respect to the content they are displayed with and otherwise in accordance with any additional terms and conditions we provide with respect to such features. Subject to the foregoing, you must not:</li>
                        <ol className="inner-ol-start" start="1">
                            <li>Establish a link from any Service that is not owned by you.</li>
                            <li>Cause the Service or portions of it to be displayed, or appear to be displayed by, for example, framing, deep linking or in-line linking, on any other website or digital platform. Otherwise take any action with respect to the materials on this Service that is inconsistent with any other provision of these Terms of Use.</li>
                            <li>The Service from which you are linking, or on which you make certain content accessible, must comply in all respects with the Content Standards set out in these Terms of Use. You agree to cooperate with us in causing any unauthorized framing or linking immediately to cease. We reserve the right to withdraw linking permission without notice.</li>
                        </ol>
                    </ol>
                    <p className="with-underline">PLEXUSS may disable all or any social media features and any links at any time without notice in its sole discretion.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">LINKS FROM OUR SERVICE TO THIRD PARTY WEBSITES AND SERVICES</h4>
                <div className="policy-subcontent">
                    <p>If the Service contains links to other sites and resources provided by third parties such as websites, landing pages, applications, etc., these links are provided for your convenience only. This includes links contained in advertisements, including banner advertisements and sponsored links. We have no control over the contents of those sites or resources, and accept no responsibility for them or for any loss or damage that may arise from your use of them. If you decide to access any of the third party sites or services linked to this Service, you do so entirely at your own risk and subject to the terms and conditions of use for such Services.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">GEOGRAPHIC RESTRICTIONS & INTERNATIONAL USE</h4>
                <div className="policy-subcontent">
                    <p>PLEXUSS servers and operations are based in the United States of America. We make no claims that the Service or any of its content is accessible or appropriate outside of the United States. Access to the Service may not be legal by certain persons or in certain countries. If you access the Service from outside the United States, you do so on your own initiative and are responsible for compliance with local laws.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">DISCLAIMER OF ANY AND ALL WARRANTIES</h4>
                <div className="policy-subcontent">
                    <p>You understand that we cannot and do not guarantee or warrant that files, content, applications, etc. available for downloading from the internet or the Service will be free of viruses or other destructive code. You are solely responsible for implementing sufficient procedures and checkpoints to satisfy your particular requirements for anti-virus protection and accuracy of data input and output, and for maintaining a means external to our Service for any reconstruction of any lost data.</p>
                    <p>PLEXUSS WILL NOT BE LIABLE FOR ANY LOSS OR DAMAGE CAUSED BY A DISTRIBUTED DENIAL-OF-SERVICE ATTACKS, VIRUSES OR OTHER TECHNOLOGICALLY HARMFUL MATERIAL THAT MAY INFECT YOUR COMPUTER EQUIPMENT, MOBILE DEVICES, MOBILE APPLICATIONS, COMPUTER PROGRAMS, DATA OR OTHER PROPRIETARY MATERIAL DUE TO YOUR USE OF THE SERVICE OR ANY SERVICES OR ITEMS OBTAINED THROUGH THE SERVICE OR TO YOUR DOWNLOADING OF ANY MATERIAL POSTED ON IT, OR ON ANY SERVICE LINKED TO IT, THIRD-PARTY OR OTHERWISE.</p>
                    <p>YOUR USE OF THE SERVICE, ITS CONTENT AND ANY SERVICES OR ITEMS OBTAINED THROUGH THE SERVICE IS AT YOUR OWN RISK. THE SERVICE, ITS CONTENT AND ANY SERVICES OR ITEMS OBTAINED THROUGH THE SERVICE ARE PROVIDED ON AN “AS IS” AND “AS AVAILABLE” BASIS, WITHOUT ANY WARRANTIES OF ANY KIND, EITHER EXPRESS OR IMPLIED. NEITHER THE COMPANY NOR ANY PERSON ASSOCIATED WITH THE COMPANY MAKES ANY WARRANTY OR REPRESENTATION WITH RESPECT TO THE COMPLETENESS, SECURITY, RELIABILITY, QUALITY, ACCURACY OR AVAILABILITY OF THE SERVICE. WITHOUT LIMITING THE FOREGOING, NEITHER THE COMPANY NOR ANYONE ASSOCIATED WITH THE COMPANY REPRESENTS OR WARRANTS THAT THE SERVICE, ITS CONTENT OR ANY SERVICES OR ITEMS OBTAINED THROUGH THE SERVICE WILL BE ACCURATE, RELIABLE, ERROR-FREE OR UNINTERRUPTED, THAT DEFECTS WILL BE CORRECTED, THAT OUR SERVICE OR THE SERVERS THAT MAKE IT AVAILABLE ARE FREE OF VIRUSES OR OTHER HARMFUL COMPONENTS OR THAT THE SERVICE OR ANY SERVICES OR ITEMS OBTAINED THROUGH THE SERVICE WILL OTHERWISE MEET YOUR NEEDS OR EXPECTATIONS.</p>
                    <p>PLEXUSS HEREBY DISCLAIMS ALL WARRANTIES OF ANY KIND, WHETHER EXPRESS OR IMPLIED, STATUTORY OR OTHERWISE, INCLUDING BUT NOT LIMITED TO ANY WARRANTIES OF MERCHANTABILITY, NON-INFRINGEMENT AND FITNESS FOR PARTICULAR PURPOSE.</p>
                    <p>THE FOREGOING DOES NOT AFFECT ANY WARRANTIES WHICH CANNOT BE EXCLUDED OR LIMITED UNDER APPLICABLE LAW.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">LIMITATION OF LIABILITY</h4>
                <div className="policy-subcontent">
                    <p>IN NO EVENT SHALL PLEXUSS, ITS AFFILIATES OR THEIR LICENSORS, SERVICE PROVIDERS, EMPLOYEES, AGENTS, CONTRACTORS, CONSULTANTS, ANDS IT AND THEIR OFFICERS OR DIRECTORS BE LIABLE FOR DAMAGES OF ANY KIND, UNDER ANY LEGAL THEORY, ARISING OUT OF OR IN CONNECTION WITH YOUR USE, OR INABILITY TO USE, THE SERVICE, ANY SERVICES LINKED TO IT, ANY CONTENT ON THE SERVICE OR SUCH OTHER SERVICES OR ANY SERVICES OR ITEMS OBTAINED THROUGH THE SERVICE OR SUCH OTHER SERVICES, INCLUDING ANY DIRECT, INDIRECT, SPECIAL, INCIDENTAL, CONSEQUENTIAL OR PUNITIVE DAMAGES, INCLUDING BUT NOT LIMITED TO, PERSONAL INJURY, PAIN AND SUFFERING, EMOTIONAL DISTRESS, LOSS OF REVENUE, LOSS OF PROFITS, LOSS OF BUSINESS OR ANTICIPATED SAVINGS, LOSS OF USE, LOSS OF GOODWILL, LOSS OF DATA, AND WHETHER CAUSED BY TORT (INCLUDING NEGLIGENCE), BREACH OF CONTRACT OR OTHERWISE, EVEN IF FORESEEABLE. THE FOREGOING DOES NOT AFFECT ANY LIABILITY WHICH CANNOT BE EXCLUDED OR LIMITED UNDER APPLICABLE LAW.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">INDEMNIFICATION</h4>
                <div className="policy-subcontent">
                    <p>You agree to defend, indemnify and hold harmless PLEXUSS, its affiliates, licensors and service providers, and its and their respective officers, directors, employees, consultants contractors, agents, licensors, suppliers, successors and assignors from and against any claims, liabilities, damages, judgments, awards, losses, costs, expenses or fees (including reasonable attorneys’ fees) arising out of or relating to your violation of these Terms of Use or your use of the Service, including, but not limited to, your User Content, any use of the Service’s content, services and products other than as expressly authorized in these Terms of Use or your use of any information obtained from the Service.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">GOVERNING LAW AND JURISDICTION</h4>
                <div className="policy-subcontent">
                    <p>All matters relating to the Service and these Terms of Use and any dispute or claim arising therefrom or related thereto (in each case, including non-contractual disputes or claims), shall be governed by and construed in accordance with the internal laws of the State of California, U.S.A. without giving effect to any choice or conflict of law provision or rule (whether of the State of California or any other jurisdiction).</p>
                    <p>You and PLEXUSS agree any legal suit, action, litigation or proceeding of any kind whatsoever against the other or any other party in any way arising from or relating to any claims of disputes that relate to your use of the Service or these Terms of Use, including, but not limited to, contract, equity, tort, fraud, and statutory claims, shall be exclusively instituted and adjudicated in the United States District Court for the Northern District of California or, if such court does not have subject matter jurisdiction, the courts of the State of California sitting in Contra Costa County only; and any appellate court from any thereof. You and PLEXUSS irrevocably and unconditionally submits to the exclusive jurisdiction of such courts and agrees to bring any such action, litigation, or proceeding exclusively in the United States District Court for the Northern District of California or, if such court does not have subject matter jurisdiction, the courts of the State of California sitting in Contra Costa County only. You and PLEXUSS agree that a final judgment in any such action, litigation, or proceeding is conclusive and may be enforced in other jurisdictions by suit on the judgment or in any other manner provided by law. Notwithstanding the above, PLEXUSS retains the right to bring any suit, action or proceeding against you for breach of these Terms of Use in your country of residence or any other relevant country. You waive any and all objections to the exercise of jurisdiction over you by such courts and to venue in such courts.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">LIMITATION ON TIME TO FILE CLAIMS</h4>
                <div className="policy-subcontent">
                    <p>ANY CAUSE OF ACTION OR CLAIM YOU MAY HAVE ARISING OUT OF OR RELATING TO THESE TERMS OF USE OR THE SERVICE MUST BE COMMENCED WITHIN ONE (1) YEAR AFTER THE CAUSE OF ACTION ACCRUES, OTHERWISE, SUCH CAUSE OF ACTION OR CLAIM IS PERMANENTLY BARRED.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">WAIVER AND SEVERABILITY</h4>
                <div className="policy-subcontent">
                    <p>No waiver of by the Company of any term or condition set forth in these Terms of Use shall be deemed a further or continuing waiver of such term or condition or a waiver of any other term or condition, and any failure of the Company to assert a right or provision under these Terms of Use shall not constitute a waiver of such right or provision. If any provision of these Terms of Use is held by a court or other tribunal of competent jurisdiction to be invalid, illegal or unenforceable for any reason, such provision shall be eliminated or limited to the minimum extent such that the remaining provisions of the Terms of Use will continue in full force and effect.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">ENTIRE AGREEMENT</h4>
                <div className="policy-subcontent">
                    <p>The Terms of Use and Privacy Policy constitute the sole and entire agreement between you and PLEXUSS with respect to the Service and supersede all prior and contemporaneous understandings, agreements, representations and warranties, both written and oral, with respect to the Service.</p>
                </div>
            </div>

            <div className="policy-content">
                <h4 className="policy-subtitle">FOR ADDITIONAL INFORMATION</h4>
                <div className="policy-subcontent">
                    <p>This Service is operated by Plexuss, Inc., a Delaware corporation.</p>
                    <p>All notices of copyright infringement claims and all other feedback, comments, requests for technical support and other communications relating to the Service should be directed <a className="link">here</a>.</p>
                    <p>Thank you for using PLEXUSS and the Service.</p>
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

export default connect(mapStateToProps, mapDispatchToProps)(TermsOfService);
