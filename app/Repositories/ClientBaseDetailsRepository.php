<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\ClientBaseDetailsRepositoryInterface;

class ClientBaseDetailsRepository implements ClientBaseDetailsRepositoryInterface
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

    public function getTerminalDetails($clientGroupId, $softwareId)
    {
       $query = $this->connection->table('clientgroup as CG')
        ->select(
            'CTD.id as terminalId',
            'CG.id as clientGroupId',
            'CG.name as clientGroupName',
            'CH.id as clientNetworkId',
            'CH.name as clientNetworkName',
            'CD.branchId',
            'CD.branchName',
            'CTD.referenceno as terminalNo'
        )
        ->leftJoin('clienthead as CH', 'CH.clientgroupid', '=', 'CG.id')
        ->leftJoin('clientdetails as CD', 'CD.clientid', '=', 'CH.id')
        ->leftJoin('clientterminaldetails as CTD', 'CTD.clientbranchid', '=', 'CD.id')
        ->where('CG.id', $clientGroupId)
        ->where('CD.branchname', '!=', '')
        ->where('CTD.referenceno', '<>', 0)
        ->where('CTD.show', '<>', 0)
        ->where('CTD.status', '<>', 0)
        ->orderBy('CG.name', 'ASC');

        if ($softwareId == 1) {
            $query->where('CTD.pos_type', '<>', 10);
        } else {
            $query->where('CTD.pos_type', '=', 10);
        }
        
        return $query->get();
    }

    public function getClientTerminalIdByUuid(string $uuid): ?object
    {
        return $this->connection->table('clientgroup as CG')
            ->leftJoin('clienthead as CH', 'CH.clientgroupid', '=', 'CG.id')
            ->leftJoin('clientdetails as CD', 'CD.clientid', '=', 'CH.id')
            ->leftJoin('clientterminaldetails as CTD', 'CTD.clientbranchid', '=', 'CD.id')
            ->where('CTD.uuid', $uuid)
            ->orderBy('CTD.referenceno', 'ASC')
            ->select('CTD.id', 'CTD.pos_type', 'CG.id as clientGroupId', 'CG.name as clientGroupName', 'CH.id as clientNetworkId', 
               'CH.name as clientNetworkName', 'CD.branchId', 'CD.branchName', 'CTD.referenceno as terminalNo',)
            ->first();
    }
}