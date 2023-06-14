method = cv2.TM_SQDIFF_NORMED
small_image = cv2.imread('icon.png')
large_image = cv2.imread(
    'imagepy.jpg')
w, h = large_image.shape[:-1]

res = cv2.matchTemplate(small_image, large_image, cv2.TM_CCOEFF_NORMED)
threshold = .8
loc = np.where(res >= threshold)
a, b = loc
if len(a) != 0 and len(b) != 0:
    with open("images/imagepy.jpg", "wb") as f:
        f.write(responseImg.content)
