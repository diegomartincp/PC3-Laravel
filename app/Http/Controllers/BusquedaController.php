<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;  //PARA LA BBDD



class BusquedaController extends Controller
{
    //FUNCIONES DE RECUPERACIÓN DE DATOS DE ADMIN
    //Ver todas las búsquedas hechas
    public function select_busquedas_admin(Request $request)
    {
        $busquedas = DB::select('select busqueda.cache_id,busqueda.id,cache.query,busqueda.created_at
        from cache
        join busqueda on cache.id = busqueda.cache_id order by busqueda.created_at DESC', []);
        return $busquedas;
    }
    //Ver contenido de la cache
    public function select_cache_admin(Request $request)
    {
        $busquedas = DB::select('SELECT cache.query, cache.id, restaurantes.nombre, restaurantes.puntuacion, restaurantes.etiquetas, scrapping.precio_m2, scrapping.precio_viviendas, scrapping.num_viviendas_venta, scrapping.num_viviendas_alquiler, cache.porcentaje_odio
        FROM cache
        join scrapping on cache.id=scrapping.cache_id join restaurantes on cache.id=restaurantes.cache_id', []);
        return $busquedas;
    }
    //Seleccionar todas las búsquedas para una query
    public function select_query(Request $request)
    {
        $query = $request->query('query');
        $busquedas = DB::select('SELECT cache.query, busqueda.id, restaurantes.nombre, restaurantes.puntuacion, restaurantes.etiquetas, scrapping.precio_m2, scrapping.precio_viviendas, scrapping.num_viviendas_venta, scrapping.num_viviendas_alquiler, cache.porcentaje_odio
        FROM cache
        join scrapping on cache.id=scrapping.cache_id
        join restaurantes on cache.id=restaurantes.cache_id
        join busqueda on cache.id = busqueda.cache_id
        where cache.query=(?)', [$query]);
        return $busquedas;
    }
    public function select_ranking(Request $request)
    {
        $busquedas = DB::select('SELECT count(busqueda.id), busqueda.query FROM `usuario-busqueda`JOIN busqueda on `usuario-busqueda`.`busqueda_id`=busqueda.id GROUP by busqueda.query order by count(busqueda.id) DESC', []);
        return $busquedas;
    }

    //FUNCIONES REGISTRO Y LOGIN ANTIGUAS
    public function registro_usuario(Request $request)
    {   //registrar usuario
        $name = $request->query('nombre_user');
        $email = $request->query('correo');
        $password = $request->query('contrasena');
        $tipo_user = $request->query('tipo_user');
        DB::insert('insert into users (name, email, password, tipo_user) values (?,?,?, ?)', [$name,$email,$password,$tipo_user]);
        return "REGISTRADO USUARIO";
        //return $name;
    }

    public function login_usuario(Request $request)
    {   //login usuario
        $email = $request->query('correo');
        $password = $request->query('contrasena');
        DB::select('select * from users where password=? AND email=?', [$password,$email]);
        return "LOGIN USUARIO";
        //return $name;
    }

    //FUNCIONES DE GESTIÓN DE CACHÉ Y BBDD
    public function crear_usuario(Request $request)
    {   //Crear usuario
        DB::insert('insert into users (name, email, password, tipo_user) values (?,?,?, ?)', ['No_registrado','-','-', 0]);
        return "CREADO USUARIO INICIAL";
    }
    public function TEST_BBDD(Request $request)
    {
        $ciudad = $request->query('ciudad');

        $cache = DB::select('select cache.id from scrapping join cache on cache.id = scrapping.cache_id where cache.created_at >= DATE_ADD(NOW(), INTERVAL -30 DAY) and cache.query= (?) order by cache.created_at DESC limit 1;', [$ciudad]);

        //NO HAY CACHE
        if (empty($cache)){

            //Odio si no hay caché
            $json_odio = self::noticias($request);
            $odio = $json_odio['resultado'];

            //Crear cache y rellenar con odio
            DB::insert('insert into cache (query, porcentaje_odio) values (?,?)', [$ciudad,$odio]); //Creamos entrada en cache
            $busqueda = DB::select('select id from cache order by id desc limit 1');    //Cogemos el ID de esa entrada para rellenar todas las tablas
            $id = $busqueda[0];
            $cache_id = $id->id; //id de búsqueda creada

            //Scrapping si no hay caché
            $json_viviendas = self::viviendas($request);
            $comprar =$json_viviendas['comprar'];
            $alquilar =$json_viviendas['alquilar'];

            $precio = self::precio($request);
            $m2 =$precio['m2'];
            $medio =$precio['medio'];
            DB::insert('insert into scrapping (cache_id, precio_m2, precio_viviendas, num_viviendas_venta, num_viviendas_alquiler) values (?, ?, ?, ?, ?)', [$cache_id,  $m2, $medio,$comprar,$alquilar ]);

            //RESTAURANTES si no hay caché
            $json_restaurantes = self::restaurantes($request);
            $nombres =$json_restaurantes['nombre'];
            $nombres = json_encode($json_restaurantes['nombre']);

            $valoraciones =$json_restaurantes['valoracion'];
            $valoraciones =json_encode($json_restaurantes['valoracion']);

            $etiquetas =$json_restaurantes['etiquetas'];
            $etiquetas =json_encode($json_restaurantes['etiquetas']);

            DB::insert('insert into restaurantes (cache_id, nombre, puntuacion, etiquetas) values (?, ?, ?, ?)', [$cache_id,  $nombres, $valoraciones, $etiquetas]);
        }
        else{
            $cache_json = $cache[0];
            $cache_id = $cache_json->id; //ESTE ES EL ID QUE YA EXISTE Y REUTILIZAMOS
        }

        // TWEETS se ejecutan siempre
        $json_tweets = self::tweets($request);
        $valores_ = json_encode($json_tweets['valores']);
        #$valores = json_encode($json_tweets->valores);
        DB::insert('insert into busqueda (cache_id, ultimos_100) values (?,?)', [$cache_id, $valores_]);  //ID REUTILIZADO

        //Final. Select de todo para esa búsqueda
        $resultado = DB::select('SELECT cache.id, restaurantes.nombre, restaurantes.puntuacion, restaurantes.etiquetas, scrapping.precio_m2, scrapping.precio_viviendas, scrapping.num_viviendas_venta, scrapping.num_viviendas_alquiler, busqueda.ultimos_100, cache.porcentaje_odio FROM cache join busqueda on cache.id = busqueda.cache_id join scrapping on cache.id=scrapping.cache_id join restaurantes on cache.id=restaurantes.cache_id where cache.id =(?)', [$cache_id]);
        return $resultado[0];

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
        $json = json_decode($result,true);

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
        $json = json_decode($result,true);
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
        $json = json_decode($result,true);

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
