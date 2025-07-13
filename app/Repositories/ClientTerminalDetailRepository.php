<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;

class ClientTerminalDetailRepository
{
    public function __construct()
    {
        $this->connection = DB::connection('mysql5'); 
    }

    public function getClientTerminalDetails($id)
    {
        return $this->connection->table('clientgroup as CG')
            ->select('CTD.id as client_terminal_id', 'CG.id as client_group_id', 'CG.name as name', 'CH.id as client_network_id', 'CD.branchid', 'CD.branchname', 'CTD.referenceno as client_terminal_no')
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

    public function getClientTerminalId(
        $client_group_id, 
        $client_network_id, 
        $client_branch_id, 
        $terminal_no,
        $pos_type
    )
    {
        return $this->connection->table('clientgroup as CG')
            ->leftJoin('clienthead as CH', 'CH.clientgroupid', '=', 'CG.id')
            ->leftJoin('clientdetails as CD', 'CD.clientid', '=', 'CH.id')
            ->leftJoin('clientterminaldetails as CTD', 'CTD.clientbranchid', '=', 'CD.id')
            ->where('CG.id', $client_group_id)
            ->where('CH.id', $client_network_id)
            ->where('CD.branchid', $client_branch_id)
            ->where('CTD.pos_type', $pos_type)
            ->where('CTD.referenceno', $terminal_no)
            ->orderBy('CG.name', 'ASC')
            ->select('CTD.id', 'CTD.pos_type', 'CTD.referenceno as terminal_no')
            ->first();
    }
}
