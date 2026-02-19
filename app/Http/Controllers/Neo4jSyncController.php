<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Jobs\RunNeo4jSyncJob;

class Neo4jSyncController extends Controller
{
    public function sync(Request $request)
    {
        Gate::authorize('sync-neo4j');

        $via = $request->input('via', 'fastapi');

        // Dispatch job (will be handled by queue worker)
        dispatch(new RunNeo4jSyncJob($via));

        return response()->json(['status' => 'dispatched', 'via' => $via]);
    }
}
