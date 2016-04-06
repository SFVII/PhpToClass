<?php 
		require('connexion.php');

		class db2class extends connexion{
			private $field;
			private $array;
			private $_qfield;
			private $_qvalues;
			private $_unset;

			function __construct($table, $name, $extends){
				$result = $this->query('DESCRIBE '.$table);
				$count = mysqli_num_rows($result);
				$i = 0;
				while ($tr = mysqli_fetch_array($result)){
					$this->field .= "\$_".$tr['Field'];
					if ($i < ($count - 1)){
						$this->field .= ",\n\t\t\t"; $this->_qfield .= $tr['Field'].", "; 
						if($tr['Key'] == "PRI"){
							$this->_qvalues .= "'',";
						}else{  
							$this->_qvalues .= "'\".\$this->_".$tr['Field'].".\"', ";
						}
					}else{
						$this->field .= ";\n\n";
						$this->_qfield .= $tr['Field'];
						$this->_qvalues .= "'\".\$this->_".$tr['Field'].".\"'";
					}
					$this->array[] = array('field' => $tr['Field'], 'type' => $this->get_type($tr['Type'], $tr['Field']));
					$i++;
					if  ($tr['Key'] == "PRI"){
						$primary = $tr['Field'];
					}
					$this->_unset .= "\$this->_".$tr['Field']." = '';\n\t\t\t";
				}
				//var_dump($this->array);
				$function = $this->createClass();
				$tpl = $this->load_tpl('library/container.tpl');
				$tpl = str_replace("{_name}", $name, $tpl);
				$tpl = str_replace("{_table}", $table, $tpl);
				$tpl = str_replace("{_qfield}", $this->_qfield, $tpl);
				$tpl = str_replace("{_qvalues}", $this->_qvalues, $tpl);
				$tpl = str_replace("{primary}", $primary, $tpl);
				$tpl = str_replace("{unset}", $this->_unset,  $tpl);
				if (!empty($extends)){
					$tpl = str_replace("{_extends}", "extends", $tpl);
					$tpl = str_replace("{_xtendname}", $extends, $tpl);
				}else{
					$tpl = str_replace("{_extends}", "", $tpl);
					$tpl = str_replace("{_xtendname}", "", $tpl);
				}
				$tpl = str_replace("{field}", $this->field, $tpl);
				$tpl = str_replace("{_init_function}", $function, $tpl);
				$tmp = fopen($name.".php", "w");
				fwrite($tmp, stripslashes($tpl));
				fclose($tmp);
				//echo $tpl;
			}

			private function createClass(){
				foreach ($this->array as $key => $value) {
					$tmp .= "\n".$this->load_tpl('library/function.tpl');
					$tmp = str_replace("{_field}", $value['field'], $tmp);
					$tmp = str_replace("{_ufield}", ucfirst($value['field']), $tmp);
					if (is_array($value['type'])){
						foreach ($value['type'] as $key => $values) {
							if ($key == "condition"){
								$tmp = str_replace("{_condition}", $values, $tmp);
							}else if ($key == "format"){
								$tmp = str_replace("{_format}", ucfirst($values), $tmp);
							}else if ($key == "size"){

								if ($values > 0){
									$tmp = str_replace("{_other}", " && strlen((string)\$".$value['field'].") <= ".$values, $tmp);
								}else{
									$tmp = str_replace("{_other}", "", $tmp);
								}
							}		
						}
					}
				}
				return $tmp;
			}

			private function get_type($type, $field){
				$value = (int)preg_replace("/[^0-9]/","",$type);
				$tmp = explode("(", $type);
				if ($tmp[0] == "int"){
					$condition = "is_int(\$".$field.")";
					$format = "\$".$field." = (int)\$".$field.";";
					$size = (int)$value;
				}else if ($tmp[0] == "varchar"){
					$condition = "is_string(\$".$field.")";
					$format = "\$".$field." = \$".$field.";";
					$size = (int)$value;	
				}else{
					$condition = "isset(\$".$field.")";
					$format = "\$".$field." = \$".$field.";";
					$size = (int)$value;
				}

				return array("condition" => $condition, 
							"format" => $format, 
							"size" => $size);
			}

			private function load_tpl($dir){
				$fd = fopen($dir, "r"); 
   				$buffer = fread($fd, filesize($dir)); 
   				fclose($fd);
   				return $buffer; 
			}


		}

		$tmp = new db2class($_GET['db'].".".$_GET['table'], $_GET['name'], $_GET['extends']);
		//echo $_GET['db'].$_GET['table']."done";
?>