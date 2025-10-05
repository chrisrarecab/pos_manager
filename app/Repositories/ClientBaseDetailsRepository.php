<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;

class ClientBaseDetailsRepository
{
    public function __construct()
    {
        $this->connection = DB::connection('mysql5'); 
    }

    public function getClientGroupIds()
    {
        return $this->connection->table('clientgroup as CG')
            ->select('CG.id as id', 'CG.name as name')
            ->where('CG.show', '<>', 0)
            ->where('CG.name', '<>', '')
            ->where('CG.name', '<>', '-')
            ->where('CG.name', '<>', '0')
            ->orderBy('CG.id', 'ASC')
            ->get();
    }

    public function getClientNetworkIds($id)
    {
        return $this->connection->table('clientgroup as CG')
            ->select('CH.id as id', 'CH.name as name')
            ->leftJoin('clienthead as CH', 'CH.clientgroupid', '=', 'CG.id')
            ->where('CG.id', $id)
            ->where('CH.show', '<>', 0)
            ->orderBy('CH.id', 'ASC')
            ->get();
    }

    public function getClientBranchIds($id)
    {
        return $this->connection->table('clientgroup as CG')
            ->select('CD.id as id', 'CD.branchname as name', 'CD.branchid as branch_id')
            ->leftJoin('clienthead as CH', 'CH.clientgroupid', '=', 'CG.id')
            ->leftJoin('clientdetails as CD', 'CD.clientid', '=', 'CH.id')
            ->where('CH.id', $id)
            ->where('CD.show', '<>', 0)
            ->orderBy('CD.branchid', 'ASC')
            ->get();
    }

    public function getClientTerminalIds($id)
    {
        return $this->connection->table('clientgroup as CG')
            ->select('CTD.id as id', 'CTD.referenceno as terminal_number', 'CTD.pos_type')
            ->leftJoin('clienthead as CH', 'CH.clientgroupid', '=', 'CG.id')
            ->leftJoin('clientdetails as CD', 'CD.clientid', '=', 'CH.id')
            ->leftJoin('clientterminaldetails as CTD', 'CTD.clientbranchid', '=', 'CD.id')
            ->where('CD.id', $id)
            ->where('CTD.show', '<>', 0)
            ->where('CTD.status', '<>', 0)
            ->where('CTD.referenceno', '>', 0)
            ->orderBy('CTD.referenceno', 'ASC')
            ->get();
    }

    public function getClientDetails($id)
    {
        return $this->connection->table('clientgroup as CG')
            ->select('CG.id as group_id', 'CG.name as group_name', 'CH.id as network_id', 'CH.name as network_name')
            ->leftJoin('clienthead as CH', 'CH.clientgroupid', '=', 'CG.id')
            ->where('CH.id', $id)
            ->get();
    }
}