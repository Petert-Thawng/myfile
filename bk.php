#!/usr/bin/php -q
<?php 

require 'phpagi.php';
require_once("moi_function.php");

$soundpath = "/moi_eth/sounds/";
$newssoundpath = "/var/www/html/moi/voicefiles/";

 $ivr_aftermainivr01 = $soundpath. "moi_AfterResgiterMainIVR01";
 $ivr_beforemainivr01 = $soundpath. "moi_BeforeResgiterMainIVR02";

//$ivr_aftermainivr01 = $soundpath. "moi_after_register";
//$ivr_beforemainivr01 = $soundpath. "moi_before_register";

$ivr_registersuccessful = $soundpath. "moi_registersuccessful";
$ivr_registerfailed = $soundpath. "moi_registerfailed";

$ivr_stoservicesuccessful = $soundpath. "moi_stopservicesucess";

$ivr_howmoiworks = $soundpath. "moi_howmoiworks";
$ivr_notmobile = $soundpath. "moi_notmobile";

$ivr_telenor = $soundpath. "moi_telenor";
$ivr_ooredoo = $soundpath. "moi_ooredoo";


## Eth subscription
$ivr_existingsub = $soundpath. "moi_existingsub";
$ivr_existingMyanmar = $soundpath. "moi_existingMyanmar";


$ivr_main = $soundpath. "moi_main";

$ivr_myanmarsub = $soundpath. "moi_myanmarsub";
$ivr_getmyanmar = $soundpath. "moi_getmyanmar";

$ivr_getreg = $soundpath. "moi_getreg";

$ivr_shansub = $soundpath. "moi_shansub";
$ivr_kachin = $soundpath. "moi_kachin";
$ivr_khaya = $soundpath. "moi_khaya";
$ivr_mon = $soundpath. "moi_mon";
$ivr_rakhine = $soundpath. "moi_rakhine";
$ivr_wha = $soundpath. "moi_wha";

$ivr_chinmain = $soundpath. "moi_chinmain";
$ivr_chinlizo = $soundpath. "moi_chinlizo";
$ivr_chinjoe = $soundpath. "moi_chinjoe";


$ivr_kayinmain = $soundpath. "moi_kayinmain";
$ivr_kayineastpoe = $soundpath. "moi_kayineastpoe";
$ivr_kayinwestpoe = $soundpath. "moi_kayinwestpoe";
$ivr_kayinzagaw = $soundpath. "moi_kayinzagaw";

$ivr_newuser = 'notnew';


$agi = new AGI();
$fromwho = formatPhoneNumber($agi->request['agi_callerid']);			## get callerid
$agi->answer();

## check if it is MPT mobile
if(!preg_match("/^959/", $fromwho)){

	# put Telenor and Ooredoo here as well
	$agi->exec("Background",$ivr_notmobile);
	$agi->hangup();
	exit;
} 

## $agi->exec("Background",$ivr_beforemainivr01);
## If existing customer


if(!checkRegistered_eth($fromwho)){ 

	## Start New Customer
	$registersuccess = 0;
	
	for ($i=0; $i < 4; $i++) { 
		
		if($i == 3){
			$agi->hangup();
			exit;
		}

		$result = $agi->get_data($ivr_chinmain, 5000,1);
		$key = $result['result'];


		if (preg_match("/^95979/", $fromwho) || preg_match("/^95978/", $fromwho)) {
				## Telenor (95979, 95978)
				telecomlog($fromwho, "Telenor subscriber");
				$agi->exec("Background",$ivr_telenor);
				$agi->hangup();
				exit;

			} elseif (preg_match("/^95997/", $fromwho)) {
				
				## Ooredoo 95997ls
				telecomlog($fromwho, "Ooredoo subscriber");
				$agi->exec("Background",$ivr_ooredoo);
				$agi->hangup();
				exit;
		} 

		if ($key == "1" || $key == "2" || $key == "3" || $key == "4" || $key == "5" || $key == "6" || $key == "7" || $key == "8" || $key == "9" )
		{
			activateaccount_eth($fromwho, 'new'); 
			$ivr_newuser = 'new';
			$i = 5;
		
		}

		/*
		#register
		if($key == "1"){

			if (preg_match("/^95979/", $fromwho) || preg_match("/^95978/", $fromwho)) {
				## Telenor (95979, 95978)
				telecomlog($fromwho, "Telenor subscriber");
				$agi->exec("Background",$ivr_telenor);
				$agi->hangup();
				exit;

			} elseif (preg_match("/^95997/", $fromwho)) {
				
				## Ooredoo 95997
				telecomlog($fromwho, "Ooredoo subscriber");
				$agi->exec("Background",$ivr_ooredoo);
				$agi->hangup();
				exit;

			} else {

				$dat = getDateAndTimeNow();
				
				$nowtime = $dat['date'];
				$nowperiod = $dat['time'];
				
				if (purchaseMOI($fromwho,$nowtime,$nowperiod)) $registersuccess = 1;
				if ($registersuccess){
					
					activateaccount($fromwho); 
					$agi->exec("Background",$ivr_registersuccessful);
					$agi->exec("Background",$ivr_howmoiworks);	
					
					break;

				} else {
					$agi->exec("Background",$ivr_registerfailed);
					$agi->hangup();
					exit;
				}
			}
		}

		*/

	}

	
} ## End New customer

#else {

	## Existing Customer (i.e. account = 1)
	for ($i=0; $i < 4; $i++) { 
		
		if($i == 3){
			$agi->hangup();
			exit;
		}

		#main

		if ($ivr_newuser == 'notnew' ) {
			$result = $agi->get_data($ivr_main, 5000,1);
			$key = $result['result'];
		}

		# =========== Myanmar ===========================
		#myanmar
		if($key == "1") {
			#Myanamr

			$subornot = getdetailsub($fromwho);



			if ($subornot['mya'] == 1) {

				#ask replay or unsubscribe
				for ($i=0; $i < 4; $i++) { 
			
					if($i == 3){
						$agi->hangup();
						exit;
					}

					$resultmm = $agi->get_data($ivr_myanmarsub, 5000,1);
					$keymm = $resultmm['result'];

					if ($keymm == '1') {
						#replay myanmar

						#$agi->exec("Background",$moi_telenor);
						$latestnews = getlatestnews_eth('mya');

						if($latestnews){
							for ($ix=0; $ix < 4; $ix++) { 
								if($ix == 3) $agi->hangup();
								
								if(preg_match("/.mp3$/", $latestnews))
									$agi->exec("MP3Player",$newssoundpath.$latestnews);
								else if(preg_match("/.wav$/", $latestnews)){
									$latestnews = preg_replace("/.wav/", "", $latestnews);
									$agi->exec("Background",$newssoundpath.$latestnews);
								}
							}			
							
						}


					} #end reply myanmar


					if ($keymm == '2') {
						#unsub myanmar
						deactivateaccount_eth($fromwho, 'mya');
						$agi->exec("Background",$ivr_stoservicesuccessful);
						$agi->hangup();


					} # end unsub myanmar

				} #3loop

			} #end myanmar
			else {
			# new Myanamr Subscriber

				for ($i=0; $i < 4; $i++) { 
			
					if($i == 3){
						$agi->hangup();
						exit;
					}

					$resultmm = $agi->get_data($ivr_getmyanmar, 5000,1);
					$keymm = $resultmm['result'];
				

					if ($keymm == '1') {


						$dat = getDateAndTimeNow();
				
						$nowtime = $dat['date'];
						$nowperiod = $dat['time'];
						
						if (purchaseMOI_eth($fromwho, 'mya' ,$nowtime,$nowperiod)) $registersuccess = 1;
						if ($registersuccess){
							
							activateaccount_eth($fromwho, 'mya'); 
							$agi->exec("Background",$ivr_registersuccessful);
							$agi->exec("Background",$ivr_howmoiworks);	
							
							break;

						} else {
							$agi->exec("Background",$ivr_registerfailed);
							$agi->hangup();
							exit;
						}

					}

				}

			}

		}
		# =========== End Myanmar ===========================

		# =========== Shan ===========================
		#Shan
		else if($key == "8"){
			#Shan

			$subornot = getdetailsub($fromwho);

			## to change 1
			if ($subornot['shn'] == 1) {

				#ask replay or unsubscribe
				for ($i=0; $i < 4; $i++) { 
			
					if($i == 3){
						$agi->hangup();
						exit;
					}

					## to change 2
					$resultmm = $agi->get_data($ivr_shansub, 5000,1);
					$keymm = $resultmm['result'];

					if ($keymm == '1') {
						#replay shan

						#to change 3
						$latestnews = getlatestnews_eth('shn');

						if($latestnews){
							for ($ix=0; $ix < 4; $ix++) { 
								if($ix == 3) $agi->hangup();
								
								if(preg_match("/.mp3$/", $latestnews))
									$agi->exec("MP3Player",$newssoundpath.$latestnews);
								else if(preg_match("/.wav$/", $latestnews)){
									$latestnews = preg_replace("/.wav/", "", $latestnews);
									$agi->exec("Background",$newssoundpath.$latestnews);
								}
							}				
						}

					} #end reply shan

					if ($keymm == '2') {
						#unsub shan
						deactivateaccount_eth($fromwho, 'shn');
						$agi->exec("Background",$ivr_stoservicesuccessful);
						$agi->hangup();

					} # end unsub shan

				} #3loop

			} #end shan
			else {
			# new shan Subscriber
				for ($i=0; $i < 4; $i++) { 
			
					if($i == 3){
						$agi->hangup();
						exit;
					}

					$resultmm = $agi->get_data($ivr_getreg, 5000,1);
					$keymm = $resultmm['result'];
				

					if ($keymm == '1') {


						$dat = getDateAndTimeNow();
				
						$nowtime = $dat['date'];
						$nowperiod = $dat['time'];
						
						if (purchaseMOI_eth($fromwho, 'shn' ,$nowtime,$nowperiod)) $registersuccess = 1;
						if ($registersuccess){
							
							activateaccount_eth($fromwho, 'shn'); 
							$agi->exec("Background",$ivr_registersuccessful);
							$agi->exec("Background",$ivr_howmoiworks);	
							
							break;

						} else {
							$agi->exec("Background",$ivr_registerfailed);
							$agi->hangup();
							exit;
						}

					}

				}

			}

		}
		# =========== End Shan ===========================

		# =========== Kachin ===========================
		#myanmar
		else if($key == "2"){
			#Myanamr

			$subornot = getdetailsub($fromwho);


			## to change 1
			if ($subornot['kac'] == 1) {

				#ask replay or unsubscribe
				for ($i=0; $i < 4; $i++) { 
			
					if($i == 3){
						$agi->hangup();
						exit;
					}

					## to change 2
					$resultmm = $agi->get_data($ivr_kachin, 5000,1);
					
					$keymm = $resultmm['result'];

					if ($keymm == '1') {
						#replay kachin

						#to change 3
						$latestnews = getlatestnews_eth('kac');

						if($latestnews){
							for ($ix=0; $ix < 4; $ix++) { 
								if($ix == 3) $agi->hangup();
								
								if(preg_match("/.mp3$/", $latestnews))
									$agi->exec("MP3Player",$newssoundpath.$latestnews);
								else if(preg_match("/.wav$/", $latestnews)){
									$latestnews = preg_replace("/.wav/", "", $latestnews);
									$agi->exec("Background",$newssoundpath.$latestnews);
								}
							}				
						}

					} #end reply kachin

					if ($keymm == '2') {
						#unsub shan
						deactivateaccount_eth($fromwho, 'kac');
						$agi->exec("Background",$ivr_stoservicesuccessful);
						$agi->hangup();

					} # end unsub kachin

				} #3loop

			} #end kachin
			else {
			# new kachin Subscriber
				for ($i=0; $i < 4; $i++) { 
			
					if($i == 3){
						$agi->hangup();
						exit;
					}

					$resultmm = $agi->get_data($ivr_getreg, 5000,1);
					$keymm = $resultmm['result'];
				

					if ($keymm == '1') {


						$dat = getDateAndTimeNow();
				
						$nowtime = $dat['date'];
						$nowperiod = $dat['time'];
						
						if (purchaseMOI_eth($fromwho, 'kac' ,$nowtime,$nowperiod)) $registersuccess = 1;
						if ($registersuccess){
							
							activateaccount_eth($fromwho, 'kac'); 
							$agi->exec("Background",$ivr_registersuccessful);
							$agi->exec("Background",$ivr_howmoiworks);	
							
							break;

						} else {
							$agi->exec("Background",$ivr_registerfailed);
							$agi->hangup();
							exit;
						}

					}

				}

			}

		}
		# =========== End Kachin ===========================


		# =========== Kayha ===========================
		#myanmar
		else if($key == "3"){
			#Kayha

			$subornot = getdetailsub($fromwho);


			## to change 1
			if ($subornot['kha'] == 1) {

				#ask replay or unsubscribe
				for ($i=0; $i < 4; $i++) { 
			
					if($i == 3){
						$agi->hangup();
						exit;
					}

					## to change 2
					$resultmm = $agi->get_data($ivr_khaya, 5000,1);
					
					$keymm = $resultmm['result'];

					if ($keymm == '1') {
						#replay Kahya

						#to change 3
						$latestnews = getlatestnews_eth('kha');

						if($latestnews){
							for ($ix=0; $ix < 4; $ix++) { 
								if($ix == 3) $agi->hangup();
								
								if(preg_match("/.mp3$/", $latestnews))
									$agi->exec("MP3Player",$newssoundpath.$latestnews);
								else if(preg_match("/.wav$/", $latestnews)){
									$latestnews = preg_replace("/.wav/", "", $latestnews);
									$agi->exec("Background",$newssoundpath.$latestnews);
								}
							}				
						}

					} #end reply Kahya

					if ($keymm == '2') {
						#unsub shan
						deactivateaccount_eth($fromwho, 'kha');
						$agi->exec("Background",$ivr_stoservicesuccessful);
						$agi->hangup();

					} # end unsub kachin

				} #3loop

			} #end kachin
			else {
			# new kachin Subscriber
				for ($i=0; $i < 4; $i++) { 
			
					if($i == 3){
						$agi->hangup();
						exit;
					}

					$resultmm = $agi->get_data($ivr_getreg, 5000,1);
					$keymm = $resultmm['result'];
				
					if ($keymm == '1') {
						$dat = getDateAndTimeNow();
				
						$nowtime = $dat['date'];
						$nowperiod = $dat['time'];
						
						if (purchaseMOI_eth($fromwho, 'kha' ,$nowtime,$nowperiod)) $registersuccess = 1;
						if ($registersuccess){
							
							activateaccount_eth($fromwho, 'kha'); 
							$agi->exec("Background",$ivr_registersuccessful);
							$agi->exec("Background",$ivr_howmoiworks);	
							
							break;

						} else {
							$agi->exec("Background",$ivr_registerfailed);
							$agi->hangup();
							exit;
						}

					}

				}

			}

		}
		# =========== End Khaya ===========================

		# =========== Mon ===========================
		#Mon
		else if($key == "6"){
			#Mon

			$subornot = getdetailsub($fromwho);


			## to change 1
			if ($subornot['mon'] == 1) {

				#ask replay or unsubscribe
				for ($i=0; $i < 4; $i++) { 
			
					if($i == 3){
						$agi->hangup();
						exit;
					}

					## to change 2
					$resultmm = $agi->get_data($ivr_mon, 5000,1);
					
					$keymm = $resultmm['result'];

					if ($keymm == '1') {
						#replay Mon

						#to change 3
						$latestnews = getlatestnews_eth('mon');

						if($latestnews){
							for ($ix=0; $ix < 4; $ix++) { 
								if($ix == 3) $agi->hangup();
								
								if(preg_match("/.mp3$/", $latestnews))
									$agi->exec("MP3Player",$newssoundpath.$latestnews);
								else if(preg_match("/.wav$/", $latestnews)){
									$latestnews = preg_replace("/.wav/", "", $latestnews);
									$agi->exec("Background",$newssoundpath.$latestnews);
								}
							}				
						}

					} #end reply Mon

					if ($keymm == '2') {
						#unsub shan
						deactivateaccount_eth($fromwho, 'mon');
						$agi->exec("Background",$ivr_stoservicesuccessful);
						$agi->hangup();

					} # end unsub Mon

				} #3loop

			} #end Mon
			else {
			# new Mon Subscriber
				for ($i=0; $i < 4; $i++) { 
			
					if($i == 3){
						$agi->hangup();
						exit;
					}

					$resultmm = $agi->get_data($ivr_getreg, 5000,1);
					$keymm = $resultmm['result'];
				
					if ($keymm == '1') {
						$dat = getDateAndTimeNow();
				
						$nowtime = $dat['date'];
						$nowperiod = $dat['time'];
						
						if (purchaseMOI_eth($fromwho, 'mon' ,$nowtime,$nowperiod)) $registersuccess = 1;
						if ($registersuccess){
							
							activateaccount_eth($fromwho, 'mon'); 
							$agi->exec("Background",$ivr_registersuccessful);
							$agi->exec("Background",$ivr_howmoiworks);	
							
							break;

						} else {
							$agi->exec("Background",$ivr_registerfailed);
							$agi->hangup();
							exit;
						}

					}

				}

			}

		}
		# =========== End Mon ===========================

		# =========== Rakhine ===========================
		#Rakhine
		else if($key == "7"){
			#Rakhine

			$subornot = getdetailsub($fromwho);


			## to change 1
			if ($subornot['rkh'] == 1) {

				#ask replay or unsubscribe
				for ($i=0; $i < 4; $i++) { 
			
					if($i == 3){
						$agi->hangup();
						exit;
					}

					## to change 2
					$resultmm = $agi->get_data($ivr_rakhine, 5000,1);
					
					$keymm = $resultmm['result'];

					if ($keymm == '1') {
						#replay Rakhine

						#to change 3
						$latestnews = getlatestnews_eth('rkh');

						if($latestnews){
							for ($ix=0; $ix < 4; $ix++) { 
								if($ix == 3) $agi->hangup();
								
								if(preg_match("/.mp3$/", $latestnews))
									$agi->exec("MP3Player",$newssoundpath.$latestnews);
								else if(preg_match("/.wav$/", $latestnews)){
									$latestnews = preg_replace("/.wav/", "", $latestnews);
									$agi->exec("Background",$newssoundpath.$latestnews);
								}
							}				
						}

					} #end reply Rakhine

					if ($keymm == '2') {
						#unsub Rakhine
						deactivateaccount_eth($fromwho, 'rkh');
						$agi->exec("Background",$ivr_stoservicesuccessful);
						$agi->hangup();

					} # end unsub Rakhine

				} #3loop

			} #end Rakhine
			else {
			# new Rakhine Subscriber
				for ($i=0; $i < 4; $i++) { 
			
					if($i == 3){
						$agi->hangup();
						exit;
					}

					$resultmm = $agi->get_data($ivr_getreg, 5000,1);
					$keymm = $resultmm['result'];
				
					if ($keymm == '1') {
						$dat = getDateAndTimeNow();
				
						$nowtime = $dat['date'];
						$nowperiod = $dat['time'];
						
						if (purchaseMOI_eth($fromwho, 'rkh' ,$nowtime,$nowperiod)) $registersuccess = 1;
						if ($registersuccess){
							
							activateaccount_eth($fromwho, 'rkh'); 
							$agi->exec("Background",$ivr_registersuccessful);
							$agi->exec("Background",$ivr_howmoiworks);	
							
							break;

						} else {
							$agi->exec("Background",$ivr_registerfailed);
							$agi->hangup();
							exit;
						}

					}

				}

			}

		}
		# =========== End Rakhine ===========================

		# =========== Wa ===========================
		#Wha
		else if($key == "9"){
			#Wha
			$subornot = getdetailsub($fromwho);

			## to change 1
			if ($subornot['wha'] == 1) {

				#ask replay or unsubscribe
				for ($i=0; $i < 4; $i++) { 
			
					if($i == 3){
						$agi->hangup();
						exit;
					}

					## to change 2
					$resultmm = $agi->get_data($ivr_wha, 5000,1);
					
					$keymm = $resultmm['result'];

					if ($keymm == '1') {
						#replay Wha

						#to change 3
						$latestnews = getlatestnews_eth('wha');

						if($latestnews){
							for ($ix=0; $ix < 4; $ix++) { 
								if($ix == 3) $agi->hangup();
								
								if(preg_match("/.mp3$/", $latestnews))
									$agi->exec("MP3Player",$newssoundpath.$latestnews);
								else if(preg_match("/.wav$/", $latestnews)){
									$latestnews = preg_replace("/.wav/", "", $latestnews);
									$agi->exec("Background",$newssoundpath.$latestnews);
								}
							}				
						}

					} #end reply Wha
					if ($keymm == '2') {
						#unsub Rakhine
						deactivateaccount_eth($fromwho, 'wha');
						$agi->exec("Background",$ivr_stoservicesuccessful);
						$agi->hangup();

					} # end unsub Wha

				} #3loop

			} #end Shan
			else {
			# new Wha Subscriber
				for ($i=0; $i < 4; $i++) { 
			
					if($i == 3){
						$agi->hangup();
						exit;
					}

					$resultmm = $agi->get_data($ivr_getreg, 5000,1);
					$keymm = $resultmm['result'];
				
					if ($keymm == '1') {
						$dat = getDateAndTimeNow();
				
						$nowtime = $dat['date'];
						$nowperiod = $dat['time'];
						
						if (purchaseMOI_eth($fromwho, 'wha' ,$nowtime,$nowperiod)) $registersuccess = 1;
						if ($registersuccess){
							
							activateaccount_eth($fromwho, 'wha'); 
							$agi->exec("Background",$ivr_registersuccessful);
							$agi->exec("Background",$ivr_howmoiworks);	
							
							break;

						} else {
							$agi->exec("Background",$ivr_registerfailed);
							$agi->hangup();
							exit;
						}

					}

				}

			}

		}
		# =========== End Wha ===========================



		# =========== Chin Main ===========================
		#Chin Main
		else if($key == "5"){
			#Chin Main
			$resultmm = $agi->get_data($ivr_chinmain, 5000,1);								
			$keymm = $resultmm['result'];


					# =========== Chin (Lizo) ===========================
					#Chin (Lizo)
					if($keymm == "1"){
						#Chin (Lizo)
						$subornot = getdetailsub($fromwho);

						## to change 1
						if ($subornot['ch1'] == 1) {

							#ask replay or unsubscribe
							for ($i=0; $i < 4; $i++) { 
						
								if($i == 3){
									$agi->hangup();
									exit;
								}

								## to change 2
								$resultmm = $agi->get_data($ivr_chinlizo, 5000,1);
								
								$keymm = $resultmm['result'];

								if ($keymm == '1') {
									#replay Chin (Lizo)

									#to change 3
									$latestnews = getlatestnews_eth('ch1');

									if($latestnews){
										for ($ix=0; $ix < 4; $ix++) { 
											if($ix == 3) $agi->hangup();
											
											if(preg_match("/.mp3$/", $latestnews))
												$agi->exec("MP3Player",$newssoundpath.$latestnews);
											else if(preg_match("/.wav$/", $latestnews)){
												$latestnews = preg_replace("/.wav/", "", $latestnews);
												$agi->exec("Background",$newssoundpath.$latestnews);
											}
										}				
									}

								} #end reply Chin (Lizo)
								if ($keymm == '2') {
									#unsub Rakhine
									deactivateaccount_eth($fromwho, 'ch1');
									$agi->exec("Background",$ivr_stoservicesuccessful);
									$agi->hangup();

								} # end unsub Chin (Lizo)

							} #3loop

						} #end Chin (Lizo)
						else {
						# new Chin (Lizo) Subscriber
							for ($i=0; $i < 4; $i++) { 
						
								if($i == 3){
									$agi->hangup();
									exit;
								}

								$resultmm = $agi->get_data($ivr_getreg, 5000,1);
								$keymm = $resultmm['result'];
							
								if ($keymm == '1') {
									$dat = getDateAndTimeNow();
							
									$nowtime = $dat['date'];
									$nowperiod = $dat['time'];
									
									if (purchaseMOI_eth($fromwho, 'ch1' ,$nowtime,$nowperiod)) $registersuccess = 1;
									if ($registersuccess){
										
										activateaccount_eth($fromwho, 'ch1'); 
										$agi->exec("Background",$ivr_registersuccessful);
										$agi->exec("Background",$ivr_howmoiworks);	
										
										break;

									} else {
										$agi->exec("Background",$ivr_registerfailed);
										$agi->hangup();
										exit;
									}

								}

							}

						}

					}
					# =========== End Chin Lizo ===========================

					# =========== Chin (Joe) ===========================
					#Chin Joe
					else if($keymm == "2"){
						#Chin Joe
						$subornot = getdetailsub($fromwho);

						## to change 1
						if ($subornot['ch2'] == 1) {

							#ask replay or unsubscribe
							for ($i=0; $i < 4; $i++) { 
						
								if($i == 3){
									$agi->hangup();
									exit;
								}

								## to change 2
								$resultmm = $agi->get_data($ivr_chinjoe, 5000,1);
								$keymm = $resultmm['result'];

								if ($keymm == '1') {
									#replay Chin Joe

									#to change 3
									$latestnews = getlatestnews_eth('ch2');

									if($latestnews){
										for ($ix=0; $ix < 4; $ix++) { 
											if($ix == 3) $agi->hangup();
											
											if(preg_match("/.mp3$/", $latestnews))
												$agi->exec("MP3Player",$newssoundpath.$latestnews);
											else if(preg_match("/.wav$/", $latestnews)){
												$latestnews = preg_replace("/.wav/", "", $latestnews);
												$agi->exec("Background",$newssoundpath.$latestnews);
											}
										}				
									}

								} #end reply Chin Joe
								if ($keymm == '2') {
									#unsub Chin Joe
									deactivateaccount_eth($fromwho, 'ch2');
									$agi->exec("Background",$ivr_stoservicesuccessful);
									$agi->hangup();

								} # end unsub Chin Joe

							} #3loop

						} #end Chin Joe
						else {
						# new Chin Joe Subscriber
							for ($i=0; $i < 4; $i++) { 
						
								if($i == 3){
									$agi->hangup();
									exit;
								}

								$resultmm = $agi->get_data($ivr_getreg, 5000,1);
								$keymm = $resultmm['result'];
							
								if ($keymm == '1') {
									$dat = getDateAndTimeNow();
							
									$nowtime = $dat['date'];
									$nowperiod = $dat['time'];
									
									if (purchaseMOI_eth($fromwho, 'ch2' ,$nowtime,$nowperiod)) $registersuccess = 1;
									if ($registersuccess){
										
										activateaccount_eth($fromwho, 'ch2'); 
										$agi->exec("Background",$ivr_registersuccessful);
										$agi->exec("Background",$ivr_howmoiworks);	
										
										break;

									} else {
										$agi->exec("Background",$ivr_registerfailed);
										$agi->hangup();
										exit;
									}

								}

							}

						}

					}
					# =========== End Chin Joe ===========================					
		}
		# =========== End Chin Main ===========================




		# =========== Ka Yin Main ===========================
		#Ka Yin Main
		else if($key == "4"){
			#Chin Main
			$resultmm = $agi->get_data($ivr_kayinmain, 5000,1);								
			$keymm = $resultmm['result'];


					# =========== Kayin (SG) ===========================
					# Kayin (SG)
					if($keymm == "1"){
						# Kayin (SG)
						$subornot = getdetailsub($fromwho);

						## to change 1
						if ($subornot['kay1'] == 1) {

							#ask replay or unsubscribe
							for ($i=0; $i < 4; $i++) { 
						
								if($i == 3){
									$agi->hangup();
									exit;
								}

								## to change 2
								$resultmm = $agi->get_data($ivr_kayinzagaw, 5000,1);
								
								$keymm = $resultmm['result'];

								if ($keymm == '1') {
									#replay Kayin (SG)

									#to change 3
									$latestnews = getlatestnews_eth('kay1');

									if($latestnews){
										for ($ix=0; $ix < 4; $ix++) { 
											if($ix == 3) $agi->hangup();
											
											if(preg_match("/.mp3$/", $latestnews))
												$agi->exec("MP3Player",$newssoundpath.$latestnews);
											else if(preg_match("/.wav$/", $latestnews)){
												$latestnews = preg_replace("/.wav/", "", $latestnews);
												$agi->exec("Background",$newssoundpath.$latestnews);
											}
										}				
									}

								} #end reply Kayin (SG)
								if ($keymm == '2') {
									#unsub Kayin (SG)
									deactivateaccount_eth($fromwho, 'kay1');
									$agi->exec("Background",$ivr_stoservicesuccessful);
									$agi->hangup();

								} # end unsub Kayin (SG)

							} #3loop

						} #end Kayin (SG)
						else {
						# new Kayin (SG) Subscriber
							for ($i=0; $i < 4; $i++) { 
						
								if($i == 3){
									$agi->hangup();
									exit;
								}

								$resultmm = $agi->get_data($ivr_getreg, 5000,1);
								$keymm = $resultmm['result'];
							
								if ($keymm == '1') {
									$dat = getDateAndTimeNow();
							
									$nowtime = $dat['date'];
									$nowperiod = $dat['time'];
									
									if (purchaseMOI_eth($fromwho, 'kay1' ,$nowtime,$nowperiod)) $registersuccess = 1;
									if ($registersuccess){
										
										activateaccount_eth($fromwho, 'kay1'); 
										$agi->exec("Background",$ivr_registersuccessful);
										$agi->exec("Background",$ivr_howmoiworks);	
										
										break;

									} else {
										$agi->exec("Background",$ivr_registerfailed);
										$agi->hangup();
										exit;
									}

								}

							}

						}

					}
					# =========== End Kayin (SG) ===========================

					# =========== Kayin (West Poe) ===========================
					#Kayin (West Poe)
					else if($keymm == "2"){
						#Kayin (West Poe)
						$subornot = getdetailsub($fromwho);

						## to change 1
						if ($subornot['kay2'] == 1) {

							#ask replay or unsubscribe
							for ($i=0; $i < 4; $i++) { 
						
								if($i == 3){
									$agi->hangup();
									exit;
								}

								## to change 2
								$resultmm = $agi->get_data($ivr_kayinwestpoe, 5000,1);
								$keymm = $resultmm['result'];

								if ($keymm == '1') {
									#Kayin (West Poe)

									#to change 3
									$latestnews = getlatestnews_eth('kay2');

									if($latestnews){
										for ($ix=0; $ix < 4; $ix++) { 
											if($ix == 3) $agi->hangup();
											
											if(preg_match("/.mp3$/", $latestnews))
												$agi->exec("MP3Player",$newssoundpath.$latestnews);
											else if(preg_match("/.wav$/", $latestnews)){
												$latestnews = preg_replace("/.wav/", "", $latestnews);
												$agi->exec("Background",$newssoundpath.$latestnews);
											}
										}				
									}

								} #end reply Kayin (West Poe)
								if ($keymm == '2') {
									#unsub Kayin (West Poe)
									deactivateaccount_eth($fromwho, 'kay2');
									$agi->exec("Background",$ivr_stoservicesuccessful);
									$agi->hangup();

								} # end unsub Kayin (West Poe)

							} #3loop

						} #end Kayin (West Poe)
						else {
						# new Kayin (West Poe) Subscriber
							for ($i=0; $i < 4; $i++) { 
						
								if($i == 3){
									$agi->hangup();
									exit;
								}

								$resultmm = $agi->get_data($ivr_getreg, 5000,1);
								$keymm = $resultmm['result'];
							
								if ($keymm == '1') {
									$dat = getDateAndTimeNow();
							
									$nowtime = $dat['date'];
									$nowperiod = $dat['time'];
									
									if (purchaseMOI_eth($fromwho, 'kay2' ,$nowtime,$nowperiod)) $registersuccess = 1;
									if ($registersuccess){
										
										activateaccount_eth($fromwho, 'kay2'); 
										$agi->exec("Background",$ivr_registersuccessful);
										$agi->exec("Background",$ivr_howmoiworks);	
										
										break;

									} else {
										$agi->exec("Background",$ivr_registerfailed);
										$agi->hangup();
										exit;
									}

								}

							}

						}

					}
					# =========== End Kayin (West Poe) ===========================		

					# =========== Kayin (East Poe) ===========================
					#Kayin (East Poe)
					else if($keymm == "3"){
						#Kayin (East Poe)
						$subornot = getdetailsub($fromwho);

						## to change 1
						if ($subornot['kay3'] == 1) {

							#ask replay or unsubscribe
							for ($i=0; $i < 4; $i++) { 
						
								if($i == 3){
									$agi->hangup();
									exit;
								}

								## to change 2
								$resultmm = $agi->get_data($ivr_kayineastpoe, 5000,1);
								$keymm = $resultmm['result'];

								if ($keymm == '1') {
									#Kayin (East Poe)

									#to change 3
									$latestnews = getlatestnews_eth('kay3');

									if($latestnews){
										for ($ix=0; $ix < 4; $ix++) { 
											if($ix == 3) $agi->hangup();
											
											if(preg_match("/.mp3$/", $latestnews))
												$agi->exec("MP3Player",$newssoundpath.$latestnews);
											else if(preg_match("/.wav$/", $latestnews)){
												$latestnews = preg_replace("/.wav/", "", $latestnews);
												$agi->exec("Background",$newssoundpath.$latestnews);
											}
										}				
									}

								} #end reply Kayin (East Poe)
								if ($keymm == '2') {
									#unsub Kayin (East Poe)
									deactivateaccount_eth($fromwho, 'kay3');
									$agi->exec("Background",$ivr_stoservicesuccessful);
									$agi->hangup();

								} # end unsub Kayin (East Poe)

							} #3loop

						} #end Kayin (East Poe)
						else {
						# new Kayin (East Poe) Subscriber
							for ($i=0; $i < 4; $i++) { 
						
								if($i == 3){
									$agi->hangup();
									exit;
								}

								$resultmm = $agi->get_data($ivr_getreg, 5000,1);
								$keymm = $resultmm['result'];
							
								if ($keymm == '1') {
									$dat = getDateAndTimeNow();
							
									$nowtime = $dat['date'];
									$nowperiod = $dat['time'];
									
									if (purchaseMOI_eth($fromwho, 'kay3' ,$nowtime,$nowperiod)) $registersuccess = 1;
									if ($registersuccess){
										
										activateaccount_eth($fromwho, 'kay3'); 
										$agi->exec("Background",$ivr_registersuccessful);
										$agi->exec("Background",$ivr_howmoiworks);	
										
										break;

									} else {
										$agi->exec("Background",$ivr_registerfailed);
										$agi->hangup();
										exit;
									}

								}

							}

						}

					}
					# =========== End Kayin (West Poe) ===========================		


		}
		# =========== End Kayin Main ===========================

	}
#} ## End Existing Customer

exit;
?>

