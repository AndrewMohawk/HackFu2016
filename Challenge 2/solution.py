import Image

def LSB(b):
        
        b = str(bin(b))[-1:]
        if(b == "1"):
                return 0
        else:
                return 255
        
image = Image.open("image")
out = image.copy()
out = image.convert('RGB')
out.putdata( [(LSB(r), LSB(g), LSB(b))
                for r, g, b in out.getdata()] )

out.save("LSB.bmp")