<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Blog</title>
<link rel="stylesheet" href="/Vf/assets/css/styles.css" type="text/css" />
{@ css @}
</head>
<body>
	<div style="margin: 1% auto auto 65%;position: absolute;">
		<?php print Vf_Loader::loadWidget('languagePicker'); ?>
	</div>
	<div id="container">
		<div id="header">
			<h1><a href="./">marcio's blog</a></h1>
			<h2>programowanie pasja i hobby</h2>
			<div class="clear"></div>
		</div>
		<div id="nav">
			<ul>
				<li><a href="./">Home</a></li>
				<li><a href="/Vf/index.php/About">About</a></li>
				<li><a href="/Vf/index.php/Contact">Contact</a></li>
				<li><a href="/Vf/index.php/Home,Register">Register</a></li>
			</ul>
		</div>
		<div id="body">
			<div id="content">
				<?php
					if(isset($component['content'])):
						foreach($component['content'] as $cmp)
							print $cmp;
					endif;
				?>
			</div>
			
			<div class="sidebar">
				<ul>
					<?php 
						if(isset($component['menu'])):
							foreach($component['menu'] as $cmp): 
					?>
								<li>
									<?php print $cmp; ?>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
					<li>
						<h4>Search</h4>
						<ul>
							<li>
								<form method="post" class="searchform" action="#" >
									<p>
										<input type="text" size="22" value="" name="s" class="s" />
										<input type="submit" class="searchsubmit formbutton" value="Search" />
									</p>
								</form>	
							</li>
						</ul>
					</li>
				</ul> 
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<div id="footer">
		<span style="font-size:10px;color:#fff;float:right;">Czas: {@time@} Sql: {@sql@} Memory: {@memory@}</span><Br />
		<div class="footer-content">
			<div class="footer-width">
			
				<span class="sitename">marcio's blog</span>
					<p class="footer-links">
					<a href="./">Home</a> |
					<a href="/Vf/index.php/About">About</a> |
					<a href="/Vf/index.php/Contact">Contact</a>

				</p>
			 </div>
		</div>
		<div class="footer-width footer-bottom">
			<p>&copy; www.eoskar.pl/Vf/ 2014.</p>
		 </div>

	</div>
<script type="text/javascript" src=https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.js></script>
<script type="text/javascript" src=https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js></script>
{@ javascripts @}
</body>
</html>
