# # this needs to run only once to load the model into memory

# import sys
# import cv2
# import numpy as np
# from PIL import Image
# # from matplotlib import pyplot as pltd
# # import easyocr
# # import time
# # from googletrans import Translator
# # import re

# # translator = Translator()

# # starttime = time.time()
# # large_image = cv2.imread("SKL_Mumbai_2023-06-16_1_mar.jpg",)
# # img = cv2.imread("pud.jpg", 0)


# # reader = easyocr.Reader(['gu', 'en'])
# # result = reader.readtext()
# # for content_tuple in result:
# #     print(translator.translate(content_tuple[1], src='gu', dest='en').text)
# # print((time.time() - starttime)/60)
# filepath = sys.argv[1]
# # imaging_gray = cv2.cvtColor(large_image, cv2.COLOR_BGR2GRAY)


# method = cv2.TM_SQDIFF_NORMED
# small_image1 = cv2.imread('icon.jpg')
# # small_image2 = cv2.imread('icon2.png')

# # cv2.imshow("Current Image", large_image)
# # cv2.imshow("test Image", small_image1)
# # k = cv2.waitKey(0)
# large_image = cv2.imread(filepath)
# # imaging_rgb = cv2.cvtColor(large_image, cv2.COLOR_BGR2RGB)
# # xml_data = cv2.CascadeClassifier('haarcascade_frontalface_alt.xml')
# # detecting = xml_data.detectMultiScale(imaging_gray, minSize=(30, 30))
# # amountDetecting = len(detecting)
# # if amountDetecting != 0:
# #     for (a, b, width, height) in detecting:
# #         cv2.rectangle(imaging_rgb, (a, b),  # Highlighting detected object with rectangle
# #                       (a + height, b + width),
# #                       (0, 275, 0), 9)

# # pltd.subplot(1, 1, 1)
# # pltd.imshow(imaging_rgb)
# # pltd.show()


# # cv2.imshow("display image", large_image)
# # cv2.waitKey(0)

# w, h = large_image.shape[:-1]
# # print(w, h)

# res1 = cv2.matchTemplate(small_image1, large_image, cv2.TM_CCOEFF_NORMED)
# # res2 = cv2.matchTemplate(small_image2, large_image, cv2.TM_CCOEFF_NORMED)
# threshold = .8
# cv2.imshow("current Image", large_image)

# loc1 = np.where(res1 >= threshold)
# a1, b1 = loc1
# # loc2 = np.where(res2 >= threshold)
# # a2, b2 = loc2

# if (len(a1) != 0 and len(b1) != 0):
#     print("classified")
# else:
#     print("")
# k = cv2.waitKey(0)
# # print(chr(k))
# cv2.destroyAllWindows()
print("hello world")
