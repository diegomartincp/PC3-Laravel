import tweepy
import sys
from datetime import datetime, timedelta

#Recoger la query
query = sys.argv[1] #Este argumento es la query
#query="Tres+Cantos"
query_ = query.replace ("+", " ") #Cambiamos el + por un espacio

#Contstruimos las fechas
date_ahora = d = datetime.today()
date_anterior = d = datetime.today() - timedelta(days=5) #Restamos un dia

date_ahora_string=date_ahora.strftime("%Y%m%d")+"0000"
date_anterior_string=date_anterior.strftime("%Y%m%d")+"0000"

#CLAVES DIEGO
#consumer_key = "9eoCAPNf9fVg2kzInNI5A8Uge"
#consumer_secret = "xwmeFMOHkMl35PSrXvMl0Bk0NhWTeSDaTKk2SP47DeQ24Th3GE"
#access_token = "1070028045938057217-zJHSSfPBahkwHrWSB7vZ5tsNbOrAbt"
#access_token_secret = "fgOGCKnMI2409tF09sgRYfvlQFFnUhPXNbg79xJ1xw93v"

#CLAVES NICO
consumer_key = "R7svH3XUt2HMAF1OaVJuIpmiI"
consumer_secret = "7aTTXIAlVkTFW0T6snH7gSwRPsOqtJ2q2MBew1Sxnh42F12Ufi"
access_token = "2957242798-Sc0zgTRK6H6V820R9e8cUDGq3VYBESPy0XObV8s"
access_token_secret = "PUkqSKm6UIkaaYmpydAVke8cZPZw0Srpw5kMOMlwr0CQx"

#Objeto con las claves de acceso
auth = tweepy.OAuth1UserHandler(
    consumer_key, consumer_secret, access_token, access_token_secret
)
#pasamos las claves y accedemos a la API
api = tweepy.API(auth)


#Realiza la búsqueda en la api
tweets = tweepy.Cursor(api.search_full_archive, #metodo para buscar todos los archivos
                   label="development", #entorno
                   query=query_,        #busqueda de ciudad
                   fromDate=date_anterior_string,#fecha inicio
                   toDate=date_ahora_string,  #fecha fin
                   maxResults=100     #numero de resultados
                   )

tweets_texto=[]

#Iteramos entre lo recuperado
for tweet in tweets.items():
    #print("ID TWEET: " + str(tweet.id))
    #print(tweet.text)
    tweets_texto.append(tweet.text)   #Añadimos al array de tweets


#Tratamiento de sentimiento
from vaderSentiment.vaderSentiment import SentimentIntensityAnalyzer
from googletrans import Translator  #Se traduce con el traductor de google

translator=Translator()
sid_obj = SentimentIntensityAnalyzer() #Construyes analizador

registro_sentimientos=[] #Donde almacenamos el resultado de los 100 tweets
for tweet in tweets_texto:
    traduccion=translator.translate(tweet)
    #print(traduccion.text)
    sentiment_dict = sid_obj.polarity_scores(traduccion.text) #Aplicas el método de polaridad a la frase
    resultado=sentiment_dict['compound']
    #print(resultado)   #Devuelves solo el compound
    registro_sentimientos.append(resultado)   #Añadimos al array de tweets

#print(registro_sentimientos)

#Exportar a JSON
import json
json_twets={}
json_twets['Sentimiento'] = {"valores": registro_sentimientos}
archivo = json.dumps(json_twets)
print(archivo)
