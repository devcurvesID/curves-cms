<?php

namespace App\Http\Controllers;

use App\Http\Requests\CityRequest;
use App\Http\Requests\CountryRequest;
use App\Http\Requests\ProvinceRequest;
use App\Model\MasterData\City;
use App\Model\MasterData\Country;
use App\Model\MasterData\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('wm-only');
    }

    public function index()
    {
        $cities = City::all()->sortBy('city');
        return view('cf_back.location.index', compact('cities'));
    }

    //COUNTRY ================================================
    public function indexCountry()
    {
        $countries = Country::all()->sortBy('country');
        $count = $countries->count();
        return view('cf_back.location.country', compact('countries', 'count'));
    }

    public function storeCountry(CountryRequest $request)
    {
        $input = $request->all();
        Country::create($input);
        Session::flash('flash_message', trans('messages.country_create_message'));
        return redirect('cf-back/country');
    }

    public function editCountry($id)
    {
        $country = Country::findOrFail($id);
        $countries = Country::all()->sortBy('country');
        $count = $countries->count();
        return view('cf_back.location.edit-country', compact('countries', 'count', 'country'));
    }

    public function updateCountry(CountryRequest $request)
    {
        $id = $request->input('id');
        $country = Country::findOrFail($id);
        $country->country = $request->input('country');
        $country->save();
        Session::flash('flash_message', trans('messages.country_update_message'));
        return redirect('cf-back/country');
    }

    public function destroyCountry($id)
    {
        $country = Country::findOrFail($id);
        $provinces = Province::orderBy('id', 'asc')->where('country_id', '=', $id);
        $count_province = $provinces->count();
        foreach ($provinces as $x) {
            $cities = City::orderBy('id', 'asc')->where('province_id', '=', $x->id);
            foreach ($cities as $y) {
                $y->delete();
            }
            $x->delete();
        }
        $country->delete();
        Session::flash('flash_message', trans('messages.country_delete_message') . ', ' . trans('messages.country_province_city_delete_message'));
        return redirect('cf-back/country');
    }

    //PROVINCE ================================================
    public function indexProvince()
    {
        $provincies = Province::all()->sortBy('province');
        $countriesForSelect = Country::pluck('country', 'id');
        $count = $provincies->count();
        return view('cf_back.location.province', compact('provincies', 'count', 'countriesForSelect'));
    }

    public function storeProvince(ProvinceRequest $request)
    {
        $input = $request->all();
        Province::create($input);
        Session::flash('flash_message', trans('messages.province_create_message'));
        return redirect('cf-back/province');
    }

    public function editProvince($id)
    {
        $province = Province::findOrFail($id);
        $provincies = Province::all()->sortBy('province');
        $countriesForSelect = Country::pluck('country', 'id');
        $count = $provincies->count();
        return view('cf_back.location.edit-province', compact('provincies', 'count', 'province', 'countriesForSelect'));
    }

    public function updateProvince(ProvinceRequest $request)
    {
        $id = $request->input('id');
        $province = Province::findOrFail($id);
        $province->province = $request->input('province');
        $province->country_id = $request->input('country_id');
        $province->save();
        Session::flash('flash_message', trans('messages.province_update_message'));
        return redirect('cf-back/province');
    }

    public function destroyProvince($id)
    {
        $province = Province::findOrFail($id);
        $cities = City::orderBy('id', 'asc')->where('province_id', '=', $id);
        $count = $cities->count();
        foreach ($cities as $x) {
            $x->delete();
        }
        $province->delete();
        Session::flash('flash_message', trans('messages.province_delete_message') . ' & ' . $count . ' ' . trans('messages.city_province_delete_message'));
        return redirect('cf-back/province');
    }

    // CITY ================================================

    public function indexCity()
    {
        $cities = City::all()->sortBy('city');
        $countriesForSelect = Country::pluck('country', 'id');
        $provinciesForSelect = Province::pluck('province', 'id');
        $count = $cities->count();
        return view('cf_back.location.city', compact('cities', 'count', 'countriesForSelect', 'provinciesForSelect'));
    }

    public function storeCity(CityRequest $request)
    {
        $input = $request->all();
        City::create($input);
        Session::flash('flash_message', trans('messages.city_create_message'));
        return redirect('cf-back/city');
    }

    public function editCity($id)
    {
        $city = City::findOrFail($id);
        $cities = City::all()->sortBy('city');
        $province = Province::findOrFail($city->province_id);
        $countriesForSelect = Country::pluck('country', 'id');
        $provinciesForSelect = Province::orderBy('province', 'asc')->where('country_id', '=', $province->country_id)->pluck('province', 'id');
        $count = $cities->count();
        return view('cf_back.location.edit-city', compact('cities', 'count', 'city', 'countriesForSelect', 'provinciesForSelect', 'province'));
    }

    public function updateCity(CityRequest $request)
    {
        $id = $request->input('id');
        $city = City::findOrFail($id);
        $city->city = $request->input('city');
        $city->province_id = $request->input('province_id');
        $city->save();
        Session::flash('flash_message', trans('messages.city_update_message'));
        return redirect('cf-back/city');
    }

    public function destroyCity($id)
    {
        $city = City::findOrFail($id);
        $city->delete();
        Session::flash('flash_message', trans('messages.city_delete_message'));
        return redirect('cf-back/city');
    }
}
