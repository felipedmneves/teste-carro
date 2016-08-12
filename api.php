<?php
//error_reporting(E_ERROR);

require_once 'Rest.class.php';
require_once 'Carro.class.php';

class API extends REST{
		public $data = '';

		const DB_SERVER   = 'localhost';
		const DB_USER     = 'root';
		const DB_PASSWORD = '';
		const DB          = 'test';

		private $db = NULL;

		public function __construct(){
			parent::__construct();	// Inicia o contructor Pai
			$this->dbConnect();			// Iniciar a conexão com o banco de dados
		}

		/*
		 * Conexão com o banco
		*/
		private function dbConnect(){
			$this->db = mysqli_connect(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD, self::DB);

			if(!$this->db){
				$error = array('status' => 'Erro',
											 'msg'    => 'Não foi possível conectar ao MySQL.' . PHP_EOL .
											 						 mysqli_connect_errno() . ': ' . mysqli_connect_error());

				$this->response($this->json($error), 500);
			}
		}

		/*
		 * Método público para acesso à API.
		 * Este método chama dinamicamente o método baseado na cadeia de consulta
		 */
		public function processApi(){
			$func = strtolower(trim(str_replace('/', '', $_REQUEST['rquest'])));

			if((int)method_exists($this, $func) > 0){
				$this->$func();
			}
			else{
				$this->response("Método {$func} inválido.", 404);	// Se o método não existir dentro desta classe, a resposta será "Página não encontrada".
			}
		}

		/*
		 *	Simples API de login
		 *  Login deve ser por método POST
		 *  email: <EMAIL DO USUÁRIO>
		 *  pwd  : <SENHA DO USUÁRIO>
		 */
		private function login(){
			if($this->get_request_method() != 'POST'){
				$error = array('status' => 'Erro',
											 'msg'    => 'Método ' . $this->get_request_method() . 'inválido.');

				$this->response($this->json($error), 406);
			}

			$email    = $this->_request['email'];
			$password = $this->_request['pwd'];

			if(empty($email) or empty($password)){
				$validation = '';

				if(empty($email)){
					$validation .= 'Campo E-mail não informado.' . PHP_EOL;
				}
				if(filter_var($email, FILTER_VALIDATE_EMAIL)){
					$validation .= 'E-mail inválido.' . PHP_EOL;
				}

				if(empty($password)){
					$validation .= 'Campo Senha não informado.' . PHP_EOL;
				}

				$error = array('status' => 'Falha',
											 'msg'    => 'Falha no Login:' . PHP_EOL . $validation);

				$this->response($this->json($error), 400);
			}

			if(!empty($email) or !empty($password)){
				$query  = " SELECT user_id,
													 user_fullname,
													 user_email
											FROM users
										 WHERE user_email    = '{$email}'
										   AND user_password = '{$password}'
										 LIMIT 1 ";

				$result = mysqli_query($this->db, $query);

				if(mysqli_num_rows($result) > 0){
					$this->response($this->json(mysqli_fetch_array($result, MYSQL_ASSOC)), 200);
				}
				else{
					if(mysqli_error($this->db)){
						$error = array('status' => 'Erro',
													 'msg'    => 'Não foi possível listar os Usuários.' . PHP_EOL .
											 								 mysqli_errno($this->db) . ': ' . mysqli_error($this->db));

						$this->response($this->json($error), 500);
					}
					else{
						$this->response('', 204);
					}
				}
			}
		}

		private function users(){
			if($this->get_request_method() != 'GET'){
				$error = array('status' => 'Erro',
											 'msg'    => 'Método ' . $this->get_request_method() . 'inválido.');

				$this->response($this->json($error), 406);
			}

			$query  = " SELECT user_id,
												 user_fullname,
												 user_email
									  FROM users
									 WHERE user_status = 1 ";

			$result = mysqli_query($this->db, $query);

			if(mysqli_num_rows($result) > 0){
				$this->response($this->json(mysqli_fetch_array($result, MYSQL_ASSOC)), 200);
			}
			else{
				if(mysqli_error($this->db)){
					$error = array('status' => 'Erro',
												 'msg'    => 'Não foi possível listar os Usuários.' . PHP_EOL .
										 								 mysqli_errno($this->db) . ': ' . mysqli_error($this->db));

					$this->response($this->json($error), 500);
				}
				else{
					$this->response('', 204);
				}
			}
		}

		private function deleteUser(){
			if($this->get_request_method() != 'DELETE'){
				$error = array('status' => 'Erro',
											 'msg'    => 'Método ' . $this->get_request_method() . 'inválido.');

				$this->response($this->json($error), 406);
			}

			$id = (int) $this->_request['id'];

			if($id > 0){
				$query  = " DELETE
										  FROM usersa
										 WHERE user_id = {$id} ";

				$result = mysqli_query($this->db, $query);

				if(mysqli_error($this->db)){
					$error = array('status' => 'Erro',
												 'msg'    => 'Não foi possível listar os Usuários.' . PHP_EOL .
										 								 mysqli_errno($this->db) . ': ' . mysqli_error($this->db));

					$this->response($this->json($error), 500);
				}
				else{
					$success = array('status' => 'Sucesso',
													 'msg'    => 'registro excluído êxito');

					$this->response($this->json($success), 200);
				}
			}
			else{
				$error = array('status' => 'Falha',
											 'msg'    => 'Parâmetro inválido');

				$this->response($this->json($error), 200);
			}
		}

		private function teste(){
			if($this->get_request_method() != 'GET'){
				$error = array('status' => 'Erro',
											 'msg'    => 'Método ' . $this->get_request_method() . 'inválido.');

				$this->response($this->json($error), 406);
			}

			$carro = new Carro();
			$carro->setCarro(1, 'Chevrolet', 'Corsa', 2009);
			$carro->setCarro(2, 'Mitsubishi', 'Pajero TR4', 2008);

			$this->response($this->json($carro->getCarro(1)), 200);
		}

		/*
		 * Codifica o Array no JSON
		*/
		private function json($data){
			if(is_array($data)){
				return json_encode($data);
			}
		}
	}

	// Inicia a Biblioteca
	$api = new API;
	$api->processApi();
?>