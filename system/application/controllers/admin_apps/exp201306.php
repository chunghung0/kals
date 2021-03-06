<?php
/**
 * exp201306
 *
 * exp201306 full description.
 *
 * @package		KALS
 * @category		Controllers
 * @author		Pudding Chen <puddingchen.35@gmail.com>
 * @copyright		Copyright (c) 2010, Pudding Chen
 * @license		http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link		http://sites.google.com/site/puddingkals/
 * @version		1.0 2013/6/10 上午 03:56:40
 */

class exp201306 extends Controller {

    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
        
        /*
select "user".user_id, count(annotation_id) as topic from annotation, "user" 
where annotation.user_id = "user".user_id
and annotation.create_timestamp > '2013-06-10 14:00:00+08'
and annotation.create_timestamp < '2013-06-17 18:00:00+08'
and "user".user_id > 2006 and "user".user_id < 2061
and topic_id is null
 group by "user".user_id  
order by topic desc

select "user".user_id, count(annotation_id) as respond from annotation, "user" 
where annotation.user_id = "user".user_id
and annotation.create_timestamp > '2013-06-10 14:00:00+08'
and annotation.create_timestamp < '2013-06-17 18:00:00+08'
and "user".user_id > 2006 and "user".user_id < 2061
and topic_id is not null
 group by "user".user_id  
order by respond desc

select "user".name, (t.topic + r.respond*2) as score , t.topic as topic, r.respond as respond 
form
(select "user".user_id, count(annotation_id) as topic from annotation, "user" 
where annotation.user_id = "user".user_id
and annotation.create_timestamp > '2013-06-10 14:00:00+08'
and annotation.create_timestamp < '2013-06-17 18:00:00+08'
and "user".user_id > 2006 and "user".user_id < 2061
and topic_id is null
and annotation.deleted is false
 group by "user".user_id  
order by topic desc) as t, 
(select "user".user_id, count(annotation_id) as respond from annotation, "user" 
where annotation.user_id = "user".user_id
and annotation.create_timestamp > '2013-06-10 14:00:00+08'
and annotation.create_timestamp < '2013-06-17 18:00:00+08'
and "user".user_id > 2006 and "user".user_id < 2061
and topic_id is not null
and annotation.deleted is false
 group by "user".user_id  
order by respond desc) as r,
"user"
where "user".user_id = t.user_id
order by score desc
limit 0, 3
         * 
select "user".name, (t.topic + r.respond*2) as score, t.topic as topic, r.respond as respond 
from
(select "user".user_id, count(annotation_id) as topic from annotation, "user" 
where annotation.user_id = "user".user_id
and annotation.deleted is false
and topic_id is null
 group by "user".user_id  
order by topic desc) as t, 
(select "user".user_id, count(annotation_id) as respond from annotation, "user" 
where annotation.user_id = "user".user_id
and topic_id is not null
and annotation.deleted is false
 group by "user".user_id  
order by respond desc) as r,
"user"
where "user".user_id = t.user_id
order by score desc
limit 3
         */

/**
 * 
select "user"."user_id", "user".name, 
"t1".topic as topic, 
case when ("r2".respond is null) then 0 else "r2".respond END AS r3,
topic + (case when ("r2".respond is null) then 0 else "r2".respond END)*2 as score

from
(select "user".user_id, count(annotation_id) as topic from annotation, "user" 
where annotation.user_id = "user".user_id
and annotation.create_timestamp > '2013-06-10 14:00:00+08'
and annotation.create_timestamp < '2013-06-17 18:00:00+08'
and "user".user_id > 2006 and "user".user_id < 2061
and topic_id is null
and annotation.deleted is false
 group by "user".user_id  
order by topic desc) t1 left join
(select "user".user_id, count(annotation_id) as respond from annotation, "user" 
where annotation.user_id = "user".user_id
and annotation.create_timestamp > '2013-06-10 14:00:00+08'
and annotation.create_timestamp < '2013-06-17 18:00:00+08'
and "user".user_id > 2006 and "user".user_id < 2061
and topic_id is not null
and annotation.deleted is false
 group by "user".user_id  
order by respond desc) r2 using (user_id), 
"user"
where "user".user_id = "t1".user_id
order by score desc
 */
        $class_exp = ' "user".user_id > 2006 and "user".user_id < 2036 ';
        $class_ctl = ' "user".user_id > 2035 and "user".user_id < 2061 ';
        $time = "and annotation.create_timestamp > '2013-06-10 14:00:00+08'
and annotation.create_timestamp < '2013-06-17 18:00:00+08'";
        
        $select_exp = 'select "user"."user_id", "user".name, 
"t1".topic as topic, 
case when ("r2".respond is null) then 0 else "r2".respond END AS respond,
topic + (case when ("r2".respond is null) then 0 else "r2".respond END)*2 as score

from
(select "user".user_id, count(annotation_id) as topic from annotation, "user" 
where annotation.user_id = "user".user_id
'.$time.'
and '.$class_exp.'
and topic_id is null
and annotation.deleted is false
 group by "user".user_id  
order by topic desc) t1 left join
(select "user".user_id, count(annotation_id) as respond from annotation, "user" 
where annotation.user_id = "user".user_id
'.$time.'
and '.$class_exp.'
and topic_id is not null
and annotation.deleted is false
 group by "user".user_id  
order by respond desc) r2 using (user_id), 
"user"
where "user".user_id = "t1".user_id
order by score desc';
        // user_id 2007~2061
        
        // 5-4 2007~2035
        // 5-2 2036~2061
        
        echo '<div style="float:left">';
        echo "<h1>5-4 排行榜</h1>";
        echo "<table border='1'><tbody>";
        echo "<tr><th>座號</th><th>總分</th><th>主旨</th><th>回應</th></tr>";
        
        
        $query = $this->db->query($select_exp);
        foreach ($query->result() as $row) {
            
            $rank_info = '<tr>
                    <td>'.$row->name.'</td>
                    <td>'.$row->score.'</td>
                    <td>'.$row->topic.'</td>
                    <td>'.$row->respond.'</td>
                </tr>';
            echo $rank_info;
        }
        
        echo "</tbody></table>";
        echo "</div>";
        
        // ----------------------------------------
        
        $select_ctl = 'select "user"."user_id", "user".name, 
"t1".topic as topic, 
case when ("r2".respond is null) then 0 else "r2".respond END AS respond,
topic + (case when ("r2".respond is null) then 0 else "r2".respond END)*2 as score

from
(select "user".user_id, count(annotation_id) as topic from annotation, "user" 
where annotation.user_id = "user".user_id
'.$time.'
and '.$class_ctl.'
and topic_id is null
and annotation.deleted is false
 group by "user".user_id  
order by topic desc) t1 left join
(select "user".user_id, count(annotation_id) as respond from annotation, "user" 
where annotation.user_id = "user".user_id
'.$time.'
and '.$class_ctl.'
and topic_id is not null
and annotation.deleted is false
 group by "user".user_id  
order by respond desc) r2 using (user_id), 
"user"
where "user".user_id = "t1".user_id
order by score desc';
        // user_id 2007~2061
        
        // 5-4 2007~2035
        // 5-2 2036~2061
        echo '<div style="float:left;margin-left: 1em;">';
        echo "<h1>5-2 排行榜</h1>";
        echo "<table border='1'><tbody>";
        echo "<tr><th>座號</th><th>總分</th><th>主旨</th><th>回應</th></tr>";
        
        
        $query = $this->db->query($select_ctl);
        foreach ($query->result() as $row) {
            
            $rank_info = '<tr>
                    <td>'.$row->name.'</td>
                    <td>'.$row->score.'</td>
                    <td>'.$row->topic.'</td>
                    <td>'.$row->respond.'</td>
                </tr>';
            echo $rank_info;
        }
        
        echo "</tbody></table>";
        echo "</div>";
    }
}

/* End of file exp201306.php */
/* Location: ./system/application/controllers/exp201306.php */