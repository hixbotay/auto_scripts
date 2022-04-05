<?php
error_reporting(0);
ini_set('display_errors', 0);

$GLOBALS['ignore'] = ['client_monthly_sales','plans'];
function debug($arr){
	echo '<pre>';print_r($arr);echo '</pre>';
}
function removeFile($folder){
	$files = glob($folder.'/*'); // get all file names
	foreach($files as $file){ // iterate files
	  if(is_file($file)) {
		unlink($file); // delete file
	  }
	}
}

function getFiles($folder){
	$result=[];
	$files = glob($folder.'/*'); // get all file names
	foreach($files as $file){ // iterate files
	  if(is_file($file)) {
		$result[]=$file;
	  }
	}

	return $result;
}
function downloadCsv($datas,$name,$header = false) {	
	// Open the output stream
	$fh = fopen('php://output', 'w');
// 			fputs($fh, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
// 			fputs($fh, pack("CCC",0xef,0xbb,0xbf)); 
// 			fputs($fh, chr(255) . chr(254)); 
	
	// Start output buffering (to capture stream contents)
	ob_start();
	
	
	foreach ($datas as $data) {	
		fputs( $fh, '"'.implode('","',$data).'"'.PHP_EOL);
	}
	fputs( $fh, "\xEF\xBB\xBF" );
	fputs($fh, pack("CCC",0xef,0xbb,0xbf)); 
	$string = ob_get_clean();
	
	header('Content-Encoding: UTF-8');
	header('Content-Type: application/octet-stream charset=UTF-8');
	header('Content-Disposition: attachment; filename="' . $name . '.csv";');
	header('Content-Transfer-Encoding: binary');
	header('Pragma: public');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Cache-Control: private', false);
	//stream_encoding($fh, 'UTF-8'); 
	// Stream the CSV data
	echo $string;
	//echo chr(255) . chr(254) . mb_convert_encoding($string, 'UTF-16LE', 'UTF-8');
	exit;

}
function convertDataToArray($data,$result=[]){
	foreach($data as $k=>$v){
		if(in_array($k,$GLOBALS['ignore'])){
			continue;
		}
		if(is_array($v) || is_object($v)){
			$result += convertDataToArray($v,$result);
		}else{
			$result[] = $v;
		}
	}
	return $result;
}
function getAllKeyFromArray($data,$result=[]){
	foreach($data as $k=>$v){
		if(in_array($k,$GLOBALS['ignore'])){
			continue;
		}
		if(is_array($v) || is_object($v)){
			$result += getAllKeyFromArray($v,$result);
		}else{
			$result[] = $k;
		}
	}
	return $result;
}

try{
	
	if(!$_FILES["fileToUpload"]["tmp_name"]){
		throw new Exception('Please upload zip file');
	}
	//clear trash
	removeFile('unzip');
	if(is_file('output.csv')){
		unlink('output.csv');
	}
	//unzip
	$zip = new ZipArchive;
	$res = $zip->open($_FILES["fileToUpload"]["tmp_name"]);
	if ($res === TRUE) {
	  $zip->extractTo('unzip');
	  $zip->close();
	  $files = getFiles('unzip');
	  $arr = [];
		$arr[] = explode('","','activation_date","is_convenience_fee_enabled","is_differential_pricing_plan","fixed_tdr","percent_tdr","email","fixed_base_rate","fixed_tdr","full_name","id","kyc_status","percent_base_rate","percent_tdr","phone","profile_image_url","total_commission","total_sales","total_transactions","username');
	  foreach($files as $i=>$f){		  
		  $content = json_decode(file_get_contents($f),true);		  
		  $arr[] = [
			$content['activation_date'],
			$content['client_fees']['is_convenience_fee_enabled'],
			$content['client_fees']['is_differential_pricing_plan'],
			implode('|',$content['client_fees']['flat_pricing_plan']),
			implode('|',$content['client_fees']['convenience_fee_pricing_plan']),
			$content['email'],
			$content['fixed_base_rate'],
			$content['fixed_tdr'],
			$content['full_name'],
			$content['id'],
			$content['kyc_status'],
			$content['percent_base_rate'],
			$content['percent_tdr'],
			$content['phone'],
			$content['profile_image_url'],
			$content['total_commission'],
			$content['total_sales'],
			$content['total_transactions'],
			$content['username'],
		  ];
	  }
	  downloadCsv($arr,'output');
	  //
	} else {
	  echo 'Unzip failed!';
	}
}catch(Exception $e){
	header("Location: index.php?msg=".$e->getMessage());
	die();
}
