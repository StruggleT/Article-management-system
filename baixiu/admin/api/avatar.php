<?php  

/**
*根据客户端的邮箱获取头像
*
*/
  require_once '../../config.php';

  if (empty($_GET['email'])) {
  	exit('<h1>请输入邮箱</h1>');
  }

    $email = $_GET['email'];
	//连接数据库
  $conn = mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);
	if (!$conn) {
    exit('<h1>连接数据库失败</h1>');
  }

  $query = mysqli_query($conn,"select avatar from users where  email='{$email}' limit 1;");

  if (!$query) {
    exit('<h1>查询数据失败</h1>');
  }
  //获取用户账号
  $user=mysqli_fetch_assoc($query);
 
   $avatar = $user['avatar'];
   echo $avatar;