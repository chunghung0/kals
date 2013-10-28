/**
 * Top_basic_annotation_loader
 *
 * @package    KALS
 * @category   Webpage Application Libraries
 * @author     Pudding Chen <puddingchen.35@gmail.com>
 * @copyright  Copyright (c) 2010, Pudding Chen
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://sites.google.com/site/puddingkals/
 * @version    1.0 2010/10/24 下午 06:40:07
 * @extends {Annotation_scope_loader}
 */
function Top_basic_annotation_loader() {
    
    Annotation_scope_loader.call(this);
    
}

Top_basic_annotation_loader.prototype = new Annotation_scope_loader();

/**
 * @type {Selection_my_manager}
 */
Top_basic_annotation_loader.prototype._selection = null;

Top_basic_annotation_loader.prototype._$load_url = 'annotation_getter/top_basic';

/**
 * 不使用自動重新讀取
 * 原本這邊是以毫秒為單位的數字，在Top_anntoation_loader這邊不使用這個功能，所以關掉
 */
Top_basic_annotation_loader.prototype._refresh_interval = null;

/**
 * 讀取標註資料並且設置
 * @param {Object} _data 預設的樣子如下：
 * _data = {
 *     1:    //類型代號，表示importance 
 *     [    //Scope_collection_param的JSON型態
 *         [1, 2],    //Scope_param的JSON型態
 *         [6, 9]
 *     ],
 *     2: [
 *     ]
 * }
 * @param {function} _callback
 */
Top_basic_annotation_loader.prototype.load_annotation = function (_data, _callback) {
    
    if ($.is_function(_data) && $.is_null(_callback))
    {
        _callback = _data;
        _data = null;
    }
    
    //$.test_msg('My_annotation_loader.load_annotation() data', _data);
    var _is_initialize = !(this.is_initialized());
    
    var _basic_scope = _data;
    for (var _i in _basic_scope)
    {
        var _type_id = _i;
        var _scope_coll_json = _basic_scope[_i];
        
        if (_scope_coll_json == null
            || _scope_coll_json.length == 0)
            continue;
        
        //$.test_msg('My_annotation_loader.load_annotation()', $.is_array(_scope_coll_json[0]));
        
        var _scope_coll = new Scope_collection_param(_scope_coll_json);
        
        //$.test_msg('My_annotation_loader.load_annotation() _scope_coll', [_scope_coll.length(), _is_initialize]); 
        
        this._selection.set_scope_coll(_type_id, _scope_coll, _is_initialize);
    }
    
    //if (this.is_initialized() == false)
    //{
        //回報已經完成初始化
    //    KALS_context.init_profile.complete('my_annotation');
    //}
    
    $.trigger_callback(_callback);
    
    return this;
};

Top_basic_annotation_loader.prototype.clear = function () {
    //$.test_msg('My_annotation_loader.clear()');
    this._selection.clear();
    
};

Top_basic_annotation_loader.prototype.initialize = function () {
    
    //$.test_msg('My_annotation_loader.initialize()', typeof(KALS_text));
    
    if (typeof(KALS_text) == 'object')
    {
        this._selection = KALS_text.selection.top_basic;
        
        var _this = this;
        //KALS_context.auth.add_listener(function (_auth, _data) {
        KALS_context.policy.add_attr_listener('top_basic', function(_policy) {
            
            var _top_basic = _policy.get_top_basic();
            
            //var _is_login = KALS_context.auth.is_login();	//因為排行榜即使登出狀態下也要顯示
            //所以這邊改為全部都假設為login=true
            var _is_login = true;
            
            //$.test_msg('My_annotation_loader.init()', ($.isset(_my_basic) && _is_login));
            
            if ($.isset(_top_basic) && _is_login)
            {
                if (_this.is_loaded())
                    return;
                    
                //_this.setup_loader(_my_basic);
                
                _this.setup_loader(_top_basic, function () {
                    _this.stop_loader();
                });
            }
            else
            {
                //$.test_msg('My_annotation_loader.init()');
                //_this.stop_loader();
                //_this._selection.clear();
                
                //_this.stop_loader();
                //_this.clear();
                _this.reset();
                
            }
        });
    }
};

Top_basic_annotation_loader.prototype._$exception_handle = function (_data) {
    
    if (this.is_initialized() == false)
    {
        //$.test_msg('My_annotation_loader._$exception_handle()');
        
        var _this = this;
        setTimeout(function () {
            _this.setup_loader(function () {
                KALS_context.init_profile.complete('top_basic_annotation');
            });    
        }, 5000);
        
    }
    
};

/* End of file My_annotation_loader */
/* Location: ./system/application/views/web_apps/My_annotation_loader.js */