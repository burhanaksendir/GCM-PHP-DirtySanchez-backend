<?php
require_once 'config.php';
require_once 'database.helpers.php';
require_once 'db.php';
require_once 'logger.php';
ini_set('display_errors', 1);
$DEFAULT_ACTION = "badAction";
// Logging class initialization
$log = new Logging();
$log -> lfile('c:/xampp/htdocs/martinrevert/atGCM/logfile.txt');

$pathInfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : "";
$pathParts = explode('/', substr($pathInfo, 1));
$action = isset($pathParts[0]) ? $pathParts[0] : $DEFAULT_ACTION;
if (function_exists($action)) {
	$action($pathParts);
} else {
	badAction();
}

//funciones REST
function badAction($parts = null) {
	print "Invalid request";
}

function register($parts) {
	//$regId = isset($parts[1]) ? $parts[1] : 'undefined';
	
	
	$db = database::get_instance();
	
	if ($_POST){
		$regId = $_POST[regId];
	}
	
	echo $regId . "<br>";
	if ($db -> checkRegister($regId)) {
		// query para guardar en la base de datos el regId si no existe
		echo "Encontrado, no es necesario registrar";
		global $log;
		$log -> lwrite('Reconfirmado preexistente : ' . $regId);
	} else {
		//$results= $db->select('register_ids');
		$results = $db -> insertRegisterId($regId);
		print_r($results);
		echo "No encontrado, hay que registrar";
		global $log;
		$log -> lwrite('Registrado nuevo id : ' . $regId);
	}
}

function unregister($parts) {
	//select para ver si ya existe en DB
	//Query para deletear regId de la DB
	//$regId = isset($parts[1]) ? $parts[1] : 'undefined';
	
	
	
	$db = database::get_instance();
	if ($db -> checkRegister($regId)) {
		// query para guardar en la base de datos el regId si no existe
		echo "Encontrado, hay que desregistrar";
		$results = $db -> delete("register_ids", "registration", $regId);
	} else {
		echo "No encontrado, no hay que hacer nada";
	}
}

function unregisterUninstall($id) {
	$db = database::get_instance();
	$results = $db -> delete("register_ids", "idregister_ids", $id);
	global $log;
	$log -> lwrite("Desregistracion: " . $id);
}

function reindex() {
	$db = database::get_instance();
	$db -> raw("CALL reindex");

}

function post($parts) {

	/*  Envío al servicio Google Cloud Messaging de un mensaje
	 *  El request se envia de al sgte manera:
	 *
	 * 		URL url = new URL("http://www.martinrevert.com.ar/atGCM/rest.php/post/"+message);
	 *
	 * */
	if ($_POST){
		$message = $_POST[message];
		$urlimagen = $_POST[imagen];
		$urlarticulo = $_POST[linke];
		$tipo = $_POST[tipo];
		$fecha = $_POST[fecha];
		
	}
	echo $message;
	echo "<BR>";
	 
	$db = database::get_instance();
/*	$message = isset($parts[1]) ? $parts[1] : 'undefined';
	$message = urldecode($message);
	$urlimagen = isset($parts[2]) ? $parts[2]:'undefined';
	$urlarticulo = isset($parts[3]) ? $parts[3]:'undefined';
	$tipo = isset($parts[4]) ? $parts[4]:'undefined';
 * 
 */
	
	

	//$message = filter_var(FILTER_SANITIZE_URL);

	$registrationIDs = $db -> getRegIds();
    
	//var_dump($registrationIDs);
	echo "<br><br>";
	/*$registrationIDs = array('APA91bFVD49mo10LHK-AwEOe0tsHWP6Xz23ATgWJ-3OhzVntAPuBlfgIDZqKC-s8FvenbsSgY5_psHK_IIljMWVUfKPvq7TZ3WCn9tTy7in-kVIU0dR6toqztfDR69z5CUFitfdatq-nJQlVHesgnmkCufoCzelumQ',
		'APA91bE4nHu5amUUVr7H3o3yyRi1zbBLCwzu4puLWCSo5YEGLV4-HrK91_zH0pPx_uof_8EEUMCA5Jwr5wSf9B688ms-Ge92Je7l_6sxPITP1XiYODumcU8tSFmWAwcHZKX9yNTJELLNZJUELVO_H3YIosQG6JM5p2QySP8zeCIDGTwGDNSXxo8',
		'APA91bFCKQBYh-LBuMoL9KOE4VJHlmuaRaU-LVpWZSkF9kwglclu51w_23DTLoVppWIvGugPOHM4k49_lf1Ps_H0nf00nmB-CpPMFup9H_A4Iw0RzaqvwXGj1uBgEpEBY75VwapNk3zpl93Jf2Vysg21gS-QkgjzBhNahtAg__WiJevTVhbdGMI',
		'APA91bGs70w8twonxAqxjbcrQLMcKr--SOuPmCNEdiwZUASJFcCfksNKIrYLmAONRbdPOXKaoiqwHz7KhMf2Ap8xTfcHgtssNnTbrUM7FSStRseYQg6HbAeI7FCYz2lErw8t54AWVWQt9mFXFoY4Djf3ro9d-mB9qPu0Cy4i4v6cmiwBwa3YfD0');
	
*/
	$apiKey = "AIzaSyCLW59FCFqInf-dLzzXfVSel3Muqo2JDM8";

	// Set POST variables
	//$url = 'https://android.googleapis.com/gcm/send?dry_run=true';
	$url = 'https://android.googleapis.com/gcm/send';

	$headers = array('Authorization: key=' . $apiKey, 'Content-Type: application/json');

	// Chunk number of registration ID's according to the maximum allowed by GCM
	$chunks_regid = array_chunk($registrationIDs, 1000);

	//print_r($registrationIDs);
	//var_dump($chunks);

	// Set the url, number of POST vars, POST data

	$curl_array = array();

	// Crea el multi recurso cURL
	$mh = curl_multi_init();
	$loop = 0;

	foreach ($chunks_regid as $valor) {

		$fields = array('registration_ids' => $valor, 'time_to_live' => 84600, 'delay_while_idle' => true, 'data' => array('message' => $message, 'tipo' => $tipo, 'urlimagen' => $urlimagen, 'urlarticulo' => $urlarticulo, 'fecha'  => $fecha ));

		$curl_array[$loop] = curl_init();
		curl_setopt($curl_array[$loop], CURLOPT_POSTFIELDS, json_encode($fields));
		curl_setopt($curl_array[$loop], CURLOPT_URL, $url);
		curl_setopt($curl_array[$loop], CURLOPT_POST, true);
		curl_setopt($curl_array[$loop], CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl_array[$loop], CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_array[$loop], CURLOPT_SSL_VERIFYPEER, false);

		curl_multi_add_handle($mh, $curl_array[$loop]);

		echo "<br><br>";
		$loop++;
	}
	$running = NULL;
	do {
		usleep(1000); //valor original 10000
		curl_multi_exec($mh, $running);
	} while($running > 0);

	$res = array();
	$resultadazo = array();
	$loop2 = 0;
	foreach ($chunks_regid as $valor) {
		$res[$loop2] = curl_multi_getcontent($curl_array[$loop2]);
		
		global $log;
		$log -> lwrite('Respuesta '.$loop2.': '.$res[$loop2]);
		
		echo "<br><br>";
		//print_r($res[$loop2]);
		echo "<br>----------<br>";
		$json_return = json_decode($res[$loop2]);

		$array_results = $json_return -> results;
		$resultadazo = array_merge($resultadazo,$array_results);
		
		//$size = count($array_results, 1);

		echo "<br><br>";
				
		$loop2++;

	}
	$borro = 0;
	foreach ($resultadazo as $key => $value) {

			print_r($key);
			echo "<br><br>";

			//print_r($value);

			$variables = get_object_vars($value);
			$keys = array_keys($variables);
			if ($keys[0] == "registration_id") {
				echo "Con este hago update: " . $value -> registration_id;
				$canonicalid = $value -> registration_id;
				//updateRegisterId($key,$value);
				$db = database::get_instance();
				$results = $db -> updateRegisterId('register_ids', 'registration', 'idregister_ids', $canonicalid, $key);

			} else if ($keys[0] == "error") {
				//restablecer para produccion
				unregisterUninstall($key);
				echo "A este lo vuelo al ocote";
				$borro++;

			} else {
				echo "Todo ok";
			}

			echo "<br><br>";

		}
		echo "Borre " . $borro . " regIds<br><br>";
	//print_r($resultadazo);
	$loop3 = 0;
	foreach ($chunks_regid as $valor) {
		curl_multi_remove_handle($mh, $curl_array[$loop3]);
		$loop3++;
	}
	curl_multi_close($mh);

	

	reindex();
}
?>