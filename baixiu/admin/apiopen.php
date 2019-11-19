<?php 
//载入公共函数模块
require_once '../functions.php';

$current_user = xiu_get_current_user();
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Navigation menus &laquo; Admin</title>
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
        <h1>音乐排行</h1>
      </div>
    </div>
    <ul id="musics"></ul>
  </div>
  <?php $message = 'apiopen'; ?>
  <?php include 'inc/aside.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $.get('https://api.apiopen.top/musicRankingsDetails?type=1', {} ,function (res) {
      
      $(res.result).each(function (i,item) {
        $('#musics').append(`<li><img src="${item.pic_small}" alt=""/>${item.title}</li>`)
      })
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
