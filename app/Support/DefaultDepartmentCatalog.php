<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Built-in default departments, keyed by sector name.
 * Sectors that already have defaults are left untouched so admin edits stay in place.
 */
class DefaultDepartmentCatalog
{
    /**
     * @return array<string, list<array{name: string, template_category: string, description: string}>>
     */
    public static function definitions(): array
    {
        return [
            'Airlines' => [
                ['name' => 'Flight Operations', 'template_category' => 'Aviation - Flight Ops', 'description' => 'Crew scheduling, flight dispatch, and airborne safety management'],
                ['name' => 'Ground Operations', 'template_category' => 'Aviation - Ground Safety', 'description' => 'Ramp services, turnaround coordination, and ground safety'],
                ['name' => 'OCC', 'template_category' => 'Aviation - OCC', 'description' => 'Operations Control Center — disruption management and crew coordination'],
                ['name' => 'Maintenance', 'template_category' => 'Aviation - Maintenance', 'description' => 'Line and base aircraft maintenance and MEL control'],
                ['name' => 'Training', 'template_category' => 'Aviation - Training', 'description' => 'Crew training, simulator sessions, and competency management'],
                ['name' => 'Safety', 'template_category' => 'Aviation - Safety', 'description' => 'Safety Management System, hazard reporting, and investigations'],
                ['name' => 'Quality', 'template_category' => 'Aviation - Quality', 'description' => 'Quality audits, NCR management, and compliance monitoring'],
            ],
            'MRO' => [
                ['name' => 'Safety Reports', 'template_category' => 'Aviation - MRO Safety', 'description' => 'MRO workplace safety, incident reporting, and risk assessment'],
                ['name' => 'Quality Reports', 'template_category' => 'Aviation - MRO Quality', 'description' => 'MRO audits, NCR/CAR/PAR, calibration, and supplier evaluation'],
                ['name' => 'Operational Reports', 'template_category' => 'Aviation - MRO', 'description' => 'Technical defects, MEL control, scheduled maintenance, and manpower utilization'],
            ],
            'Airport' => [
                ['name' => 'Safety Reports', 'template_category' => 'Aviation - Airport Safety', 'description' => 'Airside incidents, ground vehicle incidents, wildlife strikes, and runway hazards'],
                ['name' => 'Quality Reports', 'template_category' => 'Aviation - Airport Quality', 'description' => 'Internal audits, terminal inspections, compliance audits, NCR/CAR/PAR, and contractor evaluation'],
                ['name' => 'Operations Reports', 'template_category' => 'Aviation - Airport Operations', 'description' => 'Turnaround oversight, fuel handling, GSE checks, and lost & found audits'],
            ],
            'OGE Sector' => [
                ['name' => 'Safety', 'template_category' => 'OGE Safety', 'description' => 'Incident/accident, near miss, hazard, personal injury, spill, fire, equipment failure, process safety, unsafe act, risk assessment'],
                ['name' => 'Quality', 'template_category' => 'OGE Quality', 'description' => 'Internal audits, NCR, CAR, PAR, inspection, material defect, supplier, change management, calibration, quality improvement'],
            ],
            'Maritime' => [
                ['name' => 'Safety', 'template_category' => 'Logistics & Transportation - Safety', 'description' => 'Maritime incident, near miss, safety observations, man overboard, pollution/spill'],
                ['name' => 'Quality', 'template_category' => 'Logistics & Transportation - Quality', 'description' => 'NCR, audit checklists, and CAPA forms'],
            ],
            'Rail' => [
                ['name' => 'Safety', 'template_category' => 'Logistics & Transportation - Safety', 'description' => 'Track incidents, near miss, safety observations, level crossing incidents'],
                ['name' => 'Quality', 'template_category' => 'Logistics & Transportation - Quality', 'description' => 'NCR, audit checklists, and CAPA forms'],
            ],
            'Road Transport' => [
                ['name' => 'Safety', 'template_category' => 'Logistics & Transportation - Safety', 'description' => 'Road incidents, near miss, driver behavior, load/vehicle inspections'],
                ['name' => 'Quality', 'template_category' => 'Logistics & Transportation - Quality', 'description' => 'NCR, audit checklists, and CAPA forms'],
            ],
        ];
    }

    public static function seedMissingSectors(): void
    {
        $now = now();

        foreach (self::definitions() as $sectorName => $departments) {
            $sectorId = DB::table('sectors')->where('name', $sectorName)->value('id');
            if (! $sectorId) {
                continue;
            }

            $alreadyConfigured = DB::table('default_departments')
                ->where('sector_id', $sectorId)
                ->exists();

            if ($alreadyConfigured) {
                continue;
            }

            $rows = [];
            foreach ($departments as $index => $department) {
                $rows[] = [
                    'sector_id' => $sectorId,
                    'name' => $department['name'],
                    'description' => $department['description'],
                    'library_key' => self::resolveLibraryKey($department['template_category']),
                    'sort_order' => $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('default_departments')->insert($rows);
        }
    }

    /**
     * Match either the stored library key or the slug used when templates were migrated.
     */
    public static function resolveLibraryKey(string $templateCategory): string
    {
        $slug = Str::slug($templateCategory);
        $existing = DB::table('form_templates')
            ->whereIn('library_key', array_values(array_unique([$slug, $templateCategory])))
            ->value('library_key');

        return $existing ?: $slug;
    }
}
