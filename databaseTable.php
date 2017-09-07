<?php
$config = array(
    'db_host' => '127.0.0.1',
    'db_username' => 'root',
    'db_password' => 'wangziqi',
    'db_name' => 'duizhang',
    'db_port' => 3306,
    'db_pconnect' => false
);
$dsn = "mysql:dbname=$config[db_name];host=$config[db_host];port=$config[db_port];charset=utf8";
$db = new \PDO($dsn, $config['db_username'], $config['db_password'], array(
    \PDO::ATTR_PERSISTENT => $config['db_pconnect'],
    \PDO::ATTR_EMULATE_PREPARES => false,
    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
));
$res = $db->query('show tables');


$no_show_table = array(); //不需要显示的表
$no_show_field = array(); //不需要显示的字段


//取得所有的表名
foreach ($res->fetchall() as $key => $row) {
    if (!in_array($row[0], $no_show_table)) {
        $tables[]['TABLE_NAME'] = $row[0];
    }
}
//循环取得所有表的备注及表中列消息
foreach ($tables as $k => $v) {
    $sql = 'SELECT * FROM ';
    $sql .= 'INFORMATION_SCHEMA.TABLES ';
    $sql .= 'WHERE ';
    $sql .= "table_name = '{$v['TABLE_NAME']}'  AND table_schema = '{$config['db_name']}'";
    $_table = $db->query($sql);
    foreach ($_table->fetchall() as $key => $t) {
        $tables[$k]['TABLE_COMMENT'] = $t['TABLE_COMMENT'];
    }
    $sql = 'SELECT * FROM ';
    $sql .= 'INFORMATION_SCHEMA.COLUMNS ';
    $sql .= 'WHERE ';
    $sql .= "table_name = '{$v['TABLE_NAME']}' AND table_schema = '{$config['db_name']}'";
    $fields = array();
    $field_result = $db->query($sql);
    foreach ($field_result->fetchall() as $key => $t) {
        $fields[] = $t;
    }
    $tables[$k]['COLUMN'] = $fields;
}

$html = '';
//循环所有表
foreach ($tables as $k => $v) {
    $html .= '	<h3>' . ($k + 1) . '、' . $v['TABLE_COMMENT'] . '  （' . $v['TABLE_NAME'] . '）</h3>' . "\n";
    $html .= '	<table border="1" cellspacing="0" cellpadding="0" width="100%">' . "\n";
    $html .= '		<tbody>' . "\n";
    $html .= '			<tr>' . "\n";
    $html .= '				<th>字段名</th>' . "\n";
    $html .= '				<th>数据类型</th>' . "\n";
    $html .= '				<th>默认值</th>' . "\n";
    $html .= '				<th>允许为空</th>' . "\n";
    $html .= '				<th>自动递增</th>' . "\n";
    $html .= '				<th>备注</th>' . "\n";
    $html .= '			</tr>' . "\n";

    foreach ($v['COLUMN'] as $f) {
        $html .= '			<tr>' . "\n";
        $html .= '				<td class="c1">' . $f['COLUMN_NAME'] . '</td>' . "\n";
        $html .= '				<td class="c2">' . $f['COLUMN_TYPE'] . '</td>' . "\n";
        $html .= '				<td class="c3">' . $f['COLUMN_DEFAULT'] . '</td>' . "\n";
        $html .= '				<td class="c4">' . $f['IS_NULLABLE'] . '</td>' . "\n";
        $html .= '				<td class="c5">' . ($f['EXTRA'] == 'auto_increment' ? '是' : '&nbsp;') . '</td>' . "\n";
        $html .= '				<td class="c6">' . $f['COLUMN_COMMENT'] . '</td>' . "\n";
        $html .= '			</tr>' . "\n";
    }
    $html .= '		</tbody>' . "\n";
    $html .= '	</table>' . "\n";
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <style>
        body, td, th {
            font-family: "微软雅黑";
            font-size: 14px;
        }

        .warp {
            margin: auto;
            width: 900px;
        }

        .warp h3 {
            margin: 0px;
            padding: 0px;
            line-height: 30px;
            margin-top: 10px;
        }

        table {
            border-collapse: collapse;
            border: 1px solid #CCC;
            background: #efefef;
        }

        table th {
            text-align: left;
            font-weight: bold;
            height: 26px;
            line-height: 26px;
            font-size: 14px;
            text-align: center;
            border: 1px solid #CCC;
            padding: 5px;
        }

        table td {
            height: 20px;
            font-size: 14px;
            border: 1px solid #CCC;
            background-color: #fff;
            padding: 5px;
        }

        .c1 {
            width: 120px;
        }

        .c2 {
            width: 120px;
        }

        .c3 {
            width: 150px;
        }

        .c4 {
            width: 80px;
            text-align: center;
        }

        .c5 {
            width: 80px;
            text-align: center;
        }

        .c6 {
            width: 270px;
        }
    </style>
</head>
<body>
<div class="warp">
    <h1 style="text-align:center;"></h1>
    <?php echo $html; ?>
</div>
</body>
</html>