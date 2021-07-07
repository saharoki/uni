<?php


namespace App\Http\Controllers;


use App\Facade\Country;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CountryController extends Controller
{

    public function listCountry(Request $request) {

        try {
            $this->validate($request,
                [
                    'filter' => 'alpha',
                ]);
        } catch(ValidationException $e){
            throw $e;
        }

        $filter = $request->input('filter', '');

        $countries = Country::getCountriesWithStates($filter);

        return response()->json(['data' => $countries]);
    }

    public function addCountry(Request $request) {
        try {
            $this->validate($request,
                [
                    'name' => 'required|alpha|min:2',
                    'code' => 'required|alpha|max:2'
                ]);
        } catch(ValidationException $e){
            throw $e;
        }

        $name = $request->input('name');
        $code = $request->input('code');
        $response = Country::newCountry($name, $code);

        if(!is_int($response)) {
            return response()->json([$response], 400);
        }
        return response()->json(Country::getCountryById($response));
    }

    public function updateCountry(Request $request, $id) {
        try {
            $this->validate($request,
            [
                'name' => 'required|alpha|min:2',
                'code' => 'required|alpha|max:2'
            ]);
        } catch(ValidationException $e){
            throw $e;
        }

        $name = $request->input('name');
        $code = $request->input('code');

        $response = Country::updateCountry($id, $name, $code);

        if(!is_bool($response)){
            return response()->json([$response], 400);
        }

        return response()->json(['success' => true]);
    }

    public function deleteCountry(Request $request, $id) {
        $deleted = Country::deleteCountry($id);

        if(!$deleted){
            abort(400, 'Failed to delete');
        }

        return response()->json(['success' => true]);

    }

}
