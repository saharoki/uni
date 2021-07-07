<?php


namespace App\Http\Controllers;


use App\Facade\State;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StateController extends Controller
{
    public function listState(Request $request) {
        try {
            $this->validate($request,
                [
                    'filter' => 'alpha',
                ]);
        } catch(ValidationException $e){
            throw $e;
        }

        $filter = $request->input('filter', '');

        $states = State::getStatesWithCountries($filter);

        return response()->json(['data' => $states]);
    }

    public function addState(Request $request, int $id) {
        try {
            $this->validate($request,
                [
                    'name' => 'required|alpha|min:2',
                    'code' => 'required|alpha|max:10|min:2'
                ]);
        } catch(ValidationException $e){
            throw $e;
        }

        $name = $request->input('name');
        $code = $request->input('code');
        $response = State::newState($name, $code, $id);

        if(!is_int($response)) {
            return response()->json([$response], 400);
        }
        return response()->json(State::getStateById($response));
    }

    public function updateState(Request $request, $id) {
        try {
            $this->validate($request,
                [
                    'name' => 'required|alpha|min:2',
                    'code' => 'required|alpha|max:10|min:2'
                ]);
        } catch(ValidationException $e){
            throw $e;
        }

        $name = $request->input('name');
        $code = $request->input('code');

        $response = State::updateState($id, $name, $code);

        if(!is_bool($response)){
            return response()->json([$response], 400);
        }

        return response()->json(['success' => true]);
    }

    public function deleteState(Request $request, $id) {
        $deleted = State::deleteState($id);

        if(!$deleted){
            abort(400, 'Failed to delete');
        }

        return response()->json(['success' => true]);

    }
}
