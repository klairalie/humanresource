<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServiceController extends Controller
{
    

    public function showDetails($id)
{
    $item = \App\Models\ServiceRequestItem::with(['technician', 'service'])
        ->find($id);

    if (!$item) {
        return response()->json(['success' => false, 'message' => 'Item not found.']);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'service_type' => $item->service_type ?? $item->service->service_type ?? 'N/A',
            'technician_name' => $item->technician->full_name ?? 'Unassigned',
            'start_date' => $item->start_date ?? 'N/A',
            'quantity' => $item->quantity ?? 0,
            'status' => $item->status ?? 'Pending',
            'remarks' => $item->remarks ?? null,
        ]
    ]);
}

}
