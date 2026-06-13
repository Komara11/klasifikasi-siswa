<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\Prediction;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PredictionController extends Controller
{
    private $kecamatanMeta = [
        'Majalengka' => ['multiplier' => 1.35, 'base_dist' => 1.0],
        'Kadipaten' => ['multiplier' => 1.20, 'base_dist' => 12.0],
        'Jatiwangi' => ['multiplier' => 1.15, 'base_dist' => 16.0],
        'Cigasong' => ['multiplier' => 1.10, 'base_dist' => 3.5],
        'Kertajati' => ['multiplier' => 1.05, 'base_dist' => 24.0],
        'Rajagaluh' => ['multiplier' => 1.00, 'base_dist' => 18.0],
        'Dawuan' => ['multiplier' => 0.95, 'base_dist' => 10.0],
        'Talaga' => ['multiplier' => 0.85, 'base_dist' => 28.0],
        'Ligung' => ['multiplier' => 0.80, 'base_dist' => 22.0],
    ];

    public function create()
    {
        $kecamatans = array_keys($this->kecamatanMeta);
        return view('prediction.create', compact('kecamatans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'luas_tanah' => 'required|integer|min:10|max:1000',
            'luas_bangunan' => 'required|integer|min:10|max:1000',
            'kamar_tidur' => 'required|integer|min:1|max:20',
            'kamar_mandi' => 'required|integer|min:1|max:20',
            'kecamatan' => 'required|string',
            'jarak_kota' => 'required|numeric|min:0|max:100',
            'kondisi' => 'required|integer|min:1|max:5',
        ]);

        $lt = $request->luas_tanah;
        $lb = $request->luas_bangunan;
        $kt = $request->kamar_tidur;
        $km = $request->kamar_mandi;
        $kecamatan = $request->kecamatan;
        $jarak = $request->jarak_kota;
        $kondisi = $request->kondisi;
        
        $garasi = $request->has('garasi') ? true : false;
        $taman = $request->has('taman') ? true : false;
        
        // Emulate Random Forest Prediction Logic
        $basePrice = ($lb * 3800000) + ($lt * 1400000);
        
        // Kecamatan Multiplier
        $locMult = isset($this->kecamatanMeta[$kecamatan]) ? $this->kecamatanMeta[$kecamatan]['multiplier'] : 1.0;
        
        // Distance Penalty: -1.5% per km, capped at 30% reduction
        $distMult = max(0.70, 1.0 - ($jarak * 0.015));
        
        // Condition Multiplier: Kondisi 1 = 0.83, 5 = 1.15
        $kondMult = 0.75 + ($kondisi * 0.08);

        // Amenities
        $amenities = ($garasi ? 15000000 : 0) + ($taman ? 10000000 : 0);
        $roomsBonus = (($kt - 2) * 8000000) + (($km - 1) * 6000000);
        $totalAmenities = max(0, $amenities + $roomsBonus);

        // Raw Prediction Price
        $price = ($basePrice * $locMult * $distMult * $kondMult) + $totalAmenities;

        // Round to nearest million
        $price = round($price / 1000000) * 1000000;

        // Categories
        if ($price < 300000000) {
            $kategori = 'Murah';
        } elseif ($price <= 700000000) {
            $kategori = 'Menengah';
        } else {
            $kategori = 'Mewah';
        }

        // Save prediction
        $prediction = Prediction::create([
            'user_id' => Auth::id(),
            'luas_tanah' => $lt,
            'luas_bangunan' => $lb,
            'kamar_tidur' => $kt,
            'kamar_mandi' => $km,
            'kecamatan' => $kecamatan,
            'jarak_kota' => $jarak,
            'kondisi' => $kondisi,
            'garasi' => $garasi,
            'taman' => $taman,
            'harga_prediksi' => $price,
            'kategori' => $kategori,
        ]);

        return redirect()->route('prediction.show', $prediction->id)->with('success', 'Estimasi harga berhasil dihitung.');
    }

    public function show(Prediction $prediction)
    {
        // Calculate Confidence Interval: +/- 4%
        $intervalMin = round(($prediction->harga_prediksi * 0.96) / 500000) * 500000;
        $intervalMax = round(($prediction->harga_prediksi * 1.04) / 500000) * 500000;

        // Calculate comparison data with average houses in the same subdistrict
        $areaAvgLt = round(House::where('kecamatan', $prediction->kecamatan)->avg('luas_tanah') ?? 120);
        $areaAvgLb = round(House::where('kecamatan', $prediction->kecamatan)->avg('luas_bangunan') ?? 90);
        $areaAvgKt = round(House::where('kecamatan', $prediction->kecamatan)->avg('kamar_tidur') ?? 3);
        $areaAvgKm = round(House::where('kecamatan', $prediction->kecamatan)->avg('kamar_mandi') ?? 2);
        $areaAvgPrice = round(House::where('kecamatan', $prediction->kecamatan)->avg('harga_aktual') ?? 450000000);

        // Feature contribution percentages for UI display
        $factors = [
            'Lokasi Strategis' => 38,
            'Luas Bangunan & Tanah' => 32,
            'Kondisi & Kualitas Bangunan' => 18,
            'Fasilitas Penunjang (Garasi/Taman)' => 12
        ];

        return view('prediction.show', compact('prediction', 'intervalMin', 'intervalMax', 'areaAvgLt', 'areaAvgLb', 'areaAvgKt', 'areaAvgKm', 'areaAvgPrice', 'factors'));
    }

    public function history()
    {
        $predictions = Prediction::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('prediction.history', compact('predictions'));
    }

    public function methodology()
    {
        return view('methodology');
    }
}
