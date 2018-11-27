<style>
body {
	font-family: Gadugi, sans-serif;word-wrap:break-word;
}
</style>
<meta charset="utf-8" />

<?php
	//Compute the U
	function RSA_U($c, $m)
	{
		$a = $c; $b = $m; 			//a->C, b->M entiers naturels
		$r = $a; $rP = $b; $u = 1; $v = 0; $uP = 0; $vP = 1; $rs; $us; $vs; $q;
		//--------------------------------------------------

		while($rP != 0)
		{
			$q = $r/$rP;
	        $rs = $r;
	        $us = $u;
	        $vs = $v;
	        $r = $rP;
	        $u = $uP;
	        $v = $vP;
	        $rP = $rs - $q*$rP;
	        $uP = $us - $q*$uP;
	        $vP = $vs - $q*$vP;
		}
		//----------------------

		//U = (u - KM)     --      2 < U < M
	    $U = $u; $k; $tmp;
	    if($u<0) { $k = $u; }
	    else { $k = -$u; }


	    while($U < 2)
	    {
	        $U = $u - ($k*$b);
	        ++$k;
	    }

	    if($u<0) { $k = $u; }
	    else { $k = -$u; }
	    while($U > $b)
	    {
	        $U = $u - ($k*$b);
	        ++$k;
	    }

	    return $U;
	}
	//Convert the string to an ASCII values array
	function toASCIIconvert($string)
	{
		$string = (string) $string;
		$arrString = str_split($string);
		$arrASCII = array();

		for($i=0; $i < count($arrString); ++$i)
		{
			array_push($arrASCII, ord($arrString[$i]));
		}
		return $arrASCII;
	}
	//Encrypt/Decrypt
	//Decrypt -> ArrayNumbers [String]	Input
	//Encrypt -> ArrayNumbers [String] Input
	//Return -> [Array]
	function Encrypt_Decrypt($arrayNumbers, $c, $n, $u, $jobTODO)
	{
		$arrayResult = array();		//Result of encryption/decryption

		if(isset($jobTODO) && $jobTODO == 'encrypt')
		{
			//Convert to ASCII
			$arrayNumbers = toASCIIconvert($arrayNumbers);
			for($i=0; $i<count($arrayNumbers); ++$i)
			{
				$tmpCrypt = gmp_powm($arrayNumbers[$i], $c, $n);
				array_push($arrayResult, $tmpCrypt);
			}
			//Add separator -*/|-|\*-  ---> 45424712445124924245
			$tmpString = '';
			for($i=0; $i<count($arrayResult); ++$i)
			{
				if($i != count($arrayResult)-1)
					$tmpString .= $arrayResult[$i] .'45424712445124924245';
				else
					$tmpString .= $arrayResult[$i];
			}
			$arrayResult = array();
			array_push($arrayResult, $tmpString); 		//<---Crypted
		}
		else if(isset($jobTODO) && $jobTODO == 'decrypt')
		{
			//Remove separators
			$arrayNumbers = explode('45424712445124924245', $arrayNumbers);

			for($i=0; $i<count($arrayNumbers); ++$i)
			{
				$arrayNumbers[$i] = (int) $arrayNumbers[$i];
				$tmpDecrypt = gmp_powm($arrayNumbers[$i], $u, $n);
				$tmpDecrypt = (int) $tmpDecrypt;
				$tmpDecrypt = chr($tmpDecrypt);
				array_push($arrayResult, $tmpDecrypt);
			}
			//Reforming the message
			$tmpString = '';
			for($i=0; $i<count($arrayResult); ++$i)
			{
				$tmpString .= $arrayResult[$i];
			}
			$arrayResult = array();
			array_push($arrayResult, $tmpString);		//Decrypted
		}

		return $arrayResult;
	}

	////////////////////////////////Gathering data
	$numberInit = gmp_init(2);
		$numberInit = gmp_pow($numberInit, 9);
		$P = gmp_nextprime($numberInit);
	$numberLimit = gmp_init(3);
		$numberLimit = gmp_pow($numberLimit, 9);
		$Q = gmp_nextprime($numberLimit);
		//$P = gmp_init(53); $Q = gmp_init(97); 	//Debug
	//////////////////////////////////////////////
		$N = $P*$Q;
		$M = ($P-1)*($Q-1);
		$C = $M + 218;
	//////////////////////////////////////////////
	do {
		++$C;
	} while(gmp_gcd($M, $C) != 1);		//M & C prime between them.

	//$C = 7;		//Debug
	//----------------PUBLIC KEY
	$publicKey = array($N, $C);
	//--------------------------

	// 2 < U < M
	$U = RSA_U($C, $M);
	//----------------PRIVATE KEY
	$privateKey = array($U, $N);
	//---------------------------

	$text = "TRUE";
	$code = Encrypt_Decrypt($text, $C, $N, $U, 'encrypt')[0];


	echo '<br /><br /><br />Encrypted to :<br /><span style="color:red;">'. Encrypt_Decrypt($text, $C, $N, $U, 'encrypt')[0] .'</span>
		  <br /><br /><br />Decrypted to : <br />'. Encrypt_Decrypt($code, $C, $N, $U, 'decrypt')[0] .'<br /><br /><br /><br />';
	

