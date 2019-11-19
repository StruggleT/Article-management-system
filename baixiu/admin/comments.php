<?php 
 
//载入公共函数模块
require_once '../functions.php';

xiu_get_current_user();

 ?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
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
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none;" id="btn_delate">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm" id="btn_all_delete">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right">
          <li><a href="#">上一页</a></li>
          <li><a href="#">1</a></li>
          <li><a href="#">2</a></li>
          <li><a href="#">3</a></li>
          <li><a href="#">下一页</a></li>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox" id="text-center"></th>
            <th width="50">作者</th>
            <th>评论</th>
            <th class="text-center" width="150">评论在</th>
            <th class="text-center" width="150">提交于</th>
            <th>状态</th>
            <th class="text-center" width="150">操作</th>
          </tr>
        </thead>
        <tbody>
         <!--  <tr class="danger">
            <td class="text-center"><input type="checkbox"></td>
            <td>大大</td>
            <td>楼主好人，顶一个</td>
            <td>《Hello world》</td>
            <td>2016/10/07</td>
            <td>未批准</td>
            <td class="text-center">
              <a href="post-add.html" class="btn btn-info btn-xs">批准</a>
              <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          -->
        </tbody>
      </table>
    </div>
  </div>
  <?php $message = 'comments'; ?>
 <?php include 'inc/aside.php'; ?>

  <!--  模板引擎 -->
  <script id="comments_tmpl" type="text/x-jsrender">
    {{for comments}}
    <tr {{if status == 'held'}}class="warning" {{else status == 'rejected'}} class="danger"{{/if}} data-id = {{:id}}>
      <td class="text-center"><input type="checkbox" data-id = {{:id}}></td>
      <td>{{:author}}</td>
      <td>{{:content}}</td>
      <td>{{:posts_title}}</td>
      <td>{{:created}}</td>
      <td>{{:status}}</td>
      <td class="text-center">
      {{if status == 'held'}}
        <a href="post-add.html" class="btn btn-info btn-xs">批准</a>  
        <a href="post-add.html" class="btn btn-warning btn-xs">拒绝</a>  
      {{/if}}
        <a href="javascript:;" class="btn btn-danger btn-xs btn-delete">删除</a>
      </td>
    </tr>
    {{/for}}
  </script>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <!-- 引入模板引擎 -->
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script> 
  <!-- 引入分页插件 -->
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
  
  <script>
     //发送AJAX并响应的进度条
     $(document)
       .ajaxStart(function () {
       NProgress.start()       
       })
       .ajaxStop(function () {
       NProgress.done()       
       })
        
      var init_page = 1

      //分页组件使用
      function loadPageData(page) {

         $.get('/admin/api/comments.php',{page:page},function (res) {

          $('.pagination').twbsPagination({
            initiateStartPageClick: false,
            first: '&laquo',
            last: '&raquo',
            prev: '＜',
            next: '＞',
            totalPages: res.total_pages,
            visiblePages: 5,
            //点击页码时发送AJAX请求
            onPageClick: function (event, page) {
            loadPageData(page)  
            }
          });
          //渲染数据
          var html = $('#comments_tmpl').render({comments : res.comment})

          $('tbody').html(html)

          //批量删除
          var allInput = $('tbody input')
          
          var btnDelate = $('#btn_delate')
          
          //定义一个数组记录被选中的
          var allCheckeds = []
          
          allInput.on('change',function () {
              
          
              var id = $(this).data('id')
              
              if ($(this).prop('checked')) {
              
               allCheckeds.includes(id) || allCheckeds.push(id)
              }
              else{
                allCheckeds.splice(allCheckeds.indexOf(id),1)
              }
          
              allCheckeds.length ? btnDelate.fadeIn() : btnDelate.fadeOut()
              
              console.log(allCheckeds) 
              var all_id = '' + allCheckeds
          
              $('#btn_all_delete').on('click',function () {
          
                 //发送ajax请求
                 
                 $.get('/admin/api/comment-delete.php',{id : all_id},function (res) {
                   
                   if (!res) return
        
                   //重新加载页面
                   loadPageData(init_page)  
                 })
            
              })
            
          
           })
           //全选
           $('#text-center').on('change',function () {
             
             var checked = $(this).prop('checked')
             //手动触发事件
             allInput.prop('checked',checked).trigger('change')
          
           })


        })
            init_page = page
      }


      loadPageData(init_page)
      //采用事件委托
      $('tbody').on('click','.btn-delete',function () {
        
        var $tr = $(this).parent().parent()

        var id = $tr.data('id')

        //发送ajax请求
        
        $.get('/admin/api/comment-delete.php',{id : id},function (res) {
          
          if (!res) return

          // $tr.remove()
          //重新加载页面
          loadPageData(init_page)  

        })

      })

  

   

  </script>
  <script>NProgress.done()</script>
</body>
</html>
