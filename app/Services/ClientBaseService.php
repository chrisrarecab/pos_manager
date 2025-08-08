<?php

namespace App\Services;

use App\DataTransferObjects\ServiceResponse;
use App\Repositories\Interfaces\ClientBaseRepositoryInterface;

class ClientBaseService
{
    public function __construct(
        protected ClientBaseRepositoryInterface $clientBaseRepo
    ) {}

    public function getClientTerminalDetails(): ServiceResponse
    {
        try {
            $clientGroupId = session('clientGroupId');
            if (!$clientGroupId) {
                 return ServiceResponse::failure('Missing client group ID from session');
            }
            
            $result = $this->clientBaseRepo->getTerminalDetails($clientGroupId);
            return ServiceResponse::success('Client terminal details retrieved successfully', $result);
        } catch (\Throwable $e) {
            return ServiceResponse::failure('Unexpected error occurred', $e->getMessage());
        }
    }

    public function getCoreTerminalId($clientGroupId, $clientNetworkId, $clientBranchId, $terminalNo, $posType): ServiceResponse
    {
        try {
            $terminal = $this->clientBaseRepo->getCoreTerminalId(
                $clientGroupId,
                $clientNetworkId,
                $clientBranchId,
                $terminalNo,
                $posType
            );

            if (!$terminal) {
                return ServiceResponse::failure('Terminal not found');
            }

            return ServiceResponse::success('Terminal ID retrieved successfully', $terminal);
        } catch (\Throwable $e) {
            return ServiceResponse::failure('Unexpected error occurred', $e->getMessage());
        }
    }
}
