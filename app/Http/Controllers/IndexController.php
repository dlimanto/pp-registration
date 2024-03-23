<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Revolution\Google\Sheets\Facades\Sheets;

class IndexController extends Controller {

    public function index(Request $request): JsonResponse{
        if ($request->method() === 'GET') return response()->json([ 'message' => 'Hayo mau ngapain.... ԅ(≖⌣≖ԅ)' ], 400);

        $data        = null;
        $source      = Sheets::spreadsheet(config('google.post_spreadsheet_id'))->sheet('Form Responses 1')->range('B2:B')->all();
        $destination = Sheets::spreadsheet(config('google.post_spreadsheet_id'))->sheet('Registration')->range('A2:B')->all();
        
        $oldData = $request->old;
        $newData = $request->new;

        foreach ($destination as $d) {
            if ($oldData['email'] === $d[0]) {
                return response()->json([ 'message' => 'Maaf, QR code telah digunakan oleh '. $d[1] .'. Mohon cek kembali QR code yang diberikan' ], 400);
            }
        }

        foreach ($source as $v) {
            if ($oldData['email'] === $v[0]) {
                $data = $oldData;
                break;
            }
        }
        if ($data === null) return response()->json([ 'message' => 'Peserta tidak terdaftar cek kembali QR code yang digunakan' ], 400);
        
        $newData[] = Carbon::now('+7')->format('d M o H:i:s');
        Sheets::spreadsheet(config('google.post_spreadsheet_id'))->range('A:E')->sheet('Registration')->append([ $newData ]);
        return response()->json([ 'message' => 'Selamat datang, '. $newData[1] .'! Selamat mengikuti workshop Power Personality' ]);
    }
}
