{@ assets type="js" path="../widgets/assets/js/languagePicker.js" @}
{@ assets type="css" path="../widgets/assets/css/languagePicker.css" @}
<?php $base = Vf_Uri_Helper::base(true); ?>
<div class="box">
	<div id="country-select">
		<form action="<?php print $base; ?>" method="get">
			<select id="country-options" name="language">
				<?php foreach($locales as $locale): ?>
					<option title="<?php print $base; ?>Home,language,<?php print $locale['locale']; ?>" value="<?php print $locale['locale']; ?>"><?php print $locale['language']; ?></option>
				<?php endforeach; ?>
			</select>
			<input value="setLanguage" type="submit" />
		</form>
	</div>
</div>
