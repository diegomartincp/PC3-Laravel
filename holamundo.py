#print("hola mundoo")
import sys
import requests
from bs4 import BeautifulSoup
import re
import sys

ciudad = sys.argv[1]
#Reemplazamos los espacios por + y convertimos a minusculas el texto
#print("introduzca la ciudad")

#Reemplazamos los espacios por -
#print("introduzca la fecha inicio año-mes-dia")
#fechaIni = input().replace (" ", "-")           # = "2022-03-01"
#print("introduzca la fecha fin año-mes-dia")
#fechaFin = input().replace (" ", "-")           # = "2022-03-15"

#Acceder a los links de las noticias

lista_links=[] #almacenara los links de cada una de las noticias
#url y request a esta
#url="https://www.20minutos.es/busqueda/?q="+ciudad+"&sort_field=publishedAt&category=&publishedAt%5Bfrom%5D="+fechaIni+"&publishedAt%5Buntil%5D="+fechaFin
#url="https://www.20minutos.es/busqueda/?q=tres+cantos&sort_field=publishedAt&category=&publishedAt%5Bfrom%5D=2022-03-01&publishedAt%5Buntil%5D=2022-03-05"
url="https://www.20minutos.es/busqueda/?q="+ciudad+"&sort_field=publishedAt&category=&publishedAt%5Bfrom%5D=2022-03-01&publishedAt%5Buntil%5D=2022-03-05"

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
    #url="https://www.20minutos.es/busqueda/"+str(numero)+"?q=tres+cantos&sort_field=publishedAt&category=&publishedAt%5Bfrom%5D=2022-03-01&publishedAt%5Buntil%5D=2022-03-05"
    url="https://www.20minutos.es/busqueda/"+str(numero)+"?q="+ciudad+"&sort_field=publishedAt&category=&publishedAt%5Bfrom%5D=2022-03-01&publishedAt%5Buntil%5D=2022-03-05"
    r = requests.get(url)



#ACEDER AL CONTENIDO DE CADA UNA DE LAS NOTICIAS
diccionario = []     #Almacena noticias en formato json
import json
noticias = {}
numNoticia = 0
#Accedemos a cada uno de los links obtenidos anteriormente
for url in lista_links:
    r1 = requests.get(url)
    soup1 = BeautifulSoup(r1.content, "html.parser")

    #Buscamos la etiqueta que contenga el titulo de la noticia
    titulo = soup1.find('h1', {'class': 'article-title'})
    #Buscamos la etiqueta que contenga el contenido
    parrafos = soup1.find('div', {'class': 'article-text'})
    #Despues obtenemos solo los parrafos (contenido importante)
    texto = parrafos.findAll('p')
    #obtenemos la fecha
    fechas = soup1.find('span', {'class': 'article-date'})
    #obtenemos el texto de la fecha
    fecha = fechas.text
    #aplicamos regex, para obtener el dia, mes y año en grupo separado
    objetoFecha = re.search('(\d*)\.(\d*)\.(\d*)',fecha)
    #reordenamos los grupos para tener la fecha en el formato de la url
    objetoFecha = str(objetoFecha.group(3))+"-"+str(objetoFecha.group(2))+"-"+str(objetoFecha.group(1))
    #print(str(objetoFecha.group(3))+"-"+str(objetoFecha.group(2))+"-"+str(objetoFecha.group(1)))

    #Unimos el contenido de todos los parrafos, accediendo a cada uno de ellos y uniendolos (join) dejando un espacio entre ellos
    contenidoTag = []
    for i in range(len(texto)):
            contenidoTag.append((texto[i].text)) #si da error .strip() (Visto con Borja)
    tag = " ".join(contenidoTag)

    #Variable para almacenar el titulo y el contenido
    tituloCont = titulo.text.lower()    #obtenemos solo el texto y transformamos a minuscula
    parrafosCont = tag.lower()          #transformamos a minuscula

    numNoticia = numNoticia + 1
    idNoticia = "Noticia "+ str(numNoticia)
    noticias[idNoticia] = {"Titulo": tituloCont, "Contenido": parrafosCont, "fecha": objetoFecha}

    archivo = json.dumps(noticias)

    #jsonString = {"Titulo": tituloCont, "Contenido": parrafosCont, "fecha": objetoFecha}
    #diccionario.insert(0, jsonString)

#for i in range(len(diccionario)):
#    print(diccionario[i])
#print(diccionario)

print(archivo)

#parsed = json.loads(str(diccionario))
#print(json.dumps(parsed, indent=4, sort_keys=True))

