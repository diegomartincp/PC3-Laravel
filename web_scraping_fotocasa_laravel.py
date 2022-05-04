import requests
from bs4 import BeautifulSoup
import sys

ciudad = sys.argv[1]
#Indicamos la ciudad, reemplazamos espacion por - y convertimos a minuscula
#ciudad="Tres+Cantos"
ciudad = ciudad.replace ("+", "-").lower()

#En la web se puede hacer varias operaciones:
operacion= ["comprar", "alquiler"]
#En que estamos interasados:

#Almacenara el resultado final con el numero de viviendas de cada tipo
total = []

#Para cada operacion
json_viviendas = {}
for oper in operacion:
    #Accedemos a la url
    url = "https://www.fotocasa.es/es/"+oper+"/viviendas/"+ciudad+"/todas-las-zonas/l"
    r = requests.get(url)
    soup = BeautifulSoup(r.content, "html.parser")
    #buscamos las etiquetas que contienen el numero y la informacion
    numero = soup.find('span', {'class': 're-SearchTitle-count'})
    texto = soup.find('h1', {'class': 're-SearchTitle-text'})
    #unimos ambas etiquetas y las insertamos en el array final
    info = numero.text + " " + texto.text
    total.insert(0, info)
    #rellenamos el json
    if oper =="comprar":
        json_viviendas['Comprar'] = {"info": info}
    else:
        json_viviendas['Alquilar'] = {"info": info}




#Exportar a JSON

import json
archivo = json.dumps(json_viviendas)
print(archivo)

