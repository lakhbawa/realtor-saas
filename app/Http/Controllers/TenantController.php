<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Tenant;

abstract class TenantController extends Controller
{
    protected function tenant(): Tenant
    {
        return app('tenant') ?? abort(404, 'Site not found');
    }

    protected function site(): Site
    {
        return app('site') ?? abort(404, 'Site not found');
    }

    protected function template(): string
    {
        return $this->site()->template?->slug ?? 'modern';
    }

    protected function view(string $view, array $data = [])
    {
        return view("templates.{$this->template()}.{$view}", array_merge([
            'tenant' => $this->tenant(),
            'site' => $this->site(),
            'template' => $this->template(),
        ], $data));
    }
}
