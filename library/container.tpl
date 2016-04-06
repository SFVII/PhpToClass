<?php 
		/**************************************
		***************************************
		****DB To Class Generator**************
		****Licence OpenSouce Februrary 2016*** 
		****http://dev.nippon.wtf************** 
		****Contact: brice@nippon.wtf**********
		***************************************
		**************************************/

	class {_name} {_extends} {_xtendname}{
		public {field}

		private $result;

		function __construct(){

		}

		private function hydrate($data){
			foreach ($data as $key => $value) {
				$method = 'set'.ucfirst($key);
				if (method_exists($this, $method)){
					$this->$method($value);
				}
			}
		}

		private function setUnset(){
			{unset}
		}

		private function update(){
			//Have to customize by yourself
		}

		private function insert(){
			$this->query("INSERT INTO {_table}({_qfield}) VALUES ({_qvalues})");
		}

		private function remove(){
			if (is_int($this->_{primary}) && $this->_{primary} > 0){
				$this->query("DELETE FROM {_table} WHERE {primary}='".$this->_{primary}."'");
			}
		}

		private function load(){
			if (is_int($this->_{primary}) && $this->_{primary} > 0){
				$this->result = $this->query("SELECT {_qfield} FROM {_table} WHERE {primary}='".$this->_{primary}."'");
			}
		} 

		{_init_function}
	}
				

?>