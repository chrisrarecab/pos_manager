<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\ClientBaseRepositoryInterface;

class ClientBaseRepository implements ClientBaseRepositoryInterface
{
    public function __construct()
    {
        $this->connection = DB::connection('mysql5'); 
    }

    public function getTerminalDetails($id)
    {
        return $this->connection->table('clientgroup as CG')
            ->select('CTD.id as clientTerminalId', 'CG.id as clientGroupId', 'CG.name as clientGroupName', 'CH.id as clientNetworkId', 
               'CH.name as clientNetworkName', 'CD.branchId', 'CD.branchName', 'CTD.referenceno as terminalNo')
            ->leftJoin('clienthead as CH', 'CH.clientgroupid', '=', 'CG.id')
            ->leftJoin('clientdetails as CD', 'CD.clientid', '=', 'CH.id')
            ->leftJoin('clientterminaldetails as CTD', 'CTD.clientbranchid', '=', 'CD.id')
            ->where('CG.id', $id)
            ->where('CD.branchname', '!=', '')
            ->where('CTD.referenceno', '<>', 0)
            ->where('CTD.show', '<>', 0)
            ->where('CTD.status', '<>', 0)
            ->orderBy('CG.name', 'ASC')
            ->get();
    }

    public function getCoreTerminalId(
        $clientGroupId, 
        $clientNetworkId, 
        $clientBranchId, 
        $terminalNo,
        $posType
    )
    {
        return $this->connection->table('clientgroup as CG')
            ->leftJoin('clienthead as CH', 'CH.clientgroupid', '=', 'CG.id')
            ->leftJoin('clientdetails as CD', 'CD.clientid', '=', 'CH.id')
            ->leftJoin('clientterminaldetails as CTD', 'CTD.clientbranchid', '=', 'CD.id')
            ->where('CG.id', $clientGroupId)
            ->where('CH.id', $clientNetworkId)
            ->where('CD.branchid', $clientBranchId)
            ->where('CTD.pos_type', $posType)
            ->where('CTD.referenceno', $terminalNo)
            ->orderBy('CG.name', 'ASC')
            ->select('CTD.id', 'CTD.pos_type', 'CTD.referenceno as terminal_no')
            ->first();
    }
}
