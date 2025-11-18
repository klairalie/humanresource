<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServiceController extends Controller
{
    

    public function showDetails($id)
{
    $item = \App\Models\ServiceRequestItem::with([
        'service',
        'leadTechnicians',
        'assistantTechnicians',
        'serviceRequest.customer',
    ])->find($id);

    if (!$item) {
        return response()->json(['success' => false, 'message' => 'Item not found.']);
    }

    $leadNames = $item->leadTechnicians->map(fn($t) => ($t->first_name . ' ' . $t->last_name))->implode(', ');
    $assistantNames = $item->assistantTechnicians->map(fn($t) => ($t->first_name . ' ' . $t->last_name))->implode(', ');

    $customer = optional(optional($item->serviceRequest)->customer);
    $customerName = $customer?->full_name ?? 'N/A';
    $businessName = $customer?->business_name ?? 'N/A';
    $orderTotal = optional($item->serviceRequest)->order_total ?? 0;

    return response()->json([
        'success' => true,
        'data' => [
            'service_type' => $item->service_type ?? $item->service->service_type ?? 'N/A',
            'lead_technician' => $leadNames ?: 'Unassigned',
            'assistant_technician' => $assistantNames ?: 'Unassigned',
            'start_date' => $item->start_date ?? 'N/A',
            'quantity' => $item->quantity ?? 0,
            'status' => $item->status ?? 'Pending',
            'remarks' => $item->remarks ?? null,
            'customer' => $customerName,
            'business_name' => $businessName,
            'order_total' => $orderTotal,
        ]
    ]);
}

}
