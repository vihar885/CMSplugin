<?php
class elp_cls_widget
{
	public static function elp_widget_int( $atts )
	{
		echo elp_shortcode( $atts );
	}
}

function elp_shortcode( $atts ) 
{
	if ( ! is_array( $atts ) )
	{
		return '';
	}
	
	//[email-posts-subscribers namefield="YES" desc="" group=""]
	$elp_name 	= isset($atts['namefield']) ? $atts['namefield'] : 'YES';
	$elp_desc 	= isset($atts['desc']) ? $atts['desc'] : '';
	$elp_group 	= isset($atts['group']) ? $atts['group'] : '';
	
	$elp_type 	= isset($atts['type']) ? $atts['type'] : '';
	if($elp_type == "")
	{
		$elp_type  = "shortcode";
	}
	
	$elp_name 	= trim($elp_name);
	$elp_desc 	= trim($elp_desc);
	$elp_group 	= trim($elp_group);
	
	if($elp_group == "")
	{
		$elp_group = "Public";
	}
	
	$elp 		= "";
	$elp_alt_nm = '';
	$elp_alt_em = '';
	
	$elp_error 		= false;
	$elp_txt_name 	= "";
	$elp_txt_email 	= "";
	$elp_txt_group 	= "";
	$elp_alt_success = "";
	$elp_alt_error 	= "";

	wp_enqueue_style( 'elp_widget.js', ELP_URL.'widget/widget.css', '', '', '' );
	
	//////////////////////////////Robot verification//////////////////////////////////////////////////
	$elp_captcha_widget = get_option('elp_captcha_widget', '');
	//////////////////////////////Robot verification//////////////////////////////////////////////////
	
	if ( isset( $_POST['elp_btn_'.$elp_type] ) ) 
	{
		//$homeurl = home_url();
		//$samedomain = strpos($_SERVER['HTTP_REFERER'], $homeurl);
		
		//if (($samedomain !== false) && $samedomain < 5) 
		//{
			//check_admin_referer('elp_form_subscribers');
			
			if( $elp_name == "YES" )
			{
				$elp_txt_name = isset($_POST['elp_txt_name']) ? sanitize_text_field($_POST['elp_txt_name']) : '';
			}
			
			$elp_txt_email = isset($_POST['elp_txt_email']) ? sanitize_text_field($_POST['elp_txt_email']) : '';
			$elp_txt_group = isset($_POST['elp_txt_group']) ? sanitize_text_field($_POST['elp_txt_group']) : '';
					
			if($elp_txt_name == "" && $elp_name == "YES")
			{
				$elp_alt_nm = __('Please fill in the required field.', 'email-subscriber-widget');
				$elp_error = true;
			}
			
			if($elp_txt_email == "")
			{
				$elp_alt_em = __('Please fill in the required field.', 'email-subscriber-widget');
				$elp_error = true;
			}
			
			if(!is_email($elp_txt_email) && $elp_txt_email <> "")
			{
				$elp_alt_em = __('Email address seems invalid.', 'email-subscriber-widget');
				$elp_error = true;
			}
			
			//////////////////////////////Robot verification//////////////////////////////////////////////////
			if(!$elp_error)
			{
				if($elp_captcha_widget == 'YES')
				{
					$elp_captcha_secret = get_option('elp_captcha_secret', '');
					$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$elp_captcha_secret.'&response='.$_POST['g-recaptcha-response']);
					$responseData = json_decode($verifyResponse);
					if(!$responseData->success)
					{
						$elp_alt_em = __('Robot verification failed, please try again.', 'email-subscriber-widget');
						$elp_error = true;
					}
				}
			}
			//////////////////////////////Robot verification//////////////////////////////////////////////////
			
			
			if(!$elp_error)
			{
				$data = elp_cls_dbquery2::elp_setting_select(1);
				if( $data['elp_c_optinoption'] == "Double Opt In" )
				{
					$inputdata = array($elp_txt_name, $elp_txt_email, "Unconfirmed", $elp_txt_group);
				}
				else
				{
					$inputdata = array($elp_txt_name, $elp_txt_email, "Single Opt In", $elp_txt_group);
				}
				
				$action = elp_cls_dbquery::elp_view_subscriber_widget($inputdata);
				if($action == "sus")
				{
					$subscribers = array();
					$subscribers = elp_cls_dbquery::elp_view_subscriber_one($elp_txt_email);
					if( $data['elp_c_optinoption'] == "Double Opt In" )
					{
						elp_cls_sendmail::elp_sendmail("optin", $subject = "", $content = "", $subscribers);
						$elp_alt_success = __('You have successfully subscribed to our email subscription service. You will now receive information about latest offers and discount through email. THANK YOU!! ', 'email-subscriber-widget');
					}
					else
					{
						if( $data['elp_c_usermailoption'] == "YES" )
						{
							elp_cls_sendmail::elp_sendmail("welcome", $subject = "", $content = "", $subscribers);
						}
						$elp_alt_success = __('Subscribed successfully.', 'email-subscriber-widget');
					}
				}
				elseif($action == "ext")
				{
					$elp_alt_error = __('Email already exist.', 'email-subscriber-widget');
					$elp_error = true;
				}
			}
		//}
	}
	
	//////////////////////////////Robot verification//////////////////////////////////////////////////
	if($elp_captcha_widget == 'YES')
	{
		$elp = $elp  . '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
	}
	//////////////////////////////Robot verification//////////////////////////////////////////////////
	
	$elp = $elp  . '<form method="post" action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '">';
	
	if($elp_desc <> "")
	{
		$elp = $elp . '<p>';
			$elp = $elp . '<span class="elp_caption">';
				$elp = $elp . $elp_desc;
			$elp = $elp . '</span>';
		$elp = $elp . '</p>';
	}
	
	if( $elp_name == "YES" )
	{
		$elp = $elp . '<p>';
		$elp = $elp . __('Name *', 'email-subscriber-widget');
		$elp = $elp . '<br>';
		$elp = $elp . '<span class="elp_textbox">';
			$elp = $elp . '<input class="elp_textbox_class" name="elp_txt_name" id="elp_txt_name" value="" maxlength="225" type="text">';
		$elp = $elp . '</span>';
		$elp = $elp . '<span class="elp_msg" style="color:#CC0000;">'.$elp_alt_nm.'</span>';
		$elp = $elp . '</p>';
	}
	
	
	$elp = $elp . '<p>';
	$elp = $elp . __('Email *', 'email-subscriber-widget');
	$elp = $elp . '<br>';
	$elp = $elp . '<span class="elp_textbox">';
		$elp = $elp . '<input class="elp_textbox_class" name="elp_txt_email" id="elp_txt_email" value="" maxlength="225" type="text">';
	$elp = $elp . '</span>';
	$elp = $elp . '<span class="elp_msg" style="color:#CC0000;">'.$elp_alt_em.'</span>';
	$elp = $elp . '</p>';
	
	//////////////////////////////Robot verification//////////////////////////////////////////////////
	if($elp_captcha_widget == 'YES')
	{
		$elp_captcha_sitekey = get_option('elp_captcha_sitekey', '');
		$elp = $elp . '<p>';
			$elp = $elp . '<div class="g-recaptcha" data-sitekey="'.$elp_captcha_sitekey.'"></div>';	
		$elp = $elp . '</p>';
	}
	//////////////////////////////Robot verification//////////////////////////////////////////////////

	$elp = $elp . '<p>';
		$elp = $elp . '<input class="elp_textbox_button" name="elp_btn_'.$elp_type.'" id="elp_btn_'.$elp_type.'" value="'.__('Submit', 'email-subscriber-widget').'" type="submit">';
		$elp = $elp . '<input name="elp_txt_group" id="elp_txt_group" value="'.$elp_group.'" type="hidden">';
	$elp = $elp . '</p>';
	
	if($elp_error)
	{
		$elp = $elp . '<span class="elp_msg" style="color:#CC0000;">'.$elp_alt_error.'</span>';
	}
	else
	{
		$elp = $elp . '<span class="elp_msg" style="color:#009900;">'.$elp_alt_success.'</span>';
	}
		
	//$elp = $elp . wp_nonce_field('elp_form_subscribers');
		
	$elp = $elp . '</form>';
	
	return $elp;
}

function elp_subbox( $elp_name = "YES", $elp_desc = "" )
{
	$atts = array();
	$atts["namefield"] 	= $elp_name;
	$atts["desc"] 		= $elp_desc;
	$atts["group"] 		= "Public";	
	$atts["type"] 		= "subbox";	
	echo elp_shortcode( $atts );
}
?>