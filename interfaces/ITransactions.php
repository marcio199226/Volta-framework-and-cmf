<?

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/


interface ITransactions
{
	public function begin();
	public function commit();
	public function rollback();
	public function lock($tables);
	public function unlock();
}

?>