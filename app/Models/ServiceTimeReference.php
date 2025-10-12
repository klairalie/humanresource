<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTimeReference extends Model
{
    use HasFactory;

    protected $table = 'service_time_reference';

    protected $fillable = [
        'service_type',
        'aircon_type_id',
        'unit_type',
        'quantity',
        'avg_minutes',
        'samples_count',
    ];

    public static function recordSample(string $serviceType, ?int $airconTypeId, ?string $unitType, int $quantity, int $minutes): self
    {
        $quantity = max(1, $quantity);
        $minutes = max(1, $minutes);

        $row = static::firstOrNew([
            'service_type'   => $serviceType,
            'aircon_type_id' => $airconTypeId,
            'unit_type'      => $unitType,
            'quantity'       => $quantity,
        ]);

        if (!$row->exists) {
            $row->avg_minutes = $minutes;
            $row->samples_count = 1;
            $row->save();
            return $row;
        }

        // Running average update
        $n = (int)($row->samples_count ?? 0);
        $avg = (int)($row->avg_minutes ?? 0);
        $newAvg = (int) round((($avg * $n) + $minutes) / max(1, $n + 1));

        $row->avg_minutes = $newAvg;
        $row->samples_count = $n + 1;
        $row->save();

        return $row;
    }

    /**
     * Build a key=>minutes map: "service_type|aircon_type_id|quantity" => avg_minutes
     */
    public static function buildDurationReferenceMap(): array
    {
        $out = [];
        /** @var self $r */
        foreach (static::all(['service_type','aircon_type_id','unit_type','quantity','avg_minutes']) as $r) {
            $key = $r->service_type . '|' . (string)($r->aircon_type_id ?? '') . '|' . (string)($r->quantity ?? 1);
            $out[$key] = (int) $r->avg_minutes;
        }
        return $out;
    }
}
