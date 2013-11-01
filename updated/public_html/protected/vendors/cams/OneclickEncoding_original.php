<?php
//
// $Id: OneclickEncoding.php,v 1.2 2011-07-12 23:15:23 calebg Exp $
//
// Copyright (c) 2006 Accretive Technology Group, Inc.  All rights reserved.
// For use only by Accretive Technology Group, its employees and contractors.
// DO NOT DISTRIBUTE.
//
// This library is used to encode and decode the email and password parameters
// for oneclick users. For increased user security, a user's email and password
// SHOULD NOT be passed in plaintext mode over insecure HTTP channels. These are
// time-based values and you must re-generate them every time a link is
// constructed.
//
// It is extremely important to use this library as stated. If the user's email
// address and password are incorrectly encoded, user account generation will
// not be completed because the receiving script will not be able to accurately
// decode the given parameters.
//
// THIS LIBRARY was written for PHP Version 4 or greater.
//
// NOTE- THIS LIB IS SHARED WITH AFFILIATES.  ONLY PUT GENERIC FUNCTIONS
// NOTE- HERE THAT ARE OF INTEREST TO BOTH PARTIES.
//

//-----------------------------------------------------------
//	FUNCTION:
//		EncodeURLParameters
//
//	PURPOSE:
//		To encode user email addresses and passwords in a time-based, secure
//		format.
//
//	PARAMETERS:
//		$email - (required)
//			the user's email address
//
//		$password - (required)
//			the user's password
//
//		$authkey - (required)
//			an authentication key specific to each affliate
//
//		$makeassoc - (optional)
//			pass in this parameter as true if you want override the default behavior
//			and return an	associative array of values
//
//		$urlencode - (optional)
//			pass as true so the encrypted email and password are also urlencoded 
//
//	RETURNS:
//		The default behavior is to return a url-encoded query string, for example:
//			chksm=48BDA84&email=A5Y8%25KLIV5qn&pwd=yj-61&chksmv=1.5
//
//		Otherwise, if $makeassoc was passed in as true, this function
//		returns a url-encoded associative array with 3 items:
//			a checksum value, the new email value, and the new password value.
//
//	USAGE:
//		Function call for a query string:
//		$newvalues = EncodeURLParameters(
//								'someone@yahoo.com', 'password12345', 'authkey0983' );
//
//		Function call for an array:
//			$newvalues = EncodeURLParameters(
//									'someone@yahoo.com', 'password12345', 'authkey0983', 1 );
//
//	EXAMPLE:
//		require_once( '/your/library/path/OneclickEncoding.php' );
//
//		$useremail = 'myemail@mysite.com';
//		$userpassword = 'mypassword';
//		$siteauthkey = 'authkey0983';
//
//		$result = EncodeURLParameters( $useremail, $userpassword, $siteauthkey );
//
//		header( 'Location: http://your/oneclick/url/oneclick.php?'
//						. 'rid=99&site=1&pusr=paycomusername&mid=123456&co_code=n51&'
//						. 'pi_code=smn51s2&' . $result );
//		exit();
//
//-----------------------------------------------------------

function EncodeURLParameters( $email, $password, $authkey, $makeassoc = false, $urlencode = true )
{
	$result = array ( 'chksm' => '', 'email'  => '', 'pwd' => '', 'chksmv' => '' );
	$email = strtolower($email);
	$ver = '1.5';

	$result['chksm'] = OneclickMD5Checksum( $authkey, $email, $password );
	$result['email'] = OneclickEncode( $email, true, false, $ver );
	$result['pwd']   = OneclickEncode( $password, true, false, $ver );
	$result['chksmv']  = $ver;
	
	if ( $urlencode )
	{
	  $result['email'] = urlencode( $result['email'] );
	  $result['pwd']   = urlencode( $result['pwd'] );
	}

	if ( $makeassoc )
		return $result;

	$link = '';
	foreach( $result as $k => $v )
		$link .= "$k=$v&";

	return rtrim( $link, '&' );
}



















//*************************************************************
//*************************************************************
//*	THE FOLLOWING FUNCTIONS ARE FOR INTERNAL USE ONLY					*
//*************************************************************
//*************************************************************


function OneclickEncode( $str, $encode = true, $mday = false, $ver = 0 )
{
  if( $mday === false )
    $mday = gmdate( 'j' );	// month day, no leading zeros

  $x	= '' ;

  for( $i = 0 ; $i < strlen( $str ) ; $i++ )
  {
  	$shift = intval( ( $mday + $i ) * ( $encode ? 1 : -1 ) / 2 );
  	$orgval = translateChar( $str{$i}, false, $ver );

  	if ( $orgval === false )
  	{
  		$x .= $str{$i};
  		continue ;
  	}

    if ( ($newval = translateChar( ($orgval+$shift), true, $ver )) !== false )
    	$x .= $newval;
    else $x .= $str{$i};
  }

  return $x ;
}


function OneclickMD5Checksum( $authkey, $email, $pass, $timekey = false )
{
  if( $timekey === false )
    $timekey = gmdate( 'dz' );	// Month day and year day

  $str	= ":" . $authkey . ":"
	. "<" . $email   . ">"
	. "[" . $pass    . "]"
	. "{" . $timekey . "}" ;

  return md5( $str );
}

function translateChar( $c, $tostr, $ver )
{
	$enclist = array (
	  64 => '.', 65 => 't', 66 => '@', 67 => 'x', 68 => 'c', 69 => 'O',
	  70 => '1', 71 => '!', 72 => 'A', 73 => '#', 74 => '4', 75 => '%',
	  76 => 'i', 77 => 'q', 78 => 'L', 79 => 'I', 80 => 's', 81 => '3',
	  82 => 'T', 83 => 'M', 84 => '^', 85 => '8', 86 => '&', 87 => 'v',
	  88 => 'w', 89 => 'n', 90 => 'f', 91 => 'm', 92 => 'r', 93 => '*',
	  94 => 'G', 95 => '-', 96 => 'U', 97 => 'Y', 98 => 'J', 99 => '2',
	  100 => ',', 101 => 'k', 102 => '0', 103 => 'V', 104 => '+', 105 => 'X',
	  106 => '_', 107 => 'D', 108 => 'a', 109 => 'C', 110 => '7', 111 => 'E',
	  112 => 'd', 113 => 'B', 114 => 'R', 115 => 'h', 116 => 'z', 117 => 'g',
	  118 => 'K', 119 => 'y', 120 => 'Z', 121 => 'e', 122 => 'u', 123 => 'H',
	  124 => 'b', 125 => 'o', 126 => 'N', 127 => '9', 128 => '$', 129 => 'j',
	  130 => '6', 131 => 'p', 132 => 'F', 133 => '5', 134 => 'P', 135 => 'l',
	  136 => 'W', 137 => 'Q', 138 => 'S',
	);

	$maxval = max( array_keys( $enclist ) );
	$minval = min( array_keys( $enclist ) );

	if ( $tostr )
	{
		if ( isset($enclist[$c]) ) return $enclist[$c];

		// loop around to avoid going off the end of the array
		$diff = $c - $maxval;
		$testval = $minval + $diff;
		if ( $ver == '1.5' ) $testval = $testval - 1;
		if ( $diff > 0 && isset( $enclist[$testval] ) )
			return $enclist[$testval];

		// loop backwards to the end
		$diff = $minval - $c;
		$testval = $maxval - $diff;
		if ( $ver == '1.5' ) $testval = $testval + 1;
		if ( $diff > 0 && isset( $enclist[$testval] ) )
			return $enclist[$testval];

		return false;
	}
	else
	{
		$declist = array_flip($enclist);
		if ( isset($declist[$c]) ) return $declist[$c];

		return false;
	}
}

?>
