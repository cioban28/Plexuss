<?php
namespace App\Http\Controllers;
use Request, Validator, Session, XMLWriter;
use App\User;
use Illuminate\Support\Facades\Redirect;
class ShareController extends Controller
{
   /***********************************************************************
	 *====================== STORE LINKEDIN ARTICLE ========================
	 ***********************************************************************
	 * For linkedin Shares. Flashes linkedin article params to session. This is
	 * needed because we need to redirect to linkedin at least once for each user.
	 * There is no way (that I know of) to get the share article/college params
	 * back after the redirect to linkedin unless we store it somewhere.
	 */
	public function storeLinkedinArticle(){
		$input = Request::all();
		// unbox params from input
		if( isset( $input['params'] ) ){
			$json_params = $input['params'];
			$article_params = json_decode( $json_params, true );
			// validate the decoded json
			$filter = array(
				'platform' => array(
					'required',
					'regex:/^(linkedin)$/'
				),
				'title' => array(
					'required'
				),
				'picture' => array(
					'required',
					'url'
				),
				'href' => array(
					'required',
					'url'
				)
			);
			// Validate input and return error if fail
			$validator = Validator::make( $article_params, $filter );
			if( $validator->fails() ){
				return $validator->messages();
			}
			// Save the article params for later use
			Session::put( 'linkedin_share_params', $article_params );
			/* Check to see if we have an access token
			 * If we have one, we'll redirect to share-preview page, if not
			 * we'll redirect to linkedin signin to get the access token
			 */
			if( Session::get( 'userinfo.signed_in' ) ){
				$user = User::find( Session::get( 'userinfo.id' ) );
				if( isset( $user->linkedin_access_token ) && isset( $user->linkedin_access_token_expiration ) ){
					$expiration = $user->linkedin_access_token_expiration;
				}
			}
			else{
				if( Session::has( 'social.share.linkedin.access_token_expiration' ) ){
					$expiration = Session::get( 'social.share.linkedin.access_token_expiration' );
				}
			}
			// if access token has more than a day to live
			if( isset( $expiration ) && $expiration - time() > 86400 ){
				// Don't refresh token. Get view
				return redirect( '/social/share/linkedin/preview' );
			}
			// Public API key
			$linkedin_api_key = '75jyjerz88r62e';
			// Generate and set state token. Used to prevent cross-site request forgery (CSRF)
			$state = csrf_token();
			// Flash linkedin state to session
			Session::flash( 'linkedin_state', $state );
			// Redirect URI
			$redirect_uri = urlencode( 'https://plexuss.com/social/share/linkedin/getAccessToken' );
			// Redirect to URL to get linkedin Authorization Code
			return redirect(
				'https://www.linkedin.com/oauth/v2/authorization' .
					'?response_type=code' .
					'&client_id=' . $linkedin_api_key .
					//'&scope=SCOPE' . SCOPE IS NOT NEEDED; ALREADY SET IN APP SETTINGS
					'&state=' . $state .
					'&redirect_uri=' . $redirect_uri
			);
		}
	}
	/***********************************************************************
	 *===================== GET LINKEDIN ACCESS TOKEN ======================
	 ***********************************************************************
	 * For linkedin share.
	 * This method is called when we need to get a linkedin access token in order
	 * to post for a user. After a redirect to linkedin where a user either grants
	 * or denies posting permissions. Linkedin redirects to this route with the
	 * redirect_uri parameter. We receive two params:
	 * @param		string		code		the authorization code we need to send
	 * 										to linkedin in order to get the access
	 * 										token
	 * @param		string		state		the state token that WE generated earlier
	 * 										which needs to be the same as what we
	 * 										have in our flashed session. If it's not
	 * 										the same, then the request is likely a
	 * 										forged request, and we do not proceed!
	 * Once we receive these, we can get the Linkedin Access Token! We make a CURL
	 * request to the linkedin accesstoken endpoint. This is a POST. We send the
	 * authorization code, along with our public API key and secret key. Note(!):
	 * The secret key must be... SECRET! That means an HTTPS POST.
	 * A sucessful request retursn the following as JSON:
	 * @param		string		access_token		The access token we'll send along
	 * 												with each linkedin API request.
	 * @param		string		expires_in			The usable duration of the access
	 * 												token. In seconds. As of this
	 * 												writing, it's two weeks.
	 * Once we receive the access token, we store it, pull the flashed share params
	 * and send the user a preview view, with the ability to add a comment and
	 * select the visibility of the share.
	 */
	public function getLinkedinAccessToken(){
		$error_message = "There was an error logging you in.";
		if( !Session::has( 'user_table' ) ){
			if( Session::has( 'userinfo' ) && Session::get( 'userinfo.signed_in' ) == 0 ){
				// user is not signed in
			}
			else{
				// Halt. There's a problem with our sessioning
				return $error_message . ' No user table session.';
			}
		}
		// Check if linkedin return is valid
		if( Request::has( 'code' ) && Request::has( 'state' ) ){
			$input = Request::all();
			// Create validation filter
			$filter = array(
				// Code is 115 chars long, generated by Linkedin
				'code' => array(
					'required',
					//'regex:/^([0-9a-zA-Z\-_ ]){115}$/'
				),
				// State is 40 chars, generated by us
				'state' => array(
					'required',
					//'regex:/^([0-9a-zA-Z]){40}$/'
				)
			);
			// validate
			$validator = Validator::make( $input, $filter );
			if( $validator->fails() ){
				return $validator->messages();
			}
			// MAKE SURE THE STATE TOKEN MATCHES, OTHERWISE, CSRF!!!
			if( Session::pull( 'linkedin_state' ) != $input['state'] ){
				return $error_message;
			}
			$code = $input['code'];
			$state = $input['state'];
			// Prepare access token request URL, params
			$url = 'https://www.linkedin.com/oauth/v2/accessToken';
			$params = array(
				'grant_type' => 'authorization_code',
				'code' => $code,
				'redirect_uri' => 'https://plexuss.com/social/share/linkedin/getAccessToken',
				'client_id' => '75jyjerz88r62e',		// Public API key
				'client_secret' => 'DJu6NQpO6R7XOxXP'	// Don't let anyone see this!
			);
			$query_string = http_build_query( $params );
			// Initialize curl session
			$ch = curl_init();
			// set cURL options
			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_POST, count( $params ) );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $query_string );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
			// Execute connection
			$json_response = curl_exec( $ch );
			// close connection
			curl_close( $ch );
			// convert json to object
			$response = json_decode( $json_response );
			/* Response returns an access token (which we must keep and use every time
			 * we make an API request). Also returns an int: expires_in. This is the
			 * time in seconds from when Linkedin sent the response to when the token
			 * will expire.
			 */
			// Check if we have access token and its expiration
			if( isset( $response->access_token ) && isset( $response->expires_in ) ){
				// Get uid from session
				$user_table = Session::get( 'user_table' );
				// If not logged in, save token to session
				if( !$user_table && Session::get( 'userinfo.signed_in' ) != 1 ){
					Session::put( 'social.share.linkedin.access_token', $response->access_token );
					Session::put( 'social.share.linkedin.access_token_expiration', time() + $response->expires_in );
				}
				else{
					// Get user model, save linkedin access token and expiration
					$user = User::find( $user_table->id );
					$user->linkedin_access_token = $response->access_token;
					$user->linkedin_access_token_expiration = time() + $response->expires_in;
					$user->save();
				}
				// Redirect to view route
				return redirect( '/social/share/linkedin/preview' );
			}
			else{
				return "There was a problem authenticating you.";
			}
		}
	}
	/***********************************************************************
	 *======================= SHOW LINKEDIN PREVIEW ========================
	 ***********************************************************************
	 * For linkedin share.
	 * This function is called to show a preview of the linkedin share, and add
	 * the ability to comment, select visibility. This only is called when we
	 * have a valid, living access token.
	 */
	public function showLinkedinPreview(){
		$share_params = Session::get( 'linkedin_share_params' );
		if( !isset( $share_params ) ){
			return "There was a problem with the server.";
		}
		return View( 'private.social.share.linkedin', $share_params );
	}
	/***********************************************************************
	 *======================= LINKEDIN SHARE/POST ==========================
	 ***********************************************************************
	 * For linkedin share.
	 * This receives the post parameters for a linkedin share. Make a cURL POST
	 * request to linkedin at the share endpoint after generating an XML document
	 * based on the user submitted article parameters. On success, we get these
	 * parameters in XML form:
	 * @param		string		update-key		a key that we can use to request
	 * 											more information about the share
	 * @param		string		update-url		the url to the share. We redirect
	 * 											to this url on success
	 * If we don't see these params in the return, we return an error.
	 */
	public function submitLinkedin(){
		$input = Request::all();
		// Retrieve share params from session
		if( Session::has( 'linkedin_share_params' ) ){
			// retrieve and forget parameters
			$share_params = Session::pull( 'linkedin_share_params' );
		}
		else{
			return "There was a problem with the server. =/";
		}
		// Make filter to validate
		$filter = array(
			'comment' => array(
				'required'
			),
			'visibility' => array(
				'required',
				'regex:/^(connections-only|anyone)$/'
			)
		);
		// Validate Input
		$validator = Validator::make( $input, $filter );
		if( $validator->fails() ){
			return $validator->messages();
		}
		// Check if logged in
		$logged_in = Session::has( 'userinfo' ) ? Session::get( 'userinfo.signed_in' ) : 0;

		// RETRIEVE SAVED ACCESS TOKEN
		// Get access token from DB if logged in
		if( $logged_in ){
			if( Session::has( 'user_table' ) ){
				// get user table
				$user_table = Session::get( 'user_table' );
				$user = User::find( $user_table->id );
				// get/assign token
				$access_token = $user->linkedin_access_token;
				$expiration = $user->linkedin_access_token_expiration;
			}
			else{
				// Issue with building user table
				return "There was a problem with our server. =/";
			}
		}
		// Get access token from session if not logged in
		else{
			if( Session::has( 'social.share.linkedin.access_token' ) ){
				$access_token = Session::get( 'social.share.linkedin.access_token' );
				$expiration = Session::get( 'social.share.linkedin.access_token_expiration' );
			}
			else{
				// There's no token
				return "There was a problem with our server. =/";
			}
		}
		
		// Error if less than 12 hours left on token
		if( $expiration - time() < 43200 ){
			// Did this person wait 12 hours between opening the share and actually sharing?!
			return "There was a problem with your access token.";
		}
		/* Commence sharing!
		 * Linkedin expects an XML document containing the share data
		 */
		// Build XML
		$writer = new XMLWriter();
		$writer->openMemory();
		$writer->startDocument( '1.0', 'UTF-8' );
			$writer->startElement( 'share' );
				$writer->writeElement( 'comment', $input['comment'] );
				$writer->startElement( 'content' );
					$writer->writeElement( 'title', $share_params['title'] );
					$writer->writeElement( 'submitted-url', $share_params['href'] );
					$writer->writeElement( 'submitted-image-url', $share_params['picture'] );
				$writer->endElement();
				$writer->startElement( 'visibility' );
					$writer->writeElement( 'code', $input['visibility'] );
				$writer->endElement();
			$writer->endElement();
		$writer->endDocument();
		$xml = $writer->outputMemory( true );
		// Linkedin Share URL
		$url = 'https://api.linkedin.com/v1/people/~/shares';
		// Send XML
		// Initialize cURL session
		$ch = curl_init();
		// Set cURL options
		curl_setopt( $ch, CURLOPT_URL, $url );
		//curl_setopt( $ch, CURLOPT_HEADER, true );
		curl_setopt( $ch, CURLINFO_HEADER_OUT, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/atom+xml',
			'Content-Length: ' . strlen( $xml ),
			'Authorization: Bearer ' . $access_token
		) );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS , $xml );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		// Execute connection
		$response = curl_exec( $ch );
		// close connection
		curl_close( $ch );
		// Take XML response as a string and parse to array
		$sXML = simplexml_load_string( $response );
		$json = json_encode( $sXML );
		$array = json_decode( $json, true );

		// Redirect to share on success
		$url="";
		if(isset($array['update-key']) && isset($array['update-url'])){
				$url = $array['update-url'];
				$url = "https://".$url;
				return redirect()->away($url);
		}
		else{
			return "There was an error submitting your share. =/ :(";
		}		
	}
}