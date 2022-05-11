import requests
from bs4 import BeautifulSoup
import pandas as pd

def gastrorankingData(ca, municipio, nombres, valoraciones, etiquetas):
    ca = ca.replace(" ", "-").lower()
    municipio = municipio.replace(" ", "-").lower()
    url = "https://gastroranking.es/restaurantes/"+ca+"/"+municipio+"/"
    print(url)
    pageGastroranking = requests.get(url)
    print("Respuesta: "+str(pageGastroranking.status_code))
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
    for n in range(len(nombres)):
        #df.append({'nombreRestaurante' : nombres[n] , 'valoracion' : valoraciones[n], 'etiquetasetiquetas' : etiquetas[n]} , ignore_index=True)
        new_list = [ (nombres[n], valoraciones[n], etiquetas[n])]
        print(new_list)
        dfNew = pd.DataFrame(new_list, columns = ['nombreRestaurante' , 'valoracion', 'etiquetas'])
        df = df.append(dfNew,ignore_index=True)
        
    #Exportamos el dataframe a un csv
    df.to_csv('infoGastroranking.csv', index=False)
    
    return df



#Una vez hecha la llamada (con las tres listas en las que almacenaremos los datos), el metodo nos devolvera
#estas listas llenas con la información que nos interesa
nombres = []
valoraciones = []
etiquetas = []
gastrorankingData("Madrid", "Boadilla del monte", nombres, valoraciones, etiquetas)