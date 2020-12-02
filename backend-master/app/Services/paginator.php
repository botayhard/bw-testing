<?php

namespace App\Services;


class Paginator
{
    public static function paginate($data, $per_page = 10, $current_page = null, $path = null)
    {
        if ($current_page == null) {
            $current_page = request("page", null);
        }
        if ($current_page == null) {
            $current_page = 1;
        }
        $count = $data->count();
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $data->forPage($current_page, $per_page)->get(),
            $count,
            $per_page,
            $current_page,
            [
                "path" => (($path) ? $path : "/" . request()->path() . "?" . http_build_query(request()->except("page"))),
            ]
        );
    }

    public static function paginateIfNeeded($data, $per_page = null, $current_page = null, $path = null)
    {
        if ($current_page == null) {
            $current_page = request("page", null);
        }
        $per_page = $per_page ?? request("per_page", null);
        if ($per_page == null) {
            $per_page = 10;
        }
        return self::paginate($data, intval($per_page), intval($current_page), $path);
    }
}
