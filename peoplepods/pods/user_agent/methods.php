<?
		
	PeoplePod::registerMethod('isMobileUser');
        PeoplePod::registerMethod('browserIs');
	PeoplePod::registerMethod('userAgent');
        PeoplePod::registerMethod('chooseTheme');

        //returns true is the user is on a mobile device, else returns false
        function isMobileUser($POD) {

           $useragent= $_SERVER['HTTP_USER_AGENT'];

            if(preg_match("/mobile/i", $useragent)){
                    $detect= true;
            }
            else $detect= false;

            return $detect;
	}

        //userAgent returns an associative array with the device type and
        //browser type of the user
        //device type may be 'undetermined', 'mobile, or 'desktop'
        //browsers returned are relevant to the device type
        function userAgent($POD) {
            
            $useragent= $_SERVER['HTTP_USER_AGENT'];
            $mobile_browsers= array('iphone', 'ipad', 'blackberry', 'android', 'iemobile');
            $desktop_browers= array('chrome', 'firefox', 'windows', 'opera');
            
            $browser_info   = array();
            $browser['device'] = 'undetermined';
            $browser['browser']= 'undetermined';
            
            if($POD->isMobileUser()){

                $browser_info['device']= 'mobile';

                foreach($mobile_browsers as $device){

                    $search= "/".$device."/i";
                     
                     if(preg_match($search, $useragent)){

                        $browser_info['browser']= $device;
                        return $browser_info;
                        exit;
                     }
                }
                //if browser was not determined then return undetermined browser_info
                return $browser_info;
            }
            else{

                $browser_info['device']= 'desktop';

                foreach($desktop_browers as $browser){
                    $search= "/".$browser."/i";

                    if(preg_match($search, $useragent)){
                        $browser_info['browser']= $browser;
                        return $browser_info;
                        exit;
                    }
                }
                
                //safari is repeated in several browsers so only search for safari
                //by name after checking the other options
                if(preg_match('/safari/i', $useragent)){
                    $browser_info['browser']= 'safari';
                    return $browser_info;
                }
                else{
                    //this is the case where no browser was determined
                    return $browser_info;
                }
            }
      }

      //this function accepts a query for a browser or device and returns a
      //boolean value indicating whether or not the browser or device is present
      //
      //if no arguments are passed then it returns the browser_info
      function browserIs($POD, $device= null, $browser= null){

        $info= $POD->userAgent();

        if($browser && $device){

            if($browser== $info['browser'] && $device== $info['device']){
                return true;
            }
            else return false;
        }
        else if($browser){
            if($browser== $info['browser']){
                return true;
            }
            else return false;
        }
        else if($device){
            if($device== $info['device']){
                return true;
            }
            else return false;
        }
        else{
            //in the case that no args are sent treat function as a query for
            //browser_info
            return $info;
        }
      }


      //accepts an array of themes in order of precedence
      //exp: array('mobile', 'chrome', 'windows', 'opera')
      //    if browser is 'mobile' then choose mobile theme, else choose 'chrome', else 'choose'...
      function chooseTheme($POD, $browsers) {

        $info= $POD->browserIs();
        $stop= false;

        foreach($browsers as $option){

            if($info['device']== $option || $info['browser']== $option){

                if($stop== false){

                    if($POD->doesThemeExist($option)){
                       $POD->changeTheme($option);
                       $stop= true;
                    }
                }
            }
        }
      }
