/**
 * Embed_navigation
 *
 * @package    KALS
 * @category   Webpage Application Libraries
 * @author     Pudding Chen <puddingchen.35@gmail.com>
 * @copyright  Copyright (c) 2010, Pudding Chen
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://sites.google.com/site/puddingkals/
 * @version    1.0 2010/10/3 下午 09:44:04
 * @extends {Navigation_list}
 */
function Embed_navigation(_common_windows) {

    Navigation_list.call(this);

    this._$nav_items = [
        new Window_profile()
        // 外觀設定的功能暫時不做，等畢業之後有空再進行
        //, new Window_style()
        //'Window_profile'
    ];
    
    if ($.isset(_common_windows)) {
        for (var _i in _common_windows) {
			this._$nav_items.push(_common_windows[_i]);
		}
    }
}

Embed_navigation.prototype = new Navigation_list();

Embed_navigation.prototype._$classname = 'embed-navigation';

/* End of file Embed_navigation */
/* Location: ./system/application/views/web_apps/Embed_navigation.js */
