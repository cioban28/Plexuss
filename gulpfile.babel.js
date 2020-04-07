// gulpfile.js
var gulp = require('gulp'),
	concat = require('gulp-concat'),
	jshint = require('gulp-jshint'),
	stylish = require('jshint-stylish'),
    uglify = require('gulp-uglify'),
	filesize = require('gulp-filesize'),
	debug = require('gulp-debug'),
	sass = require('gulp-sass'),
	cssnano = require('gulp-cssnano'),
	util = require('gulp-util'),
	rename = require('gulp-rename'),
	browsersync = require('browser-sync').create(),
	babel = require('gulp-babel'),
	react = require('gulp-react'),
	print = require('gulp-print'),
	replace = require('gulp-replace'),
	insert = require('gulp-insert'),
	inject = require('gulp-inject-string'),
    gulpBrowser = require("gulp-browser"),
    browserify = require('browserify'),
	files = [];


//copy files
// gulp.task('copy', function(){
// 	gulp.src([
// 		'node_modules/react/dist/*.js',
// 		'node_modules/react-dom/dist/react-dom.js',
// 		'node_modules/react-dom/dist/react-dom.min.js'
// 	])
// 	.pipe(gulp.dest('public/js/reactJS/jsx'));
// });


// -- sign_up optimizations - start

//concat react dev
gulp.task('concat-react-dev', function(){
	return gulp.src([
			// 'public/js/reactJS/jsx/react.js',
			// 'public/js/reactJS/jsx/react-dom.js',
			// 'public/js/underscoreJS/underscore_dev.js',
			'public/js/reactJS/jsx/GetStarted_Breadcrumb_Component.js',
			// 'public/js/reactJS/jsx/GetStarted_Step1_Component.js',
			// 'public/js/reactJS/jsx/GetStarted_Step2_Component.js',
			// 'public/js/reactJS/jsx/GetStarted_Step3_Component.js',
			// 'public/js/reactJS/jsx/GetStarted_Step4_Component.js',
			// 'public/js/reactJS/jsx/GetStarted_Step5_Component.js',
			// 'public/js/reactJS/jsx/GetStarted_Step6_Component.js',
		])
		.pipe(debug({title: 'here: '}))
		.pipe(concat('getStarted_all_dev.js'))
		.pipe(gulp.dest('public/js/reactJS/jsx'))
});
//concat react production
gulp.task('concat-react-prod', function(){
	return gulp.src([
			'public/js/underscoreJS/underscore_prod.js',
			'public/js/reactJS/jsx/react-with-addons.min.js',
			'public/js/reactJS/jsx/react-dom.min.js'
		])
		.pipe(concat('react_all.min.js'))
		.pipe(gulp.dest('public/js/reactJS/jsx'))
});

//compile jsx to js
gulp.task('jsx-to-js', function () {
  return gulp.src('public/js/reactJS/jsx/*.jsx')
        .pipe(react({harmony: false, es6module: true}))
        .pipe(gulp.dest('public/js/reactJS/jsx'))
		.pipe(browsersync.stream());
});

//minify all js get_started steps - run when prod ready
gulp.task('js-min-steps', ['jsx-to-js'],function(){
	return gulp.src('public/js/reactJS/jsx/GetStarted*.js')
			.pipe(rename(function(path){
				path.basename += '.min';
			}))
            .pipe(babel())
			.pipe(uglify().on('error', util.log))
            .pipe(gulpBrowser.browserify())
			.pipe(gulp.dest('public/js/prod_ready/getstarted'));
});

//minify all get_started steps css - run when prod ready
gulp.task('css-min-steps', ['sassify'], function(){
	return gulp.src('public/css/getStarted_main.css')
		.pipe(rename(function(path){
			path.basename += '.min';
		}))
		.pipe(cssnano())
		.pipe(gulp.dest('public/css/prod_ready'));
});

//gulp watch
gulp.task('watch', function(){
	gulp.watch('public/js/reactJS/jsx/*.jsx', ['jsx-to-js']);
});

//compile sass to css
gulp.task('sassify', function(){
	return gulp.src('public/css/sass/getStarted_main.scss')
		.pipe(sass().on('error', sass.logError))
		.pipe(gulp.dest('public/css'));
});

gulp.task('css-gs-all', ['css-min-steps'],function(){
	return gulp.src([
			'public/css/foundation.min.css',
			'public/css/prod_ready/getStarted_main.min.css'
		])
		.pipe(concat('foundation_getStarted.min.css'))
		.pipe(gulp.dest('public/css/prod_ready'))
		.pipe(filesize())
		.pipe(browsersync.stream());
});

gulp.task('start', function(){

	//setup bsync
	// browsersync.init({
	// 	logSnippet: true,
	// 	proxy: 'http://plexuss.dev/get_started',
	// 	browser: ['google chrome']
	// });

	//watch sass changes
	gulp.watch('public/css/sass/*.scss', ['css-gs-all']);
	//compile, minify, then concat
	gulp.watch('public/js/reactJS/jsx/*.jsx', ['run-all-steps']);
	gulp.watch('app/views/get_started/*.blade.php').on('change', browsersync.reload);
});
// -- sign_up optimizations - end 





// Default Task
// gulp.task('default', ['lint', 'sass', 'scripts', 'watch']);
gulp.task('default', ['start']);






// -- frontpage optimizations - start
//concat and minify css files into one
gulp.task('concat-css-frontpage', function(){
	return gulp.src([
			'public/css/normalize.min.css',
			'public/css/foundation.min.css',	
			'public/css/homepage.css',
			'public/css/base.css',
			'public/css/default.css',
			'public/css/handshake_ticker.css',
			'public/css/plex_lightbox.css',
			'public/css/prod_ready/fp_owl_theme_noiuslider.min.css',
            'public/css/intlTelInput.css',
		])
		.pipe(concat('frontpage_all.min.css'))
		.pipe(cssnano())
		.pipe(gulp.dest('public/css/prod_ready'))
		.pipe(browsersync.stream());
});

gulp.task('concat-foundation-js-fp', function(){
	return gulp.src([
			'public/js/prod_ready/foundation/foundation.min.js',
			'public/js/prod_ready/foundation/foundation.abide.min.js',
			'public/js/prod_ready/foundation/foundation.reveal.min.js',
			'public/js/prod_ready/foundation/foundation.topbar.min.js',
			'public/js/prod_ready/foundation/foundation.interchange.min.js',
			'public/js/prod_ready/foundation/foundation.equalizer.min.js',
		])
		.pipe(concat('fp_foundation_all.min.js'))
		.pipe(uglify())
		.pipe(gulp.dest('public/js/prod_ready/foundation'));
});

// gulp.task('concat-other-js-fp', function(){
// 	return gulp.src([
// 			'public/js/masonry/masonry-pkgd.js',
// 			'public/js/pages.js',
// 			'public/js/backToTop.js',
// 			'public/js/topnavsearch.min.js',
// 			'public/js/topNavigationScripts.js',
// 			'public/js/owl.carousel.min.js',
// 			'public/js/share.min.js',
// 			'public/js/prod_ready/frontpage/plex_lightbox.min.js',
// 			'public/js/notification.min.js'
// 		])
// 		.pipe(concat('fp_other_js_all.min.js'))
// 		.pipe(uglify())
// 		.pipe(gulp.dest('public/js/prod_ready/frontpage'));
// });

gulp.task('concat-fp-old-react-lib', function(){
	return gulp.src([
			'public/js/underscoreJS/underscore_prod.js',
			'public/js/reactJS/react_v0.13.3_prod.js',
			'public/js/reactJS/JSXTransformer_v0.13.3.min.js',
			'public/js/handshake_ticker.min.js'
			
		])
		.pipe(concat('fp_react_all.min.js'))
		.pipe(uglify())
		.pipe(gulp.dest('public/js/prod_ready/frontpage'));
});

gulp.task('fp-all-js', ['uglify-hm'], function(){
	return gulp.src([
			'public/js/prod_ready/frontpage/fp_react_all.min.js',
			'public/js/masonry/masonry-pkgd.js',
			'public/js/owl.carousel.min.js',
			'public/js/pages.js',
			'public/js/backToTop.js',
            'public/js/intlTelInput.js',
			'public/js/prod_ready/frontpage/topNavigationScripts.min.js',
			'public/js/prod_ready/frontpage/topnavsearch.min.js',
			'public/js/prod_ready/frontpage/share.min.js',
			'public/js/prod_ready/frontpage/plex_lightbox.min.js',
		    // 'public/js/notification.js',
			// 'public/js/prod_ready/frontpage/notification.min.js',  //included on all if signed in...
			'public/js/prod_ready/frontpage/frontpage_section_loader.min.js',
			'public/js/prod_ready/frontpage/homepage.min.js',
			
		    // 'public/js/prod_ready/frontpage/fp_other_js_all.min.js',
			// 'public/js/topNavigationScripts.js',
			// 'public/js/topnavsearch.js'

		])
		.pipe(concat('fp_absolutely_all.min.js'))
		.pipe(uglify())
		.pipe(gulp.dest('public/js/prod_ready/frontpage'))
		.pipe(browsersync.stream());
});

//uglify js files for frontpage
gulp.task('uglify-hm', ['other-js-fp'], function(){
	return gulp.src([
			// 'public/js/foundation/*.js'
			'public/js/homepage.js',
			'public/js/topNavigationScripts.js',
			'public/js/topnavsearch.js',
			'public/js/plex_lightbox.js',
			'public/js/share.js',
			'public/js/frontpage_section_loader.js'
		])
		.pipe(rename(function(path){
			path.basename += '.min';
		}))
		.pipe(uglify().on('error', util.log))
    		.pipe(gulp.dest('public/js/prod_ready/frontpage'));
});
// -- frontpage optimizations - end 

// uglify js not to be included in the absolutly all js min 
// for example notification.js is included on othe pages -- in footer
gulp.task('other-js-fp', function(){

	return gulp.src([
		'public/js/notification.js'
	])
	.pipe(rename(function(path){
		path.basename += '.min';
	}))
	.pipe(uglify().on('error', util.log))
	.pipe(gulp.dest('public/js'));
});



gulp.task('min-share', function(){
	return gulp.src([
			'public/js/handshake_ticker.js',
		])
        .pipe(react({harmony: false, es6module: true}))
		.pipe(uglify())
		.pipe(rename(function(path){
			path.basename += '.min';
		}))
		.pipe(gulp.dest('public/js'));
});

gulp.task('fp', function(){

	//setup bsync
	browsersync.init({
		logSnippet: true,
		proxy: 'http://plexuss.dev',
		browser: ['google chrome', 'firefox']
	});

	//watch sass changes
	gulp.watch('public/css/homepage.css', ['concat-css-frontpage']);
	gulp.watch('public/js/homepage.js', ['fp-all-js']);
	gulp.watch('public/js/notification.js', ['fp-all-js']);
	gulp.watch('public/js/frontpage_section_loader.js', ['fp-all-js']);
	gulp.watch('app/views/public/homepage/homepage.blade.php').on('change', browsersync.reload);
});

gulp.task('concat-libs', function(){
	return gulp.src([
			'public/css/prod_ready/fp_owl_theme_noiuslider.min.css',
			'public/css/prod_ready/frontpage_all.min.css'
		])
		.pipe(concat('frontpage_all.min.css'))
		.pipe(cssnano())
		.pipe(gulp.dest('public/css/prod_ready'));
});

// //jshint - validate js
// gulp.task('jshint', function(){
// 	return gulp.src([
// 			'public/js/frontpage_section_loader.js'
// 		])
// 		.pipe(jshint())
// 		.pipe(jshint.reporter(stylish))
// });




// -- portal gulp tasks - start
//compile jsx to js
//1
gulp.task('port-jsx', function () {
  return gulp.src([
			'public/js/reactJS/jsx/PortalSideNav_Component.jsx',
			'public/js/reactJS/jsx/Rep_Messaging_Component.jsx',
			'public/js/reactJS/jsx/Student_Panel_Component.jsx'
	  	])
        .pipe(react({harmony: false, es6module: true}))
        .pipe(gulp.dest('public/js/reactJS/jsx'))
});

//minify js
//2
gulp.task('min-portal-components-js', ['port-jsx'], function(){
	return gulp.src([
				'public/js/reactJS/jsx/PortalSideNav_Component.js',
				'public/js/reactJS/jsx/Rep_Messaging_Component.js',
				'public/js/reactJS/jsx/Student_Panel_Component.js'
			])
			.pipe(rename(function(path){
				path.basename += '.min';
			}))
			.pipe(uglify().on('error', util.log))
			.pipe(gulp.dest('public/js/prod_ready/portal'));
});

//min all other portal js other than the react stuff
//3
gulp.task('min-all-js-portal', ['min-portal-components-js'], function(){
	return gulp.src([
				'public/js/fullcalender/lib/moment.min.js',
		        'public/js/zurb_datatable/jquery.dataTables.js',
		        'public/js/zurb_datatable/dataTables.foundation.js',
		        'public/js/jquery.idle.js',
		        'public/js/enjoyhint/enjoyhint.min.js',
		        'public/js/jquery.timeago.js',
		        'public/js/portal.js',
		        'public/js/messages.js',
		        'public/js/commonChatMessage.js'
			])
			.pipe(concat('portal_all.min.js'))
			.pipe(uglify().on('error', util.log))
			.pipe(gulp.dest('public/js/prod_ready/portal'))
			.pipe(filesize());
});

//concating just react components together for portal
//4
gulp.task('concat-just-portal-comp', ['min-all-js-portal'], function(){
	return gulp.src([
		        'public/js/reactJS/jsx/underscore_react_reactdom.min.js',
				'public/js/prod_ready/portal/PortalSideNav_Component.min.js',
				'public/js/prod_ready/profile/whatsNextComponents.min.js'
			])
			.pipe(concat('portal_components.min.js'))
			.pipe(uglify().on('error', util.log))
			.pipe(gulp.dest('public/js/prod_ready/portal'))
			.pipe(filesize())
			.pipe(browsersync.stream());
});

//minify css
gulp.task('mini-css', ['sassi'], function(){
	return gulp.src([
			'public/css/enjoyhint/enjoyhint.css',
			'public/css/portal.css',
			'public/css/messagesChat.css',
			'public/css/userPortal_main.css'
		])
		// .pipe(rename(function(path){
		// 	path.basename += '.min';
		// }))
		.pipe(concat('portal_all.min.css'))
		.pipe(cssnano())
		.pipe(gulp.dest('public/css/prod_ready'));
});

//minify default.css
gulp.task('mini-default', function(){
	return gulp.src([
			'public/css/default.css',
		])
		.pipe(rename(function(path){
			path.basename += '.min';
		}))
		.pipe(cssnano())
		.pipe(gulp.dest('public/css'))
		.pipe(browsersync.stream());
});

gulp.task('topnav', function(){
	browsersync.init({
		logSnippet: true,
		proxy: 'http://plexuss.dev/portal',
		browser: ['google chrome', 'firefox']
	});
	
	gulp.watch('public/css/default.css', ['mini-default']);
});


//minify default.css
gulp.task('mini-all', ['mini-css', 'mini-default']);

//compile sass to css
gulp.task('sassi', function(){
	return gulp.src([
			'public/css/sass/userPortal_main.scss',
			'public/css/sass/collegeMessages.scss'
		])
		.pipe(sass().on('error', sass.logError))
		.pipe(gulp.dest('public/css'))
		.pipe(browsersync.stream());
});

//browsersync to push updates
gulp.task('portal', function(){

	//setup bsync
	browsersync.init({
		logSnippet: true,
		proxy: 'http://plexuss.dev/portal',
		browser: ['google chrome', 'firefox']
	});

	files = [
		'public/js/portal.js',
        'public/js/messages.js',
        'public/js/commonChatMessage.js',
        'public/js/reactJS/jsx/PortalSideNav_Component.jsx',
		'public/js/reactJS/jsx/Rep_Messaging_Component.jsx',
		'public/js/reactJS/jsx/Student_Panel_Component.jsx',
		'public/js/prod_ready/profile/whatsNextComponents.min.js'
	];

	for (var i = 0; i < files.length; i++) {
		gulp.watch(files[i], ['concat-just-portal-comp']);
	};

	//watch sass changes
	gulp.watch('public/css/sass/_portalSideNav.scss', ['sassi']);
	gulp.watch('public/css/sass/_repPanel.scss', ['sassi']);
	gulp.watch('public/css/sass/_studentPanel.scss', ['sassi']);
	// gulp.watch('public/js/reactJS/jsx/PortalSideNav_Component.jsx', ['min-portal-components-js']);
	// gulp.watch('public/js/reactJS/jsx/Rep_Messaging_Component.jsx', ['min-portal-components-js']);
	// gulp.watch('public/js/reactJS/jsx/Student_Panel_Component.jsx', ['min-portal-components-js']);
	gulp.watch('app/views/private/portal/*').on('change', browsersync.reload);
	gulp.watch('public/css/portal.css').on('change', browsersync.reload);
});
// -- portal gulp tasks - end



// -- signup / signin tasks
gulp.task('signin-css-min', function(){
	return gulp.src('public/css/regStyles.css')
		.pipe(rename(function(path){
			path.basename += '.min';
		}))
		.pipe(cssnano())
		.pipe(gulp.dest('public/css/prod_ready'));
});

gulp.task('signin-concat-all', function(){
	return gulp.src([
			'public/css/normalize.min.css',
			'public/css/foundation.min.css',
			'public/css/prod_ready/regStyles.min.css'
		])
		.pipe(concat('signupin_all.min.css'))
		.pipe(cssnano())
		.pipe(gulp.dest('public/css/prod_ready'));
});

gulp.task('signin-js-min', function(){
	return gulp.src([
				'public/js/vendor/modernizr.js'
			])
			.pipe(rename(function(path){
				path.basename += '.min';
			}))
			.pipe(uglify().on('error', util.log))
			.pipe(gulp.dest('public/js/vendor'));
});

gulp.task('signin-js-concat-all', function(){
	return gulp.src([
				'public/js/vendor/modernizr.min.js',
				'public/js/prod_ready/foundation/foundation.min.js',
				'public/js/prod_ready/foundation/foundation.abide.min.js'
			])
			.pipe(concat('signupin_all.min.js'))
			.pipe(uglify().on('error', util.log))
			.pipe(gulp.dest('public/js/prod_ready'));
});
// -- signup / signin tasks

//minify js
gulp.task('mini-this-js', function(){
	return gulp.src([
				'public/js/topnavsearch.js'
			])
			.pipe(rename(function(path){
				path.basename += '.min';
			}))
			.pipe(uglify().on('error', util.log))
			.pipe(gulp.dest('public/js'));
});


// -- profile tasks
gulp.task('profile', function(){
	//setup bsync
	browsersync.init({
		logSnippet: true,
		proxy: 'http://plexuss.dev/profile',
		browser: ['google chrome', 'firefox']
	});

	gulp.watch('app/views/private/profile/profile.blade.php').on('change', browsersync.reload);
});

gulp.task('owl-all', function(){
	return gulp.src([
			'public/js/underscoreJS/underscore_prod.js',
			'public/js/reactJS/jsx/react.min.js',
			'public/js/reactJS/jsx/react-dom.min.js',
		])
		.pipe(concat('underscore_react_reactdom.min.js'))
		.pipe(gulp.dest('public/js/reactJS/jsx'))
		.pipe(filesize());
});

gulp.task('concat-1', ['js-min-steps'], function(){
	return gulp.src([
			'public/js/prod_ready/getstarted/GetStarted_Breadcrumb_Component.min.js',
			'public/js/prod_ready/getstarted/GetStarted_Step1_Component.min.js'
		])
		.pipe(concat('BreadCrumb_Step1_Components.min.js'))
		.pipe(gulp.dest('public/js/prod_ready/getstarted'))
		.pipe(filesize());
});

gulp.task('concat-2', ['js-min-steps'], function(){
    return gulp.src([
			'public/js/prod_ready/getstarted/GetStarted_Breadcrumb_Component.min.js',
			'public/js/prod_ready/getstarted/GetStarted_Step2_Component.min.js'
		])
		.pipe(concat('BreadCrumb_Step2_Components.min.js'))
		.pipe(gulp.dest('public/js/prod_ready/getstarted'))
		.pipe(filesize());
});

gulp.task('concat-3-old', ['js-min-steps'], function(){
	return gulp.src([
			'public/js/prod_ready/getstarted/GetStarted_Breadcrumb_Component.min.js',
			'public/js/prod_ready/getstarted/GetStarted_Step3_Component_old.min.js'
		])
		.pipe(concat('BreadCrumb_Step3_Components_old.min.js'))
		.pipe(gulp.dest('public/js/prod_ready/getstarted'))
		.pipe(filesize());
});

gulp.task('concat-3-new', ['js-min-steps'], function(){
	return gulp.src([
			'public/js/prod_ready/getstarted/GetStarted_Breadcrumb_Component.min.js',
			'public/js/prod_ready/getstarted/GetStarted_Step3_Component_new.min.js'
		])
		.pipe(concat('BreadCrumb_Step3_Components_new.min.js'))
		.pipe(gulp.dest('public/js/prod_ready/getstarted'))
		.pipe(filesize());
});

gulp.task('concat-4-old', ['js-min-steps'], function(){
	return gulp.src([
			'public/js/prod_ready/getstarted/GetStarted_Breadcrumb_Component.min.js',
			'public/js/prod_ready/getstarted/GetStarted_Step4_Component_old.min.js'
		])
		.pipe(concat('BreadCrumb_Step4_Components_old.min.js'))
		.pipe(gulp.dest('public/js/prod_ready/getstarted'))
		.pipe(filesize());
});

gulp.task('concat-4-new', ['js-min-steps'], function(){
	return gulp.src([
			'public/js/prod_ready/getstarted/GetStarted_Breadcrumb_Component.min.js',
			'public/js/prod_ready/getstarted/GetStarted_Step4_Component_new.min.js'
		])
		.pipe(concat('BreadCrumb_Step4_Components_new.min.js'))
		.pipe(gulp.dest('public/js/prod_ready/getstarted'))
		.pipe(filesize());
});

gulp.task('concat-5', ['js-min-steps'], function(){
	return gulp.src([
			'public/js/prod_ready/getstarted/GetStarted_Breadcrumb_Component.min.js',
			'public/js/prod_ready/getstarted/GetStarted_Step5_Component.min.js'
		])
		.pipe(concat('BreadCrumb_Step5_Components.min.js'))
		.pipe(gulp.dest('public/js/prod_ready/getstarted'))
		.pipe(filesize());
});

gulp.task('concat-6', ['js-min-steps'], function(){
	return gulp.src([
			'public/js/prod_ready/getstarted/GetStarted_Breadcrumb_Component.min.js',
			'public/js/prod_ready/getstarted/GetStarted_Step6_Component.min.js'
		])
		.pipe(concat('BreadCrumb_Step6_Components.min.js'))
		.pipe(gulp.dest('public/js/prod_ready/getstarted'))
		.pipe(filesize());
});

gulp.task('concat-7-1', ['js-min-steps'], function(){
	return gulp.src([
			'public/js/prod_ready/getstarted/GetStarted_Breadcrumb_Component.min.js',
			'public/js/prod_ready/getstarted/GetStarted_Step7_1_Component.min.js'
		])
		.pipe(concat('BreadCrumb_Step7_1_Components.min.js'))
		.pipe(gulp.dest('public/js/prod_ready/getstarted'))
		.pipe(filesize());
});

gulp.task('concat-7', ['js-min-steps'], function(){
	return gulp.src([
			'public/js/prod_ready/getstarted/GetStarted_Breadcrumb_Component.min.js',
			'public/js/prod_ready/getstarted/GetStarted_Step7_Component.min.js'
		])
		.pipe(concat('BreadCrumb_Step7_Components.min.js'))
		.pipe(gulp.dest('public/js/prod_ready/getstarted'))
		.pipe(filesize());
});

gulp.task('concat-8', ['js-min-steps'], function(){
	return gulp.src([
			'public/js/prod_ready/getstarted/GetStarted_Breadcrumb_Component.min.js',
			'public/js/prod_ready/getstarted/GetStarted_Step8_Component.min.js'
		])
		.pipe(concat('BreadCrumb_Step8_Components.min.js'))
		.pipe(gulp.dest('public/js/prod_ready/getstarted'))
		.pipe(filesize());
});

gulp.task('run-all-steps', [
	'concat-1', 
	'concat-2', 
	'concat-3-old', 
	'concat-3-new', 
	'concat-4-old', 
	'concat-4-new', 
	'concat-5', 
	'concat-6', 
	'concat-7-1', 
	'concat-7', 
	'concat-8'
]);
// -- profile tasks

// for profile setup release do the following
// 1. minify all js files before concatenating the steps with the breadcrumb header
// 		- use jsx-to-js to compile jsx into js 
//		- use js-min-steps to concat all GetStarted* jsx files
//		- use concat-* to concat breadcrumb component to each step
// 2. include the combined file
// 3. minify all css before concatenating with other css


// -- what's next - start
gulp.task('whatsnext', function(){
	//setup bsync
	browsersync.init({
		logSnippet: true,
		proxy: 'http://plexuss.dev/home',
		browser: ['google chrome', 'firefox']
	});

	gulp.watch('public/js/reactJS/jsx/whats*.jsx', ['min-wn']);
	gulp.watch('public/css/default.css', ['mini-default']);
	gulp.watch('app/views/private/profile/ajax/whatsNext.blade.php').on('change', browsersync.reload);
});

gulp.task('wn-jsx', function(){
	return gulp.src([
			'public/js/reactJS/jsx/whats*.jsx'
		])
		.pipe(react({harmony: false, es6module: true}))
        .pipe(gulp.dest('public/js/reactJS/jsx'))
		.pipe(browsersync.stream());
});

gulp.task('min-wn', ['wn-jsx'], function(){
	return gulp.src([
			'public/js/reactJS/jsx/whats*.js'
		])
		.pipe(rename(function(path){
			path.basename += '.min';
		}))
		.pipe(uglify().on('error', util.log))
        .pipe(gulp.dest('public/js/prod_ready/profile'))
		.pipe(filesize());
});
// -- what's next - end


// -- college pages - start
gulp.task('college', function(){
	browsersync.init({
		logSnippet: true,
		proxy: 'http://plexuss.dev/college/castleton-university',
		browser: ['google chrome', 'firefox']
	});

	gulp.watch('public/js/college.js').on('change', browsersync.reload);
	// gulp.watch('public/css/default.css', ['mini-default']);
	gulp.watch('app/views/private/college/collegeSingleView.blade.php').on('change', browsersync.reload);
});
// -- college pages - end



// -- footer optimizations
//concats and minifies all scripts required by all pages, signed in or not
gulp.task('footer-required-all', function(){
	return gulp.src([
		'public/js/jquery.ui.touch-punch.min.js',
		'public/js/fastclick.js',
		'public/js/prod_ready/foundation/foundation.min.js',
		'public/js/prod_ready/foundation/foundation.topbar.min.js',
		'public/js/prod_ready/foundation/foundation.reveal.min.js',
		'public/js/prod_ready/foundation/foundation.abide.min.js',
		'public/js/prod_ready/foundation/foundation.interchange.min.js',
		'public/daterangepicker/moment.min.js',
		'public/daterangepicker/daterangepicker.js',
		'public/js/commonfunction.js', //only file that really might be edited, so there is a watch task attached to this file
		'public/js/topAlert.js',
		'public/js/backToTop.js'
	])
	.pipe(concat('required_by_all_pages.min.js'))
	.pipe(uglify().on('error', util.log))
	.pipe(gulp.dest('public/js/prod_ready/footer'))
	.pipe(filesize());
});

//watch task to re-concat/minify required_by_all_pages.min.js when commonfunction.js changes
gulp.watch('public/js/commonfunction.js', ['footer-required-all']);

//concats all files into one minified for required signedin files
gulp.task('concat-signedin-scripts', function(){
	files = [
		'public/js/jquery.knob.js',
		'public/js/topnavsearch.js',
		'public/js/topNavigationScripts.js',
		'public/js/notification.js',
		'public/js/selectivity-full.min.js'
	];

	return gulp.src(files)
		.pipe(concat('required_signedin_scripts.min.js'))
		.pipe(uglify().on('error', util.log))
		.pipe(gulp.dest('public/js/prod_ready/footer'))
		.pipe(filesize());
});

//use to create watch tasks for all files
gulp.task('watch-signedin-scripts', function(){
	files = [
		'public/js/jquery.knob.js',
		'public/js/topnavsearch.js',
		'public/js/topNavigationScripts.js',
		'public/js/notification.js',
		'public/js/selectivity-full.min.js'
	];

	for (var i = 0; i < files.length; i++) {
		gulp.watch(files[i], ['concat-signedin-scripts']);
	}
});
// -- footer optimizations




// -- scaffoling task to create a new react project - START
// ** creates a webpack.config, top level react component (entry point for webpack), set with redux and react-router

// * if creating new react project on existing page - just do steps 1 and 2
// ** if creating new react project on new page - do all steps

// 1. Create new react project
// 2. Create a new route in app/routes.php with same name as project_name
// 3. In cmd, navigate to new project directory and run the command 'webpack' - creates a bundle file and source map
// 4. Go to browser and to new route and Hello World should be displayed - Done!
// 5. Newly created app isn't wired up with the redux store, so ask Adam for help on this.

//Naming convention for project name: 
// 		- must be capitilized
// 		- numbers ok
// 		- if multiple words, use camelcase

// *** CHANGE PROJECT_NAME and CREATE_NEW_PAGE ONLY! ***
var project_name = 'TestNumberThree'; 
var create_new_page = true;

// DO NOT ALTER THE FOLLOWING
var task_name = 'new-react-bundle',
	jsDestination = 'public/js/bundles/' + project_name,
	viewDestination = 'app/views/' + project_name,
	jsSrcFiles = 'public/js/bundles/TEMPLATE_PROJECT/**/*.js',
	viewSrcFiles = 'app/views/TEMPLATE_VIEW/**/*.php',
	dependencies = null,
	bundle_name = project_name + '_bundle',
	id_name = project_name.toLowerCase() + '_app';

dependencies = create_new_page ? ['create-new-view'] : null;

gulp.task(task_name, dependencies, function(){
	return gulp.src(jsSrcFiles)
			   .pipe(print(function(filename){
			   		return 'filename: ' + filename;
			   }))
			   .pipe(rename(function(path){
			   		switch( path.basename ){
			   			case 'templateAction':
			   				path.basename = project_name.toLowerCase() + 'Action';
			   				break;
			   			case 'templateReducer':
			   				path.basename = project_name.toLowerCase() + 'Reducer';
			   				break;
			   			default:
			   				if( path.basename.indexOf('_') > -1 ){
			   					path.basename = project_name + '_' + path.basename.split('_')[1];
			   				}
			   				break;
			   		}
			   }))
			   .pipe(replace('REACT_COMPONENT_DOM_ELEM_ID_HERE', id_name))
			   .pipe(replace('TEMPLATE_PROJECT_NAME_HERE', project_name))
			   .pipe(replace('templateAction', project_name.toLowerCase()+'Action'))
			   .pipe(replace('template_dumbComponent', project_name+'_dumbComponent'))
			   .pipe(gulp.dest(jsDestination));
});

gulp.task('create-new-view', function(){
	return gulp.src(viewSrcFiles)
			   .pipe(print(function(filename){
			   		return 'filename: ' + filename;
			   }))
			   .pipe(replace('NEW_PROJECT_FOLDER_NAME', project_name))
			   .pipe(replace('REPLACE_ME_WITH_NEW_PROJECT_BUNDLE_FILE_NAME', bundle_name))
			   .pipe(replace('REACT_COMPONENT_DOM_ELEM_ID_HERE', id_name))
			   .pipe(gulp.dest(viewDestination));
});

gulp.task('create-new-route', function(){
	return gulp.src('app/routes.php')
			   .pipe(print(function(filename){
			   		return 'filename: ' + filename;
			   }))
			   .pipe(insert.append(buildRoute()))
			   .pipe(gulp.dest('app/routes.php'));
});

function buildRoute(){
	var route = "Route::get('/"+project_name.toLowerCase()+"', function(){";
		route += 	"View::make('"+project_name+"');"
		route += "});";

	return route;
}
// -- scaffoling task to create a new react project - END
