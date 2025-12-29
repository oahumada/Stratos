<?php

namespace App\Repository;

use Illuminate\Http\Request;

/**
 * Interface RepositoryInterface
 *
 * This interface defines the methods that should be implemented by a repository class.
 */
interface RepositoryInterface
{
    public function store(Request $request);
    public function update(Request $request);
    public function destroy($id);
    public function show(Request $request, $id);
    public function search(Request $request);
    public function searchWithPerson(Request $request);
}
