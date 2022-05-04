

#PRIMERO RECUPERAMOS LA URL DE LA API DE FOTOCASA
import requests
from bs4 import BeautifulSoup
import sys

query = sys.argv[1]
#query="Tres+Cantos"
query_ = query.replace ("+", " ")

URL = "https://www.fotocasa.es/indice-precio-vivienda/ac/"+query_
r = requests.get(url = URL)
data = r.json()
url_from_api = data[0]['value'] #Tenemos la url sobre la que hacer web scrapping

URL_busqueda="https://www.fotocasa.es"+url_from_api
print(URL_busqueda)
#Ahora hacemos web scrapping
request_fotocasa = requests.get(url = URL_busqueda)
soup = BeautifulSoup(request_fotocasa.content, "html.parser")
div_contenido = soup.find('div', {'class': 't-panel comprar active'}) #Div con precios
precios = div_contenido.findAll('div', {'class': 'b-detail_title'})
print(query)
print(precios[0].text)  #Precio metro cuadrado
print(precios[1].text)  #Precio medio

#Tratamos con regex para sacar solo los números
import re
m2_regex = re.search('(\d*\.\d*)',precios[0].text)
precio_medio_regex = re.search('(\d*\.\d*)',precios[1].text)

#Sacamos el valor numérico
m2_numerico = str(precio_medio_regex.group(1))
precio_medio_numerico = str(precio_medio_regex.group(1))

#Exportar a JSON
noticias = {}
import json
noticias['Precios'] = {"m2": m2_numerico, "medio": precio_medio_numerico}

archivo = json.dumps(noticias)

print(archivo)

