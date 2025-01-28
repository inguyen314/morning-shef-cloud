<?php require_once('../../private/initialize.php'); ?>
<!--< ?php unset($_SESSION['admin_id']); ?>-->
<?php require_login(); ?>
<?php $page_title = 'Staff Menu'; ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="content">
  <div id="main-menu">
    <h2>Morning Shef Main Menu</h2>
  </div>
</div>
<br>
<br>
<?php include(SHARED_PATH . '/staff_footer.php'); ?>