<?php
session_start();
//$_SESSION['trakt']['token'] => access token stored in this
//$_SESSION['trakt']['progress'] => stores the current progress in the authorization protocol

define('TRAKT_URL', 'https://api.trakt.tv');

$TRAKT = array(
  'client_id' => '8df076798424717610776798f694d3e9cf5afe34689351529ed988ff74a62291',
  'client_secret' => 'e291e1bd170173809cad954f065d181e1ea7e1ec00c8b73a7fa7a6788952fd89',
  'redirect_uri' => 'http://localhost/joannalab8/trakt.php',
  'state' => 'sj78fmv8sl39gns7'
);

//OAuth flow
$auth = authorize($TRAKT);
if ($auth) {
  get_token($TRAKT);
}
//API functions
/**
 * Function to request an authorization code. Redirects to the Trakt login page for authorization.
 *
 * @param array $config An associative array containing important Trakt app settings for OAuth.
 * @return true Returns true if authorization code received.
 */
function authorize($config) {
  if (empty($_SESSION['trakt']['progress']) && !isset($_SESSION['trakt']['token'])) {
    $url = TRAKT_URL . '/oauth/authorize';
    $params = array(
      'response_type' => 'code',
      'client_id' => $config['client_id'],
      'redirect_uri' => $config['redirect_uri'],
      'state' => $config['state']
    );
    //http_build_query($params) => 'response_type=code&client_id=...&redirect_uri=...&state=...'
    $request = $url . '?' . http_build_query($params); //generate complete URL with parameters
    $_SESSION['trakt']['progress'] = 'authorizing';
    header("Location: $request"); //redirect to generated URL
  } else {
    return true;
  }
}
/**
 * Function to request access token.
 *
 * @param array $config An associative array containing important Trakt app settings for OAuth.
 * @return void
 */
function get_token($config) {
  if (isset($_GET['code']) && $_SESSION['trakt']['progress'] == 'authorizing') {
    if ($_GET['state'] == $config['state']) { //check that received $_GET['state'] is the same as the sent $config['state']
      $url = TRAKT_URL . '/oauth/token';
      $code = $_GET['code']; //the code GET parameter from the URL
      $data = array(
        'code' => $code,
        'client_id' => $config['client_id'],
        'client_secret' => $config['client_secret'],
        'redirect_uri' => $config['redirect_uri'],
        'grant_type' => 'authorization_code'
      );
      $opts = array(
        'http' => array(
          'header' => "Content-Type:application/json",
          'method' => 'POST',
          'content' => json_encode($data) //convert $data array to JSON format
        )
      );
      $context = stream_context_create($opts);
      $result = json_decode(file_get_contents($url, false, $context));
      //var_dump($context);
      var_dump($result);
      
      $_SESSION['trakt']['token'] = $result->access_token;
      $_SESSION['trakt']['progress'] = 'token';
      //print $_SESSION['trakt']['progress'];
    }
  }
}





