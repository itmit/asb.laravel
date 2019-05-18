<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

class RepresentativeListController extends Controller
{
    /**
     * @return Factory|View
     */
    public function index()
    {
        return view('admin.representativeList', [
            'representatives' => Role::getUsersByRoleName('representative')
        ]);
    }
}
