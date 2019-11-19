<?php 
//载入公共函数模块
require_once '../functions.php';

xiu_get_current_user();
//添加数据
  //=======================================================
  function add_users() {
    //添加
    if (empty($_POST['slug']) || empty($_POST['email']) || empty($_POST['nickname']) || empty($_POST['password'])) {
    $GLOBALS['error_mes'] = '请在表单填写相应的内容';
    $GLOBALS['success'] = false;
    return;
    }
    $slug = $_POST['slug'];
    $email = $_POST['email'];
    $nickname = $_POST['nickname'];
    $password = $_POST['password'];
    $avatar = '/static/uploads/avatar_2.png';
    $rows = xiu_execute("insert into users values (null,'{$slug}','{$email}','{$password}','{$nickname}','{$avatar}',null,'activated');");  
    $GLOBALS['success'] = $rows > 0;
    $GLOBALS['error_mes'] = $rows <= 0 ? '添加失败！' : '添加成功！';
  }
  if (empty($_GET['id'])) {
    if ($_SERVER['REQUEST_METHOD']==='POST') {
      add_users();
    } 
  }
//查询所有的users的信息
$users_data = xiu_get_mysql_all('SELECT * FROM users;');
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Users &laquo; Admin</title>
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
        <h1>用户</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->

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
      <div class="row">
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method = 'post' autocomplete='off'>
            <h2>添加新用户</h2>
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" placeholder="密码">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" href="/admin/user-delete.php" style="display: none" id="btn_delete">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
               <tr>
                <th class="text-center" width="40"><input type="checkbox" id="text-center"></th>
                <th class="text-center" width="80">头像</th>
                <th>邮箱</th>
                <th>别名</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users_data as $item): ?>
               <tr>
                <td class="text-center"><input type="checkbox" data-id='<?php echo $item['id'] ?>'></td>
                <td class="text-center"><img class="avatar" src="<?php echo $item['avatar'] ?>"></td>
                <td><?php echo $item['email'] ?></td>
                <td><?php echo $item['slug'] ?></td>
                <td><?php echo $item['nickname'] ?></td>
                <td><?php echo $item['status'] ?></td>
                <td class="text-center">
                  <a href="/admin/profile.php?id=<?php echo $item['id'] ?>" class="btn btn-default btn-xs">编辑</a>
                  <a href="/admin/user-delete.php?id=<?php echo $item['id'] ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr> 
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php $message = 'users'; ?>
  <?php include 'inc/aside.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    
    //批量删除按钮的显示
    
    $(function ($) {

      var allInput = $('tbody input')

      var btndelete = $('#btn_delete')

      //定义一个数组记录被选中的
      var allCheckeds = []
      //优化后的代码
       allInput.on('change',function () {
          
          var id = $(this).data('id')
          //如果选择了就把自定义的ID属性加到数组中，如果没有选择就移除
          if ($(this).prop('checked')) {
          //短路运算，如果有该ID，就不用加到数组里面去  
          // allCheckeds.indexOf(id)===-1 || allCheckeds.push(id)
           allCheckeds.includes(id) || allCheckeds.push(id)
          }
          else{
            allCheckeds.splice(allCheckeds.indexOf(id),1)
          }

          allCheckeds.length ? btndelete.fadeIn() : btndelete.fadeOut()

          btndelete.prop('search','?id=' + allCheckeds)   

       })

       //全选
       $('#text-center').on('change',function () {
         
         var checked = $(this).prop('checked')
         //手动触发事件
         allInput.prop('checked',checked).trigger('change')

       })

    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
