/**
 * KALS_text
 * 實體化交給KALS_context.init_component吧！
 *
 * @package    KALS
 * @category   Webpage Application Libraries
 * @author     Pudding Chen <puddingchen.35@gmail.com>
 * @copyright  Copyright (c) 2010, Pudding Chen
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://sites.google.com/site/puddingkals/
 * @version    1.0 2010/10/15 下午 04:06:28
 * @extends {KALS_user_interface}
 * @param {String|jQuery} _selector 標註範圍
 */
function KALS_text(_selector) {
    
    KALS_user_interface.call(this);
    
	if (typeof(_selector) == "undefined") {
		_selector = this.get_selector();	
	}
    //$.test_msg('KALS_text()', _selector);
    
    _selector = this.filter_selector(_selector);
    
    this.child('init', new Init_text());
    this.child('selection', new Selection_manager(_selector));
    this.child('load_my', new My_annotation_loader());
    this.child('load_navigation', new Navigation_loader());
    this.child('tool', new Annotation_tool(_selector));
    
    var _this = this;
    setTimeout(function() {
        _this.init_start();
          
        //_this.load_my.initialize();
    }, 0);
}

// Extend from KALS_user_interface
KALS_text.prototype = new KALS_user_interface();

/**
 * @type {Selection_manager}
 */
KALS_text.prototype.selection = null;

/**
 * @type {Text_margin}
 * @deprecated 2010.10.21 因為改變了定位型態
 */
//KALS_text.prototype.margin = null;

/**
 * @type {My_annotation_loader}
 */
KALS_text.prototype.load_my = null;

/**
 * @type {My_basic_annotation_loader}
 */
KALS_text.prototype.load_my_basic = null;

/**
 * @type {My_custom_annotation_loader}
 */
KALS_text.prototype.load_my_custom = null;

/**
 * @type {Recommend_loader}
 */
KALS_text.prototype.load_recommend = null;

KALS_text.prototype.init_start = function () {
    
    this.init.start();
    
    return this;
};

KALS_text.prototype.get_selector = function () {
    return KALS_context.get_text_selector();
};

/**
 * 確認選取範圍是否正確
 * @param {jQuery|String} _selector
 * @return {jQuery}
 * @version 2010 Pudding Chen
 */
KALS_text.prototype.filter_selector = function (_selector) {
    
    var _exception = null;
    
    if ($.is_string(_selector)) {
        _selector = $(_selector);
    }
    else if ($.is_jquery(_selector)) {
        //_selector = _selector;
        //不做任何事情，因為他本來就是jQuery型態           
    }
    else {
        //丟出錯誤
        _exception = new KALS_exception('kals_text.exception.selector_not_exist');
        KALS_util.show_exception(_exception);
        return null;
    }
    
    if (_selector.length > 0) {
		return _selector;
	}
	else {
		//找不到_selector，丟出錯誤
		_exception = new KALS_exception('kals_text.exception.selector_not_exist');
		KALS_util.show_exception(_exception);
		return null;
	}
};

/* End of file KALS_text */
/* Location: ./system/application/views/web_apps/KALS_text.js */