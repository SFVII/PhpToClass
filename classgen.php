<?php

/**
    @author  brice@nippon.wtf
    @description Class Generator Motor
    @requirement :      Check for 'Function' Folder and
                        'Library Folder';
    Last update : 2016-05-13
    licence : OpenSource
 **/

require_once 'connexion.php';

class db2class extends connexion
{
    private $field;
    private $array;
    private $_qfield;
    private $_qvalues;
    private $_unset;
    private $dir;

    private function normaliZE($dest){
        $i = 0;
        $i = strlen($dest);
        $i--;
        if ($dest[$i] != '/'){
            $dest .= '/';
        }
        return $dest;
    }

    public function __construct($table = '', $name = '', $extends = '', $dest = '')
    {
        // Checking information Saving existing file
        if ($dest != ''){
            $dest = $this->normaliZE($dest);
        }
        if (empty($table)) {
            die('Error Missig Table or Database');
        }if (empty($name)) {
            die('Missing Class Name');
        }if (empty($extends)) {
            echo 'Warning \'Extends\' Parameter isn\'t set<br>';
        }if (empty($dest)) {
            echo 'Warning: This Class will be load on this current folder<br>';
        }
        $dest = $this->check_files($dest, $name);
        if ($dest == false) {
            die('Something went wrong during folder creating process');
        }
        echo $dest.'<br>';

        // Start Process creating class

        $result = $this->query('DESCRIBE ' . $table);
        $count  = mysqli_num_rows($result);
        if ($count == 0) {
            die('NO DATABASE FOUND. Impossible to Create DB please check your data and try again<br>');
        }
        $i = 0;
        while ($tr = mysqli_fetch_array($result)) {
            //print_r($tr);
            $this->field .= "\$_" . $tr['Field'];
            if ($i < ($count - 1)) {
                $this->field .= ",\n\t\t\t";
                $this->_qfield .= $tr['Field'] . ", ";
                if ($tr['Key'] == "PRI") {
                    $this->_qvalues .= "'',";
                } else {
                    $this->_qvalues .= "'\".\$this->_" . $tr['Field'] . ".\"', ";
                }
            } else {
                $this->field .= ";\n\n";
                $this->_qfield .= $tr['Field'];
                $this->_qvalues .= "'\".\$this->_" . $tr['Field'] . ".\"'";
            }
            $this->array[] = array('field' => $tr['Field'], 'type' => $this->get_type($tr['Type'], $tr['Field']));
            $i++;
            if ($tr['Key'] == "PRI") {
                $primary = $tr['Field'];
            }
            $this->_unset .= "\$this->_" . $tr['Field'] . " = '';\n\t\t\t";
        }
        //var_dump($this->array);
        $function = $this->createClass();
        $tpl      = $this->load_tpl('library/container.tpl');
        $tpl      = str_replace("{_name}", $name, $tpl);
        $tpl      = str_replace("{_table}", $table, $tpl);
        $tpl      = str_replace("{_qfield}", $this->_qfield, $tpl);
        $tpl      = str_replace("{_qvalues}", $this->_qvalues, $tpl);
        $tpl      = str_replace("{primary}", $primary, $tpl);
        $tpl      = str_replace("{unset}", $this->_unset, $tpl);
        if (!empty($extends)) {
            $tpl = str_replace("{_extends}", "extends", $tpl);
            $tpl = str_replace("{_xtendname}", $extends, $tpl);
        } else {
            $tpl = str_replace("{_extends}", "", $tpl);
            $tpl = str_replace("{_xtendname}", "", $tpl);
        }
        $tpl = str_replace("{field}", $this->field, $tpl);
        $tpl = str_replace("{_init_function}", $function, $tpl);

        $tmp = fopen($this->dir, "w");
        fwrite($tmp, stripslashes($tpl));
        fclose($tmp);
        echo 'files was created with success :' . $this->dir.'<br>';
    }

    /**
    @name function check_files
    Checking and prevent erasing file.
    Will create directory if this last one doesn't exist
    Will create a backup file of existing file.
    @return type|string $log
     **/

    private function check_files($dest = '', $name = '')
    {
        $log = '';
        if (!is_dir($dest) && $dest != '') {
            if (mkdir($dest) == FALSE) {
                return false;
            }
            $log .= 'New directory \'' . $dest . '\' created<br>';
        } else {
            $log .= 'Current dir is selected '.$dest.'<br>';
        }
        if ($dest == '') {
            $dir = ucfirst($name) . '.php';
        } else {
            $dir = $dest . '/' . ucfirst($name) . '.php';
        }
       $this->dir = $dir;
        if (is_file($dir)) {
            $backup = $this->load_tpl($dir);
            echo $dest . 'backup/';
            if (!is_dir($dest . 'backup/')) { 
                if (!mkdir($dest . 'backup/')) {
                    die('Something went wrong during process creating folder backup');
                }
            }
            $fd = fopen($dest . 'backup/' . $name .'-'. date('Y-m-d_His').'.php', "w");
            fwrite($fd, $backup);
            fclose($fd);
            unset($backup);
            $log .= 'A backup file was created<br>';
            return $log;
        }else{
            return $log;
        }
    }

    private function createClass()
    {
        foreach ($this->array as $key => $value) {
            $tmp .= "\n" . $this->load_tpl('library/function.tpl');
            $tmp = str_replace("{_field}", $value['field'], $tmp);
            $tmp = str_replace("{_ufield}", ucfirst($value['field']), $tmp);
            if (is_array($value['type'])) {
                foreach ($value['type'] as $key => $values) {
                    if ($key == "condition") {
                        $tmp = str_replace("{_condition}", $values, $tmp);
                    } else if ($key == "format") {
                        $tmp = str_replace("{_format}", ucfirst($values), $tmp);
                    } else if ($key == "size") {

                        if ($values > 0) {
                            $tmp = str_replace("{_other}", " && strlen((string)\$" . $value['field'] . ") <= " . $values, $tmp);
                        } else {
                            $tmp = str_replace("{_other}", "", $tmp);
                        }
                    }
                }
            }
        }
        return $tmp;
    }

    /**
    Filter type from Database Field
     **/

    private function get_type($type, $field)
    {
        // More stuff will coming later

        $value = (int) preg_replace("/[^0-9]/", "", $type);
        $tmp   = explode("(", $type);
        if ($tmp[0] == "int") {
            $condition = "is_int(\$" . $field . ")";
            $format    = "\$" . $field . " = (int)\$" . $field . ";";
            $size      = (int) $value;
        } else if ($tmp[0] == "varchar") {
            $condition = "is_string(\$" . $field . ")";
            $format    = "\$" . $field . " = \$" . $field . ";";
            $size      = (int) $value;
        } else {
            $condition = "isset(\$" . $field . ")";
            $format    = "\$" . $field . " = \$" . $field . ";";
            $size      = (int) $value;
        }

        return array("condition" => $condition,
            "format"                 => $format,
            "size"                   => $size);
    }

    /**
    Template/File Loader/reader
     **/

    private function load_tpl($dir)
    {
        $fd     = fopen($dir, "r");
        $buffer = fread($fd, filesize($dir));
        fclose($fd);
        return $buffer;
    }
}

if ($_GET['submit'] == "true") {
    if ($_POST['db'] && $_POST['table'] && $_POST['name']) {
        extract($_POST);
        $tmp = new db2class($db . '.' . $table, $name, $extends, $dest);
        unset($tmp);
        unset($_POST);
    }
} else {
    include 'library/generator_form.php';
}
