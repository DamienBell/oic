<? 
/***********************************************
* This file is part of PeoplePods
* (c) xoxco, inc  
* http://peoplepods.net http://xoxco.com
*
* core_files/index.php
* Handles simple requests to /download
* Allows files to be downloaded with their original file name
*
* Documentation for this pod can be found here:
* http://peoplepods.net/readme
/**********************************************/

	

	include_once("../../PeoplePods.php");
	error_reporting(0);
	$POD = new PeoplePod(array('authSecret'=>@$_COOKIE['pp_auth'], 'lockdown'=>null));

        $themeOptions= array('desktop', 'chrome', 'mobile');
        //$themeOptions= $POD->browserIs();
        $POD->chooseTheme($themeOptions);
        //$POD->chooseTheme($POD->browserIs());
        $POD->header();

        echo "<h2>Testing user_agent POD</h2>";
        
        $useragent= $_SERVER['HTTP_USER_AGENT'];
        echo $useragent."<br /><br />";

        $device= $POD->browserIs();
    
        echo "testing ->browserIs() ";
        print_r($device);
        echo "<br /><br />";

        if ($POD->browserIs('desktop')){
            echo "you're on a desktop<br />";
        }
        else echo "you're not using a desktop, your browsing device is ".$device['device']."<br />";

        if($POD->browserIs(null, 'chrome')){
            echo "you're using chrome<br />";
        }
        else{
            echo "you're not using chrome, you're using ".$device['browser']."<br />";
        }
        if($POD->browserIs('desktop', 'chrome')){
            echo "you're on a desktop using chrome<br /><br />";
        }
        else {
            echo "You're not on a desktop using chrome. You're on a ".$device['device']." using ".$device['browser']."<br /><br />";
        }

        $tester= $POD->doesThemeExist('chrome');

        if($tester){
            echo "it exists";
        }
       
        
        $POD->footer();
        ?>
