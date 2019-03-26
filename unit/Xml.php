<?php

class Util_Xml {
    //单条doc
    public static function build_update_xml ($row) {
        $xw = new xmlWriter();
        $xw->openMemory();
        $xw->startDocument('1.0', 'UTF-8');
        $xw->startElement('add');
        $xw->startElement('doc');
        foreach ($row as $key=>$value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $xw->startElement('field');
                    $xw->writeAttribute('name', $key);
                    $xw->writeCdata($v);
                    $xw->endElement();
                }
            } else {
                $xw->startElement('field');
                $xw->writeAttribute('name', $key);
                $xw->writeCdata($value);
                $xw->endElement();
            }
        }
        $xw->endElement();
        $xw->endElement();
        $xw->endDocument();
        $xml = $xw->outputMemory(true);
        return $xml;
    }

    //多条doc
    public static function build_update_xmls($data){
        if(!is_array($data)){
            return self::build_update_xml($data);
        }
        $xw = new xmlWriter();
        $xw->openMemory();
        $xw->startDocument('1.0', 'UTF-8');
        $xw->startElement('add');
        foreach($data as $row){
            $xw->startElement('doc');
            foreach ($row as $key=>$value) {
                if (is_array($value)) {
                    foreach ($value as $v) {
                        $xw->startElement('field');
                        $xw->writeAttribute('name', $key);
                        $xw->writeCdata($v);
                        $xw->endElement();
                    }
                } else {
                    $xw->startElement('field');
                    $xw->writeAttribute('name', $key);
                    $xw->writeCdata($value);
                    $xw->endElement();
                }
            }
            $xw->endElement();
        }
        $xw->endElement();
        $xw->endDocument();
        $xml = $xw->outputMemory(true);
        return $xml;
    }

    public static function delete_xml($rid, $module){
        $uid = $module.'_'.$rid;
        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><delete><query>uid:$uid</query></delete>";
    }
}