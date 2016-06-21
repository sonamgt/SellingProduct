<?php

namespace App;
use App\UserRecord;
use Illuminate\Support\Facades\Mail;

/**
 * this class is used for sending mail and sms to the users 
 * and save their records in DB using Eloquent Model.
 * @author sonam
 *
 */

class Helper  {
	
	private $_UserRecord;
	
	// creating instance of class responsible for table in database.
	function __construct() {
		$this->_UserRecord	= 	new UserRecord();
	}
	
	function sendMessage($data) {
		if(isset($data['EmailID'])) {
			foreach ($data['EmailID'] as $val){
					list($emailID,$link)	= explode("||",$val);
 					if ($this->isValidEmail($emailID)) {
 								$messge		=	"It seems You are interested in buying".$link." this product";
 						
	 						//mail function for sending mail.
	  						Mail::send('email.contact',['testVar'=>$link],function($message)use ($emailID,$messge) {
	  						$message->to($emailID)->from('senderklk@gmail.com','Sender klk')->subject($messge);
	  						});
	  						
	  						//inserting data in db.
	 						$this->insertData($emailID, $messge)	;
					}
				}
			}
  		if(isset($data['MobileNum'])) {
				foreach ($data['MobileNum']as $num) {
					list($mobile,$link) = explode("||",$num);
					//checking for mobile number containing 10 or 12 digits.
					if(strlen($mobile)==10 ||strlen($mobile)==12){
						$messge	=	"it seems you are interested in buying".$link."  product";
						$this->sendSMS($num,$link);
						$this->insertData($num, $messge);
					}
				}
  	    	}
		}
	
	function sendSMS($number,$message) {
		
	}
	
	function insertData($data,$messge) {
		$this->_UserRecord						= 		new UserRecord();
		$this->_UserRecord->user_contacts	 	=   	$data;
		$this->_UserRecord->message				=		$messge;
		$this->_UserRecord->save();
	}
	
	function deleteData(){
		$users  = $this->_UserRecord->all();
		foreach ($users as $user){
			$user->delete();
		}
		
	}
	
	function isValidEmail($email){
		return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
	}
}