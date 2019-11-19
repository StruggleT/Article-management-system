<?php 
 //载入公共函数模块
require_once '../functions.php';

xiu_get_current_user();

$current_user = xiu_get_current_user();

//添加数据
//=======================================================
function add_users() {
  global $current_user;
  //添加
  if (empty($_POST['slug']) || empty($_POST['nickname'])) {
  $GLOBALS['error_mes'] = '请在表单填写相应的内容';
  $GLOBALS['success'] = false;
  return;
  }
  $slug = $_POST['slug'];
  $name = $_POST['nickname'];
  $id = $current_user['id'];

  // 接收文件并验证
  if (empty($_FILES['avatar'])) {
    $GLOBALS['error_message'] = '请上传头像';
    return;
  }

  $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
  // => jpg
  $target = '../static/uploads/avatar-' . uniqid() . '.' . $ext;

  if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
    $GLOBALS['error_message'] = '上传头像失败';
    return;
  }
  $avatar = substr($target, 2);

  $rows = xiu_execute("update users set nickname = '{$name}', slug = '{$slug}',avatar = '{$avatar}' where id={$id};");  
  $GLOBALS['success'] = $rows > 0;
  $GLOBALS['error_mes'] = $rows <= 0 ? '更新失败！' : '更新成功！';
}         
if ($_SERVER['REQUEST_METHOD']==='POST') {
  add_users();
  $updated = xiu_get_mysql_one('select * from users where id='.$current_user['id']);
}
    
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Dashboard &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>我的个人资料</h1>
      </div>
      <?php if (isset($error_mes)): ?>
        <?php if ($success): ?>
          <div class="alert alert-success">
          <strong>成功！</strong><?php echo $error_mes ?>
          </div>
        <?php else: ?>
          <div class="alert alert-danger">
          <strong>错误！</strong><?php echo $error_mes ?>
          </div> 
        <?php endif ?> 
      <?php endif ?> 
      <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF']; ?>?email='1871277273@qq.com'" method = "post" autocomplete = "off"  enctype="multipart/form-data">
        <div class="form-group">
          <label class="col-sm-3 control-label">头像</label>
          <div class="col-sm-6">
            <label class="form-image">
              <input id="avatar" type="file" name="avatar">
              <img src="<?php echo isset($updated) ?  $updated['avatar']  : $current_user['avatar']; ?>">
              <i class="mask fa fa-upload"></i>
            </label>
          </div>
        </div>
        <div class="form-group">
          <label for="email" class="col-sm-3 control-label">邮箱</label>
          <div class="col-sm-6">
            <input id="email" class="form-control" name="email" type="type" value="<?php echo $current_user['email'] ?>" placeholder="邮箱" readonly>
            <p class="help-block">登录邮箱不允许修改</p>
          </div>
        </div>
        <div class="form-group">
          <label for="slug" class="col-sm-3 control-label">别名</label>
          <div class="col-sm-6">
            <input id="slug" class="form-control" name="slug" type="type" value="<?php echo $current_user['slug'] ?>" placeholder="slug">
            <p class="help-block">https://zce.me/author/<strong>zce</strong></p>
          </div>
        </div>
        <div class="form-group">
          <label for="nickname" class="col-sm-3 control-label">昵称</label>
          <div class="col-sm-6">
            <input id="nickname" class="form-control" name="nickname" type="type" value="<?php echo $current_user['nickname'] ?>" placeholder="昵称">
            <p class="help-block">限制在 2-16 个字符</p>
          </div>
        </div>
        <div class="form-group">
          <label for="bio" class="col-sm-3 control-label">简介</label>
          <div class="col-sm-6">
            <textarea id="bio" class="form-control" placeholder="Bio" cols="30" rows="6">MAKE IT BETTER!</textarea>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-6">
            <button type="submit" class="btn btn-primary" id = "btn_ajax">更新</button>
            <a class="btn btn-link" href="password-reset.php">修改密码</a>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php $message = 'profile'; ?>
  <?php include 'inc/aside.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
