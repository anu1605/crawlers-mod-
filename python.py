import sys
import easyocr
import time
from googletrans import Translator

translator = Translator()
# starttime = time.time()

path = sys.argv[1]
languageDict = {'hin': 'hi'}
reader = easyocr.Reader(['mr', 'en'])
result = reader.readtext(path)
string = ''
classified = 0
for content_tuple in result:
    # print(translator.translate(content_tuple[1], src='mr', dest='en').text)
    string += content_tuple[1]
    if (content_tuple[1].lower() == "classified"):
        classified = 1

if (classified == 1):
    print("classified,"+string)
else:
    print("not classified,")
# print((time.time() - starttime)/60)
