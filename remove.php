<?

$token = "<YOU SPOTIFY API TOKEN>";


if(empty($_GET['get_all'])){


$arr = json_decode(file_get_contents('like.json'),true);
$del = [];

$clean  = array('Big Russian', 'GONE.Flu', 'Oxxxymiron', 'AK-47', 'MORGENSHTERN', 'Arut','INSTASAMKA','Rammstein','FACE -','Armin van Buuren','PHARAOH','Mnogoznaal','Timati','OBLADAET','Big Baby Tape');




// #method #1 [All Russian Tracks]
// for($i=0;$i<count($arr);$i++){
// 	if (preg_match("/\p{Cyrillic}/u", $arr[$i]['name'])){
// 		array_push($del,$arr[$i]);
// 	}
// }

// #method #2 [By names]
for($i=0;$i<count($arr);$i++)
{
	if (str_replace($clean, '', $arr[$i]['name']) != $arr[$i]['name']){
		array_push($del,$arr[$i]);
	}
}

// echo '<pre>';
// var_dump($del);
// var_dump($arr);
// die();



$final = array_chunk($del,50);
for($i=0;$i<count($final);$i++){
	delete_tracks($final[$i],$token);
}

echo 'good job';



}else{

echo '<pre>';
$arr = [];

$off = 0;
while(true){
	$data = get_tracks($token, $off)['items'];
	for($i=0;$i<count($data);$i++){
		array_push($arr, ['name'=>$data[$i]['track']['album']['artists'][0]['name'].' - '.$data[$i]['track']['name'], 'id'=>$data[$i]['track']['id']]);
	}
	$off+=50;
	if(count($data)<1) break;
}
file_put_contents('like.json', json_encode($arr));
exit(json_encode($arr));

}

function strpos_arr($haystack, $needle) {
    if(!is_array($needle)) $needle = array($needle);
    foreach($needle as $what) {
        if(($pos = strpos($haystack, $what))!==false) return $pos;
    }
    return false;
}


function get_tracks($token,$off){

	$arr = ['limit'=>50];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://api.spotify.com/v1/me/tracks?limit=50&offset=".$off);
	// curl_setopt($ch, CURLOPT_POST, 1);
	// curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($arr));  //Post Fields
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$headers = [
	    'Authorization: Bearer '.$token,
	    'Content-Type: application/json',
	];

	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$server_output = json_decode(curl_exec ($ch),true);
	return $server_output;
	curl_close ($ch);
}

function delete_tracks($arr,$token){


	$remove = [];
	for($i=0;$i<count($arr);$i++){
		array_push($remove,$arr[$i]['id']);
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://api.spotify.com/v1/me/tracks");
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($remove)); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$headers = [
	    'Authorization: Bearer '.$token,
	    'Content-Type: application/json',
	];

	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$server_output = json_decode(curl_exec ($ch),true);
	return $server_output;
	curl_close ($ch);
}

?>

