<?php
namespace lib\config;

class SysSession implements \SessionHandlerInterface
{
    private $dbConn;
    public function __construct(\PDO $dbConn)
    {
        $this->dbConn = $dbConn;
    }
    public function open($savePath, $sessionName):bool
    {
        if($this->dbConn){
            return true;
        }else{
            return false;
        }
    }
    public function close() : bool
    {
        unset($this->dbConn);
        return true;
    }
    public function read($id) : string
    {
        if(!$stmt = $this->dbConn->prepare("SELECT \"Session_Data\" FROM \"Session\" WHERE \"Session_Id\" = :id AND \"Session_Expires\" > :date"))
            return "";
        
        if(!$stmt->execute(["id" => $id, "date" => date('Y-m-d H:i:s')]))
            return "";

        if(!$row = $stmt->fetch(\PDO::FETCH_ASSOC))
            return "";
        else
            return $row['Session_Data'];
    }
    public function write($id, $data):bool
    {
        $DateTime = date('Y-m-d H:i:s');
        $NewDateTime = date('Y-m-d H:i:s',strtotime($DateTime.' + 1 hour'));
        if(!$stmt = $this->dbConn->prepare("SELECT * FROM \"Session\" WHERE \"Session_Id\" = :id"))
            return false;
        
        if(!$stmt->execute([ "id" => $id ]))
            return false;

        $tem_sessao = count($stmt->fetchAll(\PDO::FETCH_ASSOC)) > 0;

        if(!$tem_sessao)
        {
            if(!$stmt = $this->dbConn->prepare("INSERT INTO public.\"Session\"( \"Session_Id\", \"Session_Expires\", \"Session_Data\") VALUES (:id, :Session_expires, :data)"))
                return false;

            if(!$stmt->execute([
                "Session_expires" => $NewDateTime,
                "data" => $data,
                "id" => $id
            ]))
                return false;
            else 
                return true;
        }
        else 
        {
            if(!$stmt = $this->dbConn->prepare("UPDATE \"Session\" SET \"Session_Expires\" = :Session_expires, \"Session_Data\" = :data WHERE \"Session_Id\" = :id"))
                return false;

            if(!$stmt->execute([
                "Session_expires" => $NewDateTime,
                "data" => $data,
                "id" => $id
            ]))
                return false;
            else 
                return true;
        }
        
    }
    public function destroy($id):bool
    {
        if(!$stmt = $this->dbConn->prepare("DELETE FROM \"Session\" WHERE \"Session_Id\" = :id"))
            return false;

        if(!$stmt->execute([ "id" => $id]))
            return false;
        else 
            return true;
    }
    public function gc($maxlifetime) : bool
    {
        if(!$stmt = $this->dbConn->prepare("DELETE FROM \"Session\" WHERE ((EXTRACT(EPOCH FROM \"Session_Expires\") + :maxlifetime1) < :maxlifetime2)"))
            return false;
        if(!$stmt->execute([ "maxlifetime1" => $maxlifetime, "maxlifetime2" => $maxlifetime]))
            return false;
        else 
            return true;
    }
}