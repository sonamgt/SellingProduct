<?php

namespace App\Http\Controllers;
use Vinkla\Instagram\Facades\Instagram;
use App\Helper;

/**
 * this class is used for fetching comments from posts using instagram API 
 * and after extracting emailID and mobile- num from comments, it passes data to helper class.
 * @author sonam
 *
 */
class InstaController extends Controller {
	
	private $_helper;
	
	function __construct() {
		$this->_helper	= 	new Helper();
	}
	
	
	function getCommentData() {
		$comments		= array();
		// get all recent media posted by user,in my case used self.
	    $totalPro		= Instagram::users()->getMedia('self');
		$jsonData 		= json_encode($totalPro->get());
		$arrMediaData 	= json_decode($jsonData,true);
		foreach ($arrMediaData as $key=>$value) {
			$arr[$value['id']]	=	$value['link'];
		}
	
	foreach ($arr as $key=>$value) {
		
		// get comments of all media by given media ID's
		$cData		=	Instagram::comments()->get(intval($key));
		$cjsonD		=   json_encode($cData->get());
		$cData      =   json_decode($cjsonD,true);
		foreach ($cData as $arKey=> $arrVal) {
			$arrVal['link']	=	$value;
			$ComData[]		=	$arrVal;
		}
	}
	
	foreach ($ComData as $kval	=>	$value) {
		if(!empty($value)) {
			foreach ($value as $key => $valArray) {
				// comments array will have all comments of all media and will contain the product link .
				$comments[$value['created_time']]	=	$value['text']."||".$value['link'];
			}
		}
	}
	
	foreach ($comments as $key=>$str ) {
		// pattern for fetching emailID.
  		$pattern 		= 	'/([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' .
  		'(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)/i';
 
  		// pattern for fetching mobile num.
 		$patternMob		  =	 "^(?:(?:\+|0{0,2})91(\s*[\-]\s*)?|[0]?)?[789]\d{9}$^";
 		list($text,$link) = explode("||",$str);
 		preg_match ($pattern, $text, $matches);
 		preg_match ($patternMob, $text, $matchesMo);
 		
  		if(isset($matches[0])) {
  			$emailIDarr['EmailID'][] = $matches[0].'||'.$link;
  		}
 		
 		if(isset($matchesMo[0])) {
 			$mobilearr['MobileNum'][] = $matchesMo[0].'||'.$link;
 		}
		
		
	}
	$data		=	array_merge($emailIDarr,$mobilearr);
	
	$this->_helper->sendMessage($data);
	}

	
	
	
}