<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\DataTransferObjects\ServiceResponse;

class CirmsApiService
{
    public function getUrl(string $target, string $domain = 'dev.cirms.ph'): ?string
    {
        switch ($target) {
            case 'client-list':
                return 'https://api.cirms.ph/api/v1/clients/simple';
            case 'branch-list':
                return "https://{$domain}/api/client-base/branches";
            case 'terminal-list':
                return "https://{$domain}/api/client-base/terminals/branch/{id}";
            default:
                return null; 
        }
    }

    public function getClientIdFromDomain(string $domain): ?int
    {
        $url = $this->getUrl('client-list', $domain);
        $response = Http::get($url); 
        $data = $response->json();
        if (!isset($data['isSuccessful']) || !$data['isSuccessful']) {
            return null;
        }

        foreach ($data['values'] as $client) {
            if ($client['client_domain'] === $domain) {
                return $client['client_id'];
            }
        }

        return null; 
    }

    public function getCirmsTerminalDetails()
    {
        $domain = session('domain');
        // $domain = 10791;
        // $domain = 'kingplaza.cirms.ph';

        if (!$domain) {
            return ServiceResponse::failure('Domain not found in session.');
        }

        $clientId = $this->getClientIdFromDomain($domain);
        if ($clientId === null) {
            return ServiceResponse::failure('Client ID not found for domain.');
        }

        $branchList = $this->getUrl('branch-list', $domain);
        $branchListResponse = Http::get($branchList);
        $branchListRes = $branchListResponse->json();

        $results = [];
        if (isset($branchListRes['values']['data'])) {
            foreach ($branchListRes['values']['data'] as $branch) {
                $branchName = trim($branch['name'] ?? '');
                $branchId = $branch['id'] ?? null;

                if (isset($branch['details']['data'])) {
                    foreach ($branch['details']['data'] as $location) {
                        $locationId = $location['id'] ?? null;
                        $locationName = trim($location['name'] ?? '');

                    }
                }

                $terminalList = str_replace('{id}', $branchId, $this->getUrl('terminal-list', $domain));
                $terminalListResponse= Http::get($terminalList);
                $terminalListRes = $terminalListResponse->json();

                if (isset($terminalListRes['values']['terminals'])) {
                    foreach ($terminalListRes['values']['terminals'] as $terminal) {
                        $terminalNo = $terminal['number'] ?? '';

                        $result[] = [
                            'clientTerminalId' => $clientId .'-'. $branchId .'-'. $locationId .'-'. $terminalNo,
                            'clientNetworkName' => $domain,
                            'branchId' => $branchId,
                            'branchName' => $branchName,
                            'location' => $locationId,
                            'terminalNo' => $terminalNo,
                        ];
                    }
                }
            }
        }

        return ServiceResponse::success('success', $result);
    }
}

