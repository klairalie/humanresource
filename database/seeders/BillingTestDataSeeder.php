<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestItem;
use App\Models\Service;
use App\Models\Billing;
use App\Models\AirconType;
use App\Models\Services;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BillingTestDataSeeder extends Seeder
{
    public function run()
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            // Create test customers with addresses
            $customer1 = Customer::create([
                'full_name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'business_name' => 'Doe Enterprises',
                'contact_info' => '+639123456789'
            ]);

            $address1 = CustomerAddress::create([
                'customer_id' => $customer1->customer_id,
                'label' => 'Home',
                'street_address' => '123 Main Street',
                'barangay' => 'Ermita',
                'city' => 'Manila',
                'province' => 'NCR',
                'zip_code' => '1000',
                'is_default' => true
            ]);

            $customer2 = Customer::create([
                'full_name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'business_name' => 'Smith & Co.',
                'contact_info' => '+639987654321'
            ]);

            $address2 = CustomerAddress::create([
                'customer_id' => $customer2->customer_id,
                'label' => 'Office',
                'street_address' => '456 Business Ave',
                'barangay' => 'San Antonio',
                'city' => 'Makati',
                'province' => 'NCR',
                'zip_code' => '1200',
                'is_default' => true
            ]);

            // Create aircon types
            $airconTypes = [
                [
                    'name' => 'Window Type', 
                    'description' => 'Standard window air conditioning unit',
                    'category' => 'Window',
                    'base_price' => 15000.00,
                    'status' => 'active'
                ],
                [
                    'name' => 'Split Type', 
                    'description' => 'Wall-mounted split air conditioning unit',
                    'category' => 'Split',
                    'base_price' => 25000.00,
                    'status' => 'active'
                ],
                [
                    'name' => 'Inverter', 
                    'description' => 'Energy efficient inverter air conditioning unit',
                    'category' => 'Inverter',
                    'base_price' => 30000.00,
                    'status' => 'active'
                ],
                [
                    'name' => 'Central AC', 
                    'description' => 'Central air conditioning system',
                    'category' => 'Central',
                    'base_price' => 50000.00,
                    'status' => 'active'
                ],
                [
                    'name' => 'Portable', 
                    'description' => 'Portable air conditioning unit',
                    'category' => 'Portable',
                    'base_price' => 12000.00,
                    'status' => 'active'
                ]
            ];

            foreach ($airconTypes as $type) {
                AirconType::firstOrCreate(
                    ['name' => $type['name']],
                    $type
                );
            }

            // Create services
            $serviceTypes = [
                'Installation',
                'Cleaning',
                'Repair',
                'Maintenance',
                'Freon Refill'
            ];

            $services = [];
            foreach ($serviceTypes as $serviceType) {
                $service = Services::firstOrCreate(
                    ['service_type' => $serviceType],
                    [
                        'service_type' => $serviceType,
                        'status' => 'active'
                    ]
                );
                $services[] = $service;
            }

            // Get all services and aircon types
            $allServices = Services::all();
            $allAirconTypes = AirconType::all();

            // Create completed service requests with items
            $serviceRequests = [];
            $customers = [$customer1, $customer2];
            $addresses = [$address1, $address2];
            
            // Get the highest existing service_request_id or start from 10000
            $lastServiceRequest = ServiceRequest::orderBy('service_request_id', 'desc')->first();
            $serviceRequestId = $lastServiceRequest ? $lastServiceRequest->service_request_id + 1 : 10000;
            
            // Create 3 completed service requests
            for ($i = 0; $i < 3; $i++) {
                // Increment the ID for each request
                $serviceRequestId++;
                $customer = $customers[array_rand($customers)];
                $address = $addresses[array_rand($addresses)];
                
                $serviceRequest = ServiceRequest::create([
                    'service_request_id' => $serviceRequestId,
                    'customer_id' => $customer->customer_id,
                    'service_date' => now()->subDays(rand(1, 30)),
                    'start_date' => now()->subDays(rand(1, 30)),
                    'end_date' => now()->subDays(rand(0, 29)),
                    'start_time' => '09:00:00',
                    'end_time' => '12:00:00',
                    'type_of_payment' => ['Cash', 'Credit Card', 'Bank Transfer'][array_rand([0, 1, 2])],
                    'order_status' => 'Completed',
                    'payment_status' => ['Paid', 'Unpaid', 'Partially Paid'][array_rand([0, 1, 2])],
                    'accomplishment_date' => now()->subDays(rand(0, 29)),
                    'remarks' => 'Service request #' . ($i + 1),
                    'service_request_number' => 'SRN' . now()->format('Ymd') . str_pad($serviceRequestId, 5, '0', STR_PAD_LEFT) . time()
                ]);
                
                $serviceRequests[] = $serviceRequest;

                // Add items to each service request
                $itemCount = rand(1, 3); // 1-3 items per service request
                
                for ($j = 0; $j < $itemCount; $j++) {
                    $service = $allServices->random();
                    $airconType = $allAirconTypes->random();
                    $quantity = rand(1, 3);
                    
                    // Set a fixed price based on service type
                    $servicePrices = [
                        'Installation' => 2000.00,
                        'Cleaning' => 1500.00,
                        'Repair' => 2500.00,
                        'Maintenance' => 1200.00,
                        'Freon Refill' => 1800.00
                    ];
                    
                    $unitPrice = $servicePrices[$service->service_type] ?? 1000.00;
                    $discount = rand(0, 100) / 10; // Random discount between 0 and 10%
                    $tax = $unitPrice * 0.12; // 12% tax
                    $lineTotal = ($unitPrice * $quantity) - $discount + $tax;

                    ServiceRequestItem::create([
                        'service_request_id' => $serviceRequest->service_request_id,
                        'services_id' => $service->services_id,
                        'aircon_type_id' => $airconType->aircon_type_id,
                        'service_type' => $service->service_type,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'discount' => $discount,
                        'tax' => $tax,
                        'line_total' => $lineTotal,
                        'start_date' => $serviceRequest->start_date,
                        'end_date' => $serviceRequest->end_date,
                        'start_time' => $serviceRequest->start_time,
                        'end_time' => $serviceRequest->end_time,
                        'status' => 'Completed',
                        'billed' => false,
                        'service_notes' => 'Service item ' . ($j + 1) . ' for ' . $service->service_type
                    ]);
                }

                // Calculate total amount for billing
                $totalAmount = $serviceRequest->items()->sum('line_total');
                $totalDiscount = $serviceRequest->items()->sum('discount');
                $totalTax = $totalAmount * 0.12;

                // Create billing for this service request (every other one)
                if ($i % 2 === 0) {
                    Billing::create([
                        'service_request_id' => $serviceRequest->service_request_id,
                        'customer_id' => $serviceRequest->customer_id,
                        'billing_date' => now()->subDays(rand(1, 10)),
                        'due_date' => now()->addDays(30),
                        'total_amount' => $totalAmount,
                        'discount' => $totalDiscount,
                        'tax' => $totalTax,
                        'status' => 'Billed' // Set a default status
                    ]);

                    // Mark items as billed
                    $serviceRequest->items()->update(['billed' => true]);
                }
            }

            // Commit the transaction
            DB::commit();

            $this->command->info('Test data for billing system has been created successfully!');
            $this->command->info('Total customers created: ' . Customer::count());
            $this->command->info('Total service requests created: ' . ServiceRequest::count());
            $this->command->info('Total service request items created: ' . ServiceRequestItem::count());
            $this->command->info('Total billings created: ' . Billing::count());

        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            $this->command->error('Error creating test data: ' . $e->getMessage());
            $this->command->error($e->getTraceAsString());
        }
    }
}
