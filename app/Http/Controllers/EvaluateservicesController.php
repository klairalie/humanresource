<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequestItem;
use Illuminate\Http\Request;

class EvaluateservicesController extends Controller
{
 public function showEvaluateServices()
{
    // Cleaning Services (10 per page)
    $cleaningItems = ServiceRequestItem::with(['service', 'technician'])
        ->where('service_type', 'Cleaning')
        ->orderBy('start_date', 'desc')
        ->paginate(10, ['*'], 'cleaning_page');

    // Repair Services (10 per page)
    $repairItems = ServiceRequestItem::with(['service', 'technician'])
        ->where('service_type', 'Repair')
        ->orderBy('start_date', 'desc')
        ->paginate(10, ['*'], 'repair_page');

    // Installation Services (10 per page)
    $installmentItems = ServiceRequestItem::with(['service', 'technician'])
        ->where('service_type', 'Installment')
        ->orderBy('start_date', 'desc')
        ->paginate(10, ['*'], 'installment_page');

    return view('HR.evaluateservice', compact(
        'cleaningItems',
        'repairItems',
        'installmentItems'
    ));
}

    // ✅ AJAX update status
    public function updateStatus(Request $request, $id)
    {
        try {
            $service = ServiceRequestItem::findOrFail($id);
            $service->status = $request->input('status');
            $service->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ✅ Fetch details for modal
    public function getServiceDetails($id)
    {
        try {
            $item = ServiceRequestItem::with(['service', 'technician'])
                ->findOrFail($id);

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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch details: ' . $e->getMessage(),
            ], 500);
        }
    }
}
