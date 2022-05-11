import pickle
from warnings import catch_warnings
from sklearn.feature_extraction.text import CountVectorizer
from nltk.stem.snowball import SpanishStemmer
stemmer = SpanishStemmer()
analyzer = CountVectorizer().build_analyzer()

import sys
#Recoger la query
#query = sys.argv[1]
#query="tres+cantos"
query="alcobendas"

ruta="C:/Users/Victor/LARAVEL/PC3-Laravel/modelo_pc3_v.sav"
def stemmed_words(doc):
    return (stemmer.stem(w) for w in analyzer(doc))
fichero_cargado = pickle.load(open(ruta, 'rb'))

import requests
from bs4 import BeautifulSoup

lista_links=[] #almacenara los links de cada una de las noticias
url="https://www.20minutos.es/busqueda/?q="+query+"&sort_field=publishedAt&category=&publishedAt%5Bfrom%5D=2022-03-01&publishedAt%5Buntil%5D=2022-03-05"
print(url)
r = requests.get(url)
#print(r.status_code) #200 bueno / 404 error

numero = 1 #nos permitira acceder a las diferentes paginas de la web
#mientras que exista la pagina web...
while (r.status_code == 200):
    #Utilizamos beautfulSoup
    soup = BeautifulSoup(r.content, "html.parser")
    #Buscamos todas las etiquetas donde se encuentra el link de cada noticia
    h1 = soup.findAll('div', {'class': 'media-content'})
    #Dentro del H1, buscamos el href, obtenemos el texto y lo añadimos a la lista_links
    for element in h1:
        link = element.findChildren("a" , href=True)
        #Para cada uno de los links de los h1 extraemos SOLO el atributo href
        for i in link:
            link_completo=i['href'] #Extraemos atributo href
            lista_links.insert(0,link_completo) #Añadimos a lista_links el link de cada una de las noticias

    #Modificamos la url para acceder a la siguiente pagina (str(numero)). En caso de que no exista, salimos del bucle while.
    numero = numero + 1
    url="https://www.20minutos.es/busqueda/"+str(numero)+"?q="+query+"&sort_field=publishedAt&category=&publishedAt%5Bfrom%5D=2022-03-01&publishedAt%5Buntil%5D=2022-03-05"
    r = requests.get(url)

#ACEDER AL CONTENIDO DE CADA UNA DE LAS NOTICIAS
diccionario = []     #Almacena noticias en formato json
#Accedemos a cada uno de los links obtenidos anteriormente
for url in lista_links:
    r1 = requests.get(url)
    soup1 = BeautifulSoup(r1.content, "html.parser")

    #Buscamos la etiqueta que contenga el contenido
    parrafos = soup1.find('div', {'class': 'article-text'})
    #Despues obtenemos solo los parrafos (contenido importante)
    texto = parrafos.findAll('p')

    #Unimos el contenido de todos los parrafos, accediendo a cada uno de ellos y uniendolos (join) dejando un espacio entre ellos
    contenidoTag = []
    for i in range(len(texto)):
            contenidoTag.append((texto[i].text)) #si da error .strip() (Visto con Borja)
    tag = " ".join(contenidoTag)

    #Variable para almacenar el titulo y el contenido
    parrafosCont = tag.lower()          #transformamos a minuscula

    diccionario.insert(0, parrafosCont)

#Llegados a este punto tenemos un array de strings con los ficheros, un array con los nombres de los ficheros, el modelo, lista de palabras y lista tf-idf

nueva_lista = fichero_cargado[1].transform(diccionario)
nueva_lista_tfidf = fichero_cargado[2].transform(nueva_lista)
predicted = fichero_cargado[0].predict(nueva_lista_tfidf)
#Una vez realizadas las predicciones, lo metemos en un dataframe
import pandas as pd
df2 = pd.DataFrame(columns = ['Documento', 'Predicción'])
#Recordemos que file list son los nombres de los documentos y textos_sin_clasificar son su contenido: Tienen el mismo tamaño
for i in range(len(diccionario)):
    if predicted[i]==0:
        new_row = {'Documento':str(i), 'Predicción':"No odio"}
    else:
        new_row = {'Documento':str(i), 'Predicción':"Odio"}
    df2=df2.append(new_row, ignore_index=True)

print(df2)

result = df2.groupby('Predicción').nunique()
try:
    cant_no = result.loc['No odio'][0]
except:
    cant_no = 0
try:
    cant_o = result.loc['Odio'][0]
except:
    cant_o = 0

cant_total = cant_no + cant_o
porcentaje = cant_o/cant_total

import json
json_noticias={}
json_noticias= {"resultado": str(porcentaje)}
archivo = json.dumps(json_noticias)
print(archivo)
