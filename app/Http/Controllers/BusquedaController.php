<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;



class BusquedaController extends Controller
{
    /**
     * TWEETS
     *
     * @return \Illuminate\Http\Response
     */
    public function tweets(Request $request)
    {
        $RUTA_PYTHON=env('RUTA_PYTHON');
        $RUTA_CARPETA_LARAVEL=env('RUTA_CARPETA_LARAVEL');

        $ciudad = $request->query('ciudad');
        $ciudad_ = str_replace(" ", "+", $ciudad);

        #Llamada python
        $result = exec($RUTA_PYTHON." ".$RUTA_CARPETA_LARAVEL."/tweepy_oauthv2_sentiment_analysis_laravel.py " . $ciudad_);
        $json = json_decode($result);

        return $json;
    }
    /*prueba commit*/


        /**
     * PRECIOS DEL m2 Y PRECIO MEDIO
     *
     * @return \Illuminate\Http\Response
     */
    public function precio(Request $request)
    {
        $RUTA_PYTHON=env('RUTA_PYTHON');
        $RUTA_CARPETA_LARAVEL=env('RUTA_CARPETA_LARAVEL');

        $ciudad = $request->query('ciudad');
        $ciudad_ = str_replace(" ", "+", $ciudad);

        #Llamada python
        $result = exec($RUTA_PYTHON." ".$RUTA_CARPETA_LARAVEL."/web_scrapping_precios_laravel.py " . $ciudad_);
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
        $RUTA_PYTHON=env('RUTA_PYTHON');
        $RUTA_CARPETA_LARAVEL=env('RUTA_CARPETA_LARAVEL');

        $ciudad = $request->query('ciudad');
        $ciudad_ = str_replace(" ", "+", $ciudad);

        #Llamada python
        $result = exec($RUTA_PYTHON." ".$RUTA_CARPETA_LARAVEL."/web_scraping_fotocasa_laravel.py " . $ciudad_);
        $json = json_decode($result);

        return $json;
    }

                /**
     * ODIO NOTICIAS
     *
     * @return \Illuminate\Http\Response
     */
    public function noticias(Request $request)
    {
        $RUTA_PYTHON=env('RUTA_PYTHON');
        $RUTA_CARPETA_LARAVEL=env('RUTA_CARPETA_LARAVEL');

        $ciudad = $request->query('ciudad');
        $ciudad_ = str_replace(" ", "+", $ciudad);
        $result = exec($RUTA_PYTHON." ".$RUTA_CARPETA_LARAVEL."/prediccion.py " . $ciudad_);
        #Llamada python
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
