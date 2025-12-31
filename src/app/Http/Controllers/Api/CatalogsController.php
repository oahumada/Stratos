<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repository\CatalogsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CatalogsController extends Controller
{
    public function getCatalogs(Request $request)
    {
        $requested = $request->input('endpoints', []);
        $repo = new CatalogsRepository();
        $catalogs = $repo->getCatalogs($requested);
        return response()->json($catalogs);
    } 
}
