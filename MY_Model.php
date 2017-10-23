<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Model extends CI_Model {
	
	// table name
	var $table = '';
	
	// key table
	var $key = 'id';
	
	// Order default
	var $order = '';
	
	// field select default (VD: $select = 'id, name')
	var $select = '';
	
	/**
	 * add row
	 * $data : add data
	 */
	function create($data = array())
	{
		if($this->db->insert($this->table, $data))
		{
		   return TRUE;
		}else{
			return FALSE;
		}
	}
	
	/**
	 * update row by ID
	 * $id : ID table 
	 * $data : data update
	 */
	function update($id, $data)
	{
		if (!$id)
		{
			return FALSE;
		}
		
		$where = array();
	 	$where[$this->key] = $id;
	    $this->update_rule($where, $data);
	 	
	 	return TRUE;
	}
	
	/**
	 * update row = where
	 * $where : condition
	 * $data : update data
	 */
	function update_rule($where, $data)
	{
		if (!$where)
		{
			return FALSE;
		}
		
	 	$this->db->where($where);
	 	$this->db->update($this->table, $data);

	 	return TRUE;
	}

	/**
	 * delete row ID
	 * $id : ID value
	 */
	function delete($id)
	{
		if (!$id)
		{
			return FALSE;
		}
		//neu la so
		if(is_numeric($id))
		{
			$where = array($this->key => $id);
		}else
		{
		    //$id = 1,2,3...
			$where = $this->key . " IN (".$id.") ";
		}
	 	$this->del_rule($where);
		
		return TRUE;
	}
	
	/**
	 * delete row = where
	 * $where : condition to delete
	 */
	function del_rule($where)
	{
		if (!$where)
		{
			return FALSE;
		}
		
	 	$this->db->where($where);
		$this->db->delete($this->table);
	 
		return TRUE;
	}
	
	/**
	 * do query
	 * $sql : command sql
	 */
	function query($sql){
		$rows = $this->db->query($sql);
		return $rows->result;
	}
	
	/**
	 * select row = ID
	 * $id : ID value
	 * $field : column select
	 */
	function get_info($id, $field = '')
	{
		if (!$id)
		{
			return FALSE;
		}
	 	
	 	$where = array();
	 	$where[$this->key] = $id;
	 	
	 	return $this->get_info_rule($where, $field);
	}
	
	/**
	 * get info(row)form where
	 * $where: condition
	 * $field: row info
	 */
	function get_info_rule($where = array(), $field= '')
	{
	    if($field)
	    {
	        $this->db->select($field);
	    }
		$this->db->where($where);
		$query = $this->db->get($this->table);
		if ($query->num_rows())
		{
			return $query->row();
		}
		
		return FALSE;
	}
	
	/**
	 * get total
	 */
	function get_total($input = array())
	{
		$this->get_list_set_input($input);
		
		$query = $this->db->get($this->table);
		
		return $query->num_rows();
	}
	
	/**
	 * get total
	 * $field: row need to get total
	 */
	function get_sum($field, $where = array())
	{
		$this->db->select_sum($field);//tinh rong
		$this->db->where($where);//dieu kien
		$this->db->from($this->table);
		
		$row = $this->db->get()->row();
		foreach ($row as $f => $v)
		{
			$sum = $v;
		}
		return $sum;
	}
	
	/**
	 * get 1 row
	 */
	function get_row($input = array()){
		$this->get_list_set_input($input);
		
		$query = $this->db->get($this->table);
		
		return $query->row();
	}
	
	/**
	 * get list
	 * $input : array data
	 */
	function get_list($input = array())
	{
	    //xu ly ca du lieu dau vao
		$this->get_list_set_input($input);
		
		//thuc hien truy van du lieu
		$query = $this->db->get($this->table);
		//echo $this->db->last_query();
		return $query->result();
	}
	
	/**
	 * parse properties input get list
	 * $input : array data
	 */
	
	protected function get_list_set_input($input = array())
	{
		
		// add condition for query through $input['where'] 
		//(vi du: $input['where'] = array('email' => 'hocphp@gmail.com'))
		if ((isset($input['where'])) && $input['where'])
		{
			$this->db->where($input['where']);
		}
		
		//search like
		// $input['like'] = array('name' => 'abc');
	    if ((isset($input['like'])) && $input['like'])
		{
			$this->db->like($input['like'][0], $input['like'][1]); 
		}
		
		// add sort data through $input['order'] 
		//(ví dụ $input['order'] = array('id','DESC'))
		if (isset($input['order'][0]) && isset($input['order'][1]))
		{
			$this->db->order_by($input['order'][0], $input['order'][1]);
		}
		else
		{
			//default order (DESC id) 
			$order = ($this->order == '') ? array($this->table.'.'.$this->key, 'desc') : $this->order;
			$this->db->order_by($order[0], $order[1]);
		}
		
		// add condition limit for query through $input['limit'] 
		//(ví dụ $input['limit'] = array('10' ,'0')) 
		if (isset($input['limit'][0]) && isset($input['limit'][1]))
		{
			$this->db->limit($input['limit'][0], $input['limit'][1]);
		}
		
	}
	
	/**
	 * check exist data followed condition
	 * $where : array(condtion)
	 */
    function check_exists($where = array())
    {
	    $this->db->where($where);
	    //thuc hien cau truy van lay du lieu
		$query = $this->db->get($this->table);
		
		if($query->num_rows() > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
}
?>