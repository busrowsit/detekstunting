<?php

namespace App\Http\Controllers;

use App\Models\Artikel;
use App\Models\Deteksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DeteksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $artikels = Artikel::orderBy('tanggal', 'desc')->get();
        $berita = $artikels->first(); // Ambil satu artikel terbaru
        $user = Auth::user();
        $hasil_deteksi_terbaru = Deteksi::where('user_id', Auth::id())->latest()->first();
        return view('user.deteksi.detekdini', compact('user','hasil_deteksi_terbaru', 'artikels', 'berita'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'hpht' => 'required|date',
            'lila' => 'required|numeric',
            'tb_ibu' => 'required|numeric',
            'jumlah_anak' => 'required|integer',
            'jumlah_ttd' => 'required|integer',
            'jumlah_anc' => 'required|integer',
            'tekanan_darah' => 'required|string',
            'hb_ibu' => 'required|numeric',
        ]);
    
        $user = Auth::user();
        $tanggal_lahir = Carbon::parse($user->tanggal_lahir);
        $hpht = Carbon::parse($request->hpht);
        $usia = $hpht->diffInYears($tanggal_lahir);
    
        $kategori_usia = ($usia < 20 || $usia > 35) ? 'Berisiko' : 'Tidak berisiko';
        $kategori_lila = ($request->lila < 23.3) ? 'Berisiko' : 'Tidak berisiko';
        $kategori_tb = ($request->tb_ibu < 150) ? 'Pendek' : 'Tinggi';
        $kategori_anak = ($request->jumlah_anak > 2) ? 'Berisiko' : 'Tidak berisiko';
        $kategori_ttd = ($request->jumlah_ttd < 90) ? 'Kurang' : 'Cukup';
        $kategori_anc = ($request->jumlah_anc < 6) ? 'Kurang' : 'Lengkap';
    
        list($sistolik, $diastolik) = explode("/", str_replace(' ', '', $request->tekanan_darah));
    
        if ($sistolik <= 100 || $diastolik <= 60) {
            $kategori_td = 'Hipotensi';
        } elseif ($sistolik >= 130 || $diastolik >= 90) {
            $kategori_td = 'Hipertensi';
        } else {
            $kategori_td = 'Normal';
        }
    
        if ($request->hb_ibu < 7) {
            $kategori_hb = 'Anemia berat';
        } elseif ($request->hb_ibu >= 7 && $request->hb_ibu <= 8) {
            $kategori_hb = 'Anemia sedang';
        } elseif ($request->hb_ibu >= 9 && $request->hb_ibu <= 10) {
            $kategori_hb = 'Anemia ringan';
        } else {
            $kategori_hb = 'Normal';
        }
    
        // Panggil fungsi deteksiStunting
        $hasil_deteksi = $this->deteksiStunting(
            $kategori_hb,
            $kategori_ttd,
            $kategori_lila,
            $kategori_usia,
            $kategori_anak,
            $kategori_tb,
            $kategori_td,
            $kategori_anc
        );
    

        $rekomendasi = $this->generateRekomendasi(
            $kategori_usia, $kategori_lila, $kategori_tb, $kategori_anak,
            $kategori_ttd, $kategori_anc, $kategori_td, $kategori_hb
        );        


        // Simpan ke database
        Deteksi::create([
            'user_id' => $user->id,
            'usia' => $usia,
            'kategori_usia' => $kategori_usia,
            'hpht' => $request->hpht,
            'lila' => $request->lila,
            'kategori_lila' => $kategori_lila,
            'tb_ibu' => $request->tb_ibu,
            'kategori_tb' => $kategori_tb,
            'jumlah_anak' => $request->jumlah_anak,
            'kategori_anak' => $kategori_anak,
            'jumlah_ttd' => $request->jumlah_ttd,
            'kategori_ttd' => $kategori_ttd,
            'jumlah_anc' => $request->jumlah_anc,
            'kategori_anc' => $kategori_anc,
            'tekanan_darah' => $request->tekanan_darah,
            'kategori_td' => $kategori_td,
            'hb' => $request->hb_ibu,
            'kategori_hb' => $kategori_hb,
            'hasil_deteksi' => $hasil_deteksi,
        ]);
    
        // Simpan hasil deteksi ke session agar bisa diakses di Blade
        return redirect()->route('user.deteksi.index')->with([
            'hasil_deteksi' => $hasil_deteksi,
            'rekomendasi' => $rekomendasi
        ]);
        
    }


    private function generateRekomendasi($kategori_usia, $kategori_lila, $kategori_tb, $kategori_anak, $kategori_ttd, $kategori_anc, $kategori_td, $kategori_hb)
    {
        return [
            'usia' => [
                'teks' => ($kategori_usia == 'Berisiko') 
                    ? 'Rencanakan kehamilan di usia lebih dari 20 tahun dan kurang dari 35 tahun.' 
                    : 'Kamu berada di usia yang ideal untuk hamil. Semangat bunda!',
                'status' => ($kategori_usia == 'Berisiko') ? 'merah' : 'hijau'
            ],
            'lila' => [
                'teks' => ($kategori_lila == 'Berisiko') 
                    ? 'Konsumsi makanan bergizi dan rutin periksa kesehatan kehamilan.' 
                    : 'LILA Anda dalam batas normal. Tetap jaga kesehatan ya bunda!',
                'status' => ($kategori_lila == 'Berisiko') ? 'merah' : 'hijau'
            ],
            'tb' => [
                'teks' => ($kategori_tb == 'Pendek') 
                    ? 'Tinggi badan Anda termasuk pendek, perhatikan asupan gizi selama kehamilan.' 
                    : 'Tinggi badan Anda dalam rentang ideal.',
                'status' => ($kategori_tb == 'Pendek') ? 'merah' : 'hijau'
            ],
            'anak' => [
                'teks' => ($kategori_anak == 'Berisiko') 
                    ? 'Jumlah anak lebih dari dua dapat meningkatkan risiko stunting. Atur jarak kehamilan dengan baik.' 
                    : 'Jumlah anak masih dalam batas ideal.',
                'status' => ($kategori_anak == 'Berisiko') ? 'merah' : 'hijau'
            ],
            'ttd' => [
                'teks' => ($kategori_ttd == 'Kurang') 
                    ? 'Konsumsi minimal 90 tablet tambah darah selama kehamilan untuk mencegah anemia.' 
                    : 'Konsumsi TTD Anda sudah cukup. Pertahankan terus ya!',
                'status' => ($kategori_ttd == 'Kurang') ? 'merah' : 'hijau'
            ],
            'anc' => [
                'teks' => ($kategori_anc == 'Kurang') 
                    ? 'Lakukan pemeriksaan kehamilan minimal 6 kali selama masa kehamilan.' 
                    : 'Jumlah kunjungan ANC sudah cukup. Teruskan kebiasaan baik ini!',
                'status' => ($kategori_anc == 'Kurang') ? 'merah' : 'hijau'
            ],
            'td' => [
                'teks' => match($kategori_td) {
                    'Hipotensi' => 'Tekanan darah rendah, segera konsultasikan ke fasilitas kesehatan.',
                    'Hipertensi' => 'Tekanan darah tinggi dapat membahayakan ibu dan janin. Periksakan secara rutin.',
                    default => 'Tekanan darah normal. Pertahankan pola hidup sehat!',
                },
                'status' => in_array($kategori_td, ['Hipotensi', 'Hipertensi']) ? 'merah' : 'hijau'
            ],
            'hb' => [
                'teks' => match($kategori_hb) {
                    'Anemia berat', 'Anemia sedang', 'Anemia ringan' => 'Perbanyak konsumsi zat besi dan vitamin C.',
                    default => 'HB normal. Tetap pertahankan pola makan sehat.',
                },
                'status' => ($kategori_hb == 'Normal') ? 'hijau' : 'merah'
            ]
        ];
    }    
    

    // Fungsi deteksiStunting
    function deteksiStunting($hb, $ttd, $lila, $usia, $jumlah_anak, $tinggi_badan_ibu, $tekanan_darah, $anc) {
        if ($hb == "Anemia ringan") {
            if ($lila == "Berisiko") {
                if ($jumlah_anak == "Berisiko") {
                    return "STUNTING";
                } else { // jumlah anak = Tidak Berisiko
                    return "NORMAL";
                }
            } else { // lila = Tidak Berisiko
                return "NORMAL";
            }
        } else if ($hb == "Anemia sedang") {
            if ($usia == "Berisiko") {
                return "STUNTING";
            } else { // usia = Tidak Berisiko
                return "NORMAL";
            }
        } else if ($hb == "Normal") {
            if ($tekanan_darah == "Hipertensi") {
                if ($tinggi_badan_ibu == "Pendek") {
                    if ($usia == "Berisiko") {
                        return "NORMAL";
                    } else if ($usia == "Tidak Berisiko") {
                        if ($jumlah_anak == "Berisiko") {
                            return "NORMAL";
                        } else { // jumlah anak = Tidak Berisiko
                            return "STUNTING";
                        }
                    }
                } else { // tinggi badan ibu = Tinggi
                    return "NORMAL";
                }
            } else if ($tekanan_darah == "Hipotensi") {
                if ($tinggi_badan_ibu == "Pendek") {
                    if ($usia == "Berisiko") {
                        if ($lila == "Berisiko") {
                            return "NORMAL";
                        } else { // lila = Tidak Berisiko
                            if ($anc == "Kurang") {
                                return "STUNTING";
                            } else { // anc = Lengkap
                                return "NORMAL";
                            }
                        }
                    } else { // usia = Tidak Berisiko
                        if ($anc == "Kurang") {
                            if ($jumlah_anak == "Berisiko") {
                                return "NORMAL";
                            } else { // jumlah anak = Tidak Berisiko
                                return "STUNTING";
                            }
                        } else { // anc = Lengkap
                            if ($jumlah_anak == "Berisiko") {
                                return "STUNTING";
                            } else { // jumlah anak = Tidak Berisiko
                                if ($lila == "Berisiko") {
                                    return "NORMAL";
                                } else { // lila = Tidak Berisiko
                                    return "STUNTING";
                                }
                            }
                        }
                    }
                } else { // tinggi badan ibu = Tinggi
                    if ($jumlah_anak == "Berisiko") {
                        return "STUNTING";
                    } else { // jumlah anak = Tidak Berisiko
                        if ($lila == "Berisiko") {
                            return "STUNTING";
                        } else { // lila = Tidak Berisiko
                            return "NORMAL";
                        }
                    }
                }
            } else if ($tekanan_darah == "Normal") {
                if ($ttd == "Kurang") {
                    return "STUNTING";
                } else if ($ttd == "Cukup") {
                    if ($tinggi_badan_ibu == "Pendek") {
                        if ($lila == "Berisiko") {
                            if ($usia == "Berisiko") {
                                if ($anc == "Kurang") {
                                    return "STUNTING";
                                } else { // anc = Lengkap
                                    return "NORMAL";
                                }
                            } else { // usia = Tidak Berisiko
                                return "STUNTING";
                            }
                        } else { // lila = Tidak Berisiko
                            if ($usia == "Berisiko") {
                                return "STUNTING";
                            } else { // usia = Tidak Berisiko
                                if ($anc == "Kurang") {
                                    return "NORMAL";
                                } else { // anc = Lengkap
                                    if ($jumlah_anak == "Berisiko") {
                                        return "NORMAL";
                                    } else { // jumlah anak = Tidak Berisiko
                                        return "STUNTING";
                                    }
                                }
                            }
                        }
                    } else { // tinggi badan ibu = Tinggi
                        if ($anc == "Kurang") {
                            if ($usia == "Berisiko") {
                                return "STUNTING";
                            } else { // usia = Tidak Berisiko
                                if ($lila == "Berisiko") {
                                    return "STUNTING";
                                } else { // lila = Tidak Berisiko
                                    return "NORMAL";
                                }
                            }
                        } else { // anc = Lengkap
                            if ($lila == "Berisiko") {
                                return "NORMAL";
                            } else { // lila = Tidak Berisiko
                                if ($jumlah_anak == "Berisiko") {
                                    return "STUNTING";
                                } else { // jumlah anak = Tidak Berisiko
                                    return "NORMAL";
                                }
                            }
                        }
                    }
                }
            }
        }
        return "mbuh"; // Default jika tidak masuk ke kondisi mana pun
    }
    
    public function show(string $id, Request $request)
{
    // Ambil jumlah data per halaman dari dropdown (default 5)
    $perPage = $request->input('per_page', 5);

     // Ambil data terbaru setiap user berdasarkan created_at (hanya 1 per user)
     $hasilRiwayat = Deteksi::select('riwayat_deteksi.*')
     ->whereIn('id', function ($query) {
         $query->selectRaw('MAX(id)')
               ->from('riwayat_deteksi')
               ->groupBy('id');
     })
     ->orderBy('id', 'asc') // Urutkan dari yang terbaru
     ->paginate($perPage);

    $artikels = Artikel::orderBy('tanggal', 'desc')->get();
    $berita = $artikels->first(); // Ambil satu artikel terbaru
    $riwayatDetek = Deteksi::where('user_id', $id)->get(); // Pastikan query menghasilkan data
    return view('user.riwayat-deteksi.riwayatdetek', compact('riwayatDetek','artikels', 'berita','hasilRiwayat'));
}
   
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}