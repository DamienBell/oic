<?php 

$POD->registerPOD("gonzo_shops",'Show shops pages',
            array("^shops$"=>"gonzo_shops/index.php",
                  "^shops/(.*)/(.*)"=>'gonzo_shops/group.php?stub=$1&command=$2',
                  "^shops/(.*)"=>'gonzo_shops/group.php?stub=$1'
                ),
            array('groupPath'=>'shops'),
            dirname(__FILE__).'/methods.php' 
        );

?>