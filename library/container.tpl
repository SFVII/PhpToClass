<?php 
		/**************************************
		***************************************
		****DB To Class Generator**************
		****Licence OpenSouce MAY 2016		*** 
		****http://dev.nippon.wtf *************
		****Contact: brice@nippon.wtf**********
		***************************************
		**************************************/

	class {_name} {_extends} {_xtendname}{

		public {field}

		private $update;
		private $result;

		function __construct(){

			// Use this varible for update time 
			$this->update = date('Y-m-d H:i:s');

		}

		private function hydrate($data){
			foreach ($data as $key => $value) {
				$method = 'set'.ucfirst($key);
				if (method_exists($this, $method)){
					$this->$method($value);
				}
			}
		}

		private function result2array($result = NULL){
			$array = array();
			while($tr = mysqli_fetch_array($result)){
				$array[] = $tr;
			}
			return $array;
		}

		public function setUnset(){
			{unset}
		}

		public function update(){
			//Have to customize by yourself
		}

		private function insert(){
			$this->query("INSERT INTO {_table}({_qfield}) VALUES ({_qvalues})");
		}

		public function remove(){
			if (is_int($this->_{primary}) && $this->_{primary} > 0){
				$this->query("DELETE FROM {_table} WHERE {primary}='".$this->_{primary}."'");
			}
		}

		public function load($kind = 'array'){
			if (is_int($this->_{primary}) && $this->_{primary} > 0){
				$this->result = $this->query("SELECT {_qfield} FROM {_table} WHERE {primary}='".$this->_{primary}."'");
			}
			if (strtolower($kind) == 'array')
				return $this->result2array($this->result);
			else if (strtolower($kind) == 'json')
				return json_encode($this->result2array($this->result));
		} 

		public function loadAll($kind = 'array'){ // By default array but you can set it to JSON
			$this->result = $this->query("SELECT {_qfield} FROM {_table}");
			if (strtolower($kind) == 'array')
				return $this->result2array($this->result);
			else if (strtolower($kind) == 'json')
				return json_encode($this->result2array($this->result));

		}

		public function search(){
			//Have to customize by yourself
		}

		public function count(){
			return mysqli_num_rows($this->result);
		}

		// Check and Secure Data

		{_init_function}
	}
				

?>