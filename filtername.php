<?php

$string = 'tes>?t name 测试 name -？xx==??><>';
echo preg_replace('/[^a-zA-Z\p{Han}]/u',' ',$string);  //过滤掉其他特殊字符串，只保留字母和汉字
