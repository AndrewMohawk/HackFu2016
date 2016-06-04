#!/usr/bin/python
from PIL import Image
import _imaging
import sys

img = Image.open("image.jpg")

for i in range (0,100):
    x1 = 790 - (i * 10)
    x2 = 810 + (i * 10)
    y1 = 790 - (i * 10)
    y2 = 810 + (i * 10)
    inlay = img.crop((x1,y1,x2,y2)).rotate(90)
    img.paste(inlay, (x1,y1,x2,y2))
img.save("imagedecoded.jpg")