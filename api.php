<?php
//error_reporting(E_ERROR);

require_once 'Rest.class.php';
require_once 'Carro.class.php';

class API extends REST{
	public $data = '';
	public $carros;

	public function __construct(){
		parent::__construct();

		$this->InstanceCarros();
		$this->setCarros();		
		
		$this->processApi();
	}


	private function InstanceCarros(){
		$this->carros = new Carro();
	}

	private function setCarros(){		
		$this->cadastraCarro(1, 'Chevrolet', 'Corsa', 2009);
		$this->cadastraCarro(2, 'Mitsubishi', 'Pajero TR4', 2008);
		$this->cadastraCarro(3, 'Fiat', 'Uno', 1994);
	}

	public function processApi(){
		$func = strtolower(trim(str_replace('/', '', $_REQUEST['rquest'])));

		if((int)method_exists($this, $func) > 0){
			$this->$func();
		}
		else{
			$this->response("Método {$func} inválido.", 404);
		}
	}
	
	private function carros(){
		switch($this->get_request_method()){
			case 'GET':
				$id = $this->_request['id'];

				if(isset($id)){
					$this->retornaCarro($id);
				}
				else{
					$this->listaCarros();
				}
			break;

			case 'POST':
				$id     = $this->carros->getIndexNewRow();
				$marca  = $this->_request['marca'];
				$modelo = $this->_request['modelo'];
				$ano    = $this->_request['ano'];

				$this->cadastraCarro($id, $marca, $modelo, $ano);

				$this->listaCarros();
			break;

			case 'DELETE':
				$id = $this->_request['id'];

				$this->apagaCarro($id);

				$this->listaCarros();
			break;
		}
	}

	private function listaCarros(){
		$this->response($this->json($this->carros->listarCarros()), 200);
	}

	private function retornaCarro($id){
		$this->response($this->json($this->carros->getCarro($id)), 200);
	}

	private function cadastraCarro($id, $marca, $modelo, $ano){
		$this->carros->setCarro($id, $marca, $modelo, $ano);
	}

	private function apagaCarro($id){
		$this->carros->excluirCarro($id);
	}
	
	private function json($data){
		if(is_array($data)){
			return json_encode($data);
		}
	}
}

$api = new API;
?>