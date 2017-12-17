<?php
$config = array();
if($handle = opendir('./config')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != ".." && end(explode('.', $file)) == 'ini') {
            $ini = parse_ini_file('./config/' . $file);
			foreach($ini as $k=>$v) {
				$config[$k] = $v;
			}
        }
    }
    closedir($handle);
}
