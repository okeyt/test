<?php

class Util_Solr
{

    private $host;

    private $params;

    private $uriparam = array();

    public function __construct ($host, $params = array(), $limit = 10)
    {
        $this->host = $host;
        $this->params = $params;
        
        $this->uriparam = array(
            "q" => '',
            "sort" => 'created desc',
            "start" => 0,
            "rows" => $limit,
            "wt" => 'json'
        );
    }

    public function get_uri ($fl = '')
    {
        $url = $this->host;
        $q = trim($this->params['kw']);
        if ($q) {
            $url .= "fl=*,score";
        } else {
            $url .= "fl=";
        }
        foreach ($this->uriparam as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $val) {
                    $url .= "&" . urlencode($key) . "=" . urlencode($val);
                }
            } else {
                $url .= "&" . urlencode($key) . "=" . urlencode($value);
            }
        }
        return $url;
    }

    public function set_module ($module)
    {
        if (empty($this->params['kw'])) {
            $this->uriparam['q'] = "module:" . $module;
        } else {
            $this->uriparam['fq'][] = "module:" . $module;
        }
    }

    public function set_or_module ($module)
    {
        if (empty($this->params['kw'])) {
            $this->uriparam['q'] = "module:(" . implode(' OR ', explode(',', $module)) . ')';
        } else {
            $this->uriparam['fq'][] = "module:(" . implode(' OR ', explode(',', $module)) . ')';
        }
    }

    public function set_kw ($kw)
    {
        $q = $this->params['kw'];
        
        if ($q) {
            $this->uriparam['defType'] = "dismax";
            $this->uriparam['q'] = $q;
            // if (preg_match('/^[0-9]+$/', $q)) {
            // $this->uriparam['qf'] = 'userid^1000 username^100 title^100 sort^50 tags^50 content^20';
            // }else{
            // $this->uriparam['qf'] = 'username^200 title^100 sort^50 tags^50 content^20';
            // }
            $this->uriparam['qf'] = 'username^200 title^100 sort^50 tags^50 content^20';
        }
    }

    public function set_q ($field)
    {
        $this->uriparam['q'] = $field;
    }

    public function set_fq ($field, $value)
    {
        $this->uriparam['fq'][] = "$field:$value";
    }

    public function set_fq_between ($field, $values)
    {
        if (is_array($values) && 2 == count($values)) {
            $this->uriparam['fq'][] = "$field:[$values[0] TO $values[1]]";
        }
    }

    public function set_fq_and ($field, $values)
    {
        if (is_array($values)) {
            $ids = implode(" AND $field:", explode(",", $values));
            $this->uriparam['fq'][] = "$field:$ids";
        }
    }

    public function set_sort ($orders)
    {
        $this->uriparam['sort'] = $orders;
    }

    public function set_start ($start = 0)
    {
        $this->uriparam['start'] = abs($start);
    }

    public function set_row ($rows)
    {
        $this->uriparam['rows'] = $rows;
    }

    public function set_wt ($wt = "json")
    {
        $this->uriparam['wt'] = $wt;
    }

    public function set_mm ($value = 1)
    {
        $this->uriparam['mm'] = $value;
    }

    public function set_json_nl ($nl)
    {
        $this->uriparam['json.nl'] = $nl;
    }

    public static function get_data ($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Content-type:text/xml; charset=utf-8"
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        
        $res = curl_exec($curl);
        $info = curl_getinfo($curl);
        
        curl_close($curl);
        if ($info['http_code'] != 200) {
            return array(
                'list' => array(),
                'count' => 0,
                'hightlighting' => array()
            );
        }
        unset($info);
        $data = json_decode($res, true);
        
        return array(
            'list' => $data['response']['docs'],
            'count' => $data['response']['numFound'],
            'hightlighting' => $data['highlighting'],
            'analysis' => @$data['analysis']
        );
    }
}