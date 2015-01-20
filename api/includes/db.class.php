<?php
/**
 * Class DBSQL 数据库基础操作类
 *
 */
class DBSQL {
    private $CONN = ""; 	// 定义数据库连接变量
    /**
     * Description: 初始化构造函数，连接数据库
     * 
     */
    public function __construct(){
        try {			//捕获连接错误并显示错误文件
            $conn = mysql_connect(ServerName, UserName, Password);
            mysql_query("set names 'utf8'",$conn); 
        } catch (Exception $e){
            $msg = $e;
            include(ERRFILE);
        }
        try {		//捕获数据库选择错误并显示错误文件
            mysql_select_db(DBName, $conn);
        } catch (Exception $e){
            $msg = $e;
            echo $msg;
        }
        $this->CONN = $conn;
    }
    /**
     * Description: 数据库查询函数
     *
     * @param string $sql
     * @return: 二维数组或false
     */
    public function select($sql = ""){
        if(empty($sql)){	//如果SQL语句为空则返回FALSE
            return false;
        }
        if(empty($this->CONN)){	//如果连接为空则返回FALSE
            return false;
        }
        try {	//捕获数据库选择错误并显示错误
            $results = mysql_query($sql, $this->CONN);
        } catch(Exception $e){
            $msg = $e;
            echo $msg;
        }
        if((!$results) or (empty($results))){	//如果查询结果为空则释放结果并返回FALSE
            @mysql_free_result($results);
            return false;
        }
        $count = 0;
        $data = array();

        while($row = mysql_fetch_assoc($results)){	//把查询结果重组成一个二维数组
            $data[$count] = $row;
            $count++;
        }
        @mysql_free_result($results);

        return $data;
    }
    /**
     * Description: 数据插入函数
     *
     * @param Sring $sql
     * @return unknown
     */
    public  function insert($sql = ""){
        if(empty($sql)){	//如果SQL语句为空则返回FALSE
            return false;
        }
        if(empty($this->CONN)){	//如果连接为空则返回FALSE
            return false;
        }
        try {	//捕获数据库选择错误并显示错误
            $results = mysql_query($sql, $this->CONN);
        } catch(Exception $e){
            $msg = $e;
            echo $msg;
        }
        if(!$results){
            return 0;
        } else {
            return @mysql_insert_id($this->CONN);
        }
    }
    /**
     * Description: 数据更新函数
     *
     * @param String $sql
     * @return TRUE or FALSE
     */
    public function update($sql = ""){
        if(empty($sql)){	//如果SQL语句为空则返回FALSE
            return false;
        }
        if(empty($this->CONN)){	//如果连接为空则返回FALSE
            return false;
        }
        try {	//捕获数据库选择错误并显示错误
            $result = mysql_query($sql, $this->CONN);
        } catch(Exception $e){
            $msg = $e;
            echo $msg;
        }
        return $result;
    }
    /**
     * Destription: 数据删除函数
     *
     * @param String $sql
     * @return TRUE or FALSE
     */
    public function delete($sql = ""){
        if(empty($sql)){	//如果SQL语句为空则返回FALSE
            return false;
        }
        if(empty($this->CONN)){	//如果连接为空则返回FALSE
            return false;
        }
        try {	//捕获数据库选择错误并显示错误
            $result = mysql_query($sql, $this->CONN);
        } catch(Exception $e){
            $msg = $e;
            echo $msg;
        }
        return $result;
    }
    /**
     * Description: 定义事务
     *
     */
    public function begintransaction(){
        mysql_query("SET AUTOCOMMIT = 0");	//设置为不自动提交,因为MySQL默认立即执行
        mysql_query("BEGIN");	//开始事务定义
    }
    /**
     * Description: 回滚
     *
     */
    public function rollback(){
        mysql_query("ROLLBACK");
    }
    /**
     * Description: 提交执行
     *
     */
    public function commit(){
        mysql_query("COMMIT");
    }
    /**
     * Description：提取指定表指定ID的纪录
     *
     * @param string $id 表ID
     * @param string $name 表名称
     * @return Array
     */
    public function getInfo($id, $idFiled, $name){
        $sql = "SELECT * FROM " . $name . " WHERE {$idFiled} = {$id}";
        $r = $this->select($sql);
        return $r[0];
    }
    /**
     * Description: 向指定表中插入数据
     *
     * @param string $name 表名称
     * @param Array $data 数组(格式：$data['key(字段名)'] = value)
     * @return 插入纪录ID
     */
    public function insertData($name, $data){
        $field = implode(',', array_keys($data));	//定义SQL语句字段部分
        $i = 0;
        foreach ($data as $key => $val){	//组合SQL语句的值部分
            $value .= "'" . $val . "'";
            if($i < count($data) - 1){
                $value .= ",";
            }
            $i++;
        }
        $sql = "INSERT INTO " . $name . " (" . $field . ") VALUES(" . $value . ")"; 
        // echo $sql;die;
        return $this->insert($sql);
    }
    /**
     * Description: 更新指定表指定ID的调查表记录
     *
     * @param String $name --the table's name 表名称
     * @param String $id --the ID in the table 表ID
     * @param Array $data -- Array data(Format: data['key'] = value) 
     * 						数组 (格式：data['字段名'] = 值)
     * @return TRUE or FALSE
     */
    public function updateData($name, $id, $idField, $data){
        $col = array();
        foreach ($data as $key => $value) {
            $col[] = $key . "='" . $value . "'";
        }
        $sql = "UPDATE " . $name . " SET " . implode(',', $col) . " WHERE {$idField} = {$id}";
        return $this->update($sql);
    }
    /**
     * Description: 删除指定表指定纪录 
     *
     * @param String $id
     * @param String $name
     * @return TRUE or FALSE
     */
    public function delData($id, $idField, $name){
        $id = intval($id);
        $sql = "DELETE FROM " . $name . " WHERE {$idField} = {$id}";
        return $this->delete($sql);
    }
}
?>
