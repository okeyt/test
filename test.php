<?php
/*
$str = "123******99";

$num = substr_count($str,"*");

$first = strpos($str,"*");

$first_str = substr($str,0,$first);
$middle_str =  substr($str,$first,$num);
$last_str =  substr($str,$first+$num);

print_r([$first_str,$middle_str,$last_str]);*/

/*$array = [
    ['foo'=>10],
    ['foo'=>1],
    ['foo'=>9],
    ['foo'=>20],
    ['foo'=>46],
    ['foo'=>100],
    ['foo'=>7],
    ['foo'=>60],
    ['foo'=>3],
    ['foo'=>2],
    ['foo'=>200],
    ['foo'=>300],
];
uksort($array, function ($ak, $bk) use ($array) {
    print_r([$ak,$bk]);
    $a = $array[$ak];
    $b = $array[$bk];
    if ($a['foo'] === $b['foo']) return $ak - $bk;
    return $a['foo'] > $b['foo'] ? 1 : -1;
});

print_r($array);*/

$array[] = array("age"=>20,"name"=>"li");
$array[] = array("age"=>21,"name"=>"ai");
$array[] = array("age"=>20,"name"=>"ci");
$array[] = array("age"=>22,"name"=>"di");

foreach ($array as $key=>$value){
    $age[$key] = $value['age'];
    $name[$key] = $value['name'];
}

array_multisort($age,SORT_NUMERIC,SORT_DESC,$name,SORT_STRING,SORT_ASC);
print_r($array);

class test implements ArrayAccess{

    private $a = 1;
    private $b = 2;
    public $c = 3;
    private $data = [
        'data' => 20
    ];


    
}

$tst = new test();

print_r($tst['a']);
print_r($tst['data']['data']);