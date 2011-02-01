<?php
/*
 * PHP Closure Complier Class (Dual licensed under the MIT)
 * 风吟 (http://fengyin.name/guestbook.php)
 * DEMO http://sweet.fengyin.name/ ( closure Complier online compressor)
 ----------------------------------------------------------
 要求: jdk 1.6+  php exec() 
 作用: 使用  Closure Complier 批量压缩一个或多个和目录的 js文件.
 ----------------------------------------------------------
 */

//Windows 调用方式:
/*
$complier = new closure_complier(array(
 'java_home'=>'java', //或自己指定 jdk 安装的 bin 目录 (绝对路径)
 'jar_file'=>'D:\www\htdocs\compiler.jar', 
 'save_path'=>'D:\www\htdocs\results\\', //必须有可写权限
 //----------------------------------------   压缩配置  ----------------------------------------  //
 'optimization'=>'SIMPLE_OPTIMIZATIONS', //(默认为简单) WHITESPACE_ONLY(空白) ADVANCED_OPTIMIZATIONS (很黄很暴力)
 'charset'=>'utf-8', //文件的编码，推荐使用utf-8.
 'customize'=>'', //如果你懂你可以自定义一些参数.例如  'customize'=>'--formatting [PRETTY_PRINT | PRINT_INPUT_DELIMITER]’   
));

//$s=$complier->compress(array('D:\www\htdocs\swfobject_src.js','D:\www\htdocs\yuiloader.js'),'all.js');
//压缩多个文件，会合并成一个，第二个参数为文件名，如果不指定则是 ajj.js

//$s=$complier->compress('D:\www\htdocs\swfobject_src.js');
//压缩单个文件

//$s=$complier->directory('D:\www\htdocs\\');
//压缩一个目录,所有文件都会保存为 xxx.min.js 在你初始化类的 save_path (必须可写)

print_r($s);
 
----------------------------------------------------------
//Linux  调用方式:

$complier = new closure_complier(array(
 'java_home'=>'java', //或自己指定 java 安装的 bin 目录 (绝对路径)
 'jar_file'=>' /home/admin/compiler.jar', 
 'save_path'=>'/home/admin/results/', //必须有可写权限
 //----------------------------------------   压缩配置  ----------------------------------------  //
 'optimization'=>'SIMPLE_OPTIMIZATIONS', //(默认为简单) WHITESPACE_ONLY(空白) ADVANCED_OPTIMIZATIONS (很黄很暴力)
 'charset'=>'utf-8', //文件的编码，推荐使用utf-8.
 'customize'=>'', //如果你懂你可以自定义一些参数.例如  'customize'=>'--formatting [PRETTY_PRINT | PRINT_INPUT_DELIMITER]’   
));

//$s=$complier->compress(array('/home/admin/swfobject_src.js','/home/admin/yuiloader.js'),'all.js');
//压缩多个文件，会合并成一个，第二个参数为文件名，如果不指定则是 ajj.js

//$s=$complier->compress('/home/admin/swfobject_src.js');
//压缩单个文件

//$s=$complier->directory('/home/admin/');
//压缩一个目录,所有文件都会保存为 xxx.min.js 在你初始化类的 save_path (必须可写)

print_r($s); 
*/

class closure_complier {
        function __construct($options = array()) {
            $this->options = $options;
        }
        function args($o) {
            if (is_array($o['file'])) {
                foreach ($o['file'] as $js) {
                    $jsfile .= ' --js='.$js;
                }
                $newfile = $o['save_path'].$this->options['allfn'];
            } else {
                $jsfile = ' --js='.$o['file'];
                $newfile = $o['save_path'] .$this->replace($o['file']);
            }
            $cmd['file'] = $newfile;
            $cmd['shell'] = $o['java_home'] . ' -jar ' . $o['jar_file'].' --charset '.$o['charset'].' '.$o['customize'].' '.' --compilation_level '.$o['optimization'].$jsfile.' --js_output_file='.$newfile;
            return $cmd;
        }
        function getExtension($fn) {
            return pathinfo(strtolower($fn));
        }
        function replace($fn) {
            $exten = $this->getExtension($fn);
            return str_replace('.'.$exten['extension'], '.min.' . $exten['extension'], $exten['basename']);
        }
        function exec($cmd) {
            exec($cmd['shell'].' 2>&1', $out, $status);
            return array(
            'shell' => $cmd['shell'],
                'out' => implode("\n",$out),
                'status' => $status,
                'success' => $cmd['file'] 
			);
        }
        function ls($dir) {
            !($dh = opendir($dir)) && exit('open directory error');
            while (($file = readdir($dh)) !== false) {
                $exten = $this->getExtension($file);
                if ($exten['extension'] == 'js') {
                    $files[] = $dir . $file;
                }
            }
            closedir($dh);
            return $files;
        }
        function directory($dir) {
            !is_dir($dir) && exit('directory error');
            foreach ($this->ls($dir) as $file) {
                $fn[] = $this->compress($file);
            }
            return $fn;
        }
        function compress($file, $fn = 'all.min.js') {
            $this->options['allfn'] = $fn;
            $this->options['file'] = $file;
            return $this->exec($this->args($this->options));
        }
    }
?>