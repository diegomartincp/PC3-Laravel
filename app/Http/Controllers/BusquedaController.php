<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;  //PARA LA BBDD



class BusquedaController extends Controller
{
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

    public function crear_usuario(Request $request)
    {   //Crear usuario
        DB::insert('insert into users (name, email, password, tipo_user) values (?,?,?, ?)', ['No_registrado','-','-', 0]);
        return "CREADO USUARIO INICIAL";
    }
    public function TEST_BBDD(Request $request)
    {
        $ciudad = $request->query('ciudad');

        $cache = DB::select('select busqueda.id from scrapping join busqueda on busqueda.id = scrapping.busqueda_id where busqueda.created_at >= DATE_ADD(NOW(), INTERVAL -30 DAY) and busqueda.query= (?) order by busqueda.created_at DESC limit 1;', [$ciudad]);

        //NO HAY CACHE
        if (empty($cache)){

            //Odio si no hay caché
            $json_odio = self::noticias($request);
            $odio = $json_odio['resultado'];
            //$odio = json_encode($json_odio['resultado']);

            //Crear búsqueda y rellenar con odio
            DB::insert('insert into busqueda (usuario_id, query,porcentaje_odio) values (?, ?,?)', [1, $ciudad,$odio]);
            $busqueda = DB::select('select id from busqueda order by id desc limit 1');
            $id = $busqueda[0];
            $busqueda_id = $id->id; //id de búsqueda creada

            //Scrapping si no hay caché
            $json_viviendas = self::viviendas($request);
            $comprar =$json_viviendas['comprar'];
            //$comprar = json_encode($json_viviendas['comprar']);
            $alquilar =$json_viviendas['alquilar'];
            //$alquilar = json_encode($json_viviendas['alquilar']);

            $precio = self::precio($request);
            $m2 =$precio['m2'];
            //$m2 = json_encode($precio['m2']);
            $medio =$precio['medio'];
            //$medio = json_encode($precio['medio']);
            DB::insert('insert into scrapping (busqueda_id, precio_m2, precio_viviendas, num_viviendas_venta, num_viviendas_alquiler) values (?, ?, ?, ?, ?)', [$busqueda_id,  $m2, $medio,$comprar,$alquilar ]);

            //RESTAURANTES si no hay caché
            $json_restaurantes = self::restaurantes($request);
            $nombres =$json_restaurantes['nombre'];
            $nombres = json_encode($json_restaurantes['nombre']);

            $valoraciones =$json_restaurantes['valoracion'];
            $valoraciones =json_encode($json_restaurantes['valoracion']);

            $etiquetas =$json_restaurantes['etiquetas'];
            $etiquetas =json_encode($json_restaurantes['etiquetas']);

            DB::insert('insert into restaurantes (busqueda_id, nombre, puntuacion, etiquetas) values (?, ?, ?, ?)', [$busqueda_id,  $nombres, $valoraciones, $etiquetas]);
        }
        else{
            $cache_json = $cache[0];
            $busqueda_id = $cache_json->id; //ESTE ES EL ID QUE YA EXISTE Y REUTILIZAMOS
        }

        // TWEETS se ejecutan siempre
        $json_tweets = self::tweets($request);
        $valores_ = json_encode($json_tweets['valores']);
        #$valores = json_encode($json_tweets->valores);
        DB::insert('insert into `usuario-busqueda` (usuario_id, busqueda_id, ultimos_100) values (?,?,?)', [1, $busqueda_id, $valores_]);  //ID REUTILIZADO

        //Final. Select de todo para esa búsqueda
        $resultado = DB::select('SELECT busqueda.id, restaurantes.nombre, restaurantes.puntuacion, restaurantes.etiquetas, scrapping.precio_m2, scrapping.precio_viviendas, scrapping.num_viviendas_venta, scrapping.num_viviendas_alquiler, `usuario-busqueda`.ultimos_100, busqueda.porcentaje_odio FROM `busqueda` join `usuario-busqueda` on busqueda.id = `usuario-busqueda`.busqueda_id join scrapping on busqueda.id=scrapping.busqueda_id join restaurantes on busqueda.id=restaurantes.busqueda_id where busqueda.id =(?)', [$busqueda_id]);
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
