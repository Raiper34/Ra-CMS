<?php

namespace App\Model;

use Nette;

class FileModel extends BaseModel
{
    
    private $tableFile = 'file';

    public function __construct(Nette\Database\Context $database, Nette\Security\User $user) 
    {
        parent::__construct($database, $user);
    }
    
    public function getFiles()
    {
        return $this->database->table($this->tableFile)->where('user_id', $this->user->id)->fetchAll();
    }
    
    public function getFilesOnPage($filesPerPage, $page)
    {
        return $this->database->table($this->tableFile)->where('user_id', $this->user->id)->limit($filesPerPage, $filesPerPage * $page)->fetchAll();
    }
    
    public function getFilesCount()
    {
        return $this->database->table($this->tableFile)->count('*');
    }
    
    public function saveFile($file)
    {
        $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
        $name = basename($file["name"], ".{$extension}");
        
        $target_dir = realpath(__DIR__ . '/../../') . '/upload/';
        $target_file = $target_dir . basename($file["name"]);
        move_uploaded_file($file["tmp_name"], $target_file);
        
        $this->database->table($this->tableFile)->insert(array(
            'name' => $name,
            'extension' => $extension,
            'path' => '/upload/' . $file["name"],
            'type' => $file["type"],
            'date' => date("Y-m-d H:i:s"),
            'user_id' => $this->user->id
        ));
    }
    
    public function deleteFile($file_id)
    {
        $file = $this->database->table($this->tableFile)->where('id', $file_id)->fetch();
        if(file_exists(__DIR__ . '/../..' . $file->path))
        {
            unlink(__DIR__ . '/../..' . $file->path);
            $this->database->table($this->tableFile)->where('id', $file_id)->delete();
        }
    }
    

}
