<?php 

 //载入公共函数模块
require_once '../functions.php';

if (empty($_GET['id'])) {
	exit('请传入必须的参数');	
}

//int强制转换解决 sql注入问题(id=1 or 1=1)
// $id = (int)$_GET['id'];
$id = $_GET['id'];
$rows = xiu_execute('DELETE FROM users WHERE id IN ('.$id.');');

if ($rows<=0) {
	exit('删除失败');
}
header('Location: /admin/users.php');
