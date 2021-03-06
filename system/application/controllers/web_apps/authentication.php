<?php
include_once 'web_apps_controller.php';
/**
 * authentication
 *
 * 登入
 *
 * @package		KALS
 * @category		Controllers
 * @author		Pudding Chen <puddingchen.35@gmail.com>
 * @copyright		Copyright (c) 2010, Pudding Chen
 * @license		http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link		http://sites.google.com/site/puddingkals/
 * @version		1.0 2010/8/13 上午 10:32:18
 */

class Authentication extends Web_apps_controller {

    var $url;
    var $webpage;
    var $client_ip;

    var $CI;
    var $session;

    function __construct()
    {
        parent::__construct();
        $this->load->library('kals_actor/User');
        $this->url = get_referer_url(TRUE);

        $this->client_ip = array(
           'ip' => get_client_ip(),
           'browser' => $_SERVER['HTTP_USER_AGENT']
        );
    }

    /**
     * 
     * @param {Object} $json = {
     *     embed: TRUE //表示是不需要密碼的預設登入
     * }
     * @param {Object} $callback
     */
    function login($json, $callback)
    {
        $data = json_to_object($json);

        //2010.10.6 測試登入用
        //$data->embed = TRUE;

        $user = NULL;
        if ($data->embed)
        {
            $user = $this->user->create_user($this->url, $data->email);
        }
        else
        {
            $user = $this->user->find_user($this->url, $data->email, $data->password);
        }
        
        //$output = NULL;
        //$output['login'] = FALSE;
        $output = $this->_create_default_data();

        $action = 3;
        $user_id = NULL;
        if (isset($user))
        {
            $output = $this->_parse_user_output($user, FALSE);
            $user_id = $user->get_id();
        }
        else    //if (isset($user)) else
        {
            $action = 4;
            $output['error'] = 'user_not_found';
            //handle_error('user_not_found');
        }
        kals_log($this->db, $action, array('memo'=>$this->client_ip, 'user_id' => $user_id));
        context_complete();

        $this->_display_jsonp($output, $callback);
    }

    private function _parse_user_output($user, $embed_login) {
        $output = array();
        $output['login'] = TRUE;
        $output['embed_login'] = $embed_login;
        $output['user'] = array(
            'email' => $user->get_field('email'),
            'name' => $user->get_field('name'),
            'id' => $user->get_id(),
            'has_photo' => $user->has_photo(),
            'locale' => $user->get_locale(),
            'sex' => $user->get_sex()
        );
        
        //將使用者寫入Context當中
        set_context_user($user);

        $output = $this->_get_login_policy_output($output);

        require_once 'annotation_getter.php';
        $annotation_getter = new annotation_getter();
        $my_annotation = $annotation_getter->my();
        $output['policy']['my_basic'] = $my_annotation['basic'];
        $output['policy']['my_custom'] = $my_annotation['custom'];

        return $output;
    }

    /**
     * 登入之後使用者的權限
     */
    private function _get_login_policy_output($output = NULL) {

        if (is_null($output))
            $output = array();

        //Policy這邊還沒處理好QQ
        $output['policy'] = array(
            'read' => TRUE,
            'write' => TRUE,
            'show_navigation' => TRUE
        );
        require_once 'annotation_getter.php';
        $annotation_getter = new annotation_getter();
        $output['policy']['navigation_data'] = $annotation_getter->navigation();

        return $output;
    }

    private function _create_default_data() {

        require_once 'annotation_getter.php';
        $annotation_getter = new annotation_getter();

        $output = array(
            'login' => FALSE,
            'embed_login' => FALSE,
            'user' => array(
                'email' => NULL,
                'name' => NULL,
                'id' => NULL,
                'has_photo' => FALSE,
                'locale' => NULL,
                'sex' => NULL
            ),
            'policy' => array(
            	'read' => TRUE,
            	'write' => FALSE,
            	'show_navigation' => TRUE,
                'navigation_data' => $annotation_getter->navigation()
            )
        );

        return $output;
    }

    public function default_data($callback = NULL) {

        $output = $this->_create_default_data();

        return $this->_display_jsonp($output, $callback);
    }

    public function register($json, $callback) {

        $data = json_to_object($json);

        //test_msg('authentication.register before find_user');
        
        $user = $this->user->find_user($this->url, $data->email);
        
        
        //test_msg('authentication.register after find_user');

        //$output = NULL;
        //$output['login'] = FALSE;
        $output = $this->_create_default_data();

        $action = 8;
        $user_id = NULL;
        if (isset($user) )
        {
            //handle_error('user_already_exist');
            $output['error'] = 'user_already_existed';
            $action = 9;
        }
        else    //if (isset($user)) else
        {
            
            $user = $this->user->create_user($this->url, $data->email);
            
            $user->set_password($data->password);
            
            //test_msg('auth.regiester after create_user', $user->get_id());
            
            $user->update();
            
            //test_msg('auth.regiester after update password', $user->get_id());
            
            $output = $this->_parse_user_output($user, FALSE);
            
            
            //test_msg('auth.regiester after _parse_user_output', $output['user']);
            
            
            $user_id = $user->get_id();
        }

        kals_log($this->db, $action, array('user_id' => $user_id));
        context_complete();

        $this->_display_jsonp($output, $callback);
    }

    /**
     * 用來刪掉已經登入的使用者
     * 這是單元測試用的功能，平常不應該使用。
     * @param <String> $callback
     */
    public function deregister($callback) {

        //$data = json_to_object($json);
        //$user = $this->user->find_user($this->url, $data->email);
        $user = get_context_user();

        //$output = $this->_create_default_data();
        if (isset($user))
        {
            //$user->delete();
            //$user->update();

            //因為是測試資料，所以要完全地刪除
            $this->db->delete('user', array('user_id' => $user->get_id()));
        }

        $this->logout($callback);
    }

    public function encrypt_login($json, $callback) {
        //現在先不作加密式登入功能
        //未來有時間再來作
        
        //加密是：預設帳號+hash碼(依照refer網址產生的hash)的MD5編碼
        //然後用這個編碼去對照資料庫

        //所以就使用普通登入的功能
        $this->login($json, $callback);
    }

    public function logout($callback) {

        $user= get_context_user();
        $user_id = NULL;
        if (isset($user))
            $user_id = $user->get_id();
        kals_log($this->db, 7, array('memo'=>$this->client_ip, 'user_id' => $user_id));

        clear_context_user();
        context_complete();
        //$output = $this->_create_default_data();

        /*
        $user = get_context_user();
        $output = FALSE;
        if (is_null($user))
            $output = TRUE;
         */
        $output = $this->_create_default_data();
        $this->_display_jsonp($output, $callback);
    }

    public function check_user($callback)
    {
        $user = get_context_user();
        $id = null;
        if (isset($user))
            $id = $user->get_id();

        $data = array(
            'user' => array(
                'id' => $id
            )
        );

        $this->_display_jsonp($data, $callback);
    }

    public function check_login($callback)
    {
        $output = FALSE;

        $action = 1;
        $user_id = NULL;
        if (login_require(FALSE))   //表示有登入喔！
        {
            $user = get_context_user();
            set_context_user($user);
            $output = $this->_parse_user_output($user, FALSE);

            $user_id = $user->get_id();
        }
        else
        {
            $output = $this->_create_default_data();
            $action = 2;
        }
        
        $memo = $this->client_ip;
        kals_log($this->db, $action, array('memo'=>$memo, 'user_id' => $user_id));
        
        context_complete();
        return $this->_display_jsonp($output, $callback);
    }
}


/* End of file authentication.php */
/* Location: ./system/application/controllers/authentication.php */