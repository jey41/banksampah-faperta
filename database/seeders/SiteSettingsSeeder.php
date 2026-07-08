<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'hero_image' => '/images/hero-bfsp.jpeg',
            'hero_title_1' => 'Kelola Sampah',
            'hero_title_2' => 'Jadi Berkah',
            'hero_subtitle' => 'Ubah kebiasaan membuang menjadi menabung. Bergabunglah dengan kami untuk melestarikan lingkungan kampus sambil mengumpulkan tabungan yang bermanfaat bagi masa depan Anda.',
            'workflow_title' => 'Alur Kerja Kami',
            'workflow_description' => 'Tiga langkah mudah untuk mulai berkontribusi pada lingkungan dan ekonomi Anda.',
            'workflow_step1_title' => 'Daftar Akun',
            'workflow_step1_desc' => 'Buat akun dengan mudah melalui platform kami untuk mulai melacak kontribusi tabungan sampah Anda.',
            'workflow_step2_title' => 'Setor Sampah',
            'workflow_step2_desc' => 'Bawa sampah terpilah Anda ke depo penampungan. Petugas akan menimbang dan menginput nilai rupiahnya secara riil.',
            'workflow_step3_title' => 'Tarik Saldo',
            'workflow_step3_desc' => 'Nikmati hasil kerja keras Anda. Tarik saldo tabungan langsung ke rekening bank atau e-wallet kapan saja Anda inginkan.',
            'schedule_description' => 'Kunjungi depo kami pada jam operasional untuk menyetorkan sampah terpilah Anda. Tim petugas kami siap membantu proses penimbangan dan pencatatan saldo secara cepat, akurat, dan transparan.',
            'schedule_days' => 'Senin - Sabtu',
            'schedule_hours' => '08:00 - 16:00 WITA',
            'schedule_note' => 'Tutup pada hari Minggu dan Hari Libur Nasional.',
            'schedule_image' => '/images/bsfpxwcid.png',
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $partners = [
            [
                'name' => 'Nutrifood',
                'logo_path' => '/images/logo/logo-nutrifood.png',
                'order' => 1,
            ],
            [
                'name' => 'Pegadaian',
                'logo_path' => '/images/logo/logo-pegadaian.png',
                'order' => 2,
            ],
            [
                'name' => 'Selalu Teh',
                'logo_path' => '/images/logo/logo-selaluteh.png',
                'order' => 3,
            ],
        ];

        foreach ($partners as $partner) {
            Partner::updateOrCreate(
                ['name' => $partner['name']],
                $partner
            );
        }
    }
}
