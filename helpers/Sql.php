<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_Sql_Helper
{
	public static function import($schema, $delimiter = ';')
	{
		if(file_exists($schema))
		{
			$queryResultOk = false;
			$schema = file_get_contents($schema);
			$model = Vf_Orm::factory('adminPages'); 
			
			if(substr($schema, -1, 1) == $delimiter)
			{
				$schema = rtrim($schema, $delimiter);
			}
			
			$queries = explode($delimiter, $schema);

			try
			{
				foreach($queries as $key => $query)
				{
					$query = trim($query);
					if($model -> InsertSchema($query))
					{
						$queryResultOk = true;
					}
					else
					{
						$queryResultOk = false;
					}
				}
			}
			catch(Volta_Mysql_Adapter_Query_Exception $e)
			{
				return false;
			}
			return $queryResultOk;
		}
		return false;
	}
}
?>