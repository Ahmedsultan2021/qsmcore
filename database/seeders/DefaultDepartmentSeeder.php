<?php

namespace Database\Seeders;

use App\Support\DefaultDepartmentCatalog;
use Illuminate\Database\Seeder;

class DefaultDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        DefaultDepartmentCatalog::seedMissingSectors();
    }
}
