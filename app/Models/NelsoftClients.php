<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NelsoftClients extends Model
{
    use HasFactory;

    protected $connection = 'mysql5';
    protected $table;

    public function setTableName($table)
    {
        $this->table = $table;
        return $this;
    }

    public function getTable()
    {
        return $this->table ?? parent::getTable();
    }
}
