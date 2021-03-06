/**
 * Init_profile
 *
 * @package    KALS
 * @category   Webpage Application Libraries
 * @author     Pudding Chen <puddingchen.35@gmail.com>
 * @copyright  Copyright (c) 2010, Pudding Chen
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://sites.google.com/site/puddingkals/
 * @version    1.0 2010/8/12 下午 08:10:10
 * @extends {Task_event_dispatcher}
 * @param {function|null} _onstart 開始動作 
 * @param {function|null} _oncomplete 所有任務都完成之後的動作
 */
function Init_profile(_onstart, _oncomplete) {
    
    Task_event_dispatcher.call(this, _onstart, _oncomplete);
    
    this._$schedule_task = ['check_login', 'notification', 'navigation_annotation', 'my_style', 'hash'];
    
}

Init_profile.prototype = new Task_event_dispatcher();

Init_profile.prototype._$onstart = function () {
    //KALS_context資料初始化之後，才能進行其他資料的讀取
    
    //$.test_msg('Init_profile._$onstart()');
    
    KALS_context.init_profile.complete('notification');
    
    //KALS_text.load_my_basic.initialize();
    //KALS_text.load_my_custom.initialize();
    KALS_text.load_my.initialize();
    
    //KALS_context.init_profile.complete('my_annotation');
    
    KALS_text.load_navigation.initialize();
    //KALS_context.init_profile.complete('navigation_annotation');
    
    KALS_context.init_profile.complete('my_style');
    
    //check_login是指去確認遠端伺服器上是否已經有登入的資料，如果有的話，則將現在狀況設為登入的狀況
    //KALS_context.init_profile.complete('check_login');
    //KALS_context.init_profile.complete('my_basic_annotation');    //因為登入之後就會自動讀取，所以此處不用去確認
    
    //$.test_msg('Init_profile.prototype._$onstart()');
    
    KALS_context.auth.check_login(function() {
        KALS_context.init_profile.complete('check_login');
        
        //$.test_msg('KALS_context.auth.check_login() callback');
        
        setTimeout(function () {
			
            KALS_context.hash.check_hash(function () {
                KALS_context.init_profile.complete('hash');
            });
            
			//@TODO 暫時先關掉
			//KALS_context.init_profile.complete('hash');
        }, 500);
            
    });
};

Init_profile.prototype._$oncomplete = function () {
    
    //2010.10.21 在Init_component時已經load了
    //KALS_context.load(function () {
        //KALS_context.completed = true;
        //$.test_msg('KALS_context.completed = true;');
    //});
    
    KALS_context.completed = true;
	
	/**
	 * @version 20130224 布丁測試用
	 */
	/*
	var _sentence_list = KALS_text.selection.text.get_sentence_index();
	
	var _php_array = 'new Array(';
	for (var _i = 0; _i < _sentence_list.length; _i++)
	{
		if (_i != 0)
			_php_array = _php_array + ',';
		_php_array = _php_array + _sentence_list[_i];
	}
	_php_array = _php_array + ');';
	
	$.test_msg('count .kals-sentence-punctuation', $('.kals-sentence-punctuation').length);
	$.test_msg('list .kals-sentence-punctuation', _sentence_list);
	$.test_msg('list php array .kals-sentence-punctuation', _php_array);
	*/
};

/* End of file Init_profile */
/* Location: ./system/application/views/web_apps/Init_profile.js */