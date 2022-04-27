<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BusquedaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $ciudad = $request->query('ciudad');
        $ciudad_ = str_replace(" ", "+", $ciudad);
        #$result = exec("C:/Users/Victor/AppData/Local/Microsoft/WindowsApps/python3.9.exe c:/Users/Victor/LARAVEL/PC3-Laravel/holamundo.py " . $ciudad);
        $result = exec("C:/Users/campo/AppData/Local/Microsoft/WindowsApps/python3.9.exe C:\Users\campo\Documents\GitHub\PC3-Laravel/holamundo.py " . $ciudad_);
        $json = json_decode($result);
        #$json = '{"foo-bar": 12345}';
        #$obj = json_decode($json);
        #return $json;
        return $json;
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
