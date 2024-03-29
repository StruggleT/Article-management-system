<?php

// error_reporting(0);
// 因为这个 aside.php 是被 index.php 载入执行，所以 这里的相对路径 是相对于 index.php
// 如果希望根治这个问题，可以采用物理路径解决
require_once '../functions.php';
$message = isset($message) ? $message : '';
$current_user = xiu_get_current_user();



?>
 <div class="aside">
    <div class="profile">
      <img class="avatar" src="<?php echo $current_user['avatar']; ?>" id = 'update_avatar'>
      <h3 class="name"><?php echo $current_user['nickname']; ?></h3>
    </div>
    <ul class="nav">
      <li <?php echo $message === 'index'?'class="active"' : '';?>>
        <a href="/admin/index.php"><i class="fa fa-dashboard"></i>首页</a>
      </li>
      <?php $menu_posts = array('posts','post_add','categories'); ?>
      
      <li<?php echo in_array($message,$menu_posts)? ' class="active"' : '' ;?>>
        <a href="#menu-posts"<?php echo in_array($message,$menu_posts) ? '' : ' class="collapsed"' ;?> data-toggle="collapse">
          <i class="fa fa-thumb-tack"></i>文章<i class="fa fa-angle-right"></i>
        </a>
        <ul id="menu-posts" class="collapse<?php echo in_array($message, $menu_posts) ? ' in' : '' ?>">
          <li <?php echo $message === 'posts'?'class="active"' : '';?>><a href="/admin/posts.php">所有文章</a></li>
          <li <?php echo $message === 'post_add'?'class="active"' : '';?>><a href="/admin/post-add.php">写文章</a></li>
          <li <?php echo $message === 'categories'?'class="active"' : '';?>><a href="/admin/categories.php">分类目录</a></li>
        </ul>
      </li>
      <li <?php echo $message === 'comments'?'class="active"' : '';?>>
        <a href="/admin/comments.php"><i class="fa fa-comments"></i>评论</a>
      </li>
      <li <?php echo $message === 'users'?'class="active"' : '';?>>
        <a href="/admin/users.php"><i class="fa fa-users"></i>用户</a>
      </li>
      <?php $menu_setting = array('nav_menus','slides','settings') ?>
      <li<?php echo in_array($message,$menu_setting)? ' class="active"' : '';?>>
        <a href="#menu-settings"<?php echo in_array($message,$menu_setting) ? '' : ' class="collapsed"' ?> data-toggle="collapse">
          <i class="fa fa-cogs"></i>设置<i class="fa fa-angle-right"></i>
        </a>
        <ul id="menu-settings" class="collapse<?php echo in_array($message, $menu_setting) ? ' in' : '' ;?>">
          <li <?php echo $message === 'nav_menus'?'class="active"' : '';?>><a href="/admin/nav-menus.php">导航菜单</a></li>
          <li <?php echo $message === 'slides'?'class="active"' : '';?>><a href="/admin/slides.php">图片轮播</a></li>
          <li <?php echo $message === 'settings'?'class="active"' : '';?>><a href="/admin/settings.php">网站设置</a></li>
        </ul>
      </li>
      <li <?php echo $message === 'apiopen'?'class="active"' : '';?>>
        <a href="/admin/apiopen.php"><i class="fa fa-list-alt"></i>音乐排行</a>
      </li>
    </ul>
  </div>