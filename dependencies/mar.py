
from googletrans import Translator
import sys

command = sys.argv[1].split(' ')
filename = command[0]
target_language = command[1]

chunk_size = 1000  # Maximum chunk size allowed by Google Translate API

with open(filename, 'r', encoding='utf-8') as file:
    file_contents = file.read()

# Replace newlines with commas
file_contents = file_contents.replace('\n', ', ')

translator = Translator()

# Split the text into smaller chunks
chunks = [file_contents[i:i+chunk_size]
          for i in range(0, len(file_contents), chunk_size)]

# Translate each chunk and append the translations
translated_chunks = []
for chunk in chunks:
    translation = translator.translate(chunk, dest=target_language)
    translated_chunks.append(translation.text)

# Join the translated chunks
translated_text = ''.join(translated_chunks)

with open(filename, 'w', encoding='utf-8') as file:
    file.write(translated_text)
print(translated_text)
