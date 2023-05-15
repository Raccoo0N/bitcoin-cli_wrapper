<?php
	class BTC extends Singleton { 
		public $dbo; 
		public $login; 
		public $password; 
		public $host; 
		public $port; 
		public $agent; 
		public $wallet; 
		public static $trace; 
//
//===================================
		public function __construct( $d=array() ){
			$this->dbo = DBO::getInstance(); 
			$this->login = "";        // bitcoind login
			$this->password = "";     // bitcoind password
			$this->host = "";         // localhost
			$this->port = "";         // bitcoind port
			$this->agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"; 
			$this->wallet = "";       // 
			self::$trace = false; 
		}
//		
//-----------------------------------------------------
		    public static function getInstance( $c=null, $name="", $params=array() ){
            return parent::getInstance( $c ? $c : __CLASS__ );
        }
//
//===================================
        public function trace( $state=false ){
        	self::$trace = $state; 
        	if( self::$trace ){ echo "trace switched on\n"; } 
        	else { echo "trace switched off\n"; }
        }
//
//===================================
		public function exec( $url="", $params=array() ){ 
			//echo "process blockchain: \n";
			$headers = array( "Content-type: application/json", "Accept-Language: ru_RU" ); 
			$fields = array(
				'jsonrpc'=> "1.0", 
				'method'=> $url, 
				'params'=> $params
			); 
			$fields = json_encode( $fields );
			$endpoint = "http://". $this->login .":". $this->password ."@". $this->host .":". $this->port ."/";

			if( self::$trace ){ echo "endpoint: ". $endpoint ."\n". "fields: \n\t". $fields ."\n"; }

			$ch = curl_init( $endpoint );
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, TRUE);
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			curl_setopt( $ch, CURLOPT_VERBOSE, false );
			curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true ); 
			//curl_setopt( $ch, CURLOPT_STDERR, $fp ); 
			curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers ); 
			curl_setopt( $ch, CURLOPT_POST, 1 ) ;
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields ); 
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		    curl_setopt( $ch, CURLOPT_USERAGENT, $this->agent );
		    $Result = curl_exec($ch);  
		    $info = curl_getinfo( $ch ); 
        // $status = $info['http_code'] 
		    curl_close( $ch );  

			$json = json_decode( $Result, 1, 1024 ); 
			
			return $json; 
		} 
//
//===================================
		public function balance(){
			return $this->exec("getbalance", array(), 1);
		}
//
//===================================
		public function balances(){
			return $this->exec("getbalances", array(), 1);
		}
//
//===================================
//
// WALLETS 
//
//===================================
		public function createwallet( $name="" ){
			return $this->exec("createwallet", array($name));
		} 
//
//===================================
		public function getwalletinfo(){
			return $this->exec("getwalletinfo", array());
		}
//
//===================================
		public function loadwallet( $wallet="" ){ 
			$wallet = $wallet ? preg_replace('/[^A-Za-z0-9]/', '', $wallet ) : $this->wallet;
			return $this->exec("loadwallet", array($wallet)); 
		}
//
//===================================
		public function unloadwallet( $wallet="" ){ 
			$wallet = $wallet ? preg_replace('/[^A-Za-z0-9]/', '', $wallet ) : $this->wallet;
			return $this->exec("unloadwallet", array($wallet)); 
		}
//
//===================================
		public function listwallets(){
			return $this->exec("listwallets", array());
		}
//
//===================================
		public function listlabels(){
			return $this->exec( "listlabels", array() );
		}
//
//===================================
		public function listwalletdir(){
			return $this->exec( "listwalletdir", array() );
		}
//
//===================================
		public function getreceivedbylabel( $wallet="" ){ 
			$wallet = $wallet ? preg_replace('/[^A-Za-z0-9]/', '', $wallet ) : $this->wallet;
			return $this->exec("getreceivedbylabel", array( $wallet ) );
		}
//
//===================================
		public function dumpwallet( $wallet="" ){ 
			$wallet = $wallet ? preg_replace('/[^A-Za-z0-9]/', '', $wallet ) : $this->wallet;
			return $this->exec( "dumpwallet", array( $wallet ) );
		}
//
//===================================
		public function backupwallet( $destination="" ){
			return $this->exec( "backupwallet" , array( $destination ) );
		}
//
//===================================
		public function encryptwallet( $pass="" ){
			return $this->exec( "encryptwallet", array( $pass ) );
		}
//
//===================================
		public function walletpassphrase( $pass="", $timeout=60 ){
			return $this->exec( "walletpassphrase", array( $pass, (int)$timeout ) );
		}
//
//===================================
		public function walletpassphrasechange( $old="", $new="" ){
			return $this->exec( "walletpassphrasechange", array( $old, $new ) );
		}
//
//===================================

//
//===================================

//
// ADDRESS
//
//===================================
		public function getnewaddress( $label="", $type="p2sh-segwit" ){
			return $this->exec("getnewaddress", array($label, $type));
		}
//
//===================================
		public function setlabel( $address="", $label="" ){
			return $this->exec( "setlabel", array( preg_replace('/[^A-Za-z0-9]/', '', $address ), $label ) );
		}
//
//===================================
		public function importprivkey( $key="" ){
			return $this->exec( "importprivkey", array( $key ) );
		}
//
//===================================
		public function dumpprivkey( $address="" ){
			return $this->exec( "dumpprivkey", array( preg_replace('/[^A-Za-z0-9]/', '', $address ) ) ); 
		}
//
//===================================
		public function getaddressinfo( $address="" ){
			return $this->exec( "getaddressinfo", array( preg_replace('/[^A-Za-z0-9]/', '', $address ) ) ); 
		}
//
//===================================
		public function getaddressesbylabel( $label="" ){
			return $this->exec( "getaddressesbylabel", array( $label ) ); 
		}
//
//===================================
		public function listaddressgroupings(){
			return $this->exec( "listaddressgroupings", array() ); 
		}
//
//===================================
		public function listreceivedbylabel(){
			return $this->exec( "listreceivedbylabel", array() );
		}
//
//===================================
		public function listreceivedbyaddress( $var1=1, $var2=true ){
			return $this->exec( "listreceivedbyaddress", array( $var1, $var2 ) ); 
		}
//
//===================================
		public function listtransactions(){
			return $this->exec( "listtransactions", array() ); 
		}
//
//===================================
		public function send( $address="", $amount=0 ){ 
			$params = array(); 
			$var1 = array(); 
			$var1[ preg_replace('/[^A-Za-z0-9]/', '', $address ) ] = (float)$amount;  
			array_push( $params, $var1 );
			return $this->exec( "send", $params ); 
		}
//
//===================================
		public function sendtoaddress( $address="", $amount=0, $comment="", $comment_to="", $fee=false ){
			return $this->exec( "sendtoaddress", array( preg_replace('/[^A-Za-z0-9]/', '', $address ), (float)$amount, $comment, $comment_to, $fee ) );
		}
//
//===================================

//
//===================================
		public function gettransaction( $tid="" ){
			return $this->exec( "gettransaction", array( preg_replace('/[^A-Za-z0-9]/', '', $tid) ) );
		}
//
//===================================

//
//===================================
	}
//
//
//
