<?php

namespace App\Services;

use App\DataTransferObjects\ServiceResponse;
use App\Repositories\Interfaces\ClientBaseDetailsRepositoryInterface;

class ClientBaseService
{
    public function __construct(
        protected ClientBaseDetailsRepositoryInterface $clientBaseRepo
    ) {}

    public function getClientTerminalDetails($clientGroupId, $clientNetworkId, $softwareId): ServiceResponse
    {
        try {
           
            if (!$clientGroupId) {
                 return ServiceResponse::failure('Missing client group ID from session');
            }
            
            $result = $this->clientBaseRepo->getTerminalDetails($clientGroupId, $clientNetworkId, $softwareId);
            return ServiceResponse::success('Client terminal details retrieved successfully', $result);
        } catch (\Throwable $e) {
            return ServiceResponse::failure('Unexpected error occurred', $e->getMessage());
        }
    }

    public function getClientTerminalIdByUuid($uuid) : ServiceResponse
    {
         try {
            $terminal = $this->clientBaseRepo->getClientTerminalIdByUuid($uuid);

            if (!$terminal) {
                return ServiceResponse::failure('Terminal not found');
            }

            return ServiceResponse::success('Terminal ID retrieved successfully', $terminal);
        } catch (\Throwable $e) {
            return ServiceResponse::failure('Unexpected error occurred', $e->getMessage());
        }
    }
}
