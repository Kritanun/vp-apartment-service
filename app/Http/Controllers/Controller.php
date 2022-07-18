<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Pagination\LengthAwarePaginator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function customs_paginate($request, $data)
    {
        // Get the current page from the url if it's not set default to 1
        empty($request->page) ? $page = 1 : $page = $request->page;
            
        // Number of items per page
        empty($request->perPage ) ? $perPage = 10 : $perPage = $request->perPage;
        
        $offSet = ($page * $perPage) - $perPage; // Start displaying items from this number
        

        // Get only the items you need using array_slice (only get 10 items since that's what you need)
        $itemsForCurrentPage = array_slice($data, $offSet, $perPage, false);
        
        // Return the paginator with only 10 items but with the count of all items and set the it on the correct page
        $resultPaginate = new LengthAwarePaginator($itemsForCurrentPage, count($data), $perPage, $page);

        return $resultPaginate;
    }
}
