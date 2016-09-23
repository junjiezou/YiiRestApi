<?php
/**
 * Author : <caoxiaoming>
 * Email : <xiaoming.cao@dfs168.com>
 * Project : <wego-group.dfs168>
 * CreateTime : <2016/8/17 11:30>
 */

namespace app\filters;
use yii;
use yii\base\ActionFilter;
use app\components\AppLogs;
use app\components\ResponseData;

/**
 * 参数问过滤器
 */
class ParamFilter extends ActionFilter
{
    //在action之前运行，可用来过滤输入,如果没有登录则进行处理
    public function beforeAction($action){
        $param = $this->getQueryParam();
        $this->requestLog($param);  //把请求参数写入日志
        $this->checkParams($param); //检查参数
        return true;
    }

    /**
     * 获取参数
     * @author caoxiaoming
     */
    protected function getQueryParam($key=''){
        $param =  array_merge(Yii::$app->request->get(),Yii::$app->request->post());
        return $key ? $param[$key] : $param;
    }

    /**
     * 检验必备的参数
     * @author caoxiaoming
     */
    protected function checkParams($param){
        $this->checkVersion($param['ver']);
        $this->checkPid($param['pid']);
        $this->checkTs($param['ts']);
    }

    /**
     * 检验版本号是否合法
     * @author caoxiaoming
     */
    protected function checkVersion($ver){
        preg_match("/^[0-9]+\.[0-9]+\.[0-9]+$/",$ver) or ResponseData::returnError('ERROR_VERSION');
        array_key_exists($ver, Yii::$app->params['androidVer']) or ResponseData::returnError('ERROR_VERSION_NOT_EXIST');
    }

    /**
     * 检查手机平台是否合法
     * @author caoxiaoming
     */
    protected function checkPid($pid){
        in_array($pid,Yii::$app->params['pid']) or ResponseData::returnError('ERROR_PID');
    }

    /**
     * 检查请求时间是否正确
     * @author caoxiaoming
     */
    protected function checkTs($ts){
        $ts = intval($ts);
        if((time()-$ts) > 3600) ResponseData::returnError('ERROR_REQUEST_TIMEOUT');
        if(($ts-time()) > 3600) ResponseData::returnError('ERROR_REQUEST_TIME');
    }

    /**
     * 把请求参数写入日志
     * @author caoxiaoming
     */
    protected function requestLog($data){
        AppLogs::logFileWrite($data,'request');
    }
}