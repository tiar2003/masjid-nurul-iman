<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $periodId = DB::table('management_periods')->insertGetId([
            'name' => 'Pengurus Masjid Nurul Iman Tahun 2026',
            'starts_at' => '2026-01-03',
            'ends_at' => null,
            'status' => 'Aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $members = [
            ['Pelindung / Ketua RW IX', 'Drs. H. Bambang Sutiyono', null],
            ['Ketua Takmir', 'H. Sugeng Tiyarto', 'SH. MH.'],
            ['Wakil Ketua Takmir', 'Adi Riyatno', 'SE'],
            ['Sekretaris', 'Awaludin Gymnastiar', null],
            ['Bendahara', 'Supi', 'SH'],
            ['Sie Keagamaan', 'H. Kasmadi', null],
            ['Sie Keagamaan', 'Fadli Azmi Maarif', null],
            ['Sie Keagamaan', 'Nur Fahni', null],
            ['Sie Keagamaan', 'Ahmad Ayyash Albana', null],
            ['Sie Keremajaan', 'Lutfi Bagus Hatmawan', 'SE'],
            ['Sie Keremajaan', 'Prasetyo Ari Wibowo', null],
            ['Sie Humas', 'Yanuar Achmed Sideq', null],
            ['Sie Humas', 'Isandika Navindra Legawa', 'ST'],
            ['Sie Sarana dan Prasarana', 'Nurcahyono Ednoputranto', null],
            ['Sie Sarana dan Prasarana', 'Turbudhi Zaenul Arifin', 'ST'],
            ['Sie Sarana dan Prasarana', 'Suhartono', null],
            ['Sie Sarana dan Prasarana', 'Alief Junaedi', null],
            ['Sie Umum dan Perawatan', 'M. Wijang Suprayogi', null],
            ['Sie Umum dan Perawatan', 'Tukijo', null],
            ['Sie Umum dan Perawatan', 'Yartadin', null],
        ];

        $positionIds = [];
        foreach (array_values(array_unique(array_column($members, 0))) as $sortOrder => $position) {
            $positionIds[$position] = DB::table('positions')->insertGetId([
                'name' => $position,
                'sort_order' => $sortOrder,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($members as $sortOrder => [$position, $name, $title]) {
            DB::table('management_members')->insert([
                'period_id' => $periodId,
                'position_id' => $positionIds[$position],
                'name' => $name,
                'title' => $title,
                'sort_order' => $sortOrder,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        $period = DB::table('management_periods')->where('name', 'Pengurus Masjid Nurul Iman Tahun 2026')->first();
        if ($period) {
            DB::table('management_members')->where('period_id', $period->id)->delete();
            DB::table('management_periods')->where('id', $period->id)->delete();
        }
    }
};
