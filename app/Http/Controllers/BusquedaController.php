<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BusquedaController extends Controller
{
        /**
     * PRECIOS DEL m2 Y PRECIO MEDIO
     *
     * @return \Illuminate\Http\Response
     */
    public function precio(Request $request)
    {
        $ciudad = $request->query('ciudad');
        $ciudad_ = str_replace(" ", "+", $ciudad);

        #Precio medio y m2
        $result = exec("C:/Users/campo/AppData/Local/Microsoft/WindowsApps/python3.9.exe C:\Users\campo\Documents\GitHub\PC3-Laravel/web_scrapping_precios_laravel.py " . $ciudad_);
        $json = json_decode($result);

        return $json;
    }
            /**
     * VIVIENDAS A LA VENTA
     *
     * @return \Illuminate\Http\Response
     */
    public function viviendas(Request $request)
    {
        $ciudad = $request->query('ciudad');
        $ciudad_ = str_replace(" ", "+", $ciudad);

        #Precio medio y m2
        $result = exec("C:/Users/campo/AppData/Local/Microsoft/WindowsApps/python3.9.exe C:\Users\campo\Documents\GitHub\PC3-Laravel/web_scraping_fotocasa_laravel.py " . $ciudad_);
        $json = json_decode($result);

        return $json;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
