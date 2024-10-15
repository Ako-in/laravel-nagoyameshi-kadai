<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $company = Company::get()->last();
        return view('admin.company.index',compact('company'));
    }

    public function edit(Company $company)
    {
        // $company = Company::where()->get();
        // return view('admin.company.edit',compact('companies'));
        return view('admin.company.edit',compact('company'));
        // return redirect()->route('admin.company.index', ['company' => $id])->with('flash_message', '会社概要を編集しました。');
    }

    public function update(Request $request, string $id)
    {
        //バリデーション設定
        $request->validate([
            'name' =>'required',
            'postal_code'=>'required|digits:7', //数値かつ桁数7
            'address'=>'required',
            'representative'=>'required',
            'establishment_date'=>'required',
            'capital'=>'required',
            'business'=>'required',
            'number_of_employees'=>'required',
        ]);

        $company = new Company();
        $company->name = $request->input('name');
        $company->postal_code = $request->input('postal_code');
        $company->address = $request->input('address');
        $company->representative = $request->input('representative');
        $company->establishment_date = $request->input('establishment_date');
        $company->capital = $request->input('capital');
        $company->business = $request->input('business');
        $company->number_of_employees = $request->input('number_of_employees');
        $company->save();
        return redirect()->route('admin.company.index')->with('flash_message','会社概要を編集しました。');
    }

}
