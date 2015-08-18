<?php
class ModeloVenda extends Zend_Db_Table_Abstract implements IModeloBase {
	protected $_name = 'tb_venda';
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
	public function getByIdCustomer($id) {
		$db     = $this->_db;
        $select = $db->select();
        $select = "SELECT 
        				v.id, p.descricao AS produto, vp.qtde, v.negociacao_status, DATE_FORMAT(v.data_cadastro,'%d/%m/%Y') AS data_cadastro, 
	                	REPLACE(REPLACE(REPLACE(FORMAT((p.valor*vp.qtde), 2), '.', '@'), ',', '.'), '@', ',') AS valor, 
	                	FORMAT(vp.qtde, 0) AS qtde
	                FROM 
	                	tb_venda_produto vp 
	                	INNER JOIN tb_venda v ON v.id = vp.id_venda
	                	INNER JOIN tb_produto p ON p.id = vp.id_produto
	                WHERE 
	                	vp.resgatado = 0 AND v.id_customer={$id}
	                ORDER BY 
	                	v.data_cadastro ASC";
        $results = $db->fetchAll($select);

		return $results;
	}
	public function del($id) {
		return $this->delete($this->_primary.' = ' . $id );
	}
	
}