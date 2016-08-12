<?php

require_once 'Session.class.php';

class Carro extends Session{
	private $tb_carro;
	private $data = array();

	public function __construct(){
		$this->getInstanceSession();
	}

	private function getInstanceSession(){
		$this->tb_carro = $this->getInstance();
	}

	public function setCarro($id, $marca, $modelo, $ano){
		$this->tb_carro->rows = array('id'     => $id,
																  'marca'  => $marca,
																  'modelo' => $modelo,
																  'ano'    => $ano);
	}

	public function getCarro($id){
		var_dump($this->tb_carro->rows);

		foreach($this->tb_carro as $key => $value){
			if($id == $value){
				$this->data = $value;
			}
		}

		return $this->data;
	}
}
?>