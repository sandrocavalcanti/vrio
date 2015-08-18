<?php
class ModeloCustomer extends Zend_Db_Table_Abstract implements IModeloBase {
	protected $_name = 'tb_customer';
	protected $_primary = 'id';
	protected $_sequence = true;
	
	public function add($data) {
		$caracters = array("(",")",".");
    	$data['celular'] = str_replace($caracters, "", $data['celular']);
    	$data['senha'] = sha1($data['senha']);	
    	$data['data_cadastro'] = date('Y-m-d H:i:s');	
		
		if($this->checkExist($data) <= 0){
			$id = $this->insert ( $data );
		}else{
			$id = 0;
		}

		return $id;	
		
	}
	public function getAll() {
		return $this->fetchAll ();
	}
	public function checkExist($dados) {
		return $this->fetchAll ('celular LIKE "'.$dados['celular'].'" OR cpf LIKE "'.$dados['cpf'].'"')->count();
	}
	public function getById($id) {
		return $this->find ( $id )->current ();
	}
	public function del($id) {
		return $this->delete($this->_primary.' = ' . $id );
	}
	
}