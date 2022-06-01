#PRIMERO RECUPERAMOS LA URL DE LA API DE FOTOCASA
import requests
from bs4 import BeautifulSoup
import sys
import pandas as pd

def gastrorankingData(ca, municipio, nombres, valoraciones, etiquetas):
    ca = ca.replace(" ", "-").lower()
    municipio = municipio.replace(" ", "-").lower()

    url = "https://gastroranking.es/restaurantes/"+ca+"/"+municipio+"/"
    print(url)
    pageGastroranking = requests.get(url)
    print("Respuesta: "+str(pageGastroranking.status_code))
    if pageGastroranking.status_code!=200:
        return 0

    contenidoGastroranking = BeautifulSoup(pageGastroranking.content, 'html.parser')

    # sacar el título, ranking y etiquetas de los restaurantes
    df = pd.DataFrame(columns=['nombreRestaurante', 'valoracion', 'etiquetas'])

    #Nombre
    for h3 in contenidoGastroranking.find_all('h3', class_='restaurantName'):
        for a in h3.find_all('a'):
            nombres.append(a.getText())
    print(nombres)
    print()

    #Valoracion
    for td in contenidoGastroranking.find_all('td', class_='grInfo centerText'):
        for span in td.find_all('span', class_='rankValue'):
            entero = span.find('span', class_='big').getText()
            decimal = span.find('span', class_='decimal').getText()
            numero = str(entero)+str(decimal)
            valoraciones.append(numero)
    print(valoraciones)
    print()

    #etiquetas
    etiquetasTemporal = ""
    for div in contenidoGastroranking.find_all('div', class_='visualClear tags'):
        for a in div.find_all('a', class_='tag'):
            etiquetasTemporal = etiquetasTemporal+" "+a.getText()
        etiquetas.append(etiquetasTemporal)
        etiquetasTemporal = ""
    print(etiquetas)

    print()
    print(str(len(nombres))+" "+str(len(valoraciones))+" "+str(len(etiquetas)))

    #insertamos los datos en un dataframe REVISAR
    #for n in range(len(nombres)):
     #   #df.append({'nombreRestaurante' : nombres[n] , 'valoracion' : valoraciones[n], 'etiquetasetiquetas' : etiquetas[n]} , ignore_index=True)
      #  new_list = [ (nombres[n], valoraciones[n], etiquetas[n])]
      #  print(new_list)
       # dfNew = pd.DataFrame(new_list, columns = ['nombreRestaurante' , 'valoracion', 'etiquetas'])
        #df = df.append(dfNew,ignore_index=True)

    #Exportamos el dataframe a un csv
    #df.to_csv('infoGastroranking.csv', index=False)

    return 1

#Metodo para eliminar las tildes de una cadena de texto
import unicodedata
def elimina_tildes(cadena):
    #cadena.encode('raw_unicode_escape').decode('utf8')
    s = ''.join((c for c in unicodedata.normalize('NFD',cadena) if unicodedata.category(c) != 'Mn'))
    #s = s.replace("-", "+") #SI normaliza un + a un - lo acambiamos
    return s

#query = sys.argv[1]
query = "fuente del saz"
#query = query.encode('raw_unicode_escape').decode('utf8')
query = elimina_tildes(query)
#print(query)


urlAPI = "https://gastroranking.es/ajax_location_search?where="+query

Headers = {"X-Requested-With": "XMLHttpRequest"}
#response = requests.post(urlAPI, headers=Headers)

r = requests.get(url=urlAPI, headers=Headers)
data = r.json()

try:
    if(len(data)>1):
        url_from_api = data[1].split(', ')

    else:
        url_from_api = data[0].split(', ')

    ca = url_from_api[1].replace("Ã±", "n").encode('raw_unicode_escape').decode('utf8') #Pasamos de un caracter raro como Ã³ a ó
    municipio = url_from_api[0].replace("Ã±", "n").encode('raw_unicode_escape').decode('utf8')
    municipio = elimina_tildes(municipio) #Eliminamos las tíldes
except:
    url_from_api = ""
    ca = ""
    municipio = ""


#Una vez hecha la llamada (con las tres listas en las que almacenaremos los datos), el metodo nos devolvera
#estas listas llenas con la información que nos interesa
nombres = []
valoraciones = []
etiquetas = []

print(ca)
print(municipio)

gastrorankingData(ca,municipio,nombres,valoraciones,etiquetas)
#gastrorankingData("madrid",query,nombres,valoraciones,etiquetas)

nombresString = "::".join(nombres)
valoracionesString = "::".join(valoraciones)
etiquetasString = "::".join(etiquetas)

#Exportar a JSON
restaurantes = {}
import json

restaurantes = {"nombre": nombresString, "valoracion": valoracionesString, "etiquetas": etiquetasString}
archivo = json.dumps(restaurantes)

print()
print(archivo)
