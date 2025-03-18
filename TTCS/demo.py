from fastai.vision.all import load_learner

model = load_learner("model.pkl")

from PIL import Image

img = Image.open("images.jpg")  # Replace with your image path
prediction = model.predict(img)
print(prediction)
