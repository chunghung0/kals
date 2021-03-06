/**
 * List_collection_search-由此送出做查尋並接收result畫出無限捲軸
 *
 * @package    KALS
 * @category   Webpage Application Libraries
 * @author     Pudding Chen <puddingchen.35@gmail.com>
 * @copyright  Copyright (c) 2010, Pudding Chen
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://sites.google.com/site/puddingkals/
 * @version    1.0 2010/10/27 下午 09:03:09
 * @extends {List_collection}
 */
function List_collection_search() {
    
    List_collection.call(this);
    
	
	//.list-collection.search .list-menu {
	//  text-align: right;
	//}
}

List_collection_search.prototype = new List_collection();

List_collection_search.prototype._$name = 'search';

List_collection_search.prototype._$target_my = false;

List_collection_search.prototype._$target_like = false;

List_collection_search.prototype._$need_login = null;

//接收資料的來源
List_collection_search.prototype._$load_url = 'annotation_getter/search_annotation'; 

List_collection_search.prototype._$limit = null;




/**
 * 搜尋範圍
 * @type {String}
 */
List_collection_search.prototype._searchrange;

/**
 * 關鍵字
 * @type {String}
 */
List_collection_search.prototype._keyword;

/**
 * 排序
 * @type {type}
 */
//List_collection_search.prototype.order_by;
//因為List_collection中已經有$order_by，直接拿來使用


/**
 * 設定要搜尋的物件
 * 
 * @type {JSON}
 */
List_collection_search.prototype.get_search_data = function(){
	var _search_data = {};
	
	_search_data.searchrange = this._searchrange;
	_search_data.keyword = this._keyword;
	_search_data.order_by = this._$order_by;
	

	return _search_data;
};

/**
 * 設定搜尋範圍
 * @param {string} _searchrange
 */
List_collection_search.prototype.set_searchrange = function (_searchrange) {
	this._searchrange = _searchrange;
};

/**
 * 設定關鍵字
 * @param {string} _keyword
 */
List_collection_search.prototype.set_keyword = function (_keyword) {
	this._keyword = _keyword;
};

/**
 * 設定排序
 * @param {string} _order_by
 */
List_collection_search.prototype.set_order_by = function (_order_by) {
	this._$order_by = _order_by;
};


// 開始建立List_item-topic & respond
List_collection_search.prototype.create_list_item = function(_param) {
    if (this._$target_topic === true) {
        return new List_item_search_topic(_param, this._topic_param);
    }
    else {
        return new List_item_search_respond(_param, this._topic_param);
    }
};

List_collection_search.prototype.get_search_data = function () {
	
    var _search_data = {};
    
    //如果有指定target id，則就不需要其他參考資料
    if ($.isset(this._$topic_id)) {
        _search_data.topic_id = this._$topic_id;
        
        if ($.isset(this._$limit)) {
			_search_data.limit = this._$limit;
		}
            
        if ($.isset(this._$target_topic)) {
			_search_data.target_topic = this._$target_topic;
		}
        if ($.isset(this._$order_by) && this._$order_by != 'score') {
			_search_data.order_by = this._$order_by;
		}
            
        if ($.isset(this._offset)) {
			_search_data.offset = this._offset;
		}
            
        return _search_data;
    }
       
    //需要登入身分的兩個參數
    if (($.isset(this._$target_like) || $.isset(this._$target_my)) &&
	KALS_context.auth.is_login() === false) {
		return null;
	}
    
    if ($.isset(this._$target_like)) {
		_search_data.target_like = this._$target_like;
	}
    if ($.isset(this._$target_my)) {
		_search_data.target_my = this._$target_my;
	}
    
    if ($.isset(this._$limit)) {
		_search_data.limit = this._$limit;
	}
    
    if ($.isset(this._$target_topic)) {
		_search_data.target_topic = this._$target_topic;
	}
    if ($.isset(this._$order_by) && this._$order_by != 'score') {
		_search_data.order_by = this._$order_by;
	}
        
    if ($.isset(this._offset)) {
		_search_data.offset = this._offset;
	}
    
    //$.test_msg('Respond_list_collection.get_search_data()', _data);
    _search_data.searchrange = this._searchrange;
	_search_data.keyword = this._keyword;
	_search_data.order_by = this._$order_by;
	
    _search_data.show_total_count = true;
    

	return _search_data;
    return _data;
};

// 呈現結果數量
List_collection_search.prototype.setup_load_list = function(_data, _callback){
	
	// 做一個假的_this，讓function中使用
	var _this = this;
	
	return List_collection.prototype.setup_load_list.call(this, _data, function () {

		// 取得UI
	   var _ui = _this.get_ui();
		
	   var _search_count =_data.total_count;
	   var _search_loaded = _data.totally_loaded;
	   
	   var _show_result_row = _ui.find(".totally_loaded"); //全部讀完
	   var _show_no_result_row = _ui.find(".no_result");  //無查詢結果
	  
	   //顯示查詢訊息
	   if (_search_loaded === true && _search_count === 0){ 
		    _show_no_result_row.show();
			_show_result_row.hide();	   
		  }
		else if(_search_loaded === true && _search_count !== 0 ) {
		    _show_result_row.show();
		    _show_no_result_row.hide();
		}
	
        //顯示查詢結果	
                _ui.find(".result-number").show();
		_ui.find(".result-number dd:first").html(_search_count);
 	
		
		$.test_msg('_search_count', _search_count);
		$.test_msg('_search_loaded',_search_loaded);

		_callback();
	});
	
};

// 沒有其他查詢結果了
List_collection_search.prototype._$create_ui = function () {
    
	var _factory = KALS_window.ui; 
	
	var _ui = $('<div></div>')
        .addClass('list-collection')
        .addClass(this._$name);
    
    var _container = $('<div></div>')
        .addClass('list-container')
        .appendTo(_ui);
   
		//結果數量
		
    var _result_number; 
	var _search_number_row = _factory.row(
        new KALS_language_param('Searchnumber','window.content.searchnumber'), '0')
	    .addClass('result-number')
            .hide()
	    .prependTo(_ui); 
		
  // _search_number_row.parent(".list-collection search").find('dd').addClass('number');

	var _result_row =_factory.heading_row(
       
		new KALS_language_param('no-else-result','window.content.loaded_already'))
		.addClass('totally_loaded')
		.appendTo(_ui);
    
	var _no_result_row =_factory.heading_row(
       
		new KALS_language_param('no-result','window.content.noresult'))
		.addClass('no_result')
		.appendTo(_ui);
  
	// 隱藏,再由totally_loaded與 total_count來判斷是否顯示
	_result_row.hide(); 
	_no_result_row.hide();
	

	
    this._list_container = _container;
	
	
    return _ui;
};

/* End of file List_collection_search */
/* Location: ./system/application/views/web_apps/List_collection_search.js */