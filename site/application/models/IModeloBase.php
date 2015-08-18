<?php
interface IModeloBase {
	public function add($data) ;
	public function getAll() ;
	public function getById($id);
	public function del($id);
}