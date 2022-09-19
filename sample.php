<?php

require_once("health.class.php");

$h=new Health();

$h->age=46;
$h->gender="male";
$h->height=168;
$h->weight=80;

$h->activity=1.2;

$h->triceps=6;
$h->chest=9;
$h->subscapular=13;
$h->midaxillary=9;
$h->suprailiac=8;
$h->abdominal=15;
$h->thigh=11;

$data=$h->getdata();
print_r($data);


?>
