<?php  

/**
 * 接收客户端发过来的AJAX,返回评论数据
 */

//引入封装的函数
require_once '../../functions.php';

//接收客户端传过来的page参数

$page = empty($_GET['page']) ?  1 : (int)$_GET['page'];
//取多少条数据
$length = 30;

$offset = ($page - 1) * $length;

$comment = xiu_get_mysql_all("select 
comments.*,
posts.title as posts_title,
parent.content
from comments
inner join posts on comments.post_id = posts.id
inner join comments as parent on comments.parent_id = parent.id
order by comments.created desc
limit {$offset},{$length};");

$total_date = xiu_get_mysql_one('select 
count(1) as count
from comments
inner join posts on comments.post_id = posts.id;')['count'];

$total_pages = ceil($total_date / $length);

$result = array('comment' => $comment,
				'total_pages' => $total_pages
				 );

$json = json_encode($result);

//返回的数据类型

header('Content-Type: application/json');

echo $json;