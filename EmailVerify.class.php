<?php
##############################################################################################################
#
# Basic email address verification class
# This class can determine if an email address is correctly formatted and if the domain for the email address
#	exists, but cannot determine if the actual email address itself exists
#
# Author: Todd D. Webb
# Contact: DukeOfMarshall@gmail.com
#
# Sites
#	http://www.dukeofmarshall.com
#	http://blog.dukeofmarshall.com
#	http://www.techwerks.tv
#	http://www.soundbytes.biz
#
##############################################################################################################

# What to do if the class is being called directly and not being included in a script via PHP
# This allows the class/script to be called via other methods like JavaScript
if(basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])){
	$return_array = array();
	
	if($_GET['address_to_verify'] == '' || !isset($_GET['address_to_verify'])){
		$return_array['error'] 				= 1;
		$return_array['message'] 			= 'No email address was submitted for verification';
		$return_array['domain_verified'] 	= 0;
		$return_array['format_verified'] 	= 0;
	}else{
		$verify = new EmailVerify();
		
		if($verify->verify_formatting($_GET['address_to_verify'])){
			$return_array['format_verified'] 	= 1;
			
			if($verify->verify_domain($_GET['address_to_verify'])){
				$return_array['error'] 				= 0;
				$return_array['domain_verified'] 	= 1;
				$return_array['message'] 			= 'Formatting and domain have been verified';
			}else{
				$return_array['error'] 				= 1;
				$return_array['domain_verified'] 	= 0;
				$return_array['message'] 			= 'Formatting was verified, but verification of the domain has failed';
			}
		}else{
			$return_array['error'] 				= 1;
			$return_array['domain_verified'] 	= 0;
			$return_array['format_verified'] 	= 0;
			$return_array['message'] 			= 'Email was not formatted correctly';
		}
	}
	
	echo json_encode($return_array);
	
	exit();
}

class EmailVerify {
	public function __construct(){
	}
	
	# Verify the DNS records according to the domain name given in the email address
	public function verify_domain($address_to_verify){
		$record = 'MX'; # <-- Can be changed to check for other records like A records or CNAME records as well
		list($user, $domain) = explode('@', $address_to_verify);
		return checkdnsrr($domain, $record);
	}
	
	# Verify that the email address is formatted as an email address should be
	public function verify_formatting($address_to_verify){
		
		# Check to make sure the @ symbol is included
		if(strstr($address_to_verify, "@") == FALSE){
			return false;
		}else{
			
			# Bust up the address so that we have the name and the domain name
			list($user, $domain) = explode('@', $address_to_verify);
			
			# Verify the domain name has a period like all good domain names should
			if(strstr($domain, '.') == FALSE){
				return false;
			}else{
				return true;
			}
		}
	}
}
?>
