<?php
class ModeloVendaProduto extends Zend_Db_Table_Abstract implements IModeloBase {
	protected $_name = 'tb_venda_produto';
	protected $_primary = 'id';
	protected $_sequence = true;
	
	public function add($data) {
		$id = $this->insert ( $data );
		return $id;
	}
	public function getAll() {
		return $this->fetchAll ();
	}
	public function getById($id) {
		return $this->find ( $id )->current ();
	}
	public function del($id) {
		return $this->delete($this->_primary.' = ' . $id );
	}
	
}