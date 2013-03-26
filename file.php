<?php
/*************************************************************************
 File Name: file.php
 Author: chliny
 mail: chliny11@gmail.com
 Created Time: 2013年03月26日 星期二 21时30分34秒
 ************************************************************************/
class rwFile{
    private $filename;
    
    function __construct($file){
        $this->filename = $file;
    }
    public function write($content){
        $handle = fopen($this->filename,"a+");
        $result = fwrite($handle,$content);
        fclose($handle);
        if(!$result)
            return true;
        else 
            return false;
    }
    public function read(){
        $handle = fopen($this->filename,"r+");
        $result = fread($handle,filesize($this->filename));
        fclose($handle);
        return $result;
    }
}
?>
