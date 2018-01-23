

    public function WPHRMSendEmail2($wphrmEmployeeID, $action, $data = array()) {
        $message_details = array(
            'email' => '',
            'subject' => '',
            'message' => '',
            'headers' => '',
        );

        $URL = admin_url();
        $user_ids = array();
        if(is_array($wphrmEmployeeID)){
            $user_ids = $wphrmEmployeeID;
        }elseif(is_numeric($wphrmEmployeeID)){
            $user_ids = array($wphrmEmployeeID);
        }else{
            $user_ids = explode(',', $wphrmEmployeeID);
        }

        $wphrmGeneralSettingsInfo = $this->WPHRMGetSettings('wphrmGeneralSettingsInfo');
        $address = (isset($wphrmGeneralSettingsInfo['wphrm_company_address'])) ? esc_textarea($wphrmGeneralSettingsInfo['wphrm_company_address']) : '';

        foreach($user_ids as $user_id){
            $userDetails = $this->WPHRMUserDetails($user_id);
            $email_setting = get_option('wphrm_email_notifications', array());
            if (isset($email_setting[$action])){
                $message_details['subject'] = $email_setting[$action]['subject'];
                $body = $email_setting[$action]['content'];

                //replaces the placeholders
                $types = $this->get_email_notification_types();
                if(isset($types[$action]) && isset($data['replacement'])){
                    foreach($types[$action]['placeholder'] as $placeholder){
                        $key = str_replace('%', '', $placeholder);
                        if(isset($data['replacement'][$key])){
                            $body = str_replace($placeholder, $data['replacement'][$key], $body);
                        }
                    }
                }

                $email_template = file_get_contents(dirname(__FILE__).'/jaf_extend/partials/email-template.html', true);
                if($email_template){
                    $body = str_replace(
                        array('{{header}}',
                              '{{body}}',
                              '{{footer}}'),
                        array(
                            '',
                            nl2br($body),
                            $address),
                        $email_template
                    );
                }
                $message_details['message'] = $body;

                if(isset($userDetails['useremail'])){
                    $message_details['email'] = $userDetails['useremail'];
                }else{
                    $user_info = get_userdata($user_id);
                    $message_details['email']= $user_info->user_email;
                }
                $fromName = get_bloginfo('name');
                $fromEamil = get_bloginfo('admin_email');
            }

            $message_details['headers'] = "MIME-Version: 1.0\n" . "From: " . $fromName . " <" . $fromEamil . ">\n";
            $message_details['headers'] .= "Content-type:text/html;charset=UTF-8" . "\r\n";

            $message_details = apply_filters('wphrm_before_email_send', $message_details, $action, $user_id, $data);
            wp_mail($message_details['email'], $message_details['subject'], $message_details['message'], $message_details['headers']);
        }
    }

    function get_email_notification_types(){
        global $wpdb;
        $types = array(
            'employee_leave_approved' => array(
                'title' => 'Employee Leave Approved',
                'role' => array('employee'),
                'placeholder' => array( '%employee_name%', '%leave_date_from%', '%leave_date_to%' ),
            ),
            'employee_leave_rejected' => array(
                'title' => 'Employee Leave Rejected',
                'role' => array('employee'),
                'placeholder' => array( '%employee_name%', '%leave_date_from%', '%leave_date_to%' ),
            ),
            'employee_leave_application' => array(
                'title' => 'Employee Leave Application',
                'role' => array('employee'),
                'placeholder' => array( '%employee_name%', '%leave_date_from%', '%leave_date_to%' ),
            ),

            'employee_created' => array(
                'title' => 'Employee Account Created',
                'role' => array('employee'),
                'placeholder' => array( '%first_name%', '%last_name%', '%email%', '%user_id%', '%password%', '%site_url%' ),
            ),

            'hr_leave_application' => array(
                'title' => 'HR/Admin Leave Application Recieved',
                'role' => array('hr_manager', 'administrator'),
                'placeholder' => array( '%employee_name%', '%leave_date_from%', '%leave_date_to%' ),
            ),
            'hr_leave_application_approved' => array(
                'title' => 'HR/Admin Leave Application Approved',
                'role' => array('hr_manager', 'administrator'),
                'placeholder' => array( '%employee_name%', '%leave_date_from%', '%leave_date_to%' ),
            ),
        );

        $email_setting = $wpdb->get_var("SELECT settingValue FROM $this->WphrmSettingsTable WHERE settingKey = 'wphrmEmailNoticaitons' AND authorID = 'system_notification' ");
        if(empty($email_setting)){

        }else{

        }
        ksort($types);
        return $types;
    }

    function wphrm_get_email_notification_details(){
        global $wpdb;
        $response = array('status' => false);
        $response['notification_variable_html'] = 'None';
        $notification_key = empty($_REQUEST['email_notification_key']) ? '' : $_REQUEST['email_notification_key'];
        $types = $this->get_email_notification_types();
        $response['notification'] = array(
            'subject' => '',
            'content' => '',
        );
        if($notification_key && isset($types[$notification_key])){
            $response['notification_variable_html'] = '';
            foreach($types[$notification_key]['placeholder'] as $key => $placeholder){
                //$replacement = $types[$notification_key]['replacement'][$key];
                $response['notification_variable_html'] .= "$placeholder ";
            }
            //$email_setting = $wpdb->get_var("SELECT settingValue FROM $this->WphrmSettingsTable WHERE settingKey = 'wphrmEmailNoticaitons' AND authorID = 'system_notification' ");
            $email_setting = get_option('wphrm_email_notifications', array());
            if(!empty($email_setting) && is_array($email_setting)){
                if(isset($email_setting[$notification_key])){
                    $response['notification'] = array(
                        'subject' => $email_setting[$notification_key]['subject'],
                        'content' => $email_setting[$notification_key]['content'],
                    );
                }
            }
            $response['status'] = true;
        }
        echo json_encode($response);
        exit;
    }
    function wphrm_update_email_notication(){
        global $wpdb;
        $response = array('status' => false);
        $notification_key = empty($_REQUEST['email-notification-type']) ? '' : $_REQUEST['email-notification-type'];
        $notification_subject = empty($_REQUEST['email_notification_subject']) ? '' : $_REQUEST['email_notification_subject'];
        $notification_content = empty($_REQUEST['email_notification_content']) ? '' : $_REQUEST['email_notification_content'];
        if($notification_key){
            $email_setting = get_option('wphrm_email_notifications', array());
            if($email_setting){
                $email_setting[$notification_key] = array(
                    'subject' => $notification_subject,
                    'content' => $notification_content
                );
                update_option('wphrm_email_notifications', $email_setting);
                $response['status'] = true;
                $response['type'] = 'updated';
            }else{
                //the setting is not existing, create a new one
                $new_settings = array(
                    $notification_key => array(
                        'subject' => $notification_subject,
                        'content' => $notification_content
                    )
                );
                update_option('wphrm_email_notifications', $new_settings);
                $response['status'] = true;
                $response['type'] = 'added';
            }
        }
        echo json_encode($response);
        exit;
    }