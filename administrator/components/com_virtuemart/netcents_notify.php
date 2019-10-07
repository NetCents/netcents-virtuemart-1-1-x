<?php

    function getRequestIP() {
        $ipaddress = 'UNKNOWN';
        $keys=array('HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR','HTTP_X_FORWARDED','HTTP_FORWARDED_FOR','HTTP_FORWARDED','REMOTE_ADDR');
        foreach($keys as $k)
        {
            if (isset($_SERVER[$k]) && !empty($_SERVER[$k]) && filter_var($_SERVER[$k], FILTER_VALIDATE_IP))
            {
                $ipaddress = $_SERVER[$k];
                break;
            }
        }
        return $ipaddress;
    }

    if (isset($_POST["data"])) {
        global $mosConfig_absolute_path, $mosConfig_live_site, $mosConfig_lang, $database,
        $mosConfig_mailfrom, $mosConfig_fromname;
    
        /*** access Joomla's configuration file ***/
        $my_path = dirname(__FILE__);
        
        if( file_exists($my_path."/../../../configuration.php")) {
            $absolute_path = dirname( $my_path."/../../../configuration.php" );
            require_once($my_path."/../../../configuration.php");
        }
        elseif( file_exists($my_path."/../../configuration.php")){
            $absolute_path = dirname( $my_path."/../../configuration.php" );
            require_once($my_path."/../../configuration.php");
        }
        elseif( file_exists($my_path."/configuration.php")){
            $absolute_path = dirname( $my_path."/configuration.php" );
            require_once( $my_path."/configuration.php" );
        }
        else {
            die( "Joomla Configuration File not found!" );
        }
        
        $absolute_path = realpath( $absolute_path );
        
        // Set up the appropriate CMS framework
        if( class_exists( 'jconfig' ) ) {
			define( '_JEXEC', 1 );
			define( 'JPATH_BASE', $absolute_path );
			define( 'DS', DIRECTORY_SEPARATOR );
			
			// Load the framework
			require_once ( JPATH_BASE . DS . 'includes' . DS . 'defines.php' );
			require_once ( JPATH_BASE . DS . 'includes' . DS . 'framework.php' );

			// create the mainframe object
			$mainframe = & JFactory::getApplication( 'site' );
			
			// Initialize the framework
			$mainframe->initialise();
			
			// load system plugin group
			JPluginHelper::importPlugin( 'system' );
			
			// trigger the onBeforeStart events
			$mainframe->triggerEvent( 'onBeforeStart' );
			$lang =& JFactory::getLanguage();
			$mosConfig_lang = $GLOBALS['mosConfig_lang']          = strtolower( $lang->getBackwardLang() );
			// Adjust the live site path
			$mosConfig_live_site = str_replace('/administrator/components/com_virtuemart', '', JURI::base());
			$mosConfig_absolute_path = JPATH_BASE;
        } else {
        	define('_VALID_MOS', '1');
        	require_once($mosConfig_absolute_path. '/includes/joomla.php');
        	require_once($mosConfig_absolute_path. '/includes/database.php');
        	$database = new database( $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix );
        	$mainframe = new mosMainFrame($database, 'com_virtuemart', $mosConfig_absolute_path );
        }

        // load Joomla Language File
        if (file_exists( $mosConfig_absolute_path. '/language/'.$mosConfig_lang.'.php' )) {
            require_once( $mosConfig_absolute_path. '/language/'.$mosConfig_lang.'.php' );
        }
        elseif (file_exists( $mosConfig_absolute_path. '/language/english.php' )) {
            require_once( $mosConfig_absolute_path. '/language/english.php' );
        }
         /*** END of Joomla config ***/
    
    
        /*** VirtueMart part ***/        
        require_once($mosConfig_absolute_path.'/administrator/components/com_virtuemart/virtuemart.cfg.php');
        include_once( ADMINPATH.'/compat.joomla1.5.php' );
        require_once( ADMINPATH. 'global.php' );
        require_once( CLASSPATH. 'ps_main.php' );
        
        /* @MWM1: Logging enhancements (file logging & composite logger). */
        $vmLogIdentifier = "notify.php";
        require_once(CLASSPATH."Log/LogInit.php");
		global $vmLogger;
		 
        /* Load the NetCents Configuration File */ 
        require_once( CLASSPATH. 'payment/ps_netcents.cfg.php' );
        
	    // Constructor initializes the session!
        $sess = new ps_session();             
	    if (gethostbyname("www.net-cents.com") == getRequestIP()) {
            $signature = $_POST['signature'];
            $data = $_POST['data'];
            $signing = $_POST['signing'];
            $exploded_parts = explode(",", $signature);
            $timestamp_explode = explode("=", $exploded_parts[0]);
            $timestamp = intval($timestamp_explode[1]);
            $signature_explode = explode("=", $exploded_parts[1]);
            $signature = $signature_explode[1];
            $decoded_data = json_decode(base64_decode(urldecode($data)));
            $hashable_payload = $timestamp . '.' . $data;
            $hash_hmac = hash_hmac("sha256", $hashable_payload, $signing);
            $timestamp_tolerance = 120;
            $date = new DateTime();
            if ($hash_hmac == $signature && ($current_timestamp - $timestamp) / 60 < $timestamp_tolerance) {
                $order_id = intval($decoded_data->external_id);
                $qv = "SELECT order_id, order_number, user_id FROM jos_vm_orders WHERE order_id='" . $order_id . "'";
                $dbbt = new ps_DB;
                $dbbt->query($qv);
                if ($dbbt->next_record()) {
                    $d['order_id'] = $dbbt->f("order_id");
                    $d['notify_customer'] = "Y";
                    if ($decoded_data->transaction_status == 'overpaid' || $decoded_data->transaction_status == 'underpaid') {
                        $d['order_status'] = "X";
                    } 
                    if ($decoded_data->transaction_status == 'paid') {
                        $d['order_status'] = "C";
                    }
                    require_once ( CLASSPATH . 'ps_order.php' );
                    $ps_order= new ps_order;
                    $ps_order->order_status_update($d);
                }
            }
        }
    }
?>