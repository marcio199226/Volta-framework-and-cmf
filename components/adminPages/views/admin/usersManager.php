{@ assets type="js" path="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.js" @}
{@ assets type="js" path="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" @}
{@ assets type="js" path="../components/adminPages/assets/js/userManager.js" @}
{@ js_inline @}
	$(document).ready(function() {
		//make function form bind events by id not by class name, pass through php array with users id then bind it
		$('.ban_user').bind('click', ban);
		$('.unban_user').bind('click', unban);
		$('.active_account').bind('click', active_account);
		$('.disable_account').bind('click', disable_account);
	});
{@ end @}
<?php 
	$base = Vf_Uri_Helper::base(true);
	Vf_Form_Helper::open(); 
	Vf_Form_Helper::text('username', 'uzytkownik', 'height:30px;width:200px;padding:2px;background-color:#ffffff;border:2px solid #B8860B;');
	Vf_Form_Helper::submit('search_user', 'Szukaj', 'height:35px;width:80px;');
	Vf_Form_Helper::close();
	$form = Vf_Form_Helper::get_form();
 ?>
<div>
	<?php if(sizeof($users) > 0): ?>
	<h4>Uzytkownicy</h4>
	<?php print $form['form_open']; ?>
	<table>
		<tr>
			<td><a style="text-decoration:none;" href="<?php print $base; ?>Admin,Index,addNewUser">Dodaj uzytkownika</a></td>
			<td>Znajdz uzytkownika:</td>
			<td><?php print $form['username'];  ?></td>
			<td><?php print $form['search_user']; ?></td>
		</tr>
	</table>
	<?php if(isset($userNotExists)): ?>
	<?php print Vf_Box_Helper::error($userNotExists); ?>
	<?php endif; ?>
	<?php print $form['form_close']; ?>
	<table class="default" cellspacing="0">
		<tr>
			<th>id</th>
			<th>uzytkownik</th>
			<th>email</th>
			<th>grupa</th>
			<th>Akcje</th>
		</tr>
	<?php foreach($users as $tab): ?>
		<tr>
			<td><?php print $tab['id']; ?></td>
			<td><?php print $tab['login']; ?></td>
			<td><?php print $tab['email']; ?></td>
			<td><?php print $tab['role']; ?></td>
			<td>
				<a style="text-decoration:none;" href="<?php print $base; ?>Admin,Index,editUserData,<?php print $tab['id']; ?>">Edytuj</a> |
				<a style="text-decoration:none;" href="<?php print $base; ?>Admin,Index,deleteUser,<?php print $tab['id']; ?>">Usun</a> |
				<?php if($tab['ban_id'] != null): ?>
					<a style="text-decoration:none;" href="#" id="unban_<?php print $tab['id']; ?>" class="unban_user" user="<?php print $tab['login']; ?>" action_url="<?php print Vf_Uri_Helper::site(false); ?>">Odbanuj</a> |
				<?php else: ?>
					<a style="text-decoration:none;" href="#" id="ban_<?php print $tab['id']; ?>" class="ban_user" user="<?php print $tab['login']; ?>" action_url="<?php print Vf_Uri_Helper::site(false); ?>">Banuj</a> |
				<?php endif; ?>
				<?php if($tab['active'] == 0): ?>
					<a style="text-decoration:none;" href="#" id="active_account<?php print $tab['id']; ?>" user_id="<?php print $tab['id']; ?>" class="active_account" user="<?php print $tab['login']; ?>" action_url="<?php print Vf_Uri_Helper::site(false); ?>">Aktywuj</a> |
				<?php else: ?>
					<a style="text-decoration:none;" href="#" id="disable_account<?php print $tab['id']; ?>" user_id="<?php print $tab['id']; ?>" class="disable_account" user="<?php print $tab['login']; ?>" action_url="<?php print Vf_Uri_Helper::site(false); ?>">Dezaktywuj</a>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
	<div id="msg" style="display:none;"></div>
	<div style="margin-top:10px;">
		<?php print $pager; ?>
	</div>
	<?php if(isset($msg_user)): ?>
	<?php print Vf_Box_Helper::success($msg_user); ?>
	<?php endif; ?>
	<?php if(isset($error_user)): ?>
	<?php print Vf_Box_Helper::error($error_user); ?>
	<?php endif; ?>
	<?php else: ?>
	<?php print Vf_Box_Helper::error('Nie ma zadnych uzytkownikow.'); ?>
	<?php endif; ?>
</div>