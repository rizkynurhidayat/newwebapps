<?php

namespace Database\Seeders;

use App\Enums\ItemCondition;
use App\Enums\ItemStatus;
use App\Enums\LoanStatus;
use App\Enums\StockLogType;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemLoan;
use App\Models\ItemMutation;
use App\Models\ItemStockLog;
use App\Models\Location;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Pengguna (Users)
        $admin = User::firstOrCreate(
            ['email' => 'admin@company.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'phone' => '081234567890',
                'department' => 'IT & Manajemen Sistem',
                'is_active' => true,
            ]
        );

        $staff = User::firstOrCreate(
            ['email' => 'staff@company.com'],
            [
                'name' => 'Ahmad Fauzi (Logistik)',
                'password' => Hash::make('password'),
                'role' => UserRole::Staff,
                'phone' => '081298765432',
                'department' => 'Gudang & Inventaris',
                'is_active' => true,
            ]
        );

        $employee1 = User::firstOrCreate(
            ['email' => 'employee@company.com'],
            [
                'name' => 'Budi Pratama',
                'password' => Hash::make('password'),
                'role' => UserRole::Employee,
                'phone' => '081311223344',
                'department' => 'Pemasaran & Bisnis',
                'is_active' => true,
            ]
        );

        $employee2 = User::firstOrCreate(
            ['email' => 'siti.rahma@company.com'],
            [
                'name' => 'Siti Rahmawati',
                'password' => Hash::make('password'),
                'role' => UserRole::Employee,
                'phone' => '081355667788',
                'department' => 'Keuangan & Akuntansi',
                'is_active' => true,
            ]
        );

        // 2. Kategori Barang (Categories)
        // $catElec = Category::firstOrCreate(
        //     ['code' => 'ELEC'],
        //     ['name' => 'Elektronik & Komputer', 'description' => 'Perangkat komputer, laptop, printer, dan monitor.']
        // );

        // $catFurn = Category::firstOrCreate(
        //     ['code' => 'FURN'],
        //     ['name' => 'Perabot & Furnitur', 'description' => 'Meja kerja, kursi ergonomis, lemari berkas, dan partisi.']
        // );

        // $catAtk = Category::firstOrCreate(
        //     ['code' => 'ATK'],
        //     ['name' => 'Alat Tulis Kantor', 'description' => 'Kertas HVS, pulpen, map, spidol, dan perlengkapan habis pakai.']
        // );

        // $catNetw = Category::firstOrCreate(
        //     ['code' => 'NETW'],
        //     ['name' => 'Peralatan Jaringan', 'description' => 'Router, switch manageable, access point, dan kabel LAN.']
        // );

        // $catVeh = Category::firstOrCreate(
        //     ['code' => 'VEHI'],
        //     ['name' => 'Kendaraan Operasional', 'description' => 'Mobil dinas operasional dan sepeda motor pengiriman.']
        // );

        // 3. Lokasi & Ruangan (Locations)
        // $locGdg = Location::firstOrCreate(
        //     ['code' => 'GDG-01'],
        //     ['name' => 'Gudang Utama Lt. 1', 'pic_name' => 'Ahmad Fauzi', 'description' => 'Tempat penyimpanan pusat dan barang transit.']
        // );

        // $locSrv = Location::firstOrCreate(
        //     ['code' => 'IT-SRV'],
        //     ['name' => 'Ruang Server & IT Lt. 2', 'pic_name' => 'Super Administrator', 'description' => 'Pusat infrastruktur IT dan rak server.']
        // );

        // $locOps = Location::firstOrCreate(
        //     ['code' => 'OPS-02'],
        //     ['name' => 'Ruang Kerja Operasional Lt. 2', 'pic_name' => 'Budi Pratama', 'description' => 'Area kerja staf pemasaran dan keuangan.']
        // );

        // $locRpt = Location::firstOrCreate(
        //     ['code' => 'RPT-03'],
        //     ['name' => 'Ruang Rapat Utama Lt. 3', 'pic_name' => 'Siti Rahmawati', 'description' => 'Ruang rapat dewan direksi dan presentasi klien.']
        // );

        // 4. Vendor / Supplier
        // $vendor1 = Vendor::firstOrCreate(
        //     ['name' => 'PT Mitra Sarana Komputindo'],
        //     [
        //         'contact_name' => 'Hendra Kusuma',
        //         'phone' => '021-5551234',
        //         'email' => 'sales@mitrasarana.com',
        //         'address' => 'Kawasan Industri Pulogadung, Jakarta Timur',
        //     ]
        // );

        // $vendor2 = Vendor::firstOrCreate(
        //     ['name' => 'CV Furnitur Sejahtera Mandiri'],
        //     [
        //         'contact_name' => 'Dewi Sartika',
        //         'phone' => '021-8889999',
        //         'email' => 'order@furnitursejahtera.com',
        //         'address' => 'Jl. Otista Raya No. 45, Jakarta Timur',
        //     ]
        // );

        // $vendor3 = Vendor::firstOrCreate(
        //     ['name' => 'Toko ATK Sentosa Prima'],
        //     [
        //         'contact_name' => 'Bambang Pamungkas',
        //         'phone' => '021-7776655',
        //         'email' => 'sales@sentosaprima.co.id',
        //         'address' => 'Kwitang No. 12, Jakarta Pusat',
        //     ]
        // );

        // 5. Data Barang (Items)
        // $item1 = Item::firstOrCreate(
        //     ['code' => 'BRG-ELEC-0001'],
        //     [
        //         'barcode' => '89910010001',
        //         'name' => 'Laptop Dell Latitude 5430 Core i7',
        //         'description' => 'RAM 16GB, SSD 512GB, Display 14" FHD, OS Windows 11 Pro.',
        //         'category_id' => $catElec->id,
        //         'location_id' => $locSrv->id,
        //         'vendor_id' => $vendor1->id,
        //         'unit' => 'Unit',
        //         'stock' => 1,
        //         'min_stock' => 0,
        //         'is_consumable' => false,
        //         'condition' => ItemCondition::Baik,
        //         'status' => ItemStatus::Tersedia,
        //         'purchase_price' => 16500000.00,
        //         'purchase_date' => '2026-01-15',
        //     ]
        // );

        // $item2 = Item::firstOrCreate(
        //     ['code' => 'BRG-ELEC-0002'],
        //     [
        //         'barcode' => '89910010002',
        //         'name' => 'Laptop Lenovo ThinkPad T14 Gen 4',
        //         'description' => 'RAM 16GB, SSD 512GB, garansi resmi Lenovo Premier 3 tahun.',
        //         'category_id' => $catElec->id,
        //         'location_id' => $locOps->id,
        //         'vendor_id' => $vendor1->id,
        //         'unit' => 'Unit',
        //         'stock' => 1,
        //         'min_stock' => 0,
        //         'is_consumable' => false,
        //         'condition' => ItemCondition::Baik,
        //         'status' => ItemStatus::Dipinjam,
        //         'purchase_price' => 18200000.00,
        //         'purchase_date' => '2026-02-10',
        //     ]
        // );

        // $item3 = Item::firstOrCreate(
        //     ['code' => 'BRG-ELEC-0003'],
        //     [
        //         'barcode' => '89910010003',
        //         'name' => 'Proyektor Epson EB-X500 3600 Lumens',
        //         'description' => 'Resolusi XGA, port HDMI & VGA, tas jinjing dan remote kontrol.',
        //         'category_id' => $catElec->id,
        //         'location_id' => $locRpt->id,
        //         'vendor_id' => $vendor1->id,
        //         'unit' => 'Unit',
        //         'stock' => 1,
        //         'min_stock' => 0,
        //         'is_consumable' => false,
        //         'condition' => ItemCondition::Baik,
        //         'status' => ItemStatus::Tersedia,
        //         'purchase_price' => 6800000.00,
        //         'purchase_date' => '2025-11-20',
        //     ]
        // );

        // $item4 = Item::firstOrCreate(
        //     ['code' => 'BRG-ELEC-0004'],
        //     [
        //         'barcode' => '89910010004',
        //         'name' => 'Monitor Dell UltraSharp 27" 4K (U2723QE)',
        //         'description' => 'Panel IPS Black, USB-C 90W charging, 4K UHD. Mengalami flicker ringan.',
        //         'category_id' => $catElec->id,
        //         'location_id' => $locSrv->id,
        //         'vendor_id' => $vendor1->id,
        //         'unit' => 'Unit',
        //         'stock' => 1,
        //         'min_stock' => 0,
        //         'is_consumable' => false,
        //         'condition' => ItemCondition::RusakRingan,
        //         'status' => ItemStatus::DalamPerbaikan,
        //         'purchase_price' => 7900000.00,
        //         'purchase_date' => '2025-08-14',
        //     ]
        // );

        // $item5 = Item::firstOrCreate(
        //     ['code' => 'BRG-FURN-0001'],
        //     [
        //         'barcode' => '89920010001',
        //         'name' => 'Kursi Kerja Ergonomis Sihoo M57',
        //         'description' => 'Mesh breathable, lumbar support 2D, armrest 3D, kaki aluminium.',
        //         'category_id' => $catFurn->id,
        //         'location_id' => $locOps->id,
        //         'vendor_id' => $vendor2->id,
        //         'unit' => 'Unit',
        //         'stock' => 12,
        //         'min_stock' => 2,
        //         'is_consumable' => false,
        //         'condition' => ItemCondition::Baik,
        //         'status' => ItemStatus::Tersedia,
        //         'purchase_price' => 2100000.00,
        //         'purchase_date' => '2026-01-05',
        //     ]
        // );

        // $item6 = Item::firstOrCreate(
        //     ['code' => 'BRG-ATK-0001'],
        //     [
        //         'barcode' => '89930010001',
        //         'name' => 'Kertas HVS PaperOne A4 80gr',
        //         'description' => 'Isi 1 box = 5 rim. Kertas putih kualitas premium untuk cetak dokumen resmi.',
        //         'category_id' => $catAtk->id,
        //         'location_id' => $locGdg->id,
        //         'vendor_id' => $vendor3->id,
        //         'unit' => 'Box',
        //         'stock' => 45,
        //         'min_stock' => 10,
        //         'is_consumable' => true,
        //         'condition' => ItemCondition::Baik,
        //         'status' => ItemStatus::Tersedia,
        //         'purchase_price' => 245000.00,
        //         'purchase_date' => '2026-03-01',
        //     ]
        // );

        // $item7 = Item::firstOrCreate(
        //     ['code' => 'BRG-ATK-0002'],
        //     [
        //         'barcode' => '89930010002',
        //         'name' => 'Spidol Whiteboard Snowman Hitam',
        //         'description' => 'Spidol papan tulis dapat dihapus. Stok menipis!',
        //         'category_id' => $catAtk->id,
        //         'location_id' => $locGdg->id,
        //         'vendor_id' => $vendor3->id,
        //         'unit' => 'Pcs',
        //         'stock' => 4,
        //         'min_stock' => 12,
        //         'is_consumable' => true,
        //         'condition' => ItemCondition::Baik,
        //         'status' => ItemStatus::Tersedia,
        //         'purchase_price' => 11000.00,
        //         'purchase_date' => '2026-02-18',
        //     ]
        // );

        // $item8 = Item::firstOrCreate(
        //     ['code' => 'BRG-NETW-0001'],
        //     [
        //         'barcode' => '89940010001',
        //         'name' => 'Switch Cisco Catalyst 24-Port Gigabit (C1000-24T-4G-L)',
        //         'description' => '24-Port 10/100/1000 Ethernet + 4x 1G SFP uplink. Switch jaringan utama.',
        //         'category_id' => $catNetw->id,
        //         'location_id' => $locSrv->id,
        //         'vendor_id' => $vendor1->id,
        //         'unit' => 'Unit',
        //         'stock' => 2,
        //         'min_stock' => 1,
        //         'is_consumable' => false,
        //         'condition' => ItemCondition::Baik,
        //         'status' => ItemStatus::Tersedia,
        //         'purchase_price' => 13500000.00,
        //         'purchase_date' => '2025-10-10',
        //     ]
        // );

        // // 6. Data Transaksi Peminjaman (Item Loans)
        // ItemLoan::firstOrCreate(
        //     ['loan_code' => 'PJ-202609-0001'],
        //     [
        //         'item_id' => $item2->id,
        //         'user_id' => $employee1->id,
        //         'processed_by' => $staff->id,
        //         'quantity' => 1,
        //         'loan_date' => '2026-09-01',
        //         'due_date' => '2026-09-15',
        //         'status' => LoanStatus::Dipinjam,
        //         'notes' => 'Peminjaman untuk keperluan presentasi luar kota ke klien.',
        //     ]
        // );

        // ItemLoan::firstOrCreate(
        //     ['loan_code' => 'PJ-202608-0001'],
        //     [
        //         'item_id' => $item3->id,
        //         'user_id' => $employee2->id,
        //         'processed_by' => $staff->id,
        //         'quantity' => 1,
        //         'loan_date' => '2026-08-20',
        //         'due_date' => '2026-08-22',
        //         'return_date' => '2026-08-22',
        //         'status' => LoanStatus::Kembali,
        //         'notes' => 'Rapat koordinasi anggaran Q3.',
        //         'return_condition' => ItemCondition::Baik,
        //         'return_notes' => 'Barang dikembalikan lengkap dengan kabel dan remote dalam kondisi normal.',
        //     ]
        // );

        // ItemLoan::firstOrCreate(
        //     ['loan_code' => 'PJ-202609-0002'],
        //     [
        //         'item_id' => $item1->id,
        //         'user_id' => $employee2->id,
        //         'processed_by' => null,
        //         'quantity' => 1,
        //         'loan_date' => '2026-09-12',
        //         'due_date' => '2026-09-20',
        //         'status' => LoanStatus::Diajukan,
        //         'notes' => 'Pengajuan peminjaman laptop cadangan untuk rekap laporan keuangan tahunan.',
        //     ]
        // );

        // 7. Data Mutasi Lokasi (Item Mutations)
        // ItemMutation::firstOrCreate(
        //     ['mutation_code' => 'MUT-202609-0001'],
        //     [
        //         'item_id' => $item4->id,
        //         'from_location_id' => $locOps->id,
        //         'to_location_id' => $locSrv->id,
        //         'quantity' => 1,
        //         'moved_by' => $admin->id,
        //         'mutation_date' => '2026-09-05',
        //         'reason' => 'Dipindahkan ke Ruang IT untuk pengecekan servis garansi terkait display flickering.',
        //     ]
        // );

        // 8. Data Log Stok (Item Stock Logs)
        // ItemStockLog::firstOrCreate(
        //     ['item_id' => $item6->id, 'type' => StockLogType::In],
        //     [
        //         'quantity' => 50,
        //         'before_stock' => 0,
        //         'after_stock' => 50,
        //         'notes' => 'Penerimaan stok awal kertas HVS dari PT Sentosa Prima.',
        //         'created_by' => $staff->id,
        //     ]
        // );

        // ItemStockLog::firstOrCreate(
        //     ['item_id' => $item6->id, 'type' => StockLogType::Out],
        //     [
        //         'quantity' => 5,
        //         'before_stock' => 50,
        //         'after_stock' => 45,
        //         'notes' => 'Distribusi pemakaian berkala ke Divisi Operasional Lt. 2.',
        //         'created_by' => $staff->id,
        //     ]
        // );

        // 9. Seed Manufacturing & Six Sigma Data
        $this->call(ManufacturingSeeder::class);
    }
}
