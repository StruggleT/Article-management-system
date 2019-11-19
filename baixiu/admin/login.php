<?php 

//载入公共函数
require_once  '../functions.php';
//载入配置文件
// require_once '../config.php';

// //给用户找一个箱子(session)
// session_start();

function login(){
  //表单校验
  if (empty($_POST['email'])) {
    $GLOBALS['error_mes'] = '请输入邮箱';
    return;
  }
  if (empty($_POST['password'])) {
    $GLOBALS['error_mes'] = '请输入密码';
    return;
  }

  $email = $_POST['email'];
  $password = $_POST['password'];

  //当客户端提交过来的完整的表单信息就应该开始对其进行数据校验

  $user = xiu_get_mysql_one("select * from users where  email='{$email}' limit 1;"); 

  if (!$user) {
    $GLOBALS['error_mes'] = '账号与密码不匹配';
    return;
  }
  if ($user['password'] !== md5($password)) {
    $GLOBALS['error_mes'] = '账号与密码不匹配';
    return;
  }

  //存一个用户标识
  $_SESSION['current_login_user'] = $user;

  $user_avatar = $user['avatar'];

  $user_email = $user['email'];
  //设置cookie 来储存头像和email，以便于退出登录时显示
  setcookie('user_avatar',$user_avatar);

  setcookie('user_email',$user_email);

  header('Location: /admin/');

}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    login();
}

//退出登录(把session去掉)
if ($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['action']) && $_GET['action'] ==='logout') {
    unset($_SESSION['current_login_user']);
    //判断是否有cookie
    if (!empty($_COOKIE['user_avatar'])&&!empty($_COOKIE['user_email'])) {
      $auto_get_avatar=$_COOKIE['user_avatar'];
      $auto_get_email=$_COOKIE['user_email'];
    }    
}

 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <link rel="stylesheet" href="/static/assets/vendors/animate/animate1.css?v=20190110">
</head>
<body>
  <div class="login">
    <form class="login-wrap<?php echo isset($error_mes)?' shake animated':'' ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="on" novalidate>
      <img class="avatar" src="<?php echo isset($auto_get_avatar) ? $auto_get_avatar : '/static/assets/img/default.png' ?>">
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong> 用户名或密码错误！
      </div> -->
      <?php if (isset($error_mes)): ?>
        <div class="alert alert-danger">
        <strong>错误!</strong> <?php echo $error_mes; ?>
      </div>    
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus value="<?php echo isset($auto_get_email)?$auto_get_email :'' ?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block" href="index.html">登 录</button>
    </form>
  </div>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script>
      //键盘抬起时候发送AJAX请求，从服务端获取到头像路径
      $(function ($) {

        var emailFormat = /^[0-9a-zA-Z_.-]+[@][0-9a-zA-Z_.-]+([.][a-zA-Z]+){1,2}$/


        $('#email').on('keyup',function () {

          var value = $(this).val()

          if (!value || !emailFormat.test(value)) return
      
          $.get('/admin/api/avatar.php',{email : value}, function (res) {

            if (!res) return

             $('.avatar').fadeOut(function () {
                //图片全部加载完成
               $(this).on('load',function () {

                 $(this).fadeIn()
                 
               }).attr('src',res)
             })
          })
        })
      })
  </script>
</body>
</html>
