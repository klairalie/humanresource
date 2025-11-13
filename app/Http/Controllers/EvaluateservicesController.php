<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // ✅ For DB operations

class EvaluateservicesController extends Controller
{
    public function showEvaluateServices()
    {
        // Cleaning Services (10 per page)
        $cleaningItems = ServiceRequestItem::with(['service', 'leadTechnicians', 'assistantTechnicians'])
            ->where('service_type', 'Cleaning')
            ->orderBy('start_date', 'desc')
            ->paginate(10, ['*'], 'cleaning_page');

        // Repair Services (10 per page)
        $repairItems = ServiceRequestItem::with(['service', 'leadTechnicians', 'assistantTechnicians'])
            ->where('service_type', 'Repair')
            ->orderBy('start_date', 'desc')
            ->paginate(10, ['*'], 'repair_page');

        // Installation Services (10 per page)
     $installmentItems = ServiceRequestItem::with(['service', 'leadTechnicians', 'assistantTechnicians'])
    ->whereIn('service_type', ['Buy and Install', 'Buy Only', 'Install Only'])
    ->orderBy('start_date', 'desc')
    ->paginate(10, ['*'], 'installment_page');

        return view('HR.evaluateservice', compact(
            'cleaningItems',
            'repairItems',
            'installmentItems'
        ));
    }

    // ✅ AJAX: Update service status
    public function updateStatus(Request $request, $id)
    {
        try {
            $service = ServiceRequestItem::findOrFail($id);
            $newStatus = $request->input('status');

            // ✅ Update the service_request_items status
            $service->status = $newStatus;
            $service->save();

            // ✅ If Rescheduled, also update technician_assignments table
            if ($newStatus === 'Rescheduled') {
                DB::table('technician_assignments')
                    ->where('item_id', $service->item_id) // use correct FK column name
                    ->update(['status' => 'Rescheduled']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ✅ Fetch service details for modal
    public function getServiceDetails($id)
    {
        try {
            $item = ServiceRequestItem::with(['service', 'leadTechnicians', 'assistantTechnicians'])
                ->findOrFail($id);

            $leadNames = $item->leadTechnicians->map(fn($t) => "{$t->first_name} {$t->last_name}")->implode(', ');
            $assistantNames = $item->assistantTechnicians->map(fn($t) => "{$t->first_name} {$t->last_name}")->implode(', ');

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
