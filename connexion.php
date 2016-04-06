<?php 
	class connexion{
		public $key;
		public 	$qr;
		function query($query){
			if (isset($query)){
				$this->key = mysqli_connect("HOST", "USER", "PASSWORD", "TABLE");
				return mysqli_query($this->key, $query);
			}
		}
	}
?>