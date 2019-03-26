<?php

class Util_Page
{

    public static function getUrl($url, $params) {
        if ($url != '') {
            $url = rtrim($url, '/');
            if (! empty($params)) {
                $delimiter = '/';
                foreach ($params as $k => $v) {
                    if (false !== stripos($url, '{' . $k . '}')) {
                        $url = str_replace('{' . $k . '}', $v, $url);
                    } else {
                        $url .= $delimiter . urlencode($k) . $delimiter . urlencode($v);
                    }
                }
            }
        } else {
            $url = Util_Common::getUrl($url, $params, true);
        }

        return $url;
    }

    public static function getPage ($total, $currpage, $pagesize, $url = '', $params = array())
    {
        if ($total <= $pagesize) {
            return false;
        }
        $pager = '<form action="' . self::getUrl($url, $params) . '"><ul class="pagination">';
        if ($currpage <= 1) {
            $pager .= '<li class="disabled"><a href="javascript:;">首页</a></li><li class="disabled"><a href="javascript:;">上一页</a></li>';
        } else {
            $params['page'] = 1;
            $pager .= '<li><a href="' . self::getUrl($url, $params) . '">首页</a></li>';
            $params['page'] = $currpage - 1;
            $pager .= '<li class=""><a href="' . self::getUrl($url, $params) . '">上一页</a></li>';
        }

        $c_page = $currpage;
        $a_page = ceil($total / $pagesize);
        if ($c_page < 5) {
            $s_page = 1;
            $e_page = $a_page > 5 ? 5 : $a_page;
        } else {
            if(($c_page + 2) > $a_page) {
                $s_page = $c_page - 4;
                $e_page = $c_page;
            } else {
                $s_page = $c_page - 2;
                $e_page = $c_page + 2;
            }
        }

        for ($i = $s_page; $i <= $e_page; $i ++) {
            $params['page'] = $i;
            if ($c_page == $i) {
                $pager .= '<li class="disabled"><a href="javascript:;">' . $i . '</a></li>';
            } else {
                $pager .= '<li class=""><a href="' . self::getUrl($url, $params) . '">' . $i . '</a></li>';
            }
        }

        if ($currpage >= $a_page) {
            $pager .= '<li class="disabled"><a href="javascript:;">下一页</a></li>';
            $pager .= '<li class="disabled"><a href="javascript:;">尾页</a></li>';
        } else {
            $params['page'] = $currpage + 1;
            $pager .= '<li class=""><a href="' . self::getUrl($url, $params) . '">下一页</a></li>';

            $params['page'] = $a_page;
            $pager .= '<li class=""><a href="' . self::getUrl($url, $params) . '">尾页</a></li>';
        }
        $pager .= '<li class="disabled"><a href="javascript:;">第' . $currpage . '页 / 共' . $a_page . '页'.$total.'条数据</a></li>';
        $pager .= '<li class="">&nbsp;跳转至<input type="text" size="3" value="' . $currpage . '" name="page">&nbsp;<input type="submit" name="pageBtn"></li>';
        $pager .= "</ul></form>";

        return $pager;
    }


    public static function getFrontPage ($total, $currpage, $pagesize, $url = '', $params = array())
    {
        if ($total <= $pagesize) {
            return false;
        }
        $a_page = ceil($total / $pagesize);
        $pager = '<ul class="pagination">';

        if ($currpage < 6) {
            $s_page = 1;
            $e_page = $a_page > 6 ? 6 : $a_page;
            if ($currpage == 5) {
                $e_page = $a_page > 6 ? 7 : $a_page;
            }

        } else {
            if ($a_page >= ($currpage + 2)) {
                $s_page = ($currpage - 3);
                $e_page = ($currpage + 2);
            } else {
                $s_page = ($a_page - 5);
                $e_page = $a_page;
            }
            if ($a_page == ($e_page+1)) {
                $e_page = $a_page;
            }
        }

        if ($currpage <= 1) {
            $pager .= '<li class="disabled"><a href="javascript:;">上一页</a></li>';
        } else if ($s_page >= 3){
            $params['page'] = $currpage - 1;
            $pager .= '<li class=""><a href="' . self::getUrl($url, $params) . '">上一页</a></li>';
            $params['page'] = 1;
            $pager .= '<li class=""><a href="' . self::getUrl($url, $params) . '">1</a></li>';
            $pager .= '<li class="disabled dot">...</li>';
        } else {
            $params['page'] = $currpage - 1;
            $pager .= '<li class=""><a href="' . self::getUrl($url, $params) . '">上一页</a></li>';
        }

        for ($i = $s_page; $i <= $e_page; $i ++) {
            $params['page'] = $i;
            if ($currpage == $i) {
                $pager .= '<li class="disabled current"><a href="javascript:;">' . $i . '</a></li>';
            } else {
                $pager .= '<li class=""><a href="' . self::getUrl($url, $params) . '">' . $i . '</a></li>';
            }
        }


        if ($currpage >= $a_page) {
            $pager .= '<li class="disabled"><a href="javascript:;">下一页</a></li>';
        } else if (($a_page - 2) >= $e_page) {
            $params['page'] = $a_page;
            $pager .= '<li class="disabled dot">...</li>';
            $pager .= '<li class=""><a href="' . self::getUrl($url, $params) . '">'.$params['page'].'</a></li>';

            $params['page'] = $currpage + 1;
            $pager .= '<li class=""><a href="' . self::getUrl($url, $params) . '">下一页</a></li>';
        } else {

            $params['page'] = $currpage + 1;
            $pager .= '<li class=""><a href="' . self::getUrl($url, $params) . '">下一页</a></li>';
        }
        $pager .= "</ul>";

        return $pager;
    }
}