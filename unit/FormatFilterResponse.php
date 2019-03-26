<?php
class FormatFilterResponse
{
	// 类型格式化函数前缀名
	const FORMAT_FILTER_FUNCTION_PREFIX = 'formatFilter';
	/*$filter = array(
	    'name' => array('type'=>'string'),
	    'age' => array('type' => 'integer'),
	    'blogs' => array('type' => 'array','child'=>array(
	          'blog_name' => array('type'=>'string'),
	          'views' => array('type'=>'integer'),
	          'comments' => array('type'=>'array','child' => array(
	            'content' => array('type'=>'string'),
	            'datetime' => array('type'=>'mask','start' => 5, 'length'=>6, 'replace'=>'*'),
	          )),
	      )),
	  );
	$data = array(
	  'name' => 'abc_name',
	  'age' => '22',
	  'money' => '200',
	  'blogs'=>array(
	      'blog_name'=>'博客名称',
	      'views' => '20',
	      'comments' => array(
	        'content' => '评论内容',
	        'datetime' => '2016-06-28 20:20:22',
	      )
	    )
	  );*/

	/**
	 * @todo  待定
	 * 过滤强制转换成整型
	 * @param  string  $data    数据
	 * @param  array|null $params 参数
	 * @return string
	 * xml:
	 * <column>
	 * 		<type>array</type>
	 * 		<params>
	 *   		<type>数组内值类型</type>
	 * 		</params>
	 * </column>
	 */
	public static function formatFilterArray($data,$params=null){
	  $type=isset($params['type'])?$params['type']:0;// 数组内数据的类型
	  return $data;
	}
	/**
	 * 过滤强制转换成整型
	 * @param  string  $data    数据
	 * @param  array|null $params 参数
	 * @return string
	 * xml:
	 * <column>
	 * 		<type>integer</type>
	 * </column>
	 */
	public static function formatFilterInteger($data,$params=null){
	  return intval($data);
	}
	/**
	 * 过滤强制转换成float
	 * @param  string  $data    数据
	 * @param  array|null $params 参数
	 * @return string
	 * xml:
	 * <column>
	 * 		<type>float</type>
	 * 		<params>
	 *   		<digit></digit>
	 * 		</params>
	 * </column>
	 */
	public static function formatFilterFloat($data,$params=null){
	  $digit=isset($params['digit'])?$params['digit']:0;// 保留小数点位数
	  return floatval(sprintf('%.' . $digit . 'f',(float)$data));//不带四舍五入，带四舍五入：floatval(number_format($data,$digit));
	}
	/**
	 * 过滤强制转换成字符串
	 * @param  string  $data    数据
	 * @param  array|null $params 参数
	 * @return string
	 * xml:
	 * <column>
	 * 		<type>array</type>
	 * 		<params>
	 *   		<start>2</start>
	 *   		<length>2</length>
	 * 		</params>
	 * </column>
	 * 
	 */
	public static function formatFilterString($data,$params=null){
	  $start=isset($params['start'])?$params['start']:0;
	  $length=isset($params['length'])?$params['length']:0;
	  return strval($data);
	}
	/**
	 * @todo  暂时不支持中文
	 * @param  string  $data    数据
	 * @param  array|null $params 参数
	 * @return string
	 * xml:
	 * <column>
	 * 		<type>array</type>
	 * 		<params>
	 *   		<start>2</start>
	 *   		<length>2</length>
	 *   		<replace>*</replace>
	 * 		</params>
	 * </column>
	 */
	public static function formatFilterMask($data,$params=null){
	  $start=isset($params['start'])?$params['start']:0;
	  $length=isset($params['length'])?$params['length']:0;
	  $replace=isset($params['replace'])?$params['replace']:'*';
	  return $length>0?substr_replace($data,str_repeat($replace,$length),$start,$length):$data;
	}
	public static function response($data,$filter){
	  $ret = array();
	  if(is_array($data)){
	    foreach ($data as $key => $value) {
	      if(in_array($key, array_keys($filter))){
	        if('array' == $filter[$key]['type'] && isset($filter[$key]['child'])){
	          $ret[$key] = self::response($value,$filter[$key]['child']);
	        }else{
	          $fun = self::FORMAT_FILTER_FUNCTION_PREFIX.ucfirst($filter[$key]['type']);
	          $ret[$key]= self::$fun($value,isset($filter[$key]['params'])?$filter[$key]['params']:null);
	        }
	      }
	    }
	  }
	  return $ret;
	}


}
