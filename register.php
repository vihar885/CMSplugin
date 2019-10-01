<?php
class elp_cls_registerhook
{
	public static function elp_activation()
	{
		global $wpdb;
		
		add_option('email-subscriber-widget', "3.9");
		add_option('elp_cron_mailcount', "75");
		add_option('elp_cron_trigger_option', "YES");
		add_option('elp_cron_adminmail', "Hi Admin, \r\n\r\nCron URL has been triggered successfully on ###DATE### for the mail ###SUBJECT###. And the mail has been sent to ###COUNT### recipient. \r\n\r\nThank You");
		
		// Plugin tables
		$array_tables_to_plugin = array('elp_templatetable','elp_emaillist','elp_sendsetting','elp_sentdetails','elp_deliverreport','elp_pluginconfig');
		$errors = array();
		
		// loading the sql file, load it and separate the queries
		$sql_file = ELP_DIR.'sql'.DS.'createDB.sql';
		$prefix = $wpdb->prefix;
        $handle = fopen($sql_file, 'r');
        $query = fread($handle, filesize($sql_file));
        fclose($handle);
        $query=str_replace('CREATE TABLE IF NOT EXISTS ','CREATE TABLE IF NOT EXISTS '.$prefix, $query);
        $queries=explode('-- SQLQUERY ---', $query);

        // run the queries one by one
        $has_errors = false;
        foreach($queries as $qry)
		{
            $wpdb->query($qry);
        }
		
		// list the tables that haven't been created
        $missingtables=array();
        foreach($array_tables_to_plugin as $table_name)
		{
			if(strtoupper($wpdb->get_var("SHOW TABLES like  '". $prefix.$table_name . "'")) != strtoupper($prefix.$table_name))  
			{
                $missingtables[]=$prefix.$table_name;
            }
        }
		
		// add error in to array variable
        if($missingtables) 
		{
			$errors[] = __('These tables could not be created on installation ' . implode(', ',$missingtables), 'email-subscriber-widget');
            $has_errors=true;
        }
		
		// if error call wp_die()
        if($has_errors) 
		{
			wp_die( __( $errors[0] , 'email-subscriber-widget' ) );
			return false;
		}
		else
		{
			elp_cls_dbinsert::elp_template_default();
			elp_cls_dbinsert::elp_pluginconfig_default();
			elp_cls_dbinsert::elp_sendsetting_default();
			elp_cls_dbinsert::elp_subscriber_default();
			elp_cls_dbquerynote::elp_notification_default();
		}
        return true;
	}
	
	public static function elp_synctables()
	{
		$elp_c_plugin_ver = get_option('email-subscriber-widget');
		if($elp_c_plugin_ver <> "3.0")
		{
			global $wpdb;
			
			// loading the sql file, load it and separate the queries
			$sql_file = ELP_DIR.'sql'.DS.'createDB.sql';
			$prefix = $wpdb->prefix;
			$handle = fopen($sql_file, 'r');
			$query = fread($handle, filesize($sql_file));
			fclose($handle);
			$query=str_replace('CREATE TABLE IF NOT EXISTS ','CREATE TABLE '.$prefix, $query);
			$query=str_replace('ENGINE=MyISAM /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/','', $query);
			$queries=explode('-- SQLQUERY ---', $query);
	
			// includes db upgrade file
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			
			// run the queries one by one
			foreach($queries as $sSql)
			{
				dbDelta( $sSql );
			}
			elp_cls_dbinsert::elp_db_value_sync();
			
			update_option('email-subscriber-widget', "3.0" );
			add_option('elp_cron_mailcount', "75");
		}
	}
	
	public static function elp_synctables_3_4()
	{
		$elp_c_plugin_ver = get_option('email-subscriber-widget');
		if($elp_c_plugin_ver <> "3.4")
		{
			global $wpdb;
			
			// loading the sql file, load it and separate the queries
			$sql_file = ELP_DIR.'sql'.DS.'createDB.sql';
			$prefix = $wpdb->prefix;
			$handle = fopen($sql_file, 'r');
			$query = fread($handle, filesize($sql_file));
			fclose($handle);
			$query=str_replace('CREATE TABLE IF NOT EXISTS ','CREATE TABLE '.$prefix, $query);
			$query=str_replace('ENGINE=MyISAM /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/','', $query);
			$queries=explode('-- SQLQUERY ---', $query);
	
			// includes db upgrade file
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			
			// run the queries one by one
			foreach($queries as $sSql)
			{
				dbDelta( $sSql );
			}
			elp_cls_dbquerynote::elp_notification_default();
			
			update_option('email-subscriber-widget', "3.4" );
		}
	}
	
	public static function elp_synctables_3_9()
	{
		$elp_c_plugin_ver = get_option('email-subscriber-widget');
		$elp_c_plugin_ver = floatval($elp_c_plugin_ver);
		
		if($elp_c_plugin_ver < 3.9)
		{
			$elp_cron_trigger_option = get_option('elp_cron_trigger_option', '0');
			if($elp_cron_trigger_option == "0")
			{
				add_option('elp_cron_trigger_option', "YES");
			}
			update_option('email-subscriber-widget', "3.9" );
		}
	}
	
	public static function elp_synctables_all_versions()
	{
		$elp_c_plugin_ver = get_option('email-subscriber-widget');
		$elp_c_plugin_ver = floatval($elp_c_plugin_ver);
		
		if($elp_c_plugin_ver >= 3.4 and $elp_c_plugin_ver <= 3.8)
		{
			elp_cls_registerhook::elp_synctables_3_9();
		}
		elseif($elp_c_plugin_ver >= 3.1 and $elp_c_plugin_ver <= 3.3)
		{
			elp_cls_registerhook::elp_synctables_3_4();
			elp_cls_registerhook::elp_synctables_3_9();
		}
		elseif($elp_c_plugin_ver <= 3.0)
		{
			elp_cls_registerhook::elp_synctables();
			elp_cls_registerhook::elp_synctables_3_4();
			elp_cls_registerhook::elp_synctables_3_9();
		}
	}
	
	public static function elp_deactivation()
	{
		// do not generate any output here
	}
	
	public static function elp_uninstall()
	{
		// do not generate any output here
	}
	
	public static function elp_adminmenu()
	{
		
		$elp_c_rolesandcapabilities = get_option('elp_c_rolesandcapabilities', 'norecord');
		if($elp_c_rolesandcapabilities == 'norecord' || $elp_c_rolesandcapabilities == "")
		{
			$elp_role_subscriber 	= "manage_options";
			$elp_role_templates 	= "manage_options";
			$elp_role_mailconfig 	= "manage_options";
			$elp_role_crondetails 	= "manage_options";
			$elp_role_setting 		= "manage_options";
			$elp_role_sendemail 	= "manage_options";
			$elp_role_sentmail 		= "manage_options";
			$elp_role_roles 		= "manage_options";
			$elp_role_help 			= "manage_options";
		}
		else
		{
			$elp_role_subscriber 	= $elp_c_rolesandcapabilities['elp_role_subscriber'];
			$elp_role_templates 	= $elp_c_rolesandcapabilities['elp_role_templates'];
			$elp_role_mailconfig 	= $elp_c_rolesandcapabilities['elp_role_mailconfig'];
			$elp_role_crondetails 	= $elp_c_rolesandcapabilities['elp_role_crondetails'];
			$elp_role_setting 		= $elp_c_rolesandcapabilities['elp_role_setting'];
			$elp_role_sendemail 	= $elp_c_rolesandcapabilities['elp_role_sendemail'];
			$elp_role_sentmail 		= $elp_c_rolesandcapabilities['elp_role_sentmail'];
			$elp_role_roles 		= $elp_c_rolesandcapabilities['elp_role_roles'];
			$elp_role_help 			= $elp_c_rolesandcapabilities['elp_role_help'];
		}
		
		add_menu_page( __( 'Email Posts', 'email-subscriber-widget' ), 
			__( 'Email Posts', 'email-subscriber-widget' ), 'admin_dashboard', 'email-post', 'elp_admin_option', ELP_URL.'images/mail.png', 53 );
			
		add_submenu_page('email-post', __( 'Subscribers', 'email-subscriber-widget' ), 
			__( 'Subscribers', 'email-subscriber-widget' ), $elp_role_subscriber , 'elp-view-subscribers', array( 'elp_cls_intermediate', 'elp_subscribers' ));
						
		
			
		
	}
	
	public static function elp_widget_loading()
	{
		register_widget( 'elp_widget_register' );
	}
	
	public static function elp_load_scripts()
	{
		if( !empty( $_GET['page'] ) )
		{
			switch ( $_GET['page'] ) 
			{
				case 'elp-view-subscribers':
					wp_register_script( 'elp-view-subscribers', ELP_URL . 'subscribers/view-subscriber.js', '', '', true );
					wp_enqueue_script( 'elp-view-subscribers' );
					$elp_script_params = array(
						'elp_subscribers_del_record1' => _x( 'Do you want to delete this record?', 'elp-subscribers-script', 'email-subscriber-widget' ),
						'elp_subscribers_del_record2' => _x( 'Are you sure you want to delete?', 'elp-subscribers-script', 'email-subscriber-widget' ),
						'elp_subscribers_add_email'   => _x( 'Please enter email address.', 'elp-subscribers-script', 'email-subscriber-widget' ),
						'elp_subscribers_email_group' => _x( 'Please enter email group.', 'elp-subscribers-script', 'email-subscriber-widget' ),
						'elp_subscribers_add_status'  => _x( 'Please select the status.', 'elp-subscribers-script', 'email-subscriber-widget' ),
						'elp_subscribers_bulk_action' => _x( 'Please select the bulk action.', 'elp-subscribers-script', 'email-subscriber-widget' ),
						'elp_subscribers_resend' 	  => _x( 'Do you want to resend confirmation email? Also please note, this will update subscriber current status to Unconfirmed.', 'elp-subscribers-script', 'email-subscriber-widget' ),
						'elp_subscribers_add_group'   => _x( 'Please select new subscriber group.', 'elp-subscribers-script', 'email-subscriber-widget' ),
						'elp_subscribers_update_group'=> _x( 'Do you want to update subscribers group?.', 'elp-subscribers-script', 'email-subscriber-widget' ),
						'elp_subscribers_export_email'=> _x( 'Do you want to export the emails?.', 'elp-subscribers-script', 'email-subscriber-widget' ),
						'elp_subscribers_csv'		  => _x( 'Please select only csv file. Please check official website for csv structure.', 'elp-subscribers-script', 'email-subscriber-widget' ),
						'elp_subscribers_num_letters' => _x( 'Please input numeric and letters only.', 'elp-subscribers-script', 'email-subscriber-widget' ),
					);
					wp_localize_script( 'elp-view-subscribers', 'elp_subscribers_script', $elp_script_params );
					break;
					
				case 'elp-email-template':
					wp_register_script( 'elp-email-template', ELP_URL . 'template/template.js', '', '', true );
					wp_enqueue_script( 'elp-email-template' );
					$elp_script_params = array(
						'elp_template_heading' => _x( 'Please enter template heading.', 'elp-template-script', 'email-subscriber-widget' ),
						'elp_template_delete'  => _x( 'Do you want to delete this record?', 'elp-template-script', 'email-subscriber-widget' ),
					);
					wp_localize_script( 'elp-email-template', 'elp_template_script', $elp_script_params );
					break;
					
				case 'elp-composenewsletter':
					wp_register_script( 'elp-composenewsletter', ELP_URL . 'template/template.js', '', '', true );
					wp_enqueue_script( 'elp-composenewsletter' );
					$elp_script_params = array(
						'elp_composenewsletter_heading' => _x( 'Please enter newsletter subject.', 'elp-composenewsletter-script', 'email-subscriber-widget' ),
						'elp_composenewsletter_delete'  => _x( 'Do you want to delete this record?', 'elp-composenewsletter-script', 'email-subscriber-widget' ),
					);
					wp_localize_script( 'elp-composenewsletter', 'elp_composenewsletter_script', $elp_script_params );
					break;
				
				case 'elp-configuration':
					wp_register_script( 'elp-configuration', ELP_URL . 'configuration/configuration.js', '', '', true );
					wp_enqueue_script( 'elp-configuration' );
					$elp_script_params = array(
						'elp_configuration_mailsub' => _x( 'Please enter mail subject.', 'elp-configuration-script', 'email-subscriber-widget' ),
						'elp_configuration_mailtemp'=> _x( 'Please select template for this configuration.', 'elp-configuration-script', 'email-subscriber-widget' ),
						'elp_configuration_delete'  => _x( 'Do you want to delete this record?', 'elp-configuration-script', 'email-subscriber-widget' ),
					);
					wp_localize_script( 'elp-configuration', 'elp_configuration_script', $elp_script_params );
					break;
					
				case 'elp-sendemail':
					wp_register_script( 'elp-sendemail', ELP_URL . 'sendmail/sendmail.js', '', '', true );
					wp_enqueue_script( 'elp-sendemail' );
					$elp_script_params = array(
						'elp_sendemail_configuration' 	=> _x( 'Please select mail configuration.', 'elp-sendemail-script', 'email-subscriber-widget' ),
						'elp_sendemail_mailtype'		=> _x( 'Please select your mail type.', 'elp-sendemail-script', 'email-subscriber-widget' ),
						'elp_sendemail_confirm'  		=> _x( 'Are you sure you want to send email to all selected email address?', 'elp-sendemail-script', 'email-subscriber-widget' ),
					);
					wp_localize_script( 'elp-sendemail', 'elp_sendemail_script', $elp_script_params );
					break;
					
				case 'elp-sendnewsletter':
					wp_register_script( 'elp-sendnewsletter', ELP_URL . 'sendmail/sendmail.js', '', '', true );
					wp_enqueue_script( 'elp-sendnewsletter' );
					$elp_script_params = array(
						'elp_sendnewsletter_newsletter' => _x( 'Please select your newsletter.', 'elp-sendnewsletter-script', 'email-subscriber-widget' ),
						'elp_sendnewsletter_mailtype'	=> _x( 'Please select your mail type.', 'elp-sendnewsletter-script', 'email-subscriber-widget' ),
						'elp_sendnewsletter_group'		=> _x( 'Please select subscriber group.', 'elp-sendnewsletter-script', 'email-subscriber-widget' ),
						'elp_sendnewsletter_confirm'  	=> _x( 'Are you sure you want to send email to selected subscriber group?', 'elp-sendnewsletter-script', 'email-subscriber-widget' ),
					);
					wp_localize_script( 'elp-sendnewsletter', 'elp_sendnewsletter_script', $elp_script_params );
					break;
					
				case 'elp-postnotification':
					wp_register_script( 'elp-postnotification', ELP_URL . 'notification/notification.js', '', '', true );
					wp_enqueue_script( 'elp-postnotification' );
					$elp_script_params = array(
						'elp_notification_group' 		=> _x( 'Please select email subscribers group.', 'elp-postnotification-script', 'email-subscriber-widget' ),
						'elp_notification_status'		=> _x( 'Please select notification status.', 'elp-postnotification-script', 'email-subscriber-widget' ),
						'elp_notification_subject'		=> _x( 'Please enter notification mail subject.', 'elp-postnotification-script', 'email-subscriber-widget' ),
						'elp_notification_categories'  	=> _x( 'Please select post categories for this notification.', 'elp-postnotification-script', 'email-subscriber-widget' ),
						'elp_notification_delete'		=> _x( 'Do you want to delete this record?', 'elp-postnotification-script', 'email-subscriber-widget' ),
					);
					wp_localize_script( 'elp-postnotification', 'elp_notification_script', $elp_script_params );
					break;
					
				case 'elp-sentmail':
					wp_register_script( 'elp-sentmail', ELP_URL . 'sentmail/sentmail.js', '', '', true );
					wp_enqueue_script( 'elp-sentmail' );
					$elp_script_params = array(
						'elp_sentmail_delete'		=> _x( 'Do you want to delete this record?', 'elp-sentmail-script', 'email-subscriber-widget' ),
						'elp_sentmail_delete_all'	=> _x( 'Do you want to delete all records except latest 20?', 'elp-sentmail-script', 'email-subscriber-widget' ),
					);
					wp_localize_script( 'elp-sentmail', 'elp_sentmail_script', $elp_script_params );
					break;
					
				case 'elp-crondetails':
					wp_register_script( 'elp-crondetails', ELP_URL . 'cron/cron.js', '', '', true );
					wp_enqueue_script( 'elp-crondetails' );
					$elp_script_params = array(
						'elp_crondetails_number1'	=> _x( 'Please enter number of mails you want to send on every cron trigger.', 'elp-crondetails-script', 'email-subscriber-widget' ),
						'elp_crondetails_number2'	=> _x( 'Please enter the mail count, only number.', 'elp-crondetails-script', 'email-subscriber-widget' ),
					);
					wp_localize_script( 'elp-crondetails', 'elp_crondetails_script', $elp_script_params );
					break;
					
				case 'elp-settings':
					wp_register_script( 'elp-settings', ELP_URL . 'settings/settings.js', '', '', true );
					wp_enqueue_script( 'elp-settings' );
					
				case 'elp-roles':
					wp_register_script( 'elp-roles', ELP_URL . 'roles/roles.js', '', '', true );
					wp_enqueue_script( 'elp-roles' );
					
				case 'elp-recaptcha':
					wp_register_script( 'elp-recaptcha', ELP_URL . 'recaptcha/recaptcha.js', '', '', true );
					wp_enqueue_script( 'elp-recaptcha' );
					$elp_script_params = array(
						'elp_recaptcha_sitekey_add'		=> _x( 'Please enter valid site key value.', 'elp-recaptcha-script', 'email-subscriber-widget' ),
						'elp_recaptcha_secretkey_add'	=> _x( 'Please enter valid secret key value.', 'elp-recaptcha-script', 'email-subscriber-widget' ),
						'elp_recaptcha_save_all'		=> _x( 'Do you want to update all the details.', 'elp-recaptcha-script', 'email-subscriber-widget' ),
					);
					wp_localize_script( 'elp-recaptcha', 'elp_recaptcha_script', $elp_script_params );
					break;
			}
		}
	}
}

class elp_widget_register extends WP_Widget 
{
	function __construct() 
	{
		$widget_ops = array('classname' => 'widget_text elp-widget', 'description' => __(ELP_PLUGIN_DISPLAY, 'email-subscriber-widget'), ELP_PLUGIN_NAME);
		parent::__construct(ELP_PLUGIN_NAME, __(ELP_PLUGIN_DISPLAY, 'email-subscriber-widget'), $widget_ops);
	}
	
	function widget( $args, $instance ) 
	{
		extract( $args, EXTR_SKIP );
		
		$elp_title 	= apply_filters( 'widget_title', empty( $instance['elp_title'] ) ? '' : $instance['elp_title'], $instance, $this->id_base );
		$elp_desc	= $instance['elp_desc'];
		$elp_name	= $instance['elp_name'];
		$elp_group	= $instance['elp_group'];

		echo $args['before_widget'];
		if ( ! empty( $elp_title ) )
		{
			echo $args['before_title'] . $elp_title . $args['after_title'];
		}
		// Call widget method
		$arr = array();
		//$arr["elp_title"] 	= $elp_title;
		$arr["namefield"] 	= $elp_name;
		$arr["desc"] 		= $elp_desc;
		$arr["group"] 		= $elp_group;
		$arr["type"] 		= "widget";	
		echo elp_cls_widget::elp_widget_int($arr);
		// Call widget method
		
		echo $args['after_widget'];
	}
	
	function update( $new_instance, $old_instance ) 
	{
		$instance 				= $old_instance;
		$instance['elp_title'] 	= ( ! empty( $new_instance['elp_title'] ) ) ? strip_tags( $new_instance['elp_title'] ) : '';
		$instance['elp_desc'] 	= ( ! empty( $new_instance['elp_desc'] ) ) ? strip_tags( $new_instance['elp_desc'] ) : '';
		$instance['elp_name'] 	= ( ! empty( $new_instance['elp_name'] ) ) ? strip_tags( $new_instance['elp_name'] ) : '';
		$instance['elp_group'] 	= ( ! empty( $new_instance['elp_group'] ) ) ? strip_tags( $new_instance['elp_group'] ) : '';
		return $instance;
	}
	
	function form( $instance ) 
	{
		$defaults = array(
			'elp_title' => '',
            'elp_desc' 	=> '',
            'elp_name' 	=> '',
			'elp_group'  => ''
        );
		$instance 		= wp_parse_args( (array) $instance, $defaults);
		$elp_title 		= $instance['elp_title'];
        $elp_desc 		= $instance['elp_desc'];
        $elp_name 		= $instance['elp_name'];
		$elp_group 		= $instance['elp_group'];
		?>
		<p>
			<label for="<?php echo $this->get_field_id('elp_title'); ?>"><?php _e('Widget Title', 'email-subscriber-widget'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('elp_title'); ?>" name="<?php echo $this->get_field_name('elp_title'); ?>" type="text" value="<?php echo $elp_title; ?>" />
        </p>
		<p>
            <label for="<?php echo $this->get_field_id('elp_name'); ?>"><?php _e('Name Field', 'email-subscriber-widget'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('elp_name'); ?>" name="<?php echo $this->get_field_name('elp_name'); ?>">
				<option value="YES" <?php $this->elp_selected($elp_name == 'YES'); ?>>YES</option>
				<option value="NO" <?php $this->elp_selected($elp_name == 'NO'); ?>>NO</option>
			</select>
        </p>
		<p>
			<label for="<?php echo $this->get_field_id('elp_desc'); ?>"><?php _e('Short Description', 'email-subscriber-widget'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('elp_desc'); ?>" name="<?php echo $this->get_field_name('elp_desc'); ?>" type="text" value="<?php echo $elp_desc; ?>" />
			Short description about your widget.
        </p>
		<p>
			<label for="<?php echo $this->get_field_id('elp_group'); ?>"><?php _e('Subscriber Group', 'email-subscriber-widget'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('elp_group'); ?>" name="<?php echo $this->get_field_name('elp_group'); ?>" type="text" maxlength="20" value="<?php echo $elp_group; ?>" />
        </p>
		<?php
	}
	
	function elp_selected($var) 
	{
		if ($var==1 || $var==true) 
		{
			echo 'selected="selected"';
		}
	}
}

function elp_sync_registereduser( $user_id ) 
{        
	$elp_c_syncemail = get_option('elp_c_syncemail', 'norecord');
	if($elp_c_syncemail == 'norecord' || $elp_c_syncemail == "") 
	{
		// No action is required
	} 
	else 
	{
		if(($elp_c_syncemail['elp_registered'] == "YES") && ($user_id <> "")) 
		{
			$user_info = get_userdata($user_id);
			$user_firstname = $user_info->user_firstname;
			if($user_firstname == "") 
			{
				$user_firstname = $user_info->user_login;
			}
			$user_mail = $user_info->user_email;
			
			$form['elp_email_name'] = $user_firstname;
			$form['elp_email_mail'] = $user_mail;
			$form['elp_email_status'] = "Confirmed";
			$form['elp_email_group'] = $elp_c_syncemail['elp_registered_group'];
			$inputdata = array($form['elp_email_name'], $form['elp_email_mail'], $form['elp_email_status'], trim($form['elp_email_group']));
			$action = "";
			$action = elp_cls_dbquery::elp_view_subscriber_ins($inputdata);	
			if($action == "sus")
			{
				//Inserted successfully. Below 3 line of code will send WELCOME email to subscribers.
				//$subscribers = array();
				//$subscribers = elp_cls_dbquery::elp_view_subscriber_one($user_mail);
				//elp_cls_sendmail::elp_sendmail("welcome", $subject = "", $content = "", $subscribers);
			}
		}
	}
}


function elp_cron_activation() 
{
	if (! wp_next_scheduled ( 'elp_cron_hourly_event' )) 
	{
		wp_schedule_event(time(), 'hourly', 'elp_cron_hourly_event');
    }
}

function elp_cron_deactivation() 
{
	wp_clear_scheduled_hook('elp_cron_hourly_event');
}

function elp_cron_trigger_hourly() 
{
	$elp_cron_trigger_option = get_option('elp_cron_trigger_option');
	if ($elp_cron_trigger_option <> "YES")
	{
		return;
	}
	
	$elp_c_croncount = get_option('elp_cron_mailcount');
	if(!is_numeric($elp_c_croncount))
	{
		$elp_c_croncount = 50;
	}
	
	$data = array();
	$data = elp_cls_dbquery::elp_configuration_cron_trigger();
	
	if(count($data) > 0)
	{
		$subject = $data[0]['elp_set_name'];
		$content = elp_cls_newsletter::elp_template_compose($data[0]['elp_set_templid'], $data[0]['elp_set_postcount'], 
				$data[0]['elp_set_postcategory'], $data[0]['elp_set_postorderby'], $data[0]['elp_set_postorder'], "send");
		
		if($content == "NO_POST_FOUND_FOR_THIS_MAIL_CONFIGURATION")
		{
			$sendguid = elp_cls_common::elp_generate_guid(60);
			$currentdate = date('Y-m-d G:i:s');
			elp_cls_dbquery2::elp_sentmail_ins($sendguid, 0, "cron", $currentdate, $currentdate, 0, $content, "Cron Mail", $subject);
		}
		else
		{
			if(!is_numeric($data[0]['elp_set_totalsent']))
			{
				$elp_set_totalsent = 9999;
			}
			else
			{
				$elp_set_totalsent = $data[0]['elp_set_totalsent'];
			}
			
			$elp_set_emaillistgroup = $data[0]['elp_set_emaillistgroup'];
			if($elp_set_emaillistgroup == "")
			{
				$elp_set_emaillistgroup = "Public";
			}
			
			elp_cls_sendmail::elp_prepare_newsletter($subject, $content, 1, $elp_set_totalsent, $elp_set_emaillistgroup);
			elp_cls_dbquery::elp_configuration_cron_trigger_update($data[0]['elp_set_guid']);
		}
	}
	else
	{
		$sentmail = array();
		$sentmail = elp_cls_dbquery2::elp_sentmail_select_cron_trigger();
		if(count($sentmail) > 0)
		{		
			$delivery = array();
			$delivery = elp_cls_dbquery2::elp_delivery_select_cron_trigger($sentmail[0]['elp_sent_guid'], 0, $elp_c_croncount);
			elp_cls_sendmail::elp_sendmail_cron_trigger("newsletter", $sentmail[0]['elp_sent_subject'], $sentmail[0]['elp_sent_preview'], $delivery);
			elp_cls_dbquery2::elp_sentmail_select_cron_update($sentmail[0]['elp_sent_guid']);
		}
	}
}
add_action('elp_cron_hourly_event', 'elp_cron_trigger_hourly');
?>