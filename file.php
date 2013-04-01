<?php
/*************************************************************************
 File Name: file.php
 Author: chliny
 mail: chliny11@gmail.com
 Created Time: 2013年03月26日 星期二 21时30分34秒
 ************************************************************************/
/*读写文件类
 */
class rwFile{
    private $filename;
    
    /*
     * @file 要读写的文件
     */
    function __construct($file){
        $this->filename = $file;
    }
    /*
     * 写文件
     * @content 要写入的内容
     * @return 写入成功则返回true，失败则返回false
     */
    public function write($content){
        $handle = fopen($this->filename,"a+");
        $result = fwrite($handle,$content);
        fclose($handle);
        if(!$result)
            return true;
        else 
            return false;
    }
    /*
     * 读文件
     * @return  读取的内容
     */
    public function read(){
        $handle = fopen($this->filename,"r+");
        $result = fread($handle,filesize($this->filename));
        fclose($handle);
        return $result;
    }
}
?>
