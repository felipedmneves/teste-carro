<?php

require_once 'Session.class.php';

class Carro extends Session{
	private $tb_carro = array();
	private $index    = 0;

	public function __construct(){
		//$this->getInstanceSession();
	}

	private function getInstanceSession(){
		$this->tb_carro = $this->getInstance();
	}

	private function getRow($id){
		foreach($this->tb_carro as $key => $value){
			if($id == $value['id']){
				$this->row = $key;
			}
		}

		return $this->row;
	}

	public function setCarro($id, $marca, $modelo, $ano){
		$row = $this->getRow($id);

		if(isset($row)){
			$this->tb_carro[$row]['id']     = $id;
			$this->tb_carro[$row]['marca']  = $marca;
			$this->tb_carro[$row]['modelo'] = $modelo;
			$this->tb_carro[$row]['ano']    = $ano;
		}
		else{
			$carro = array('id'     => $id,
						   'marca'  => $marca,
						   'modelo' => $modelo,
						   'ano'    => $ano);

			array_push($this->tb_carro, $carro);
		}
	}

	public function getCarro($id){
		foreach($this->tb_carro as $key => $value){
			if($id == $value['id']){
				$this->data = $value;
			}
		}

		return $this->data;
	}

	public function listarCarros(){
		return $this->tb_carro;
	}

	public function excluirCarro($id){
		$row = $this->getRow($id);

		if(isset($row)){
			unset($this->tb_carro[$row]);
		}
	}

	public function getIndexNewRow(){
		return count($this->tb_carro) + 1;
	}
}
?>