<?php


namespace App\Helper;

use Illuminate\Support\Facades\DB;


class Country
{
    /**
     * @param string $filter
     * @return array
     */
    public function getCountriesWithStates(string $filter='') : array
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
            ->where(function($query) {
                $query->where('state.deleted', 0)
                    ->orWhereNull('state.id');
            });

        if(!empty($filter)) {
            $countriesQ->where('country.name', 'like', '%'.$filter.'%');
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
     * @param int $countryId
     * @param string $name
     * @param string $code
     * @return bool|string
     */
    public function updateCountry(int $countryId, string $name, string $code)
    {
        // check if country exists
        $country = $this->getCountryById($countryId);

        if(!$country){
            return 'Country not found';
        }

        // find duplicate name or code
        $duplicated = DB::table('country')
            ->where('id', '!=', $country->id)
            ->where('deleted', 0)
            ->where(function($query) use ($name, $code) {
                $query->where('name', 'like', $name)
                    ->orWhere('code', 'like', $code);
            })->first();

        if($duplicated) {
            return 'Duplicated data';
        }

        DB::table('country')
            ->where('id', $countryId)
            ->where('deleted', 0)
            ->update(['name' => $name, 'code' => $code]);

        return true;
    }

    /**
     * @param string $name
     * @param string $code
     * @return int|string
     */
    public function newCountry(string $name, string $code)
    {
        // find duplicate
        $duplicated = DB::table('country')
            ->where('deleted', 0)
            ->where(function($query) use ($name, $code) {
                $query->where('name', $name)
                    ->orWhere('code', $code);
            })->first();

        if($duplicated) {
            return 'Duplicate Values';
        }

        $id = DB::table('country')
            ->insertGetId(['name' => $name, 'code' => $code]);
        return $id;
    }

    /**
     * @param int $countryId
     * @return bool
     */
    public function deleteCountry(int $countryId): bool
    {
        $country = $this->getCountryById($countryId);

        if(!$country) {
            return false;
        }

        // delete states
        DB::table('state')
            ->where('country_id', $countryId)
            ->update(['deleted' => 1]);

        DB::table('country')
            ->where('id', $countryId)
            ->update(['deleted' => 1]);

        return true;
    }

    /**
     * @param int $countryId
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getCountryById(int $countryId)
    {
        $country = DB::table('country')
            ->where('id', $countryId)
            ->where('deleted', 0)
            ->first();

        return $country;
    }
}
