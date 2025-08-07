<?php

namespace App\Http\Controllers;

use App\Http\Requests\LicensePlateRequest;
use Illuminate\Http\Request;
use App\Models\LicensePlate;
use Illuminate\Support\Facades\Auth;
class LicenseplateController extends Controller
{
 
    public function store(LicensePlateRequest $request){
     $plateData = $request->validated();
        $plateData['user_id'] = Auth::id();
     
     $plate=   LicensePlate::create($plateData);
        // Here you would typically create the license plate in the database
        // LicensePlate::create($request->validated());
        // return redirect()->route('home')->with('success', 'License Plate added successfully!');
        return view('customer.plate_detail',compact('plate'));  
    }
}
