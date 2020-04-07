<?php

namespace App\Http\Controllers;

use Request, Validator, Session;
use App\Comment, App\CommentLike, App\CommentThread;

class CommentController extends Controller
{
    private $raw_comments; // Raw comments straight from DB
	private $parent_comment_ids; // holds an array of parent comment ids, used
								 // to get specified child comments
	private $cooked_comments; // Comment array that has been stripped of unnecessary
							 // data and has comment html
	private $reply_template; // HTML string. Reply template
	private $visibility_template;  // HTML string. More template for more/less button
	private $earlier_flag = false;	// default false. When true, adds a comment param
									// that will be picked up in the js so the comment
									// is injected at the bottom of the list
	private $comments_grouped = false;	// for prepAndMakeComments. Indicates that
										// the raw_comments array are grouped by
										// parent comment ids, and that we need to
										// go one level deeper
	private $reply_parent;	// the id of a user's reply parent. If set, we add a class

	public function setRawComments( $raw_comments ){
		$this->raw_comments = $raw_comments;
	}

	public function setEarlierFlag(){
		$this->earlier_flag = true;
	}

	public function setReplyParent( $parent ){
		$this->reply_parent = $parent;
	}

	public function getParentCommentIds(){
		return $this->parent_comment_ids;
	}

	public function getCookedComments(){
		return $this->cooked_comments;
	}

	public function getReplyTemplate(){
		return $this->reply_template;
	}

	public function getVisibilityTemplate(){
		return $this->visibility_template;
	}

	public function newComment(){

		// Get input
		$input = Request::all();

		// Build data array
		$viewDataController = new ViewDataController();
		$data = $viewDataController->buildData();

		// build alerts
		$error_alert = array(
			'img' => '/images/topAlert/urgent.png',
			'bkg' => '#de4728',
			'textColor' => '#fff',
			'type' => 'soft',
			'dur' => '7000',
		);

		// User must be signed in to comment
		if( !$data['signed_in'] ){
			$error_alert['msg'] = 'You must be signed in to comment!';
			return json_encode( array( 'top_alert' => $error_alert ) );
		}

		// Validation Rules
		$rules = array(
			// comment thread id
			'comment_thread' => array(
				'integer'
			),
			// id of the item: eg college id could be 1, news could also be 1
			'item_id' => array(
				'integer'
			),
			// comment content
			'comment_textarea' => array(
				'required',
				'regex:/^[\s\S]{1,}$/'
			),
			'latest_comment_id' => array(
				'required',
				'integer'
			),
			// parent id of the submitted reply
			'parent' => array(
				'integer'
			),
			// user id of the reply's parent comment
			'ref_user_id' => array(
				'integer'
			),
			// anon flag. Sets ALL posts by user anon
			'post_anon' => array(
				'boolean'
			)
		);

		// Validate against Rules
		$validator = Validator::make( $input, $rules );
		if( $validator->fails() ){
			return $validator->messages();
		}

		// format comment string
		$formatter = new TextFormatter;
		$formatter->set_text( $input['comment_textarea'] );
		$formatter->strip_html();
		$formatter->auto_link();
		$input['comment_textarea'] = $formatter->get_text();

		/* THIS SHOULD BE SAFE TO DELETE... THIS CODE CREATED A NEW COMMENT
		 * THREAD ON THE FLY, BUT AS WE NOW CREATE A THREAD ON PAGE LOAD, WE
		 * SHOULDN'T NEED THIS. I will keep this here for now. -AW
		// Make comment thread and assign thread_id to appropriate table
		if( $input['comment_thread'] == '' ){

			// Create table string to query correct table
			$current_page = $input['current_page'];
			switch( $current_page ){
				case 'news':
					$table = 'news_articles';
					break;
				case 'college':
					break;
			}

			// Get current item by item_id
			$record = DB::table( $table )
				->where( 'id', $input['item_id'] )
				->first();

			// exit if no matching item
			if( !$record ){
				return "There was an error creating your comment. =/ Record not found!";
			}

			// Check if record is set, if not, create comment thread, otherwise, use article id
			if( is_null( $record->comment_thread_id ) ){
				// Create new comment Thread
				$comment_thread = new CommentThread;
				$comment_thread->save();

				// Set comment_thread_id column in appropriate table to match new comment thread
				DB::table( $table )
					->where( 'id', $input['item_id'] )
					->update( array( 'comment_thread_id' => $comment_thread->id ) );
			}

			$thread_id = $record->comment_thread_id;
		}
		else{
			$thread_id = $input['comment_thread'];
		}
		 */
		/* We always have thread id because it is created on page load if
		 * it is not there */
		$thread_id = $input['comment_thread'];

		// update anonymous flag
		$user = User::find( $data['user_id'] );
		$user->comment_anon = isset( $input['post_anon'] ) ? $input['post_anon'] : 0;
		$user->save();

		// Set all universal comment parameters
		$comment = new Comment;
			$comment->user_id_submitted = $data['user_id'];
			$comment->content = $input['comment_textarea'];
			$comment->comment_thread_id = $thread_id;

			// Set variable comment params
			$comment->parent_id = isset( $input['parent'] ) && $input['parent'] != '' ? $input['parent'] : null;
			$comment->reference_user_id = isset( $input['ref_user_id'] ) && $input['ref_user_id'] != '' ? $input['ref_user_id'] : null;
			$comment->save();

		/**************************************** NOTIFICATION CODE********************************************************/

		///replied to your comment
		/*
		if(isset($input['parent']) && $input['parent'] != '' && $data['user_id'] != $input['ref_user_id'] ){
			$ntn = new NotificationController();
			$ntn->create( $user->fname.' '. $user->lname, 'user', 3, '', $input['ref_user_id']);
		}
		*/
		/**************************************** END OF NOTIFICATION CODE**************************************************/


		// Get top level comments
		$comment->setTake( 10 );
		$comment->setThreadId( $thread_id );
		$comment->setLatestId( $input['latest_comment_id'] );
		$comment->findTopLevel();
		$raw_comments = $comment->getArray();

		$prepped = new CommentController;
			// prep top level comments
			$prepped->setRawComments( $raw_comments );
			$prepped->setReplyParent( $comment->parent_id );
			$prepped->prepAndMakeComments();
			$new_comments = $prepped->getCookedComments();
			$parent_comment_ids = $prepped->getParentCommentIds();
			// sets parent comment to fetch new child reply
			if( $input['parent'] != '' ){
				$parent_comment_ids[] = $input['parent'];
			}

		// get child comments
		$comment->setParentIds( $parent_comment_ids );
		$comment->findChildren();
		$raw_child_comments = $comment->getArray();

			// Prep new child comments
			$prepped->setRawComments( $raw_child_comments );
			$prepped->groupByParent();
			$prepped->prepAndMakeComments();
			$new_child_comments = $prepped->getCookedComments();

			/* Loop through child comments and place identifier for the comment
			 * the user just added. BUT only if the comment was a reply.
			 */

		// Set latest_comment_id and earliest. So we don't return duplicate comments
		$latest_comment_id = max( array_merge( $parent_comment_ids, $prepped->getParentCommentIds() ) );
		$earliest_comment_id = min( $parent_comment_ids );

		// Package return array.
		$return_data = array();
		$return_data['new_comments'] = $new_comments;
		$return_data['new_child_comments'] = $new_child_comments;
		$return_data['latest_comment_id'] = $latest_comment_id;
		$return_data['earliest_comment_id'] = $earliest_comment_id;
		$return_data['anon'] = $user->comment_anon;

		return json_encode( $return_data );
	}
	/***********************************************************************/

	/***********************************************************************
	 *======================= LIKE A COMMENT AJAX ==========================
	 ***********************************************************************
	 * Receives AJAX POST to like a comment. Tries to delete a like row, based
	 * on user id and comment id. If not successsful, we insert a row to record
	 * the like. Then we fetch the comment's num_likes, increment/decrement
	 * and then update it. Then we send the num_likes back to the user,
	 * along with a 'updated' string of 'liked' or 'unliked'
	 * @param		input		array		has the following:
	 * @param		comment_id	int			the id of the comment to be liked
	 * @return		data		json		has: $count, and $updated
	 */
	public function likeComment(){
		// get input
		$input = Request::all();

		// build alerts
		$error_alert = array(
			'img' => '/images/topAlert/urgent.png',
			'bkg' => '#de4728',
			'textColor' => '#fff',
			'type' => 'soft',
			'dur' => '7000',
		);

		// make validation rules
		$rules = array(
			'comment_id' => array(
				'required',
				'integer'
			)
		);

		// validate
		$v = Validator::make( $input, $rules );
		if( $v->fails() ){
			return $v->messages();
		}

		// check user
		$session = Session::all();
		if( !$session['userinfo']['signed_in'] ){
			$error_alert['msg'] = 'You need to be signed in to like a comment.';
			$error = array( 'topAlert' => $error_alert );
			return json_encode( $error_alert );
		}

		// get user and comment id
		$user_id = $session['userinfo']['id'];
		$comment_id = $input['comment_id'];

		// check for like
		$like = new CommentLike;

		// try delete (or unlike)
		$deleted = CommentLike::where( 'comment_id', $comment_id )
			->where( 'user_id', $user_id )
			->delete();
		// if not deleted, insert row to record like
		if( !$deleted ){
			// insert new like row
			CommentLike::insert( array(
				'user_id' => $user_id,
				'comment_id' => $comment_id
			) );
			$updated = 'liked';
		}
		else{
			$updated = 'unliked';
		}

		// get comment row
		$comment = Comment::where( 'id', $comment_id )->first();
		$num_likes = $comment->num_likes;
		// increment or decrement num_likes
		$num_likes = $deleted ? $num_likes - 1 : $num_likes + 1;
		// send value back to db
		$comment->num_likes = $num_likes;
		$comment->save();

		// make return array
		$data = array();
		$data['count'] = $num_likes;
		$data['updated'] = $updated;

		return json_encode( $data );
	}
	/***********************************************************************/

	/***********************************************************************
	 *============== GET EARLIER COMMENTS FOR 'MORE' BUTTON ================
	 ***********************************************************************
	 * Gets earlier, instead of later, comments for the main, bottom-of-the-
	 * page more button.
	 * @param		input		array		parameter containing the earliest
	 * 										comment id, and the comment thread
	 * 										id we're getting comments from
	 */
	public function getEarlier(){
		// Get input
		$input = Request::all();

		// Set validation rules
		$rules = array(
			'earliest_comment_id' => array(
				'required',
				'integer'
			),
			'thread_id' => array(
				'integer'
			)
		);

		// Validate against rules
		$validator = Validator::make( $input, $rules );
		if( $validator->fails() ){
			return $validator->messages();
		}

		// Set variables for db query
		$comment = new Comment;
			// get earlier comments
			$comment->setTake( 10 );
			$comment->setResultsEarlier();
			$comment->setThreadId( $input['thread_id'] );
			$comment->setEarliestId( $input['earliest_comment_id'] );
			$comment->findTopLevel();
			$raw_comments = $comment->getArray();
				// if no later comments, return end signifier
			if( empty( $raw_comments ) ){
				return json_encode( array( 'earliest_comment_id' => 0 ) );
			}
			// Prepare new comments
			$prepper = new CommentController;
				// prep top level comments
				$prepper->setRawComments( $raw_comments );
				$prepper->setEarlierFlag();
				$prepper->prepAndMakeComments();
				$new_comments = $prepper->getCookedComments();
				$parent_comment_ids = $prepper->getParentCommentIds();

			// get child comments
			$comment->setParentIds( $parent_comment_ids );
			$comment->findChildren();
			$raw_child_comments = $comment->getArray();

			// Prep new child comments
			$prepper->setRawComments( $raw_child_comments );
			$prepper->groupByParent();
			$prepper->prepAndMakeComments();
			$new_child_comments = $prepper->getCookedComments();

		/* set earliest comment id so we don't return duplicate earlier comments
		 * we don't need to merge to find 
		 */
		$earliest_comment_id = min( $parent_comment_ids );

		// Create reply template
		$prepper->makeReplyTemplate();
		$prepper->makeVisibilityTemplate();

		// Package return array
		$return_data = array();
		$return_data['new_comments'] = $new_comments;
		$return_data['new_child_comments'] = $new_child_comments;
		$return_data['earliest_comment_id'] = $earliest_comment_id;
		$return_data['append'] = 1;

		// encode as JSON and return
		return json_encode( $return_data );
	}
	/***********************************************************************/

	/***********************************************************************
	 *========================= HEARTBEAT COMMENTS =========================
	 ***********************************************************************
	 * Gets latest comments for a given article id via heartbeat, OR, used to get
	 * the latest comments for an article on page load
	 */
	public function getLatest( $input = null ){
		// this function is multipurpose. It uses the parameter for page loads, and
		// the input for the heartbeat
		if( is_null( $input ) ){
			// heartbeat url params
			$input = Request::all();
		}
		else if( !is_array( $input ) ){
			return "Expected parameters not present.";
		}

		// set validation rules
		$rules = array(
			'thread_id' => array(
				'required',
				'integer'
			),
			'latest_comment_id' => array(
				'integer'
			),
			'is_heartbeat' => array(
				'boolean'
			)
		);

		// validate with rules
		$v= Validator::make( $input, $rules );
		if( $v-> fails() ){
			return $v->messages();
		}

		$comment = new Comment;
			// Get top level comments
			$comment->setTake( 10 );
			$comment->setThreadId( $input['thread_id'] );
			$comment->setLatestId( $input['latest_comment_id'] );
			$comment->findTopLevel();
			$raw_comments = $comment->getArray();
				// If no new comments, return input, which has latest ID
			/*
				if( empty( $raw_comments ) ){
					return $input;
				}
			 */

			// Prepare new comments
			$prepped = new CommentController;
				// prep top level comments
				$prepped->setRawComments( $raw_comments );
				$prepped->prepAndMakeComments();
				$new_comments = $prepped->getCookedComments();
				$parent_comment_ids = $prepped->getParentCommentIds();

			// get child comments
			$comment->setParentIds( $parent_comment_ids );
			// prevents 'whereIn' clause so heartbeat can pick up new replies
			if( isset( $input['is_heartbeat'] ) ){
				$comment->limitChildren( false );
			}
			$comment->findChildren();
			$raw_child_comments = $comment->getArray();

				// Prep new child comments
				$prepped->setRawComments( $raw_child_comments );
				$prepped->groupByParent();
				$prepped->prepAndMakeComments();
				$new_child_comments = $prepped->getCookedComments();

			// make array of returned comment ids
			$comment_ids = array_merge( $parent_comment_ids, $prepped->getParentCommentIds() );

			// Set latest_comment_id. So we don't return duplicate comments
			if( !empty( $comment_ids ) ){
				$latest_comment_id = max( $comment_ids );
			}
			// set earliest only when received
			if( !empty( $parent_comment_ids ) ){
				$earliest_comment_id = min( $parent_comment_ids );
			}

			// Create reply template
			$prepped->makeReplyTemplate();
			$prepped->makeVisibilityTemplate();

			// Package return array
			$return_data = array();
			$return_data['new_comments'] = $new_comments;
			$return_data['new_child_comments'] = $new_child_comments;
			$return_data['reply_template'] = $prepped->getReplyTemplate();
			$return_data['visibility_template'] = $prepped->getVisibilityTemplate();

			// not always returned
			if( isset( $latest_comment_id ) ){
				$return_data['latest_comment_id'] = $latest_comment_id;
			}
			if( isset( $earliest_comment_id ) ){
				$return_data['earliest_comment_id'] = $earliest_comment_id;
			}

			/*
			echo "<pre>";
			dd( $return_data );
			 */
			return json_encode( $return_data );
	}
	/***********************************************************************/

	/***********************************************************************
	 *======================== MAKE MORE TEMPLATE ==========================
	 ***********************************************************************
	 * Makes the 'more'/'expand' button to hide/reveal comments
	 */
	public function makeVisibilityTemplate(){
		$html = '';
		$html .= "<div class='row visibility-button-row'>";
		$html .= 	"<div class='column comment-visibility comment-visibility-temp text-center'>";
		$html .= 		"<span class='comment-more'>+ show more</span>";
		$html .= 		"<span class='comment-less'>- show less</span>";
		$html .= 		"<span class='comment-end'>no older comments</span>";
		$html .= 		"<span class='comment-loading'>fetching comments...</span>";
		$html .= 	"</div>";
		$html .= "</div>";
		$this->visibility_template = $html;
	}
	/***********************************************************************/

	/***********************************************************************
	 *======================= MAKE REPLY TEMPLATE ==========================
	 ***********************************************************************
	 * Makes a reply template without width or offset css classes. Adds the
	 * user's profile image
	 */
	public function makeReplyTemplate(){
		$session = Session::all();

		// Set handle
		if( $session['userinfo']['signed_in'] ){
			$user_table = $session['user_table'];
			$handle = $user_table->fname . ' ' . $user_table->lname;

			// Get user type
			$user_type = '';
			if( $user_table->is_student || $user_table->is_intl_student ){
				$user_type = 'Student';
			}
			if( $user_table->is_student ){
				$user_type = 'Student';
			}
			if( $user_table->is_alumni ){
				$user_type = 'Alumni';
			}
			if( $user_table->is_parent ){
				$user_type = 'Parent';
			}
			if( $user_table->is_counselor ){
				$user_type = 'Counselor';
			}

			// get profile image location
			if( !is_null( $user_table->profile_img_loc ) ){
				$profile_image = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/' . $user_table->profile_img_loc;
			}
			else{
				$profile_image = '/images/profile/default.png';
			}
		}
		else{
			$handle = 'Guest';
			$user_type = '';
			$profile_image = '/images/profile/default.png';
		}

		$html = '';
		$html .= "<div class='row collapse'>";
		$html .= 	"<div class='small-12 column reply-dialog reply-dialog-temp'>";
		$html .= 		"<form class='reply-form' data-abide='ajax' action='/social/comment/new/' method='POST'>";
		$html .= 			"<div class='row'>";
		$html .= 				"<div class='small-11 column small-offset-1 comment-item reply-item reply-preview reply-preview-temp'>";
										// user info row
		$html .= 						"<div class='row'>";
		$html .= 							"<div class='small-12 column'>";
		$html .= 								"<div class='comment-user-image' style='background-image: url(" . $profile_image . ")'>";
		$html .= 								"</div>";
		$html .= 								"<div class='comment-user-info'>";
		$html .= 									"<div>";
		$html .= 										"<span class='comment-user-name'>";
		$html .= 											htmlentities( $handle );
		$html .= 										"</span>";
		$html .= 									"</div>";
		$html .= 									"<div>";
		$html .= 										"<span class='comment-user-type'>";
		$html .= 											$user_type;
		$html .= 										"</span>";
		$html .= 									"</div>";
		$html .= 								"</div>";
		$html .= 							"</div>";
		$html .= 						"</div>";
										// end user info row
										// @user, reference row
		$html .= 						"<div class='row'>";
		$html .= 							"<div class='small-12 column comment-reply-ref comment-reply-ref-temp'>";
		$html .= 								"<span></span>";
		$html .= 							"</div>";
		$html .= 						"</div>";
										// end @user, reference row
										// content row
		$html .= 						"<div class='row'>";
		$html .= 							"<div class='small-12 column'>";
		$html .= 								"<textarea name='reply_textarea' class='reply_textarea' placeholder='comment...' rows='2' required pattern='onechar'></textarea>";
		$html .= 								"<small class='error'>Please enter a comment of at least one character</small>";
		$html .= 							"</div>";
		$html .= 						"</div>";
										// end content row
		$html .= 				"</div>";
		$html .= 			"</div>";
							// button row
		$html .= 			"<div class='row collapse'>";
		$html .= 				"<div class='small-12 column text-right reply-buttons'>";
		$html .= 					"<input id='reply_anon' type='checkbox' value='1' name='reply_anon' autocomplete='off'>";
		$html .= 					"<label class='comment-label reply-label' for='reply_anon'>Don't show my name</label>";
		$html .= 					"<div class='button btn-cancel reply-cancel reply-cancel-temp'>Cancel</div>";
		$html .= 					"<input class='reply-post-button button btn-save' type='submit' value='Post'>";
		$html .= 				"</div>";
		$html .= 			"</div>";
							// end button row
		$html .= 		"</form>";
		$html .= 	"</div>";
		$html .= "</div>";

		$this->reply_template = $html;
	}
	/***********************************************************************/

	/***********************************************************************
	 *========================== GROUP BY PARENT ===========================
	 ***********************************************************************
	 * Groups comments by parent_id and orders correctly for frontend JS to
	 * inject.
	 */
	public function groupByParent(){
		$raw_comments = $this->raw_comments;
		$grouped_comments = array();
		// loop through coments and create grouping arrays
		foreach( $raw_comments as $key => $comment ){
			// make parent_id grouping if not exist
			if( !isset( $grouped_comments[$comment['parent_id']] ) ){
				$grouped_comments[$comment['parent_id']] = array();
			}

			// put in correct group
			$grouped_comments[$comment['parent_id']][$comment['id']] = $comment;
		}

		// sort groups by newest first (not really needed but wth)
		//krsort( $grouped_comments );
		// loop through comment groups and sort children by oldest first
		foreach( $grouped_comments as $g_key => $group ){
			// sort grouped comments older first
			ksort( $grouped_comments[$g_key] );
		}

		$this->comments_grouped = true;
		$this->raw_comments = $grouped_comments;
	}
	/***********************************************************************/

	/***********************************************************************
	 *======================= PREPARE AND MAKE COMMENTS ====================
	 ***********************************************************************
	 * Loops through an array of raw, straight-from-the-database comments and
	 * prepares comment parameters. Then builds comment html for each comment.
	 * Also keeps track of each comment id and puts it in an array for later
	 * retrieval if it is later needed for the Comment object's setParentIds()
	 * method ( used to find child comments of the given array of ids ).
	 */
	public function prepAndMakeComments(){
		$raw_comments = $this->raw_comments;
		$parent_comment_ids = array();

		if( $this->comments_grouped ){
			// instantiate self, call this method for each comment group
			foreach( $raw_comments as $g_key => $group ){
				$prepper = new CommentController;
				$prepper->setRawComments( $group );
				if( $this->reply_parent == $g_key ){
					$prepper->setReplyParent( $g_key );
				}
				$prepper->prepAndMakeComments();
				$raw_comments[$g_key] = $prepper->getCookedComments();
				// merge new parent_comment_ids with current set
				$parent_comment_ids = array_merge( $parent_comment_ids, $prepper->getParentCommentIds() );
			}
		}
		else{
			// Loop through raw comments, prepare params, make html
			foreach( $raw_comments as $key => $new_comment ){

				// Get array of parent comment ids. This is needed in order to get child comments below
				$parent_comment_ids[] = $new_comment['id'];

				// Prepares comment params from db results
				$raw_comments[$key] = $this->prepCommentParams( $new_comment );

				// Generate HTML
				$raw_comments[$key]['comment_html'] = $this->makeComment( $raw_comments[$key] );
			}
		}

		$this->parent_comment_ids = $parent_comment_ids;
		$this->cooked_comments = $raw_comments;
	}

	/***********************************************************************
	 *============================ MAKE COMMENT ============================
	 ***********************************************************************
	 * Generates MOST of the html for a comment based on comment parameters.
	 * The remaining items that need to be added to the comment html are the
	 * classes for size and offset: small-[n] and small-offset-[n2]
	 * @param		array		comment		the comment array item which contains
	 * 										the comment parameters
	 */
	private function makeComment( $comment ){
		$id = $comment['id'];

		// Build comment html
		$html = '';
		$html .= "<div class='row' id='comment-row-" . $comment['id'] . "'>";
		$html .= 	"<div class='column comment-item hidden-comment";
		// ADD CLASSSES
		$html .= 	$comment['classes'];
		// END ADD CLASSES
		$html .= 		"' ";
		$html .= 		"data-comment_id='" . $comment['id'] . "' ";
		$html .= 		"id='comment-" . $comment['id'] . "'";
		$html .= 		">";
						// User info row
		$html .= 		"<div class='row'>";
		$html .= 			"<div class='small-12 column'>";
		$html .= 				"<div class='comment-user-image' style='background-image: url(" . $comment['profile_image'] . ")'>";
		$html .= 				"</div>";
		$html .= 				"<div class='comment-user-info'>";
		$html .= 					"<div>";
		$html .= 						"<span class='comment-user-name'>";
		$html .= 							htmlentities( $comment['handle'] );
		$html .= 							"<span class='comment-info-inline'>";
		$html .= 								htmlentities( $comment['time_ago'] );
		$html .= 							"</span>";
		$html .= 						"</span>";
		$html .= 					"</div>";
		$html .= 					"<div>";
		$html .= 						"<span class='comment-user-type'>";
		$html .= 							$comment['user_type'];
		$html .= 						"</span>";
		$html .= 					"</div>";
		$html .= 				"</div>";
		$html .= 			"</div>";
		$html .= 		"</div>";
						// End user Info Row
						// @user, reference row
		$html .= 		"<div class='row'>";
		$html .= 			"<div class='small-12 column comment-reply-ref'>";
		$html .= 				"<span>";
		$html .=					isset( $comment['ref_handle'] ) ? '@' . $comment['ref_handle'] : '';
		$html .= 				"</span>";
		$html .= 			"</div>";
		$html .= 		"</div>";
						// end @user, reference row
						// Comment content row
		$html .= 		"<div class='row'>";
		$html .= 			"<div class='small-12 column comment-content-column'>";
		$html .= 				"<span class='comment-content'>";
		$html .= 					htmlentities( $comment['content'] );
		$html .= 				"</span>";
		$html .= 			"</div>";
		$html .= 		"</div>";
						// end comment content row
						// Comment Interaction row
		$html .= 		"<div class='row'>";
		$html .= 			"<div class='small-12 column text-right comment-interaction'>";
		$html .= 				"<span class='comment-like-count'>" . $comment['num_likes'] . "</span>";
		$html .= 				"<span class='comment-like-button' data-comment_id='" . $comment['id'] . "'>Like!</span>";
		$html .= 				"<span class='reply-button' id='reply-button-" . $comment['id'] .
								"' data-comment_id='" . $comment['id'] .
								"' data-reply_to_user_handle='" . $comment['reply_to_user_handle'] .
								"' data-reply_to_user_id='" . $comment['reply_to_user_id'] .
								"' data-reply_to_id='" . $comment['reply_to_id'] . "'>";
		$html .= 					"Reply";
		$html .= 						"<img src='/images/comments/reply_arrow.png' alt='reply'/>";
		$html .= 				"</span>";
		$html .= 			"</div>";
		$html .= 		"</div>";
						// End Comment Interaction row
		$html .= 	"</div>";
		$html .= "</div>";

		// if comment is a parent, add reply container
		if( is_null( $comment['parent_id'] ) ){
			$html .= "<div class='row collapse'>";
			$html .= 	"<div id='reply-wrapper-" . $comment['id'] . "' class='small-12 column'>";
			$html .= 	"</div>";
			$html .= "</div>";
		}
		
		return $html;
	}
	/***********************************************************************/

	private function prepCommentParams( $comment ){
		// Create handle/username
		if( $comment['anon'] ){
			$comment['handle'] = 'Anonymous';
		}
		else if( is_null( $comment['fname'] ) && is_null( $comment['lname'] ) ){
			$comment['handle'] = 'deleted';
		}
		else{
			$comment['handle'] = $comment['fname'] . ' ' . $comment['lname'];
		}

		// set user image
		if( $comment['anon'] || $comment['handle'] == 'deleted' || !$comment['profile_image'] ){
			$comment['profile_image'] =  '/images/profile/default.png';
		}
		else{
			$comment['profile_image'] = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/' . $comment['profile_image'];
		}

		// Set user type string
		if( $comment['is_student'] ){
			$comment['user_type'] = 'Student';
		}
		if( $comment['is_intl_student'] ){
			$comment['user_type'] = 'Student';
		}
		if( $comment['is_alumni'] ){
			$comment['user_type'] = 'Alumni';
		}
		if( $comment['is_parent'] ){
			$comment['user_type'] = 'Parent';
		}
		if( $comment['is_counselor'] ){
			$comment['user_type'] = 'Counselor';
		}
		// Catch all
		if( !isset( $comment['user_type'] ) ){
			$comment['user_type'] = '';
		}

		// Check if ref data is set and prep
		if( isset( $comment['ref_anon'] ) && $comment['ref_anon'] ){
			// set anon
			$comment['ref_handle'] = 'Anonymous';
		}
		else if( isset( $comment['ref_fname'] ) ){
			// set fname
			$comment['ref_handle'] = $comment['ref_fname'];
			//set lname if set
			if( isset( $comment['ref_lname'] ) ){
				$comment['ref_handle'] .= ' ' . $comment['ref_lname'];
			}
		}

		// set data for @Some User reference_user_id variables
		// if a child comment
		if( !is_null( $comment['parent_id'] ) ){
			$comment['reply_to_id'] = $comment['parent_id'];
			$comment['reply_to_user_id'] = $comment['user_id_submitted'];
			$comment['reply_to_user_handle'] = $comment['handle'];
		}
		else{
			$comment['reply_to_id'] = $comment['id'];
			$comment['reply_to_user_id'] = '';
			$comment['reply_to_user_handle'] = '';
		}

		// Set and format posted on/ updated on dates
		// create new time object to get 'time ago' text
		// $time = new Time();

		// // pass in timestamp to time object
		// $time->setTimestamp( $comment['created_at'] );

		// // make time info string
		// $time->makeTimeAgo();
		// $time_ago = $time->getTimeAgo();
		$comment['time_ago'] = $this->xTimeAgo($comment['created_at'] , date("Y-m-d H:i:s"));

		// set earlier comments flag for JS
		if( is_null( $comment['parent_id'] ) && $this->earlier_flag ){
			$comment['append_earlier'] = true;
		}

		// set likes to '' if 0
		$comment['num_likes'] = $comment['num_likes'] ? '+' . $comment['num_likes'] : '';

		// set classes
		$classes = '';
		if( $comment['parent_id'] ){
			$classes .= ' child-of-' . $comment['parent_id'];
			$classes .= ' small-11 small-offset-1';
			$classes .= ' comment-new-reply';
			// if the user replied to this comment's parent, we add a class to show it
			if( $this->reply_parent == $comment['parent_id'] ){
				$classes .= ' comment-show-reply-group-' . $comment['parent_id'];
			}
		}
		else{
			$classes .= ' small-12';
		}
		$comment['classes'] = $classes;


		// Strip first/last name
		unset( $comment['fname'] );
		unset( $comment['lname'] );

		// Strip User Info
		unset( $comment['user_id_submitted'] );
		unset( $comment['anon'] );

		// Strip user types
		unset( $comment['is_student'] );
		unset( $comment['is_intl_student'] );
		unset( $comment['is_alumni'] );
		unset( $comment['is_parent'] );
		unset( $comment['is_counselor'] );
		unset( $comment['is_organization'] );

		// Strip created/updated
		unset( $comment['created_at'] );
		unset( $comment['updated_at'] );

		// strip ref data
		unset( $comment['ref_anon'] );
		unset( $comment['ref_fname'] );
		unset( $comment['ref_lname'] );

		return $comment;
	}
}
