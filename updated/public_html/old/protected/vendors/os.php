<?php

//http://www.geekpedia.com/code47_Detect-operating-system-from-user-agent-string.html

class OS
{
	public function getOS($userAgent="")
    {
        $os='';
          
		if (!$userAgent && !isset($_SERVER['HTTP_USER_AGENT']))
            return $os;
          
		if (!$userAgent)
			$userAgent = $_SERVER['HTTP_USER_AGENT'];
            
		$OSList = array
        (
                  // Match user agent string with operating systems
                'Windows 3.11' => 'Win16',
                'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
                'Windows 98' => '(Windows 98)|(Win98)',
                'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
                'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
                'Windows Server 2003' => '(Windows NT 5.2)',
                'Windows Vista' => '(Windows NT 6.0)',
                'Windows 7' => '(Windows NT 7.0)|(Windows NT 6.1)',
                'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
                'Windows ME' => 'Windows ME',
                'Windows CE' => 'windows ce',
                'Windows (version unknown)' => 'windows',
                'OpenBSD' => 'openbsd',
                'SunOS' => 'sunos',
                'Ubuntu' => 'ubuntu',
                'Linux' => '(linux)|(x11)',
                'Mac OSX Beta (Kodiak)' => 'mac os x beta',
                'Mac OSX Cheetah' => 'mac os x 10.0',
                'Mac OSX Puma' => 'mac os x 10.1',
                'Mac OSX Jaguar' => 'mac os x 10.2',
                'Mac OSX Panther' => 'mac os x 10.3',
                'Mac OSX Tiger' => 'mac os x 10.4',
                'Mac OSX Leopard' => 'mac os x 10.5',
                'Mac OSX Snow Leopard' => 'mac os x 10.6',
                'Mac OSX Lion' => 'mac os x 10.7',
                'Mac OSX (version unknown)' => 'mac os x',
                'Mac OS (classic)' => '(mac_powerpc)|(macintosh)',
                'QNX' => 'QNX',
                'BeOS' => 'beos',
                'OS2' => 'os/2',
                'Search Bot'=>'(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(MSNBot)|(Ask Jeeves/Teoma)|(ia_archiver)|(spider)|(bingbot)|(YandexBot)|(Java/1.)|(bot)'
		);
        // Loop through the array of user agents and matching operating systems
        foreach($OSList as $CurrOS=>$Match)
        {
            // Find a match
        	if (@eregi($Match, $userAgent))
            {
            	// We found the correct match
                $os = $CurrOS;
                break;
			}
		}
          
        return $os;          
	} 
  
}

