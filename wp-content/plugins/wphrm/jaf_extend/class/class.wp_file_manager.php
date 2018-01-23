<?php
class JAF_WP_File_Manager {

    private $folder_taxonomy = 'wphrm_document_folder';

    function __construct(){
        $this->add_admin_ajax('jaf_load_files');
        $this->add_admin_ajax('share_file');
        $this->add_admin_ajax('delete_file');
        $this->add_admin_ajax('wphrm_upload_file');
        $this->add_admin_ajax('add_new_folder');
    }

    function add_admin_ajax($action){
        add_action( "wp_ajax_$action", array($this, $action ) );
        add_action( "wp_ajax_nopriv_$action", array($this, $action ) );
    }

    function jaf_load_files(){
        $data = array(
            'status' => false,
            'message' => '',
        );

        //get the current term to get the list of files and folders
        $term_folder = empty($_POST['folder_term']) ? 0 : $_POST['folder_term'];

        $current_user = wp_get_current_user();


        /*Get the term ancestor*/
        $ancestor = '<li><a class="file-element" data-type="dir" data-id="0" data-url="#" ><i class="fa fa-folder-open"></i>'.__('Root', 'wphrm').' '.($term_folder != 0 ? '<i class="fa fa-angle-right"></i>' : '').'</a></li>';
        if($term_folder != 0){
            $ancestor_ids = get_ancestors( $term_folder, '', 'taxonomy' );
            $ancestor_ids[] = $term_folder;
            $ctr = 0;
            foreach($ancestor_ids as $ancestor_id){
                $ctr++;
                $term = get_term($ancestor_id);
                if(!is_wp_error($term) && !empty($term)){
                    $url = get_term_link($term->term_id, $this->folder_taxonomy);
                    $ancestor .= '<li>
                        <a class="file-element" data-type="dir" data-id="'.$term->term_id.'" data-url="'.$url.'" >
                            <i class="fa fa-folder-open"></i>'.$term->name.' '. ($ctr < count($ancestor_ids)  ? '<i class="fa fa-angle-right"></i>' : '') .'
                        </a>
                    </li>';
                }
            }
        }

        /*get the folder term under the current one*/
        $terms = get_terms(array(
            'taxonomy' => $this->folder_taxonomy,
            'hide_empty' => false,
            'parent' => $term_folder,
        ));


        $has_folders = false;
        $html = '<div class="row" >';

        if( !is_wp_error($terms) && !empty($terms)){
            $has_folders = true;
            $data['status'] = !$data['status'] ? true : true;

            foreach($terms as $term){

                //check if the term has files for non admin users, if none dont display the folder
                $args = array(
                    'post_type' => 'wphrm_document_cpt',
                    'post_status' => 'publish',
                );
                $args['meta_query'] = array(
                    'relation' => 'OR',
                    array(
                        'key' => 'share_to_user',
                        'value' => get_current_user_id(),
                    ),
                    array(
                        'key' => 'share_to_department',
                        'value' => $this->get_user_department(get_current_user_id()),
                    ),
                );
                $args['tax_query'] = array(array(
                    'taxonomy' => $this->folder_taxonomy,
                    'terms' => $term->term_id,
                    'field' => 'term_id',
                ));
                $folder_files = get_posts($args);
                if(count($folder_files) == 0 && !current_user_can('manageOptionsFileDocuments')) continue;

                $url = get_term_link($term->term_id, $this->folder_taxonomy);
                $html .= '<div class="col-md-1 col-sm-2 col-xs-4 file-container" >
                    <a class="file-element" data-type="dir" data-id="'.$term->term_id.'" data-url="'.$url.'" >
                        <div class="file-icon folder-icon-wrapper"><i class="fa fa-folder" aria-hidden="true"></i></div>
                        <div class="folder-name">'.$term->name.'</div>
                    </a>
                    <div class="status '.(current_user_can('manageOptionsFileDocuments') ? '' : 'hidden').'">
                        <a class="file-delete text-danger" title="Delete" data-toggle="modal" data-target="#delete-file-folder" data-type="dir" data-id="'.$term->term_id.'" ><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </div>
                </div>';
            }
        }else{
            $data['message'] .= ' Cant load folders. ';
        }


        //now get the list of file under this term, if no category here
        $args = array(
            'post_type' => 'wphrm_document_cpt',
            'post_status' => 'publish',
            'posts_per_page' => '-1',
        );

        if($term_folder){
            $args['tax_query'] = array(array(
                'taxonomy' => $this->folder_taxonomy,
                'terms' => $term_folder,
                'field' => 'term_id',
            ));
        }else{
            $args['tax_query'] = array(array(
                'taxonomy' => $this->folder_taxonomy,
                'terms'    => get_terms( $this->folder_taxonomy, array( 'fields' => 'ids'  ) ),
                'operator' => 'NOT IN'
            ));
        }


        if(!current_user_can('manageOptionsFileDocuments')){
            $args['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key' => 'share_to_user',
                    'value' => get_current_user_id(),
                ),
                array(
                    'key' => 'share_to_department',
                    'value' => $this->get_user_department(get_current_user_id()),
                ),
            );
        }

        $posts = get_posts($args);

        if( !empty($posts)){
            $data['status'] = !$data['status'] ? true : true;

            foreach($posts as $post){
                $file_upload_data = get_post_meta($post->ID, 'file_upload_data', true);
                //$file_upload_data = $file_upload_data ? unserialize($file_upload_data);
                $url = $file_upload_data->url;

                $shared_to_user = get_post_meta($post->ID, 'share_to_user', true);
                $shared_to_department = get_post_meta($post->ID, 'share_to_department', true);
                $shared_to_manager = get_post_meta($post->ID, 'share_to_manager', true);

                $html .= '<div class="col-md-1 col-sm-2 col-xs-4 file-container" >
                    <a class="file-element" data-type="file" data-id="'.$post->ID.'" data-url="'.$url.'" >
                        <div class="file-icon file-icon-wrapper"><i class="fa fa-file-text" aria-hidden="true"></i></div>
                        <div class="file-name">'.$post->post_title.'</div>
                    </a>
                    <div class="status '.(current_user_can('manageOptionsFileDocuments') ? '' : 'hidden').'">
                        <a class="file-share text-info" title="Share" data-toggle="modal" data-target="#share-file-modal" data-type="file" data-id="'.$post->ID.'" data-shared_user="'.$shared_to_user.'" data-shared_department="'.$shared_to_department.'" data-shared_manager="'.$shared_to_manager.'" ><i class="fa fa-share" aria-hidden="true"></i></a>
                        <a class="file-delete text-danger" title="Delete" data-toggle="modal" data-target="#delete-file-folder" data-type="file" data-id="'.$post->ID.'" ><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </div>
                    <div class="action"></div>
                    <div class="status"></div>
                </div>';
            }
        }elseif(!$has_folders){
            $data['status'] = !$data['status'] ? true : true;
            $html .= '<div class="col-md-12" >
                        <p class="description">No files to show.</p>
                </div>';
        }else{
            $data['message'] .= ' No Files under this folder. ';
        }

        $html .= '</div>';

        $data['ancestor'] = $ancestor;
        //$data['count'] = count($posts);
        $data['args'] = $args;
        $data['html'] = $html;

        echo json_encode($data);
        exit;
    }

    function get_user_department($user_id){
        //get_user_department
        $wphrmEmployeeBasicInfo = $this->WPHRMGetUserDatas($user_id, 'wphrmEmployeeInfo');
        return intval($wphrmEmployeeBasicInfo['wphrm_employee_department']);
    }

    function WPHRMGetUserDatas($ID, $key) {
        $wphrmUserInfo = get_user_meta($ID, $key, true);
        $wphrmUserDatas = array();
        if ($wphrmUserInfo != '') :
        $wphrmUserDatas = unserialize(base64_decode($wphrmUserInfo));
        endif;
        return $wphrmUserDatas;
    }

    function share_file(){
        $type = empty($_REQUEST['file_type']) ? false : $_REQUEST['file_type'];
        $id = empty($_REQUEST['file_id']) ? false : $_REQUEST['file_id'];
        //$share_to_user = isset($_REQUEST['share_to_user']) ? $_REQUEST['share_to_user'] : '';
        $share_to_user = array();
        foreach($_REQUEST['share_to_user'] as $selected){
            $share_to_user[] = $selected;
        }
        //$share_to_department = isset($_REQUEST['share_to_department']) ? $_REQUEST['share_to_department'] : '';
        $share_to_department = array();
        foreach($_REQUEST['share_to_department'] as $selected){
            $share_to_department[] = $selected;
        }
        $share_to_manager = isset($_REQUEST['share_to_manager']) ? $_REQUEST['share_to_manager'] : 0;
        /*$share_to_manager = array();
        foreach($_REQUEST['share_to_manager'] as $selected){
            $share_to_manager[] = $selected;
        }*/

        if(!$type || !$id) wp_send_json_error();

        update_post_meta($id, 'share_to_user', implode(',', $share_to_user));
        update_post_meta($id, 'share_to_department', implode(',', $share_to_department));
        update_post_meta($id, 'share_to_manager', $share_to_manager);

        wp_send_json_success($_REQUEST);
        exit;

    }

    function delete_file(){
        $type = empty($_POST['file_type']) ? false : $_POST['file_type'];
        $id = empty($_POST['file_id']) ? false : $_POST['file_id'];

        if(!$type || !$id) wp_send_json_error();

        if($type == 'dir'){
            //delete term
            $result = wp_delete_term($id, $this->folder_taxonomy);
            if($result) wp_send_json_success();
        }elseif($type == 'file'){
            //delete post
            $result = wp_delete_post($id, true);
            if($result) wp_send_json_success();
        }else{
            wp_send_json_error();
        }
        wp_send_json_error();

    }


    function wphrm_upload_file(){
        $response = array(
            'status' => false,
        );
        if(class_exists('UploadHandler')){

            $folder_id = empty($_REQUEST['current_folder']) ? 0 : $_REQUEST['current_folder'];

            $upload = wp_upload_dir();
            $upload_dir = $upload['basedir'];
            $upload_url = $upload['baseurl'];
            $upload_dir = $upload_dir . '/jaf-wphrm-files/'.$folder_id.'/';
            $upload_url = $upload_url . '/jaf-wphrm-files/'.$folder_id.'/';
            if (! is_dir($upload_dir)) {
                mkdir( $upload_dir, 0755 );
            }
            $options = array(
                'max_file_size' => (1048576 * 10),
                'image_file_types' => '/\.(gif|jpe?g|png|doc|docx|pdf|ppt|pptx|xls|xlsx)$/i',
                'upload_dir' => $upload_dir,
                'upload_url' => $upload_url,
                //'thumbnail' => array('max_width' => 80,'max_height' => 80),
                'print_response' => false,
            );
            $upload_obj = new UploadHandler($options);
            $files = $upload_obj->response['files'];
            $response['upload_obj'] = $upload_obj;
            $response['upload_data'] = $upload_obj->response['files'];
            $response['upload_count'] = count($upload_obj->response['files']);

            foreach($upload_obj->response['files'] as $file){
                //$response['filename'] = $file->name;
                $new_file = array(
                    'post_title'    => sanitize_text_field( $file->name ),
                    'post_content'  => '',
                    'post_type'    => 'wphrm_document_cpt',
                    'post_status'   => 'publish',
                    'post_author'   => is_user_logged_in() ? get_current_user_id() : 1,
                    //'tax_input'     => array( 'wphrm_document_folder' => array($folder_id) ),
                );
                $post_id = wp_insert_post( $new_file );
                $response['new_post'] = $post_id;
                if( !is_wp_error($post_id) ){
                    $tag = array( (int)$folder_id );
                    wp_set_post_terms( $post_id, $tag, 'wphrm_document_folder' );
                    update_post_meta($post_id, 'file_upload_data', $file);
                    $response['status'] =  true;
                    $response['post_ids'][] = $post_id;
                }else{
                    $response['message'] = 'cant add new post';
                }
                $response['result'][] = $post_id;
            }
        }
        echo json_encode($response);exit;
    }

    function add_new_folder(){
        $response = array(
            'status' => false,
            'message' => '',
        );

        $current_term = empty($_REQUEST['current_folder']) ? 0 : (int)$_REQUEST['current_folder'];
        $folder_name = empty($_REQUEST['folder-name']) ? false : trim($_REQUEST['folder-name']);

        if($folder_name){
            $args = array();
            if($current_term != 0){ $args['parent'] = $current_term; }

            if( !term_exists( $folder_name, $this->folder_taxonomy ) ){

                if(wp_insert_term( $folder_name, $this->folder_taxonomy, $args )){
                    $response['status'] = true;
                    $response['message'] = 'Folder added';
                }else{
                    $response['message'] = 'No folder name set';
                }
            }else{
                $response['message'] = 'Folder name already exists!';
            }

            echo json_encode($response);exit;
        }else{
            $response['message'] = 'No folder name set';
        }

    }
}

new JAF_WP_File_Manager();
