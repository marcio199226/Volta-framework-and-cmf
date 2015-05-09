<?php 

//Vf_Translate_Helper::t('test.xml', 'dogs', array('%ile%'), array('0'), true, 0);

$translate['pl']['dogs'] = array(
								'text' => 'W domu u Oskara %how% %ile% %plural%', 
								'pluralize' => array(
													'%plural%' => array(
																			0 => 'pies',
																			1 => 'psy',
																			2 => 'psow'
																		),
													'%how%' => array(
																		0 => 'jest',
																		1 => 'sa',
																		2 => 'jest'
																	)
													)
								);
								
?>