import easyocr
from googletrans import Translator

# Initialize EasyOCR reader
reader = easyocr.Reader(['mr'])  # Set the language to Marathi ('mr')

# Read text from the image
image_path = './nvme/SKL_Pune_2023-07-04_1_admin_mar.jpg'
results = reader.readtext(image_path)

# Extract the text from the results
marathi_text = ' '.join([result[1] for result in results])

# Translate the Marathi text to English
translator = Translator()
english_translation = translator.translate(marathi_text, dest='en').text

# Print the translated text
print(english_translation)
