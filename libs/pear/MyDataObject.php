<?php

include_once 'MDB/QueryTool.php';

class MyDataObject extends MDB_QueryTool {
	function MyDataObject($dsn, $options=array(),$MDBversion=2) {
		parent::MDB_QueryTool($dsn, $options, $MDBversion);
	}
    // }}}
    // {{{ add()

    /**
     * add a new member in the DB
     *
     * @param   array   contains the new data that shall be saved in the DB
     * @return  mixed   the inserted id on success, or false otherwise
     * @access  public
     */
    function add($newData)
    {
        // if no primary col is given, get next sequence value
        if (empty($newData[$this->primaryCol])) {
            if ($this->primaryCol) {
                // do only use the sequence if a primary column is given
                // otherwise the data are written as given
                if($this->_MDBversion == 2) {
                  if($this->auto_increment) {
                    $this->db->loadModule('Extended');
                    // fetch the next ID in the sequence or return php null
                    $id = $this->db->extended->getBeforeID($this->sequenceName, true, false);
                  } else {
                    $id = $this->db->nextID($this->sequenceName);
                  }
                } else {
                  $id = $this->db->nextId($this->sequenceName);
                }
                //$nextid_func = ($this->_MDBversion == 2) ? 'nextID' : 'nextId';
                //$id = (int)$this->db->$nextid_func($this->sequenceName);
                //$id = $this->db->$nextid_func($this->sequenceName);
                $newData[$this->primaryCol] = $id;
            } else {
                // if no primary col is given return true on success
                $id = true;
            }
        } else {
            $id = $newData[$this->primaryCol];
        }

        //unset($newData[$this->primaryCol]);

        $newData = $this->_checkColumns($newData, 'add');
        $newData = $this->_quoteArray($newData);
        
        //quoting the columns
        $tmpData = array();
        foreach ($newData as $key=>$val) {
            $tmpData[$this->db->quoteIdentifier($key)] = $val;
        }
        $newData = $tmpData;
        unset($tmpData);

        $query = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', array_keys($newData)),
            $this->_localeSafeImplode(', ', $newData)
        );
        $this->db->loadModule('Extended');
        $id = $this->db->extended->getAfterID($id, $this->sequenceName);
        return $this->execute($query, 'query') ? $id : false;
    }

    // }}}
    // {{{ addMultiple()

    /**
     * adds multiple new members in the DB
     *
     * @param   array   contains an array of new data that shall be saved in the DB
     *                  the key-value pairs have to be the same for all the data!!!
     * @return  mixed   the inserted ids on success, or false otherwise
     * @access  public
     */
    function addMultiple($data)
    {
        if (!sizeof($data)) {
            return false;
        }
        $ret = true;
        // the inserted ids which will be returned or if no primaryCol is given
        // we return true by default
        $retIds = $this->primaryCol ? array() : true;
        $dbtype = $this->db->phptype;
        if ($dbtype == 'mysql') { //Optimise for MySQL
            $allData = array();                     // each row that will be inserted
            foreach ($data as $key => $aData) {
                $aData = $this->_checkColumns($aData,'add');
                $aData = $this->_quoteArray($aData);

                if (empty($aData[$this->primaryCol])) {
                    if ($this->primaryCol) {
                        // do only use the sequence if a primary column is given
                        // otherwise the data are written as given
                        //$func = ($this->_MDBversion == 2) ? 'nextID' : 'nextId';
                        //$retIds[] = $id = (int)$this->db->$func($this->sequenceName);
                        if($this->_MDBversion == 2) {
                          if($this->auto_increment) {
                            $this->db->loadModule('Extended');
                            // fetch the next ID in the sequence or return php null
                            $id = $this->db->extended->getBeforeID($this->sequenceName, true, false);
                          } else {
                            $id = $this->db->nextID($this->sequenceName);
                          }
                        } else {
                          $id = $this->db->nextId($this->sequenceName);
                        }                        
                        $aData[$this->primaryCol] = $id;
                    }
                } else {
                    $aData[$this->primaryCol];
                }
                $allData[] = '('.$this->_localeSafeImplode(', ', $aData).')';
            }

            //quoting the columns
            $tmpData = array();
            foreach ($aData as $key=>$val) {
                $tmpData[$this->db->quoteIdentifier($key)] = $val;
            }
            $newData = $tmpData;
            unset($tmpData);

            $query = sprintf(
                'INSERT INTO %s (%s) VALUES %s',
                $this->table ,
                implode(', ', array_keys($aData)) ,
                $this->_localeSafeImplode(', ', $allData)
            );
            $this->db->loadModule('Extended');
            $retIds[] = $this->db->extended->getAfterID($id, $this->sequenceName);            
            return $this->execute($query, 'query') ? $retIds : false;
        }

        //Executing for every entry the add method
        foreach ($data as $entity) {
            if ($ret) {
                $res = $this->add($entity);
                if (!$res) {
                    $ret = false;
                } else {
                    $retIds[] = $res;
                }
            }
        }
        //Setting the return value to the array with ids
        if ($ret) {
            $ret = $retIds;
        }
        return $ret;
    }
}
?>
