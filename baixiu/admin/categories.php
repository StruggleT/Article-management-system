<?php 
  //载入公共函数模块
require_once '../functions.php';

  xiu_get_current_user();

  //修改要在查询之前

  //添加数据
  //=======================================================
  function add_category() {
    //添加
    if (empty($_POST['slug']) || empty($_POST['name'])) {
    $GLOBALS['error_mes'] = '请在表单填写相应的内容';
    $GLOBALS['success'] = false;
    return;
    }
    $slug = $_POST['slug'];
    $name = $_POST['name'];
    $rows = xiu_execute("insert into categories values (null,'{$slug}','{$name}');");  
    $GLOBALS['success'] = $rows > 0;
    $GLOBALS['error_mes'] = $rows <= 0 ? '添加失败！' : '添加成功！';
  }

  //编辑数据
  //=============================================================
  function edit_category() {
  //编辑
    global $current_edit_form;
    if (empty($_POST['slug']) || empty($_POST['name'])) {
    $slug = $current_edit_form['slug'];
    $name = $current_edit_form['name'];  
    }
    else{
    $slug = $_POST['slug'];
    $name = $_POST['name'];
    }

    $current_edit_form['slug'] = $slug;
    $current_edit_form['name'] = $name;
    $id = $current_edit_form['id'];
    $rows = xiu_execute("update categories set name = '{$name}', slug = '{$slug}' where id={$id}");  
    $GLOBALS['success'] = $rows > 0;
    $GLOBALS['error_mes'] = $rows <= 0 ? '更新失败' : '更新成功！';

  }

  //判断是添加主线还是编辑主线 
  //=========================================================
  if (empty($_GET['id'])) {
    if ($_SERVER['REQUEST_METHOD']==='POST') {
      add_category();
    }   
  }
  else{
    $current_edit_form = xiu_get_mysql_one('select * from categories where id='.$_GET['id']);
     if ($_SERVER['REQUEST_METHOD']==='POST') {
      edit_category();
    } 
  }

  //查询所有的categories的信息
  $categories_data = xiu_get_mysql_all('select * from categories;');
 ?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 --> 
      <?php if (isset($error_mes)): ?>
        <?php if ($success): ?>
        <div class="alert alert-success">
        <strong>成功！</strong><?php echo $error_mes; ?>
        </div> 
        <?php else: ?>
        <div class="alert alert-danger">
        <strong>错误！</strong><?php echo $error_mes; ?>
        </div>
        <?php endif ?>
      <?php endif ?>
      <div class="row">
        <div class="col-md-4">
          <?php if (isset($current_edit_form)): ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $current_edit_form['id']; ?>" method = "post" autocomplete = "off">
            <h2>编辑(<?php echo $current_edit_form['name'] ?>)</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类类别" value="<?php echo $current_edit_form['name'] ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $current_edit_form['slug'] ?>">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">更新</button>
            </div>
          </form>
          <?php else: ?>
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method = "post" autocomplete = "off">
            <h2>添加新分类目录</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
          <?php endif ?>
          
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_delate"  class="btn btn-danger btn-sm" href="/admin/categories-delete.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input id="text-center" type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
             
             <?php foreach ($categories_data as $item): ?>
                <tr>
                <td class="text-center"><input type="checkbox" data-id = <?php echo $item['id'] ?>></td>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['slug']; ?></td>
                <td class="text-center">
                  <a href="/admin/categories.php?id=<?php echo $item['id'] ?>" class="btn btn-info btn-xs">编辑</a>
                  <a href="/admin/categories-delete.php?id=<?php echo $item['id'] ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
             <?php endforeach ?>
             
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  
  <?php $message = 'categories'; ?>
  <?php include 'inc/aside.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>

    //批量删除按钮的显示
    
    $(function ($) {

      var allInput = $('tbody input')

      var btnDelate = $('#btn_delate')

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

          allCheckeds.length ? btnDelate.fadeIn() : btnDelate.fadeOut()

          btnDelate.prop('search','?id=' + allCheckeds)   

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
