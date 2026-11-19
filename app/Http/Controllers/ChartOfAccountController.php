<?php

namespace App\Http\Controllers;

use App\Model\Accounting\ChartOfAccount;
use App\Model\Accounting\MainAccount;
use App\Model\Accounting\AccountingAccount;
use App\Model\Jubilant\Jubilant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class ChartOfAccountController extends Controller
{
    const COA_DEFAULT = [
        [
            'coa_number' => '1-1100',
            'coa_name' => 'Kas',
            'main_account_id' => 1,
        ],
        [
            'coa_number' => '1-1210',
            'coa_name' => 'Piutang Usaha',
            'main_account_id' => 3,
        ],
        [
            'coa_number' => '1-1220',
            'coa_name' => 'Piutang Karyawan',
            'main_account_id' => 3,
        ],
        [
            'coa_number' => '1-1230',
            'coa_name' => 'Piutang Pemegang Saham',
            'main_account_id' => 3,
        ],
        [
            'coa_number' => '1-1240',
            'coa_name' => 'Piutang Lain - Lain',
            'main_account_id' => 3,
        ],
        [
            'coa_number' => '1-1250',
            'coa_name' => 'Uang Muka Pembelian',
            'main_account_id' => 3,
        ],
        [
            'coa_number' => '1-1260',
            'coa_name' => 'Pendapatan Jasa di Terima di Muka',
            'main_account_id' => 3,
        ],
        [
            'coa_number' => '1-1410',
            'coa_name' => 'Sewa Dibayar Dimuka',
            'main_account_id' => 5,
        ],
        [
            'coa_number' => '1-1420',
            'coa_name' => 'Asuransi Dibayar Dimuka',
            'main_account_id' => 5,
        ],
        [
            'coa_number' => '1-1430',
            'coa_name' => 'PPN Masukan',
            'main_account_id' => 5,
        ],
        [
            'coa_number' => '1-1440',
            'coa_name' => 'PPN Keluaran',
            'main_account_id' => 5,
        ],
        [
            'coa_number' => '1-1450',
            'coa_name' => 'PPN Yang Belum Dikreditkan',
            'main_account_id' => 5,
        ],
        [
            'coa_number' => '1-1460',
            'coa_name' => 'Pajak Dibayar Dimuka PPH 25',
            'main_account_id' => 5,
        ],
        [
            'coa_number' => '1-1470',
            'coa_name' => 'Biaya Dibayar Dimuka',
            'main_account_id' => 5,
        ],
        [
            'coa_number' => '1-2200',
            'coa_name' => 'Bangunan',
            'main_account_id' => 7,
        ],
        [
            'coa_number' => '1-2300',
            'coa_name' => 'Kendaraan',
            'main_account_id' => 7,
        ],
        [
            'coa_number' => '1-2400',
            'coa_name' => 'Inventaris Kantor',
            'main_account_id' => 7,
        ],
        [
            'coa_number' => '1-2500',
            'coa_name' => 'Software',
            'main_account_id' => 7,
        ],
        [
            'coa_number' => '1-2600',
            'coa_name' => 'Mesin',
            'main_account_id' => 7,
        ],
        [
            'coa_number' => '1-3100',
            'coa_name' => 'Ak Penyusutan Bangunan',
            'main_account_id' => 8,
        ],
        [
            'coa_number' => '1-3200',
            'coa_name' => 'Ak Penyusutan Kendaraan',
            'main_account_id' => 8,
        ],
        [
            'coa_number' => '1-3300',
            'coa_name' => 'Ak Penyusutan Inventaris Kantor',
            'main_account_id' => 8,
        ],
        [
            'coa_number' => '1-3400',
            'coa_name' => 'Ak Amortisasi Mesin',
            'main_account_id' => 8,
        ],
        [
            'coa_number' => '2-1000',
            'coa_name' => 'Hutang Usaha',
            'main_account_id' => 10,
        ],
        [
            'coa_number' => '2-1100',
            'coa_name' => 'Hutang Sewa',
            'main_account_id' => 10,
        ],
        [
            'coa_number' => '2-2000',
            'coa_name' => 'Hutang Bank',
            'main_account_id' => 10,
        ],
        [
            'coa_number' => '2-3100',
            'coa_name' => 'Pajak YMH Dibayar - PPN',
            'main_account_id' => 11,
        ],
        [
            'coa_number' => '2-3200',
            'coa_name' => 'Pajak YMH Dibayar - PPh 21',
            'main_account_id' => 11,
        ],
        [
            'coa_number' => '2-3300',
            'coa_name' => 'Pajak YMH Dibayar - PPh 23',
            'main_account_id' => 11,
        ],
        [
            'coa_number' => '2-3400',
            'coa_name' => 'Pajak YMH Dibayar - PPh 4(2)',
            'main_account_id' => 11,
        ],
        [
            'coa_number' => '2-3500',
            'coa_name' => 'Pajak YMH Dibayar - PPh 25',
            'main_account_id' => 11,
        ],
        [
            'coa_number' => '2-3600',
            'coa_name' => 'Pajak YMH Dibayar - PPh 29',
            'main_account_id' => 11,
        ],
        [
            'coa_number' => '2-3700',
            'coa_name' => 'Pajak YMH Dibayar - lain lain',
            'main_account_id' => 11,
        ],
        [
            'coa_number' => '2-3800',
            'coa_name' => 'Uang Muka Penjualan',
            'main_account_id' => 11,
        ],
        [
            'coa_number' => '2-3900',
            'coa_name' => 'By. Yg Masih Harus Dibayar',
            'main_account_id' => 11,
        ],
        [
            'coa_number' => '2-4100',
            'coa_name' => 'Hutang Pemegang Saham',
            'main_account_id' => 12,
        ],
        [
            'coa_number' => '2-4200',
            'coa_name' => 'Hutang Lain - Lain',
            'main_account_id' => 12,
        ],
        [
            'coa_number' => '3-1000',
            'coa_name' => 'Modal Saham',
            'main_account_id' => 13,
        ],
        [
            'coa_number' => '3-2100',
            'coa_name' => 'R/E sampai dengan tahun lalu',
            'main_account_id' => 14,
        ],
        [
            'coa_number' => '3-2200',
            'coa_name' => 'R/E tahun berjalan',
            'main_account_id' => 14,
        ],
        [
            'coa_number' => '4-1000',
            'coa_name' => 'Penjualan jasa',
            'main_account_id' => 15,
        ],
        [
            'coa_number' => '4-2000',
            'coa_name' => 'Potongan Harga',
            'main_account_id' => 15,
        ],
        [
            'coa_number' => '4-3000',
            'coa_name' => 'Retur Penjualan',
            'main_account_id' => 15,
        ],
        [
            'coa_number' => '4-4000',
            'coa_name' => 'Penjualan produk',
            'main_account_id' => 15,
        ],
        [
            'coa_number' => '4-5000',
            'coa_name' => 'Penjualan sewa loker',
            'main_account_id' => 15,
        ],
        [
            'coa_number' => '4-6000',
            'coa_name' => 'Penjualan lainnya',
            'main_account_id' => 15,
        ],
        [
            'coa_number' => '5-1000',
            'coa_name' => 'Kenaikan (Penurunan) Persediaan',
            'main_account_id' => 16,
        ],
        [
            'coa_number' => '5-2000',
            'coa_name' => 'Beban Packing',
            'main_account_id' => 16,
        ],
        [
            'coa_number' => '6-1000',
            'coa_name' => 'Beban Pemasaran',
            'main_account_id' => 17,
        ],
        [
            'coa_number' => '6-1100',
            'coa_name' => 'Beban Aksesoris',
            'main_account_id' => 17,
        ],
        [
            'coa_number' => '6-2100',
            'coa_name' => 'Gaji',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2101',
            'coa_name' => 'BPJS',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2102',
            'coa_name' => 'Biaya Keperluan Kantor',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2103',
            'coa_name' => 'Beban Konsumsi',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2104',
            'coa_name' => 'Beban Listrik',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2105',
            'coa_name' => 'Beban Telephone / Fax / Hand Phone',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2106',
            'coa_name' => 'Beban Internet',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2107',
            'coa_name' => 'Beban Air',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2108',
            'coa_name' => 'Beban Bahan Bakar',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2109',
            'coa_name' => 'Beban Pemeliharaan Kendaraan',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2110',
            'coa_name' => 'Beban Pemeliharaan Peralatan',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2111',
            'coa_name' => 'Beban Perjalanan Dinas',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2112',
            'coa_name' => 'Beban Pengiriman',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2113',
            'coa_name' => 'Beban ATK',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2114',
            'coa_name' => 'Beban Asuransi',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2115',
            'coa_name' => 'Beban Pajak Kendaraan',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2116',
            'coa_name' => 'Beban Administrasi Bank',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2117',
            'coa_name' => 'Beban Bunga Pinjaman Bank',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2118',
            'coa_name' => 'Beban Perizinan',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2119',
            'coa_name' => 'Jasa Konsultasi/Tenaga Ahli',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2120',
            'coa_name' => 'Beban parkir, retribusi, tol, dll',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2121',
            'coa_name' => 'Beban Sewa',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2122',
            'coa_name' => 'Beban Iuran/Sumbangan',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2123',
            'coa_name' => 'Beban Pajak',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2124',
            'coa_name' => 'Beban Penyusutan',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2125',
            'coa_name' => 'Beban Pelatihan dan Seminar',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2126',
            'coa_name' => 'Beban Teknologi Informasi',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2127',
            'coa_name' => 'Beban Pemeliharaan Gedung',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '6-2128',
            'coa_name' => 'Beban Lain - Lain',
            'main_account_id' => 18,
        ],
        [
            'coa_number' => '7-1000',
            'coa_name' => 'Pendapatan Jasa Giro',
            'main_account_id' => 19,
        ],
        [
            'coa_number' => '7-2000',
            'coa_name' => 'Pendapatan Lain - Lain',
            'main_account_id' => 19,
        ],
        [
            'coa_number' => '8-1100',
            'coa_name' => 'Pajak Bunga Bank',
            'main_account_id' => 20,
        ],
        [
            'coa_number' => '0-0000',
            'coa_name' => 'Default',
            'main_account_id' => 22,
        ],
        [
            'coa_number' => '1-1211',
            'coa_name' => 'Piutang Produk Investor',
            'main_account_id' => 3,
        ],
        [
            'coa_number' => '1-1212',
            'coa_name' => 'Piutang Royalti Investor',
            'main_account_id' => 3,
        ],
        [
            'coa_number' => '1-1213',
            'coa_name' => 'Piutang Manage Service Investor',
            'main_account_id' => 3,
        ],
        [
            'coa_number' => '1-1214',
            'coa_name' => 'Piutang Marketing Brand Investor',
            'main_account_id' => 3,
        ],
    ];

    public function __construct()
    {
        $this->middleware('acc-auth');
    }

    public function chart_of_account(){

//        $club = Auth::user()->user_club;
//        $club = ClubInfo::where('status', '=', 'Active')
//            ->whereHas('club_accounting', function ($q){
//                $q->where('accounting_id', '=', Session::get('account')->id);
//            })->get();

        $user = AccountingAccount::find(Session::get('account')->id);

        $main_accounts = MainAccount::pluck('account_abv_name', 'id');
        // OLD
        // $coas = ChartOfAccount::where('coa_number', '<>', '0-0000')->get();
        // END OF OLD

        // NEW
        $gate = false;
        if(empty($user->accounting_jubilant)){
            $jubilants = [];
        }else{
            $selected_id = '(';
            $count = 0;
            foreach ($user->accounting_jubilant as $jubilant) {
                if(!empty($_GET['selected_jubilant'])){
                    if($jubilant->id == $_GET['selected_jubilant']){
                        $gate = true;
                    }
                }

                $selected_id = $selected_id . strval($jubilant->id);

                if(empty($user->accounting_jubilant[$count+1])){
                    $selected_id = $selected_id . ')';
                }else{
                    $selected_id = $selected_id . ', ';
                }
                $count++;
            }

            $jubilants = Jubilant::whereRaw('id IN '.$selected_id)->pluck('jubilant_name','id');
        }

        $theid = 0;
        if(empty($_GET['selected_jubilant'])){
            if(empty($jubilants)){
                $coas = [];
            }else{
                foreach ($jubilants as $key => $value) {
                    $theid = $key;
                    break;
                }
                $coas = ChartOfAccount::where('coa_number','<>','0-0000')->where('jubilant_id','=',$theid)->get();
            }
        }else{
            if($gate){
                $coas = ChartOfAccount::where('coa_number','<>','0-0000')->where('jubilant_id','=',$_GET['selected_jubilant'])->get();
            }else{
                $coas = [];
            }
        }

        $selectedJubilant = 0;
        if(empty($_GET['selected_jubilant'])){
            if(!empty($jubilants[0])){
                $selectedJubilant = $theid;
            }
        }else{
            $selectedJubilant = $_GET['selected_jubilant'];
        }

        return view('cf_acc.accounting.chart-of-account', compact('main_accounts', 'coas','jubilants','selectedJubilant','user'));
    }

    public function store_chart_of_account(Request $request){
        $this->validate($request, [
            'number' => 'required|unique:chart_of_accounts,coa_number,NULL,'.$request->coa_id.',jubilant_id,'.$request->jubilant_id,
            'name' => 'required',
            'coa_abv' => 'required',
            'coa_main' => 'required',
            'jubilant_id' => 'required'
        ]);

        $coa = new ChartOfAccount;
        $coa->coa_number = $request->number;
        $coa->coa_name = $request->name;
        $coa->coa_abv_name = $request->coa_abv;
        $coa->main_account_id = $request->coa_main;
        $coa->jubilant_id = $request->jubilant_id;
        $coa->user_id = Session::get('account')->id;
        $coa->save();

        Session::flash('flash_message', trans('messages.chart_of_account_successfully_added'));

        return redirect('cf-acc/accounting/chart-of-account');
    }

    public function update_chart_of_account(Request $request){
        $this->validate($request, [
            'edit_number' => 'required|unique:chart_of_accounts,coa_number,NULL,'.$request->coa_id.',jubilant_id,'.$request->edit_jubilant_id,
            'edit_name' => 'required',
            'edit_coa_abv' => 'required',
            'edit_coa_main' => 'required',
        ]);

        $coa = ChartOfAccount::findOrFail($request->coa_id);
        if(!empty($coa)){
            $coa->coa_number = $request->edit_number;
            $coa->coa_name = $request->edit_name;
            $coa->coa_abv_name = $request->edit_coa_abv;
            $coa->main_account_id = $request->edit_coa_main;
            $coa->jubilant_id = $request->edit_jubilant_id;
            $coa->user_id = Session::get('account')->id;

            if($coa->save()){
                Session::flash('flash_message',trans('messages.chart_of_account_updated'));
            }else{
                Session::flash('error_message',trans('messages.failed_to_update_chart_of_account'));
            }
        }else{
            Session::flash('error_message',trans('messages.chart_of_account_not_found'));
        }
        return redirect('cf-acc/accounting/chart-of-account');
    }

    public function delete_chart_of_account($id){
        $coa = ChartOfAccount::findOrFail($id);
        try{
            $coa->delete();
            Session::flash('flash_message',trans('messages.chart_of_account_has_been_deleted'));
        }catch(\Illuminate\Database\QueryException $e){
            Session::flash('error_message',trans('messages.failed_to_delete_chart_of_account'));
        }

        return redirect('cf-acc/accounting/chart-of-account');
    }

    public function edit_chart_of_account_ajax(Request $request){
        $this->validate($request, [
            'coa_id' => 'required',
        ]);

        $coa = ChartOfAccount::findOrFail($request->coa_id);
        return response()->json(array('coa'=>$coa));
    }

    public function generate_coa_index(){
        return view('cf_lite.developer.generate-coa');
    }

    public function generate_coa(Request $request){
        foreach(self::COA_DEFAULT as $default){
            $is_exist = ChartOfAccount::where('coa_number','=',$default["coa_number"])
                                        ->where('coa_name','=',$default["coa_name"])
                                        ->where('main_account_id','=',$default["main_account_id"])
                                        ->where('jubilant_id','=',$request->jubilant)
                                        ->first();
            if(empty($is_exist)){
                $coa = new ChartOfAccount;
                $coa->coa_number = $default["coa_number"];
                $coa->coa_name = $default["coa_name"];
                $coa->coa_abv_name = $default["coa_name"];
                $coa->main_account_id = $default["main_account_id"];
                $coa->user_id = 1;
                $coa->jubilant_id = $request->jubilant;
                $coa->save();
            }
        }
    }
}
