# import sys
# import easyocr
# import time
# from googletrans import Translator

# translator = Translator()
# starttime = time.time()

# path = sys.argv[1]
# languageDict = {'hin': 'hi'}
# reader = easyocr.Reader(['mr', 'en'])
# result = reader.readtext(path)
# string = ''
# classified = 0
# for content_tuple in result:
# print(translator.translate(content_tuple[1], src='mr', dest='en').text)
# string += content_tuple[1]
# if (content_tuple[1].lower() == "classified"):
#     classified = 1

# if (classified == 1):
#     print("classified,"+string)
# else:
#     print("not classified,")
# print((time.time() - starttime)/60)
import cv2
import numpy as np

small_image = cv2.imread('icon.jpg', 0)
large_image = cv2.imread('AU_Delhi_2023-06-14_18_admin_hin.jpg', 0)

result = cv2.matchTemplate(large_image, small_image, cv2.TM_CCORR_NORMED)
threshold = 0.9
locations = np.where(result >= threshold)

if locations[0].size > 0:
    print("Image found.")
else:
    print("Image not found.")
