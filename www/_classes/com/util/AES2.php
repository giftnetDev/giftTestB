<?php

	// 기본 32자
	$key = hex2bin2("92745678z912345678z912345678z977");
	$iv =  hex2bin2("92745678901234567890123456789011");
	

	function hex2bin2($hexdata) {
		$bindata = "";
		for ($i=0;$i < strlen($hexdata);$i+=2) {
			$bindata .= chr(hexdec(substr($hexdata,$i,2)));
		}
		return $bindata;
	}

	function toPkcs7($value) {
		if (is_null($value)) $value = "" ;
		$padSize = 16 - (strlen($value) % 16);
		return $value . str_repeat(chr($padSize), $padSize);

	}

	function fromPkcs7($value) {

		$valueLen = strlen($value);  
		if ($valueLen % 16 > 0) $value = "";
		$padSize = ord($value{$valueLen - 1});
		if ( ($padSize < 1) or ($padSize > 16) ) $value = "";
		// Check padding.
		for ($i = 0;$i < $padSize;$i++) {
			if (ord($value{$valueLen - $i - 1}) != $padSize) $value = "";
		}
		return substr($value, 0, $valueLen - $padSize);
	}

	function encrypt($key, $iv, $value) {
		if (is_null($value)) $value = "";
		$value = toPkcs7($value);  
		$output = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $value, MCRYPT_MODE_CBC, $iv);
		return base64_encode($output);
	}

	function decrypt($key, $iv, $value) {
		if (is_null($value)) $value = "";
		$value = base64_decode($value);
		$output = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $value, MCRYPT_MODE_CBC, $iv);
		return fromPkcs7($output);
	}
?>