<?php
/**
 * debug类
 * @author jsxiao
 */

namespace app\helpers;

class Debug
{

    /**
     * 利用print_r()函数打印变量
     * print $var
     */
    static function pr($var, $exit = true)
    {
        echo '<pre>' . print_r($var, true) . '</pre>';
        if ($exit)
            exit;
    }

    /**
     * 利用var_dump()函数打印变量
     * @param unknown $var
     * @param string $exit
     */
    static function vd($var, $exit = true)
    {
        var_dump($var);
        if ($exit) {
            exit;
        }
    }
    
    /**
     * 对函数进行debug分析,打印所有入参及当前文件名
     * @param string $exit
     * @author zoujunjie
     */
    static function fd($exit = true) {
		$numargs = func_num_args ();
		$arg_list = func_get_args ();
		
		for($i = 0; $i < $numargs; $i ++) {
			echo "第${i}个变量的值为：";
			self::vd($arg_list [$i]);
			echo PHP_EOL;
		}
		
		echo '当前文件名为:', FILE, PHP_EOL;
		
		if ($exit) {
			exit ();
		}
	}
    
	/**
	 * 打印函数嵌套调用情况
	 * 在接口调试时，便于分析函数的调用栈，较少日志的记录量
	 * @param string $exit
	 * @author zoujunjie
	 */
	static function ftd($exit = true){
		debug_print_backtrace();
		
		if ($exit) {
			exit ();
		}
	}
	
	
}
