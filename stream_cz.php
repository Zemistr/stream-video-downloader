<?
if($_GET['adresa'] != ''){
	$obsah = @file_get_contents($_GET['adresa']);

	preg_match('/<title>(.+)<\/title>/isU',$obsah,$vysledek_nazev);
	
	if(preg_match('/\("cdnLQ", "([0-9]+)"\)/isU', $obsah,$vysledek_lq)){
	}else if(preg_match('/cdnLQ=([0-9]+)&/isU',$obsah,$vysledek_lq)){
	}else if(preg_match('/cdnID=([0-9]+)&/isU',$obsah,$vysledek_lq)){
	}
	
	if(preg_match('/cdnHQ=([0-9]+)&/isU',$obsah,$vysledek_hq)){
	}else if(preg_match('/hdID=([0-9]+)&/isU',$obsah,$vysledek_hq)){
	}else if(preg_match('/\("cdnHQ", "([0-9]+)"\)/isU', $obsah,$vysledek_hq)){
	}
	
	if(preg_match('/cdnHD=([0-9]+)&/isU',$obsah,$vysledek_hd)){
	}else if(preg_match('/\("cdnHD", "([0-9]+)"\)/isU', $obsah,$vysledek_hd)){
	}
	
	echo "<?xml version='1.0' encoding='UTF-8'?>\n";
	echo "<StreamVideoDownloader>\n";
	echo "	<Nazev>".$vysledek_nazev[1]."</Nazev>\n";
	echo "	<LQ_verze>";
	if($vysledek_lq[1] != ""){
		echo $vysledek_lq[1];
	}else{
		echo 'nenalezena';
	}
	echo "</LQ_verze>\n";
	echo "	<HQ_verze>";
	if($vysledek_hq[1] != ""){
		echo $vysledek_hq[1];
	}else{
		echo 'nenalezena';
	}
	echo "</HQ_verze>\n";
	echo "	<HD_verze>";
	if($vysledek_hd[1] != ""){
		echo $vysledek_hd[1];
	}else{
		echo 'nenalezena';
	}
	echo "</HD_verze>\n";
	echo "</StreamVideoDownloader>\n";
	
	@file_get_contents('http://data.zemistr.eu/counter:stream/num-1-'.time().'.png');


	$server_ip = isset($_SERVER['REMOTE_ADDR']) && $_SERVER["REMOTE_ADDR"] != '' ? $_SERVER["REMOTE_ADDR"] : '';

	@file_get_contents('http://s01.zemistr.eu/piwik/piwik.php?idsite=3&rec=1&url='.$_GET['adresa'].'&action_name='.rawurlencode($vysledek_nazev[1]).'&cip='.$server_ip.'&_cvar={"1":["from","web"]}&token_auth=189b9ac0cf4f973d483038481cd0042b');
}
?>
