<?php
/**
 * URL_fragment_dispatcher Unit Test
 *
 * @package             KALS
 * @category		Webpage Application QUnit
 * @author		Pudding Chen <puddingchen.35@gmail.com>
 * @copyright		Copyright (c) 2010, Pudding Chen
 * @license		http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link		http://sites.google.com/site/puddingkals/
 * @version		1.0 2010/7/20 上午 12:17:29
 */

//load_toolkit();     //讀取常用工具
//load_core();        //讀取核心類別

load_package();

?>
<script type="text/javascript">
QUNIT_TITLE = "URL_hash_dispatcher";
QUNIT_ASSERT = 7;

unit(function () {
    
    var test = TEST;
    
    var _listener = function (_dispatcher, _data) {
        
        var _annotation = _data.annotation;
        
        if (typeof(LOCK) != 'undefined')
        {
            test_equals(false, true
                , '失敗了，沒有正確地取消監聽');
        }
        else
        {
            //$.test_msg('那_data是什麼？', _data);
            test_equals(_annotation, '15'
                , '訂閱有出現，而且資料正確傳送到了！');    
        }
        
        LOCK = true;
        
        KALS_context.hash.delete_listener(_listener);
    };
    
    KALS_context.hash.add_listener(_listener);
    
    KALS_context.hash.set_field('annotation', 15);
    
    setTimeout(function () {
        test_not_equals(location.href.indexOf('annotation=15'), -1
            , '網址上是否出現annotation=15？');
              
        unit();    
    }, 100);
});

unit(function () {
    
    //var test = TEST;
    //return;
    
    test_equals(KALS_context.hash.get_field('annotation')
        , '15'
        , '測試取得資料');
    
    unit();
});

unit(function () {
    
    KALS_context.hash.set_field('annotation', 64);
    
    setTimeout(function () {
        test_not_equals(location.href.indexOf('annotation=64'), -1
            , '第二次設定，網址上是否出現annotation=64？');
              
        unit();
    }, 100);
});

unit(function () {
    //偵測上一頁事件
    
    var _listener = function (_disp, _data) {
        
        test_equals(typeof(_data.backward), 'boolean'
            , '是否真的有取得上一頁事件？');
        
        if (typeof(_data.backward) == 'boolean')
        {
            test_equals(_data.backward, true
                , '再次確認_data.backward的內容，是否真的有取得上一頁事件？');    
        }
        
        KALS_context.hash.delete_listener(_listener);
    };
    
    KALS_context.hash.add_listener(_listener);
    
    //執行上一頁看看
    history.back();
    
    setTimeout(function () {
        
        test_equals(KALS_context.hash.get_field('annotation'), '15'
            , '上一頁之後呢？');
        
        unit();
        
    }, 100);
});

</script>
<style type="text/css">
/**
 * 您可以在此寫入CSS內容
 */
</style>

<!--
  您可以在此寫入HTML內容
  -->

<?php
/* End of file URL_fragment_dispatcher.php */
/* Location: ./system/application/views/qunit/core/URL_fragment_dispatcher.php */