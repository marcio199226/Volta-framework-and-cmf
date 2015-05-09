{@ assets type="css" path="../components/auth/assets/css/styles.css" @}
<div class="boxAuthorized">
	<!-- <li> remove it if you change template, it's for sidebar box of current layout-->
		<h5>Witaj <?php print $user; ?></h5>
		<a href="/Vf/index.php/Admin,News,addNews"><span>Dodaj wpis</span></a><Br />
		<?php if(Vf_User_Helper::is('admin')): ?>
			<a href="/Vf/index.php/Admin"><span">Admin</span></a><Br />
		<?php endif; ?>
		<a href="/Vf/index.php/Home,Index,logout"><span>Wyloguj</span></a>
	<!-- </li> remove it if you change template, it's for sidebar box of current layout-->
</div>