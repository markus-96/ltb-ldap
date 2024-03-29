<?php

include 'Criticity.php';
include 'ErrorCode.php';



$arr = array();
$arr["captcharequired"] = "hi!";

$start = microtime(true);
#for ($i = 0; $i < 10; $i++) {
$error_code = ResultCode::CAPTCHA_REQUIRED;
$fa_class = $error_code->get_fa_class();
#}
print $arr[$error_code->value];
print number_format(microtime(true)-$start, 10);
