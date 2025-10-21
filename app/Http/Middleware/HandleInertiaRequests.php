<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
               'user' => $request->user()
                ? [
                    'userId'        => $request->user()->id,
                    'clientGroupId' => $request->user()->client_group_id,
                    'clientNetworkId' => $request->user()->client_network_id,
                    'softwareId'    => $request->user()->software_id,
                    'domain'        => $request->user()->domain_name,
                    'fullName'      => $request->user()->full_name,
                    'username'      => $request->user()->username,
                    'permissions'   => $request->user()
                        ->permissions()
                        ->pluck('code')
                        ->filter()
                        ->toArray(),
                ]
                : null,
            ],
        ]);
    }

}
