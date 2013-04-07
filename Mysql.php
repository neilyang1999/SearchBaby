<?php
/*************************************************************************
 File Name: Mysql.php
 Author: chliny
 mail: chliny11@gmail.com
 Created Time: 2013年03月26日 星期二 17时51分47秒
 ************************************************************************/
/*
 * 数据库封装类
 */
class MyDatabase{
	  private $link=NULL;
	  function connect($hostname,$dbuser,$dbpass,$dbname,$charset='utf-8'){
		  //str_replace('-', '','utf-8')
		  $this->link=mysql_connect($hostname,$dbuser,$dbpass) or die('连接类中出错1：'.mysql_error());
		  mysql_select_db($dbname,$this->link) or die('连接类中出错2：'.mysql_error());	
		  $charset=str_replace('-', '',$charset);
		  mysql_query('SET character_set_connection='.$charset.', character_set_results='.$charset.', character_set_client=binary',$this->link)  or die('连接类中出错：'.mysql_error());
		    }
		function db_select($dbname){
			mysql_select_db($dbname,$this->link) or die('连接类中出错3：'.mysql_error());
		}
		function query($sql){
			//$sql=mysql_escape_string($sql);
			$query=mysql_query($sql,$this->link) or die('连接类中出错4：'.mysql_error());
			return $query;
		}
		function fetch_array($query){
			return mysql_fetch_array($query);
		}
		function insert_id(){
			return mysql_insert_id($this->link);
		}
		function num_rows($query){
			return mysql_num_rows($query);
		}
		function __destruct(){
			mysql_close($this->link);
		}
}

?>
