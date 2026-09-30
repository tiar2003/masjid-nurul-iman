<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $templates = [
            ['QURBAN_HEWAN', 'Surat Hewan Qurban', 'Qurban', 'MASJID/QURBAN/PAKET SURAT QURBAN/SURAT HEWAN QURBAN.docx', ['issue_date', 'letter_number', 'owner_name', 'animal_type', 'participant_name', 'address', 'phone', 'signed_by']],
            ['QURBAN_CARPET', 'Permohonan Karpet Qurban', 'Qurban', 'MASJID/QURBAN/PAKET SURAT QURBAN/PERMOHONAN KARPET QURBAN.docx', ['issue_date', 'letter_number', 'recipient', 'subject', 'body', 'signed_by']],
            ['QURBAN_COORDINATION', 'Undangan Rapat Koordinasi Qurban', 'Qurban', 'MASJID/QURBAN/PAKET SURAT QURBAN/SURAT UNDANGAN RPT KOORDINASI.docx', ['issue_date', 'letter_number', 'recipient', 'meeting_date', 'time', 'venue', 'agenda', 'signed_by']],
            ['QURBAN_RECEIPT', 'Tanda Terima Qurban', 'Qurban', 'MASJID/QURBAN/PAKET SURAT QURBAN/TANDA TERIMA QURBAN.docx', ['date', 'giver', 'receiver', 'animal_type', 'quantity', 'signed_by']],
            ['RAMADAN_CHARITY_REPORT', 'Hasil Kotak Amal Shalat Tarawih', 'Kegiatan', 'MASJID/ROMADON/KULTUM & JADWAL/HASIL KOTAK AMAL  SHALAT TARAWIH.docx', ['year', 'entries', 'total']],
            ['PROPOSAL_COVER', 'Cover Proposal', 'Proposal', 'MASJID/Seketetariat/Proposal/Cover.docx', ['proposal_number', 'proposal_date', 'title', 'recipient']],
            ['PROPOSAL_FUNDING', 'Proposal Permohonan Dana', 'Proposal', 'MASJID/Seketetariat/Proposal/PROPOSAL PERMOHONAN DANA 2.docx', ['proposal_number', 'proposal_date', 'title', 'recipient', 'amount', 'content', 'signed_by']],
            ['PROPOSAL_RECOMMENDATION', 'Permohonan Rekomendasi Bantuan Dana', 'Proposal', 'MASJID/Seketetariat/Proposal/Permohonan Rekomendasi bantuan dana masjid nurul iman.docx', ['proposal_number', 'proposal_date', 'recipient', 'content', 'signed_by']],
            ['PROPOSAL_FORWARDING', 'Surat Pengantar Proposal', 'Proposal', 'MASJID/Seketetariat/Proposal/Surat Pengantar.docx', ['issue_date', 'letter_number', 'recipient', 'subject', 'attachment', 'signed_by']],
            ['MOSQUE_DECISION_REQUEST', 'Surat Permohonan Surat Keputusan', 'Sekretariat', 'MASJID/Seketetariat/Surat Permohonan Surat Keputusan.docx', ['issue_date', 'letter_number', 'recipient', 'subject', 'body', 'signed_by']],
        ];

        foreach ($templates as [$key, $name, $category, $sourcePath, $fields]) {
            DB::table('letter_templates')->insertOrIgnore([
                'template_key' => $key,
                'name' => $name,
                'category' => $category,
                'version' => 1,
                'source_path' => $sourcePath,
                'page_width_mm' => 210,
                'page_height_mm' => 297,
                'orientation' => 'portrait',
                'margins_mm' => json_encode(['top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20]),
                'fields' => json_encode($fields),
                'numbering_key' => null,
                'numbering_pattern' => null,
                'is_verified' => false,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('letter_templates')->whereIn('template_key', [
            'QURBAN_HEWAN', 'QURBAN_CARPET', 'QURBAN_COORDINATION', 'QURBAN_RECEIPT', 'RAMADAN_CHARITY_REPORT',
            'PROPOSAL_COVER', 'PROPOSAL_FUNDING', 'PROPOSAL_RECOMMENDATION', 'PROPOSAL_FORWARDING', 'MOSQUE_DECISION_REQUEST',
        ])->delete();
    }
};
