<?php
/************************TRAKT PORTION BELOW******************************/
define('TRAKT_URL', 'https://api.trakt.tv');

$TRAKT = array(
    'client_id' => '8df076798424717610776798f694d3e9cf5afe34689351529ed988ff74a62291',
    'client_secret' => 'e291e1bd170173809cad954f065d181e1ea7e1ec00c8b73a7fa7a6788952fd89',
    'redirect_uri' => 'http://localhost/joannalab8/trakt.php',
    'state' => 'sj78fmv8sl39gns7'
);
//grabs ALL data for top15 movies
$traktresult = top15movie($TRAKT);
//grabs review from tmdb for a movie
//$tmdbreview = gettmdbreview($traktresult);
 //var_dump($tmdbreviews);
 //print_r($tmdbreview);
 //print_r($tmdbreviews->results);
//echo '<pre>' . var_export($tmdbreviews, true) . '</pre>';

//grabs the list of topmovies
function top15movie($config) {
  $url = TRAKT_URL . '/movies/trending?limit=10';
  $headers = array(
    "Content-Type:application/json",
    "trakt-api-version:2",
    "trakt-api-key:$config[client_id]"
  );
  $opts = array(
    'http' => array(
      'header' => $headers,
      'method' => 'GET'
    )
  );
  $context = stream_context_create($opts);
  $result = json_decode(file_get_contents($url, false, $context));

  return $result;

  /*  print '<table border="3"><thead><tr><th>Movie title</th><th>Year</th></tr></thead><tbody>';
  foreach ($result as $movie) {
    $title = (string) $movie->movie->title;
    $imdbid = (string) $movie->movie->ids->imdb;
    $year = (string) $movie->movie->year;
    print "<tr><td><a href='https://www.imdb.com/title/$imdbid'>$title</a></td><td>$year</td></tr>";
  }
  print '</tbody></table>';*/

}
/******************TMDB PORTION BELOW*****************************/
//gets all reviews for a particular movie
function gettmdbreview($movieid){

    $url = "http://api.themoviedb.org/3/movie/$movieid/reviews?api_key=1008033c4d3429e0fad13fc9b6f9fa2d";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);

    $results = curl_exec($ch);

    curl_close($ch);

    $final =  json_decode($results);


    return $final;
}
//gets movie details from tmdb. i am using it just for getting the poster path
function gettmdbmovie($id){
    $url = "https://api.themoviedb.org/3/movie/$id?api_key=1008033c4d3429e0fad13fc9b6f9fa2d&language=en-US";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);

    $results = curl_exec($ch);

    curl_close($ch);

    $final =  json_decode($results);


    return $final;
}
//var_dump($test->poster_path);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.css">
    <meta name="viewport" content="width=device-width">
    <title>Movie Review Page</title>
</head>
<body>
    <table>

        <?php foreach($traktresult as $movie){ ?>
            <tr>
                <td>
                    <h2><?=$movie->movie->title;?>(<?=$movie->movie->year;?>)</h2>
                    <div>
                    <?php
                        $moviedetail = gettmdbmovie($movie->movie->ids->tmdb);
                        $posterpath = $moviedetail->poster_path;
                        echo '<img src='."https://image.tmdb.org/t/p/original/$posterpath".' alt='."movie poster".' width='."300".' height='."400".'>';

                    ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <?php $review = gettmdbreview($movie->movie->ids->tmdb);
                if ($review->results[0]->author == NULL){
                    echo 'no reviews at the moment';
                }
                else {
                    foreach ($review->results as $r) {
                        //$author = $review->results[0]->author;
                        //$content = $review->results[0]->content;
                        print_r('Author: '.$review->results[0]->author.' Review: '.$review->results[0]->content);
                    }
                }
                    ?>
                </td>
            </tr>

        <?php }?>
    </table>


</body>
</html>



