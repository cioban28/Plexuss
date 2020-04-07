// /Dashboard/constants.js

export const THOUSAND = 1000;

export const VERIFIED = [
	{name: 'verifiedHs', label: 'Handshakes', route: '/admin/inquiries/verifiedHs', tip_name: 'Verified Handshakes', tip: 'Are students whos phone number and method of contacts has been verified'},
	{name: 'prescreened', label: 'Prescreen', route: '/admin/inquiries/prescreened', tip_name: 'Prescreen Handshakes', tip: 'Students who have been interviewed and vetted by the Plexuss team', noNew: true},
	{name: 'verifiedApp', label: 'Applications', route: '/admin/inquiries/verifiedApp', tip_name: 'Verified Applications', tip: 'Students who have reported they have applied to your school and are verified by college and or plexuss team'},
];

export const RECRUITMENT = [
	{name: 'inquiry', label: 'Inquiries', route: '/admin/inquiries'},
	{name: 'recommend', label: 'Recommended', route: '/admin/inquiries/recommendations', expiration: true},
	{name: 'pending', label: 'Pending', route: '/admin/inquiries/pending'},
	{name: 'approved', label: 'Handshakes', route: '/admin/inquiries/approved'},
];

export const COMMUNICATION = [
	{name: 'message', label: 'Messages', route: '/admin/messages'},
	{name: 'text', label: 'Text Messages', route: '/admin/textmsg'},
	{name: 'campaign', label: 'Campaigns', route: '/admin/groupmsg', noNew: true},
	{name: 'chat', label: 'Chat', route: '/admin/chat'},
];

export const STATS = [
	{name: 'pendingHandshake_stat', val_name: 'pending_hs_ratio', label: 'Pending/Handshake', postText: '%', containerClass: 'column large-3 medium-3 small-12 statsbox',},
	{name: 'avgAge_stat', val_name: 'avg_age_of_students', label: 'Average age of students', postText: '', containerClass: 'column large-4 medium-3 small-12 statsbox'},
	{name: 'male_stat', val_name: 'avg_num_of_male', label: '', img: 'male', postText: '%', containerClass: 'column large-1 medium-2 small-4 stats-picbox'},
	{name: 'female_stat', val_name: 'avg_num_of_female', label: '', img: 'female', postText: '%', containerClass: 'column large-1 medium-1 small-4 stats-picbox'},
	{name: 'pageviews_stat', val_name: 'college_page_views', label: 'Page views', postText: '', containerClass: 'column large-2 medium-2 small-4 statsbox'},	
];

export const PREMIUM_PLANS = [
    {name: 'cost_per_lead', label: 'Cost Per Click/Lead', description: 'Target students based on the programs you wish to promote and receive their information with ease through CRM integration.'},
    {name: 'cost_per_application', label: 'Cost Per Application', description: 'A first in the education industry, this plan only requires you to pay once a student has submitted a complete application to your institution.'},
    {name: 'annual_subscription', label: 'Annual Subscription', description: 'An annual flat fee that gives you complete access to the student recruitment features available on Plexuss. Also included in this plan are access to leads and fully-qualified college applications.'},
    {name: 'progression_fee', label: 'Progression Fee', description: 'A comprehensive student recruitment solution giving you access to Plexuss\' full library of recruitment tools.  Meet your institution\'s yearly enrollment goals with the help of our exclusive partner agents located in 50+ countries around the world.'},
];

export const PREMIUM_FEATURES = [
    {name: 'targeting', label: 'Targeting', description: 'Save time by focusing your attention on students that meet your institution\'s profile. Our exhaustive targeting filters include location, desired field of study, and unique criteria such as financial ability.', videoLink: '', pricing: 'Included'}, //https://player.vimeo.com/video/184889832
    {name: 'daily_recommendations', label: 'Daily Recommendations', description: 'Student recommendations delivered straight to your inbox. Selected using your targeting filters in conjunction with machine learning technology ensuring the best-fit students.', videoLink: '', pricing: '$1 to $10 per Student'},
    {name: 'advanced_search', label: 'Advanced Search', description: 'Gain access to our database of 1.3 million+ students. Specify your filters and start generating interest using the many recruitment tools available to you.', videoLink: '', pricing: '$500 per month'},
    {name: 'verification', label: 'Verification', description: 'Use Plexuss automated IVR or text messaging to verify if a student is still interested in your program.', videoLink: '', pricing: '$1 to $3 per Student'},
    {name: 'hot_transfer', label: 'Hot Transfer', description: 'Use Plexuss team or automated IVR to transfer a verified student to your representatives.', videoLink: '', pricing: '$50 to $100 per Student'},
    {name: 'call_center', label: 'Call Center Auto Dialer', description: 'Have multiple representatives call prospects. Leverage Plexuss auto-dialer and texting to communicate. Setup daily reporting and analytics to automize performance. Includes unlimited calling and texting.', videoLink: '', pricing: '$100 per User every month'},
    {name: 'texting', label: 'Texting', description: 'The most popular method of communication among college-bound students. Send SMS texts to students to keep them in the loop with the admission process.', videoLink: '', pricing: '1.5 cents per SMS'},
    {name: 'chat_bot', label: 'Chat Bot', description: 'We know how hard it can be to address inquiries in a timely fashion. A chatbot will be up and running to assist prospective students while you are offline.', videoLink: '', pricing: '1 cent per message'},
];

export const FREE_SERVICES = [
    {name: 'inquiry', label: 'Inquiries', route: '/admin/inquiries'},
    {name: 'message', label: 'Messages', route: '/admin/messages'},
];

export const verifiedTooltip = {
    cursor: 'pointer',
	color: '#eeeeee',
	border: 'none',
	backgroundColor: '#797979',
	height: '14px',
	width: '14px',
	fontSize: '10px',
	fontWeight: '700',
	margin: '-12px 0px 0px 3px'
};

export const veriInnerTooltip = {
	color: '#2AC56C',
	border: 'none',
	backgroundColor: '#ffffff',
	height: '14px',
	width: '14px',
	fontSize: '10px',
	fontWeight: '700',
	margin: '-2px 0px 0px 5px'
};

export const verifiedTip = {
	fontSize: '12px',
	fontWeight: '400',
	marginTop: '5px',
	width: '170px',
	marginLeft: '-100px'
};

export const verifiedInnerTip = {
	width: '290px',
	padding: '20px 20px 10px 20px',
	color: '#ffffff',
	marginLeft: '-248px',
	marginTop: '5px'
};


export const greenTooltip = {
	color: '#eeeeee',
	border: 'none',
	backgroundColor: '#2AC56C',
	height: '14px',
	width: '14px',
	fontSize: '10px',
	fontWeight: '700',
	margin: '-2px 0px 0px 5px'

}

export const downloadContainer = {
	backgroundColor: '#ffffff',
	borderRadius: '2px',
	boxShadow: '1px 1px 10px rgba(0,0,0,.2)',
	padding: '25px 25px 30px 25px'
};

export const BANNER_STYLES = {
	container: {
		backgroundColor: '#ffffff',
		padding: '30px',
		borderRadius: '5px',
		color: '#767676',
		textAlign: 'right',
	},
	title: {
		color: '#767676'
	},
	subtitle: {
		color: '#767676'
	},
	msgBox: {
		width: '99%',
		height: '120px',
		margin: '25px 0px'
	},
	submitBtn: {
		backgroundColor: '#2AC56C',
		borderRadius: '2px',
		fontWeight: '600'
	},
	close: {
		dispay: 'inline',
		cursor: 'pointer',
		fontSize: '42px',
		fontWeight: '600',
		marginTop: '-33px',
	},
	doneMsg: {
		fontSize: '25px',
		maxWidth: '350px',
		color: '#24b26b', 
		margin: '0 auto 20px',
		textAlign: 'center'
	}
};