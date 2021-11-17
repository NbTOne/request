<?php
/**
 * Created by PhpStorm.
 * User: le
 * Date: 2020/11/8
 * Time: 17:55
 */

namespace pysec\request;



use think\Exception;
use think\Response;


class Utils
{
    /**
     * @param $params
     * 参数名=>[数据类型，默认值（无默认值为必须）]
     * 数据类型 integer string array
     * @return mixed
     */
    
    
    public static function test(){
        echo "1111";
}


    public static function getRequestParams(array $params)
    {

        if($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            $body = $_GET;
        }
        else{
            $body = file_get_contents('php://input');
            $body = json_decode($body,true);
        }

        foreach ($params as $key => &$value){
            if (isset($body[$key]) && !empty($body[$key])) {
                //GET转换类型
                if($_SERVER['REQUEST_METHOD'] === 'GET' && $value[0]=='integer'){
                    if(is_numeric($body[$key])){
                        $body[$key] = intval($body[$key]);
                    }
                    else{
                        throw new Exception("$key 参数必须，并且为$value[0]");
                    }
                }

                if(gettype($body[$key])!=$value[0]){

                    throw new Exception("$key 参数必须，并且为$value[0]");
                }
            }
            elseif (isset($body[$key]) && is_numeric($body[$key])){
                $body[$key]=0;
            }
            elseif(isset($value[1])){
                $body[$key] = $value[1];
            }
            else{

                throw new Exception("$key 参数必须，并且为$value[0]");
            }
        }
        return $body;
    }

    public static function jsonSuccess($data,int $code = null,string $msg = 'success',int $status = 200){
        return self::result($msg,$data,$code??ErrorCode::SUCCESS);
    }

    /**
     * @param string $msg
     * @param int|null $code
     * @param $data
     * @param int $status
     */
    public static function jsonError(string $msg = '', int $code = 0, $data, int $status=200){
        return self::result($msg,$data,$code??ErrorCode::ERROR);
    }

    protected static function result($msg, $data = null, $code = 0, $type = null, array $header = [])
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'time'=>time(),
            'data' => $data
        ];
        $response = Response::create($result, 'json', $code)->header($header);
        return $result;

    }



}