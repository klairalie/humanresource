<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EvaluateservicesController extends Controller
{
    public function showEvaluateServices()
    {
        // Cleaning Services
        $cleaningItems = ServiceRequestItem::with(['service', 'leadTechnicians', 'assistantTechnicians'])
            ->where('service_type', 'Cleaning')
            ->orderBy('start_date', 'desc')
            ->paginate(10, ['*'], 'cleaning_page');

        // Repair Services
        $repairItems = ServiceRequestItem::with(['service', 'leadTechnicians', 'assistantTechnicians'])
            ->where('service_type', 'Repair')
            ->orderBy('start_date', 'desc')
            ->paginate(10, ['*'], 'repair_page');

        // Installation Services
        $installmentItems = ServiceRequestItem::with(['service', 'leadTechnicians', 'assistantTechnicians'])
            ->whereIn('service_type', ['Buy and Install', 'Buy Only', 'Install Only'])
            ->orderBy('start_date', 'desc')
            ->paginate(10, ['*'], 'installation_page');

        // Maintenance Services
        $maintenanceItems = ServiceRequestItem::with(['service', 'leadTechnicians', 'assistantTechnicians'])
            ->where('service_type', 'Maintenance')
            ->orderBy('start_date', 'desc')
            ->paginate(10, ['*'], 'maintenance_page');

        return view('HR.evaluateservice', compact(
            'cleaningItems',
            'repairItems',
            'installmentItems',
            'maintenanceItems'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $service = ServiceRequestItem::findOrFail($id);
            $oldStatus = $service->status;
            $newStatus = $request->input('status');

            $service->status = $newStatus;
            $service->save();

            // 🔹 If rescheduled, also update technician assignments
            if ($newStatus === 'Rescheduled') {
                DB::table('technician_assignments')
                    ->where('item_id', $service->item_id)
                    ->update(['status' => 'Rescheduled']);
            }

            // ✅ Prepare log data
            $userEmail = session('user_email') ?? 'HR@gmail.com';
            $serviceType = $service->service_type ?? 'Unknown Service';
            $ipAddress = request()->ip();

            $description = "ServiceRequestItem #{$service->item_id} ({$serviceType}) status changed from '{$oldStatus}' to '{$newStatus}'.";

            // ✅ Insert into act_logs (local HR log)
            DB::table('act_logs')->insert([
                'action_type' => 'Service Status Updated',
                'email' => $userEmail,
                'description' => $description,
                'action_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ✅ Insert into centralized admin_activity_logs
            DB::connection('capstone_central')->table('admin_activity_logs')->insert([
                'actor_email' => $userEmail,
                'target_email' => null,
                'module' => 'HR',
                'action' => 'Service Status Updated',
                'changes' => json_encode([
                    'service_request_item_id' => $service->item_id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ]),
                'ip_address' => $ipAddress,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Status updated and logged successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getServiceDetails($id)
    {
        try {
            $item = ServiceRequestItem::with(['service', 'leadTechnicians', 'assistantTechnicians'])->findOrFail($id);

            $leadNames = $item->leadTechnicians->map(fn($t) => "{$t->first_name} {$t->last_name}")->implode(', ');
            $assistantNames = $item->assistantTechnicians->map(fn($t) => "{$t->first_name} {$t->last_name}")->implode(', ');

            return response()->json([
                'success' => true,
                'data' => [
                    'service_type' => $item->service_type ?? $item->service->service_type ?? 'N/A',
                    'lead_technician' => $leadNames ?: 'Unassigned',
                    'assistant_technician' => $assistantNames ?: 'Unassigned',
                    'start_date' => $item->start_date,
                    'quantity' => $item->quantity,
                    'status' => $item->status,
                    'remarks' => $item->remarks,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load service details: ' . $e->getMessage(),
            ], 500);
        }
    }
}
