<?php 
 //载入公共函数模块
require_once '../functions.php';
//控制页面访问权限
xiu_get_current_user();

//获取分类和状态的参数
//======================================================

//利用注入默认为未分类
$where = ' 1 = 1';

$search = '';

if (isset($_GET['category']) && $_GET['category'] !== 'all') {
  
   $where .= ' and posts.category_id = ' . $_GET['category'];

   $search .= '&category='.$_GET['category'];
}

if (isset($_GET['statu']) && $_GET['statu'] !== 'all') {
  
   $where .= " and posts.status ='{$_GET['statu']}'"; 

   $search .= '&statu='.$_GET['statu'];
}

//获取分页参数
//======================================================

//每页最多为多少条  
$size = 20;
//第几页
$page = empty($_GET['page']) ? 1 : (int) $_GET['page'];
//最大页码的计算
$all_data = xiu_get_mysql_one("select count(1) as num from posts 
inner join categories on posts.category_id = categories.id
inner join users on posts.user_id = users.id 
where {$where};")['num']; 

$max_end = (int)ceil($all_data / $size);

// $page = $page < 1 ? 1 : $page;
if ($page < 1) {
  header('Location: /admin/posts.php?page=1'.$search);
}

// $page = $page > $max_end ? $max_end : $page;
if ($page > $max_end ) {
  header('Location: /admin/posts.php?page=' . $max_end . $search);
}


//获取全部数据
//======================================================

//越过多少条取size
$offset =  ($page - 1) * $size;

$current_posts = xiu_get_mysql_all("select
  posts.id,
  posts.title,
  users.nickname as user_name,
  categories.name as category_name,
  posts.created,
  posts.status
from posts
inner join categories on posts.category_id = categories.id
inner join users on posts.user_id = users.id
where {$where}
order by posts.created desc
limit {$offset}, {$size};");

//获取全部的分类参数
$current_categories = xiu_get_mysql_all('select * from categories');

//计算分页页码
//=======================================================

//可见区域是5页
$visiables = 5;

$section = ($visiables-1)/2; 

$begin = $page - $section;

$end = $begin + $visiables;//最大页码+1

//$begin必须大于0
if ($begin < 1) {
  $begin = 1;
  $end = $begin + $visiables;
}


//end必须小于等于最大页码

if ($end > $max_end + 1) {
  $end = $max_end+1;
  $begin = $end -$visiables ;
}

//处理数据转换格式
//==========================================================

/**
 * [xiu_convert_status description]
 * @param  [string] $status [英文状态]
 * @return [string]         [中文状态]
 */
function convert_status($status) {

  $all_status = array(
  'published' => '已发布' ,                
  'drafted' => '草稿',                   
  'trashed' => '回收站'
   );
return isset($all_status[$status]) ? $all_status[$status] : '未知';
}

//发表日期
function convert_date($created) {
  $timetamp = strtotime($created);
  //r是一种存储数据的格式，所以这里要转义
  return date('Y年m月d日<b\r>H:i:s',$timetamp);
}

 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
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
        <h1>所有文章</h1>
        <a href="post-add.html" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="/admin/post-delete.php" style="display: none" id="btn_delete">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF'] ?>">
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach ($current_categories as $item): ?>
            <option value="<?php echo $item['id']?>"<?php echo isset($_GET['category']) && $_GET['category'] == $item['id'] ? ' selected' : ''  ?>><?php echo $item['name'] ?></option>
            <?php endforeach ?>
          </select>
          <select name="statu" class="form-control input-sm">
            <option value="all">所有状态</option>
            <option value="drafted"<?php echo isset($_GET['statu']) && $_GET['statu'] == 'drafted' ? ' selected' : ''  ?>>草稿</option>
            <option value="published"<?php echo isset($_GET['statu']) && $_GET['statu'] == 'published'  ? ' selected' : ''  ?>>已发布</option>
            <option value="trashed"<?php echo isset($_GET['statu']) && $_GET['statu'] == 'trashed' ? ' selected' : ''  ?>>回收站</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <?php if ($page - 1 >0): ?>
            <li><a href="/admin/posts.php?page=<?php echo $page - 1; ?>">上一页</a></li>   
          <?php endif ?>
          <!-- 省略号 -->
          <?php if ($begin > 1) {
           echo('<li class="disabled"><span>∙∙∙</span></li>'); 
          } ?>
          <?php for ($i=$begin; $i < $end; $i++): ?>
            <li<?php echo $i === $page ? ' class="active"' : '' ?>><a href="/admin/posts.php?page=<?php echo $i.$search ?>"><?php echo $i ?></a></li>
          <?php endfor ?>
          <?php if ($begin < $max_end) {
           echo('<li class="disabled"><span>∙∙∙</span></li>'); 
          } ?> 
          <?php if ($page + 1 < $max_end): ?>
            <li><a href="/admin/posts.php?page=<?php echo $page + 1; ?>">下一页</a></li>   
          <?php endif ?>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox" id="text-center"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
         <?php foreach ($current_posts as $items): ?>
           <tr>
            <td class="text-center"><input type="checkbox" data-id='<?php echo $items['id'] ?>'></td>
            <td><?php echo $items['title'] ?></td>
            <td><?php echo $items['user_name'] ?></td>
            <td><?php echo $items['category_name'] ?></td>
            <td class="text-center"><?php echo convert_date($items['created'])?></td>
            <td class="text-center"><?php echo convert_status($items['status']); ?></td>
            <td class="text-center">
              <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
              <a href="/admin/post-delete.php?id=<?php echo $items['id'] ?>" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>   
          <?php endforeach ?>  
        
        </tbody>
      </table>
    </div>
  </div>
  <?php $message = 'posts'; ?>
  <?php include 'inc/aside.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    //批量删除
    $(function ($) {
        
    $(function ($) {

      var allInput = $('tbody input')

      var btnDelete = $('#btn_delete')

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

          // console.log(allCheckeds)
          allCheckeds.length ? btnDelete.fadeIn() : btnDelete.fadeOut()

          btnDelete.prop('search','?id=' + allCheckeds)   
       })

       //全选
       $('#text-center').on('change',function () {
         
         var checked = $(this).prop('checked')
         //手动触发事件
         allInput.prop('checked',checked).trigger('change')

       })

    })


    })

  </script>
  <script>NProgress.done()</script>
</body>
</html>
