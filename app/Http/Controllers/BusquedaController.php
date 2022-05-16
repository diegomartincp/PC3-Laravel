<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;  //PARA LA BBDD



class BusquedaController extends Controller
{

    public function TEST_BBDD(Request $request)
    {   //DB::insert('insert into users (name, email, password, tipo_user) values (?,?,?, ?)', ['No_registrado','-','-', 0]);
        $ciudad = $request->query('ciudad');
        //1. Crear búsqueda
        DB::insert('insert into busqueda (usuario_id, query) values (?, ?)', [1, $ciudad]);
        $busqueda = DB::select('select id from busqueda order by id desc limit 1');
        $id = $busqueda[0];
        $busqueda_id = $id->id;

        //2. Scrapping
        $json_viviendas = self::viviendas($request);
        $comprar =$json_viviendas['comprar'];
        $alquilar =$json_viviendas['alquilar'];

        $precio = self::precio($request);
        $m2 =$precio['m2'];
        $medio =$precio['medio'];
        DB::insert('insert into scrapping (busqueda_id, precio_m2, precio_viviendas, num_viviendas_venta, num_viviendas_alquiler) values (?, ?, ?, ?, ?)', [$busqueda_id,  $m2, $medio,$comprar,$alquilar ]);

        // 3. tweets
        $json_tweets = self::tweets($request);
        $valores = json_encode($json_tweets->valores);
        DB::insert('insert into tweets (busqueda_id, ultimos_100) values (?, ?)', [$busqueda_id,  $valores]);
        return $json_tweets->valores;
    }

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
        $json = json_decode($result,true);

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
        $json = json_decode($result,true);  //OJO IMPLEMENTAMOS TRUE PARA USAR ARRAY ASOCIATIVO

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
     * RANKING DE RESTAURANTES
     *
     * @return \Illuminate\Http\Response
     */
    public function restaurantes(Request $request)
    {
        $RUTA_PYTHON=env('RUTA_PYTHON');
        $RUTA_CARPETA_LARAVEL=env('RUTA_CARPETA_LARAVEL');

        $ciudad = $request->query('ciudad');
        $ciudad_ = str_replace(" ", "+", $ciudad);

        #Llamada python
        $result = exec($RUTA_PYTHON." ".$RUTA_CARPETA_LARAVEL."/web_scrapping_gastrorankingAPI.py " . $ciudad_);
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
