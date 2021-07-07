<?php


namespace App\Helper;

use Illuminate\Support\Facades\DB;
use App\Facade\Country;


class State
{
    /**
     * @param string $filter
     * @return array
     */
    public function getStatesWithCountries(string $filter=''): array
    {
        $countriesQ = DB::table('country')
            ->select(
                'country.id',
                'country.name',
                'country.code',
                'state.id as state_id',
                'state.name as state_name',
                'state.code as state_code',
            )
            ->leftJoin('state', 'country.id', '=', 'state.country_id')
            ->where('country.deleted', 0)
            ->where('state.deleted', 0);

        if(!empty($filter)) {
            $countriesQ->where('state.name', 'like', '%'.$filter.'%');
        }

        $countries = $countriesQ->orderBy('country.name')
            ->orderBy('state_name')
            ->get();

        $response = [];
        foreach ($countries as $country){
            $tmp = [];
            if(!array_key_exists($country->id, $response)){
                $tmp = [
                    'id' => $country->id,
                    'name' => $country->name,
                    'code' => $country->code,
                    'state' => []
                ];
                $response[$country->id] = $tmp;
            }
            if($country->state_id){
                $response[$country->id]['state'][] = [
                    'id' => $country->state_id,
                    'name' => $country->state_name,
                    'code' => $country->state_code,
                ];
            }
        }
        return array_values($response);
    }

    /**
     * @param int $stateId
     * @param string $name
     * @param string $code
     * @return bool|string
     */
    public function updateState(int $stateId, string $name, string $code)
    {
        // check if state exists
        $state = $this->getStateById($stateId);

        if(!$state){
            return 'State not found';
        }

        // find duplicate name or code
        $duplicated = DB::table('state')
            ->where('id', '!=', $stateId)
            ->where('country_id', $state->country_id)
            ->where(function($query) use ($name, $code) {
                $query->where('name', $name)
                    ->orWhere('code', $code);
            })->first();

        if($duplicated) {
            return 'Duplicated values';
        }

        DB::table('state')
            ->where('id', $stateId)
            ->update(['name' => $name, 'code' => $code]);

        return true;
    }

    /**
     * @param string $name
     * @param string $code
     * @param int $countryId
     * @return int|string
     */
    public function newState(string $name, string $code, int $countryId)
    {
        //find country
        $country = Country::getCountryById($countryId);
        if(!$country){
            return 'country not found';
        }

        // find duplicate
        $duplicated = DB::table('state')
            ->where('deleted', 0)
            ->where('country_id', $countryId)
            ->orWhere(function($query) use ($name, $code) {
                $query->where('name', $name)
                    ->where('code', $code);
            })->first();
        if($duplicated) {
            return 'Duplicate Values';
        }

        $now = new \DateTime();
        $id = DB::table('state')
            ->insertGetId([
                'name' => $name,
                'code' => $code,
                'country_id' => $countryId,
                'deleted' => 0
                ]);
        return $id;
    }

    /**
     * @param int $stateId
     * @return bool
     */
    public function deleteState(int $stateId): bool
    {
        $state = $this->getStateById($stateId);

        if(!$state) {
            return false;
        }

        DB::table('state')
            ->where('id', $stateId)
            ->update(['deleted' => 1]);

        return true;
    }

    /**
     * @param int $stateId
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getStateById(int $stateId)
    {
        $state = DB::table('state')
            ->where('id', $stateId)
            ->where('deleted', 0)
            ->first();

        return $state;
    }
}
