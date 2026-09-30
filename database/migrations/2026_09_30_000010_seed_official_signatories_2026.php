<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $officials = [
            ['name' => 'H. Sugeng Tiyarto', 'title' => 'SH. MH.', 'position' => 'Ketua Takmir', 'sort_order' => 1, 'is_signatory' => true],
            ['name' => 'Awaludin Gymnastiar', 'title' => null, 'position' => 'Sekretaris', 'sort_order' => 2, 'is_signatory' => true],
            ['name' => 'Drs. H. Bambang Sutiyono', 'title' => null, 'position' => 'Ketua RW IX / Mengetahui', 'sort_order' => 3, 'is_signatory' => true],
        ];

        foreach ($officials as $official) {
            DB::table('officials')->updateOrInsert(
                ['name' => $official['name'], 'position' => $official['position']],
                array_merge($official, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    public function down(): void
    {
        DB::table('officials')->whereIn('name', [
            'H. Sugeng Tiyarto',
            'Awaludin Gymnastiar',
            'Drs. H. Bambang Sutiyono',
        ])->delete();
    }
};
