<?php
/**
 * Created by PhpStorm.
 * User: showkw
 * Date: 2017/11/18
 * Time: 15:39
 */

namespace Hai;

use Hai\Exception\DbException;
use PDO;
use Hai\Db\Database;

class Db
{
    protected static $instance;
    
    protected $driver;
    
    protected $pdo; // PDO Obj
    
    protected $stm; //PDOStatement
    
    protected $debug = false;
    
    protected $isCache = false;
    
    protected $sql = [];
    
    protected $db;
    
    protected $tableName;
    
    protected $pk;
    
    protected $type;
    
    protected $select;
    
    protected $insert;
    
    protected $update;
    
    protected $where = [];
    
    protected $params = [];
    
    protected $order;
    
    protected $limit;
    
    protected $group;
    
    protected $having;
    
    protected $quote;
    
    protected $except = [];
    
    protected $rows = [];
    
    protected $transactions;
    
    protected function __construct()
    {
    }
    
    public static function getInstance( $config, $tableName = '' )
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        self::$instance->init( $config, $tableName );
        
        return self::$instance;
    }
    
    public function init( $config, $tableName = '' )
    {
        $this->driver = $config['driver'];
        $this->db     = $config['dbName'];
        $this->pdo    = Database::getInstance( $config );
        if ( $tableName ) {
            $this->tableName = $tableName;
        }
        $this->stm    = null;
        $this->select = null;
        $this->params = [];
        $this->where  = [];
        $this->insert = [];
        $this->order  = null;
        $this->limit  = null;
        $this->order  = null;
        $this->group  = null;
        $this->rows   = [];
        $this->except = [];
        $this->quote();
        $this->isCache();
    }
    
    protected function isCache()
    {
       return $this->isCache = config( 'db_cache_fields' );
    }
    
    public function table( $tableName )
    {
        $this->tableName = $tableName;
        
        return $this;
    }
    
    public function getPk()
    {
        $sql = "select COLUMN_KEY,COLUMN_NAME from INFORMATION_SCHEMA.COLUMNS where table_name= ? AND COLUMN_KEY='PRI'";
       $res = $this->query( $sql, [$this->tableName] );
       $this->pk = $res[0]['COLUMN_NAME'];
       return $this->pk;
    }
    
    public function getFieldsList()
    {
        $sql = "select COLUMN_NAME from information_schema.COLUMNS where table_name = ? and table_schema = ?";
        $res = $this->query( $sql, [$this->tableName, $this->db] );
        foreach( $res as $v ){
            foreach( $v as $item ){
                $rs[] = $item;
            }
        }
        return $rs;
    }
    
    public function query( $sql , Array $params = [] )
    {
        if( !$params ){
            $this->beginTime = microtime_float();
            $results = $this->pdo->query( $sql, PDO::FETCH_ASSOC );
            foreach( $results as $row ){
                $this->rows[] = $row;
            }
            $this->endTime = microtime_float();
            $sql           = '[' . round( ( $this->endTime - $this->beginTime ), 4 ) . 'ms] ' . $sql;
            $this->sql[] = trim($sql,';').';';
        }else{
            $this->beginTime = microtime_float();
            $this->stm = $this->prepare( $sql );
            $this->params = [];
            $this->params = $params;
            $this->bindParam( $this->stm );
            if ( $this->params ) {
                $sql .= '------\'' . implode( '\',\'', $this->params ) . '\'';
            }
            $this->endTime = microtime_float();
            $sql           = '[' . round( ( $this->endTime - $this->beginTime ), 4 ) . 'ms] ' . $sql;
    
            $this->sql[] = trim($sql,';').';';
            if ( $this->stm->execute() ) {
                $this->rows = $this->stm->fetchAll( PDO::FETCH_ASSOC );
            } else {
                $this->rows = [];
            }
        }
    
        $this->debug( $this->debug );
    
        return $this->rows;
    }
    
    public function exec( $sql )
    {
        $this->beginTime = microtime_float();
        $results = $this->pdo->exec( $sql);
        $this->endTime = microtime_float();
        $sql           = '[' . round( ( $this->endTime - $this->beginTime ), 4 ) . 'ms] ' . $sql;
        $this->sql[] = trim($sql,';').';';
        
    }
    
    public function debug( $isDebug = false )
    {
        
        $this->debug = $isDebug;
        $rs          = Response::getInstance();
        if ( $this->debug && !HAI_DEBUG ) {
            $rs->setData( 'sql', $this->sql );
        }
        
        $rs->setDebug( 'sql', $this->sql );
        
        return $this;
    }
    
    /**
     * 设置select查询字段
     *
     * @access public
     *
     * @param string|array $select 查询字段
     *
     * @return $this
     *
     */
    public function select( $select )
    {
        $select = $select ? $select : '*';
        
        //处理查询字段
        $this->select = $this->storeFields( $select );
        $this->type   = 'select';
        
        return $this;
    }
    
    /**
     * 查询结果排除指定字段列
     *
     * @access public
     *
     * @param string|array $fields 排除字段
     *
     *
     * @return $this
     *
     */
    public function except( $fields )
    {
        if ( is_string( $fields ) && $fields !== '*' ) {
            if ( strpos( trim( $fields ), ',' ) !== false ) {
                $fieldsArr = explode( ',', $fields );
                foreach ( $fieldsArr as $v ) {
                    $this->except[] = $v;
                }
            } else {
                $this->except[] = $fields;
            }
        } elseif ( is_array( $fields ) ) {
            foreach ( $fields as $v ) {
                $this->except[] = $v;
            }
        } else {
            $this->except = [];
        }
        
        
        return $this;
    }
    
    /**
     * 设置where条件
     *
     * @access public
     *
     * @param string       $where where查询条件
     * @param string|array $param 预定义变量参数
     *
     * @return $this
     *
     */
    public function where( $where, $params = null )
    {
        if ( $params ) {
            if ( is_array( $params ) ) {
                foreach ( $params as $v ) {
                    $this->params[] = $v;
                }
            } else {
                $this->params[] = $params;
            }
        }
        $this->where[] = str_replace( 'and', 'AND', $where );
        
        return $this;
    }
    
    /**
     * 设置and条件
     *
     * @access public
     *
     * @param string       $where where查询条件
     * @param string|array $param 预定义变量参数
     *
     * @return $this
     *
     */
    public function whereAnd( $where, $params = null )
    {
        return $this->where( $where, $params );
    }
    
    /**
     * 设置order by 排序条件
     *
     * @access public
     *
     * @param string $order 排序条件
     *
     * @return $this
     *
     */
    public function order( $order )
    {
        $this->order = $order;
        
        return $this;
    }
    
    /**
     * 设置limit 分页
     *
     * @access public
     *
     * @param string|int $begin 起始条数
     * @param string|int $size  分页大小
     *
     * @return $this
     *
     */
    public function limit( $begin, $size = 0 )
    {
        if ( is_numeric( $begin ) ) {
            $this->limit = $size ? $begin . ',' . $size : '0,' . $begin;
        } else {
            if ( strpos( $begin, ',' ) !== false ) {
                $this->limit = $begin;
            }
        }
        
        return $this;
    }
    
    /**
     * 设置group分组查询条件
     *
     * @access public
     *
     * @param string $group 分组条件
     *
     * @return $this
     *
     */
    public function group( $group )
    {
        $this->group = $group;
        
        return $this;
    }
    
    /**
     * 设置having子查询
     *
     * @access public
     *
     * @param string $having 子查询条件
     *
     * @return $this
     *
     */
    public function having( $having )
    {
        $this->having = $having;
        
        return $this;
    }
    
    /**
     * 获取单条结果集数据
     *
     * @access public
     *
     * @return array 结果集数组
     *
     */
    public function fetch()
    {
        return $this->fet( 'fetch' );
    }
    
    /**
     * 获取所有结果集数据
     *
     * @access public
     *
     * @return array 结果集数组
     *
     */
    public function fetchAll()
    {
        return $this->fet( 'fetchAll' );
    }
    
    /**
     * 执行结果集获取
     *
     * @access protected
     *
     * @param string $type 值可选 获取方式 单条 fetch  多条所有 fetchAll 默认获取所有
     *
     * @return array 结果集数据
     *
     */
    protected function fet( $type = 'fetchAll' )
    {
        //组装sql语句
        $sql             = $this->createSql();
        $this->beginTime = microtime_float();
        $this->stm       = $this->prepare( $sql );
        $this->bindParam( $this->stm );
        if ( $this->params ) {
            $sql .= '------\'' . implode( '\',\'', $this->params ) . '\'';
        }
        $this->endTime = microtime_float();
        $sql           = '[' . round( ( $this->endTime - $this->beginTime ), 4 ) . 'ms] ' . $sql;
    
        $this->sql[] = trim($sql,';').';';
    
    
        if ( $this->stm->execute() ) {
            $this->rows = $this->stm->$type( PDO::FETCH_ASSOC );
            if ( $this->except && $this->rows ) {
                $this->rows = $this->storeExcept( $this->rows );
            }
        } else {
            $this->rows = [];
        }
        $this->debug( $this->debug );
        
        return $this->rows;
    }
    
    
    /**
     * insert插入单条数据
     *
     * @access public
     *
     * @param Array $data  数据数组
     *
     * @return boolean false/单条插入返回最后插入的id
     *
     */
    public function insert( Array $data )
    {
        $this->type   = 'insert';
        $this->insert = $data;
        //单条
        if(count( $data ) == count( $data, 1 )){
            $sql = $this->createSql();
            $this->beginTime = microtime_float();
            $this->stm       = $this->prepare( $sql );
            $this->endTime = microtime_float();
            $sql           = '[' . round( ( $this->endTime - $this->beginTime ), 4 ) . 'ms] ' . $sql;
            $this->sql[] = trim($sql,';').';';
            if ( $this->stm->execute() ) {
                $return = $this->getLastInsertId();
            }else{
                $return = false;
            }
        }else{
            //插入多条
          $return = $this->insert_more( $data );
        }
        $this->debug( $this->debug );
        return $return;
    }
    
    /**
     * insert插入多条数据
     *
     * @access public
     *
     * @param Array $data  二维数组
     *
     * @return boolean true/false
     *
     */
    public function insert_more( Array $data )
    {
        $this->type = 'insert_more';
        $this->insert = $data;
        $sqls = $this->createSql();
        //事物处理
        $this->begin();
        
        foreach ( $sqls as $k=>$sql ) {
            $this->beginTime = microtime_float();
            $this->stm = $this->prepare( $sql );
            $this->endTime = microtime_float();
            $sql           = '[' . round( ( $this->endTime - $this->beginTime ), 4 ) . 'ms] ' . $sql;
            $inSql[] = trim($sql,';').';';
            if( !$this->stm->execute() ){
                $this->rollBack();
                return false;
            }
        }
        $this->commit();
        $this->sql = array_merge( $this->sql, $inSql );
        $this->debug( $this->debug );
        return true;
        
    }
    
    /**
     * 获取上一条insert插入的主键id
     *
     * @access public
     *
     * @return boolean|int false/最后插入的id
     *
     */
    public function getLastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
    
    public function update( $data )
    {
        $this->type = 'update';
        $this->update = $data;
        $sql = $this->createSql();
        $this->beginTime = microtime_float();
        $this->stm       = $this->prepare( $sql );
        $this->bindParam( $this->stm );
        if ( $this->params ) {
            $sql .= '------\'' . implode( '\',\'', $this->params ) . '\'';
        }
        $this->endTime = microtime_float();
        $sql           = '[' . round( ( $this->endTime - $this->beginTime ), 4 ) . 'ms] ' . $sql;
        $this->sql[] = trim($sql,';').';';
        $return = $this->stm->execute() ?true:false;
        $this->debug( $this->debug );
        return $return;
    }
    
    public function delete( $data = null )
    {
        $this->type = 'delete';
        if( $data ){
            if( is_numeric( $data ) ){
            
            }
        }
        $this->delete = $data;
        $sql = $this->createSql();
    }
    
    /**
     * 开启一个事物
     *
     * @access public
     *
     */
    public function begin()
    {
        ++$this->transactions;
        if ( $this->transactions == 1 ) {
            $this->pdo->beginTransaction();
        }
    }
    
    /**
     * 提交一个事物
     *
     * @access public
     *
     */
    public function commit()
    {
        if ( $this->transactions == 1 ){
            $this->pdo->commit();
        }
        --$this->transactions;
    }
    
    /**
     * 回滚一个事物
     *
     * @access public
     *
     */
    public function rollBack()
    {
        if ( $this->transactions == 1 ) {
            $this->transactions = 0;
            $this->pdo->rollBack();
        } else {
            --$this->transactions;
        }
    }
    
    /**
     *
     * 获取字段修饰符
     *
     * @access protected
     * @return string $this->quote 字段修饰符
     *
     */
    protected function quote()
    {
        switch ( $this->driver ) {
            case 'mysql':
                $this->quote = '`';
                break;
        }
        
        return $this->quote;
    }
    
    /**
     * 防止字段与系统字段冲突进行字段添加修饰符处理
     *
     * @access protected
     *
     * @param string|array $fields 待处理的字段
     *
     * @return mixed  处理后的字段
     *
     */
    protected function storeFields( $fields )
    {
        if ( is_string( $fields ) && $fields != '*' ) {
            if ( strpos( trim( $fields ), ',' ) !== false ) {
                $fields = $this->quote . implode( $this->quote . ',' . $this->quote, explode( ',', $fields ) ) . $this->quote;
            }
        } elseif ( is_array( $fields ) ) {
            $fields = $this->quote . implode( $this->quote . ',' . $this->quote, $fields ) . $this->quote;
        } elseif ( $fields == '*' ) {
            $fields = '*';
        } else {
            throw new DbException( 'SELECT查询字段：' . $fields . ' 无法识别！请检查' );
        }
        
        return $fields;
    }
    
    /**
     * 发送预处理sql模板
     *
     * @access protected
     *
     * @param string $sql
     *
     * @return \PDOStatement
     */
    protected function prepare( $sql )
    {
        $this->stm = $this->pdo->prepare( $sql );
        
        return $this->stm;
    }
    
    /**
     * 绑定sql语句的预定义参数
     *
     * @access protected
     *
     * @param \PDOStatement $stm
     *
     * @return \PDOStatement
     */
    protected function bindParam( \PDOStatement $stm )
    {
        foreach ( $this->params as $k => $v ) {
            $index = $k + 1;
            if ( is_int( $v ) ) {
                $param = PDO::PARAM_INT;
            } elseif ( is_bool( $v ) ) {
                $param = PDO::PARAM_BOOL;
            } elseif ( is_null( $v ) ) {
                $param = PDO::PARAM_NULL;
            } elseif ( is_string( $v ) ) {
                $param = PDO::PARAM_STR;
            } else {
                $param = false;
            }
            if ( is_int( $param ) ) {
                $stm->bindValue( $index, $v, $param );
            }
        }
        
        return $stm;
    }
    
    /**
     * 执行预处理sql语句
     *
     * @access protected
     * @return boolean
     */
    protected function execute()
    {
        return $this->stm->execute();
    }
    
    /**
     * 组装sql语句
     *
     * @access protected
     * @return string $sql
     */
    protected function createSql()
    {
        switch ( $this->type ) {
            case 'select':
                $sql = $this->createSqlByselect();
                break;
            case 'insert':
                $sql = $this->createSqlByInsert();
                break;
            case 'insert_more':
                $sql = $this->createSqlByInsertMore();
                break;
            case 'update':
                $sql = $this->createSqlByUpdate();
                break;
            case 'delete':
                $sql = $this->createSqlByDelete();
                break;
        }
        
        return $sql;
    }
    
    protected function createSqlByselect()
    {
        $sql = '';
        if ( $this->except && $this->select != '*' ) {
            $select       = explode( ',', $this->select );
            $selectArr    = $this->storeExcept( $select );
            $this->select = $this->storeFields( $selectArr );
        }
        $sql .= "SELECT " . $this->select . ' ';
        
        if ( $this->tableName ) {
            $sql .= 'FROM ' . $this->tableName . ' ';
        } else {
            throw new DbException( '数据库表名未配置' );
        }
        
        if ( $this->where ) {
            if ( count( $this->where ) > 1 ) {
                $sql .= ' WHERE ' . implode( " AND ", $this->where ) . ' ';
            } else {
                $sql .= ' WHERE ' . $this->where[0] . ' ';
            }
        }
        
        if ( $this->group ) {
            $sql .= ' GROUP BY ' . $this->group . ' ';
        }
        if ( $this->having ) {
            $sql .= ' HAVING ' . $this->having . ' ';
        }
        if ( $this->order ) {
            $sql .= ' ORDER BY ' . $this->order . ' ';
        }
        if ( $this->limit ) {
            $sql .= ' Limit ' . $this->limit . ' ';
        }
        
        return $sql.' ;';
    }
    
    protected function createSqlByInsert()
    {
        $sql = 'INSERT INTO ';
        if ( $this->tableName ) {
            $sql .= $this->tableName . ' ';
        } else {
            throw new DbException( '数据库表名未配置' );
        }
        foreach ( $this->insert as $key => $value ) {
            $fields[] = $key;
            $values[] = $value;
        }
        $fields = $this->storeFields( $fields );
        $sql    .= '(' . $fields . ') VALUES( \''. implode( '\',\'', $values ) . '\') ;';
        return $sql;
    }
    
    protected function createSqlByInsertMore()
    {
        foreach ( $this->insert as $k => $v ) {
            $sql = 'INSERT INTO ';
            $fields = [];
            $values = [];
            if ( $this->tableName ) {
                $sql .= $this->tableName . ' ';
            } else {
                throw new DbException( '数据库表名未配置' );
            }
            foreach ( $v as $key => $item ) {
                if ( !is_array( $item ) ) {
                    $fields[] = $key;
                    $values[] = $item;
                }
            }
            $fields = $this->storeFields( $fields );
            $sql    .= '(' . $fields . ') VALUES( \''. implode( '\',\'', $values ) . '\') ;';
            $sqls[] = $sql;
        }
        
        return $sqls;
    }
    
    protected function createSqlByUpdate()
    {
        $sql = 'UPDATE ';
        if ( $this->tableName ) {
            $sql .= $this->tableName . ' SET ';
        } else {
            throw new DbException( '数据库表名未配置' );
        }
        //单条
        $set = [];
        foreach ( $this->update as $key => $value ) {
          $set[] = $this->quote.$key.$this->quote.'=\''.$value.'\'';
        }
        $sql .= implode(',', $set);
        if( $this->where ){
            $sql .= ' WHERE '.implode( ' AND ',$this->where ).' ;';
        }
        return $sql;
    }
    
    protected function createSqlByDelete()
    {
        $sql = 'DELETE ';
        if ( $this->tableName ) {
            $sql .= 'FROM '.$this->tableName;
        } else {
            throw new DbException( '数据库表名未配置' );
        }
        if( $this->where ){
            $sql .= ' WHERE '.implode( ' AND ',$this->where ).' ;';
        }
        return $sql;
    }
    
    /*
     * 处理排除字段
     *
     * @access protected
     *
     * @param array $data
     *
     * @return array $data
     */
    protected function storeExcept( $data = [] )
    {
        if ( $data && $this->except ) {
            foreach ( $data as $k => $v ) {
                if ( is_array( $v ) ) {
                    foreach ( $v as $key => $value ) {
                        $key = trim( $key, $this->quote );
                        if ( in_array( $key, $this->except ) ) {
                            unset( $data[ $k ][ $key ] );
                        }
                    }
                } else {
                    $k = trim( $k, $this->quote );
                    if ( in_array( $k, $this->except ) ) {
                        
                        unset( $data[ $k ] );
                    }
                }
            }
        }
        //清空排除字段
        $this->except = [];
        
        return $data;
    }
}