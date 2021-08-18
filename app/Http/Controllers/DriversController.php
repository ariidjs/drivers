<?php

namespace App\Http\Controllers;

use App\Models\Drivers;
use Illuminate\Http\Request;

class DriversController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getDriver($id)
    {
        $driver = Drivers::find($id);

        if($driver) {
            return response()->json([
                'success' => true,
                'message' => 'Success fetching data!',
                'data' => $driver
            ], 200);
        }else {
            return response()->json([
                'success' => false,
                'message' => 'Failed fetching data!',
                'data' => ''
            ], 404);
        }
    }
}